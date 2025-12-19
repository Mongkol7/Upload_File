"""
Self-hosted AI Image Analysis Service using CLIP
Provides semantic image search capabilities
Optimized for speed, accuracy, and performance
"""

from flask import Flask, request, jsonify
from flask_cors import CORS
import torch
try:
    import clip
except ImportError:
    # If clip-by-openai is not available, try installing it
    import subprocess
    import sys
    print("Installing clip-by-openai...")
    subprocess.check_call([sys.executable, "-m", "pip", "install", "git+https://github.com/openai/CLIP.git"])
    import clip
from PIL import Image
import requests
from io import BytesIO
from concurrent.futures import ThreadPoolExecutor, as_completed
import numpy as np
import os
from dotenv import load_dotenv
import logging
import time

# Load environment variables
load_dotenv()

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

app = Flask(__name__)
CORS(app)  # Enable CORS for PHP requests

# Global variables for model
device = None
model = None
preprocess = None

# Cache for text embeddings (same query = same embedding)
text_embedding_cache = {}

# Thread pool for parallel image downloads
download_executor = ThreadPoolExecutor(max_workers=5)

def load_clip_model():
    """Load CLIP model (runs once on startup) - Optimized"""
    global device, model, preprocess
    
    try:
        logger.info("Loading CLIP model...")
        device = "cuda" if torch.cuda.is_available() else "cpu"
        logger.info(f"Using device: {device}")
        
        # Load CLIP model (ViT-B/32 is optimized for speed/accuracy balance)
        # For better accuracy, you can use "ViT-L/14" but it's slower
        model_name = os.getenv('CLIP_MODEL', 'ViT-B/32')
        model, preprocess = clip.load(model_name, device=device)
        model.eval()
        
        # Enable optimizations
        if device == 'cpu':
            # Use CPU optimizations
            torch.set_num_threads(4)  # Use 4 threads for CPU
        
        logger.info(f"CLIP model '{model_name}' loaded successfully on {device}")
        logger.info("Optimizations: Batch processing, parallel downloads, caching enabled")
        return True
    except Exception as e:
        logger.error(f"Error loading CLIP model: {str(e)}")
        return False

def download_image(url):
    """Download image from URL with optimization"""
    try:
        # Use smaller images for faster processing (Cloudinary optimization)
        # If it's already a Cloudinary URL, optimize it
        if 'res.cloudinary.com' in url and '/upload/' in url:
            # Add transformation to get smaller, optimized image
            if '/upload/' in url and not any(x in url for x in ['w_', 'h_', 'c_']):
                # Insert optimization before /upload/
                url = url.replace('/upload/', '/upload/w_512,h_512,c_limit,f_auto,q_auto/')
        
        response = requests.get(url, timeout=15, stream=True)
        response.raise_for_status()
        img = Image.open(BytesIO(response.content))
        
        # Convert RGBA to RGB if needed
        if img.mode == 'RGBA':
            img = img.convert('RGB')
        
        # Resize if too large (faster processing)
        max_size = 512
        if max(img.size) > max_size:
            img.thumbnail((max_size, max_size), Image.Resampling.LANCZOS)
        
        return img
    except Exception as e:
        logger.error(f"Error downloading image from {url[:50]}...: {str(e)}")
        return None

def download_images_parallel(urls):
    """Download multiple images in parallel"""
    results = {}
    futures = {download_executor.submit(download_image, url): url for url in urls}
    
    for future in as_completed(futures):
        url = futures[future]
        try:
            results[url] = future.result()
        except Exception as e:
            logger.error(f"Error downloading {url[:50]}...: {str(e)}")
            results[url] = None
    
    return results

def get_text_embedding(query):
    """Get cached text embedding for a query"""
    # Use simple dict cache (lru_cache doesn't work well with torch tensors)
    if query in text_embedding_cache:
        return text_embedding_cache[query]
    
    text = clip.tokenize([query]).to(device)
    with torch.no_grad():
        text_features = model.encode_text(text)
        text_features = text_features / text_features.norm(dim=-1, keepdim=True)
    
    # Cache the result
    text_embedding_cache[query] = text_features
    return text_features

# Remove expand_query as we'll use a simpler approach

def analyze_images_batch(image_urls, query, threshold=0.20):
    """Analyze multiple images in batch for much better performance"""
    try:
        start_time = time.time()
        
        # Download all images in parallel
        logger.info(f"Downloading {len(image_urls)} images in parallel...")
        images_dict = download_images_parallel(image_urls)
        
        # Filter out None values
        valid_images = {url: img for url, img in images_dict.items() if img is not None}
        
        if not valid_images:
            return {}
        
        logger.info(f"Downloaded {len(valid_images)}/{len(image_urls)} images in {time.time() - start_time:.2f}s")
        
        # Get text embedding (cached)
        text_features = get_text_embedding(query)
        
        # Prepare all images for batch processing
        image_tensors = []
        valid_urls = []
        
        for url, img in valid_images.items():
            try:
                tensor = preprocess(img).unsqueeze(0)
                image_tensors.append(tensor)
                valid_urls.append(url)
            except Exception as e:
                logger.warning(f"Error preprocessing {url[:50]}...: {str(e)}")
                continue
        
        if not image_tensors:
            return {}
        
        # Batch process all images at once (much faster!)
        batch_tensor = torch.cat(image_tensors, dim=0).to(device)
        
        # Get embeddings for all images at once
        with torch.no_grad():
            image_features = model.encode_image(batch_tensor)
            image_features = image_features / image_features.norm(dim=-1, keepdim=True)
            
            # Calculate similarities for all images at once
            similarities = (image_features @ text_features.T).cpu().numpy().flatten()
        
        # Create results dictionary
        results = {}
        for url, similarity in zip(valid_urls, similarities):
            matches = similarity >= threshold
            results[url] = {
                'matches': bool(matches),
                'similarity': float(similarity)
            }
        
        logger.info(f"Batch analyzed {len(results)} images in {time.time() - start_time:.2f}s total")
        return results
        
    except Exception as e:
        logger.error(f"Error in batch analysis: {str(e)}")
        return {}

def analyze_image(image_url, query):
    """Analyze a single image (fallback for single image requests)"""
    results = analyze_images_batch([image_url], query)
    if image_url in results:
        result = results[image_url]
        return result['matches'], result['similarity']
    return False, 0.0

@app.route('/health', methods=['GET'])
def health():
    """Health check endpoint"""
    return jsonify({
        'status': 'healthy',
        'model_loaded': model is not None,
        'device': device if device else 'unknown'
    })

@app.route('/analyze', methods=['POST'])
def analyze():
    """Analyze a single image"""
    try:
        data = request.json
        image_url = data.get('url')
        query = data.get('query')
        
        if not image_url or not query:
            return jsonify({
                'success': False,
                'error': 'Missing url or query parameter'
            }), 400
        
        matches, similarity = analyze_image(image_url, query)
        
        return jsonify({
            'success': True,
            'matches': matches,
            'similarity': float(similarity)
        })
        
    except Exception as e:
        logger.error(f"Error in analyze endpoint: {str(e)}")
        return jsonify({
            'success': False,
            'error': str(e)
        }), 500

@app.route('/search', methods=['POST'])
def search():
    """Search multiple images for a query - OPTIMIZED VERSION"""
    try:
        start_time = time.time()
        data = request.json
        files = data.get('files', [])
        query = data.get('query', '').strip().lower()
        
        if not query:
            return jsonify({
                'success': False,
                'error': 'Missing query parameter'
            }), 400
        
        if not files:
            return jsonify({
                'success': True,
                'files': [],
                'processing_time': 0
            })
        
        # Filter and prepare files
        files_to_analyze = []
        url_to_file = {}
        
        for file in files[:30]:  # Increased limit to 30 for batch processing
            file_url = file.get('url')
            resource_type = file.get('resource_type', 'image')
            
            # Only analyze images and videos
            if resource_type not in ['image', 'video']:
                continue
            
            if not file_url:
                continue
            
            # For videos, use optimized thumbnail
            if resource_type == 'video':
                # Cloudinary optimized thumbnail
                if '/upload/' in file_url:
                    file_url = file_url.replace('/upload/', '/upload/w_512,h_512,c_limit,f_auto,q_auto/')
                else:
                    file_url = file_url.replace('/upload/', '/upload/w_512,h_512,c_limit,f_auto,q_auto/')
            
            files_to_analyze.append(file_url)
            url_to_file[file_url] = file
        
        if not files_to_analyze:
            return jsonify({
                'success': True,
                'files': [],
                'processing_time': 0
            })
        
        logger.info(f"Searching {len(files_to_analyze)} files for query: '{query}'")
        
        # Dynamic threshold based on query length and type
        # Shorter queries = lower threshold (more results)
        # Longer queries = higher threshold (more specific)
        query_words = len(query.split())
        if query_words == 1:
            threshold = 0.18  # Single word - be more lenient
        elif query_words <= 3:
            threshold = 0.20  # Short phrase
        else:
            threshold = 0.22  # Long phrase - be more specific
        
        # Batch analyze all images at once (MUCH FASTER!)
        results = analyze_images_batch(files_to_analyze, query, threshold)
        
        # Build matched files list
        matched_files = []
        for url, result in results.items():
            if result['matches'] and url in url_to_file:
                matched_files.append({
                    **url_to_file[url],
                    'similarity': result['similarity']
                })
        
        # Sort by similarity (highest first)
        matched_files.sort(key=lambda x: x.get('similarity', 0), reverse=True)
        
        processing_time = time.time() - start_time
        logger.info(f"Found {len(matched_files)} matching files in {processing_time:.2f}s (avg {processing_time/len(files_to_analyze):.3f}s per image)")
        
        return jsonify({
            'success': True,
            'files': matched_files,
            'count': len(matched_files),
            'processing_time': round(processing_time, 2),
            'threshold_used': threshold
        })
        
    except Exception as e:
        logger.error(f"Error in search endpoint: {str(e)}", exc_info=True)
        return jsonify({
            'success': False,
            'error': str(e)
        }), 500

if __name__ == '__main__':
    # Load model on startup
    if load_clip_model():
        port = int(os.getenv('PORT', 5000))
        host = os.getenv('HOST', '127.0.0.1')
        logger.info(f"Starting server on {host}:{port}")
        app.run(host=host, port=port, debug=False)
    else:
        logger.error("Failed to load CLIP model. Exiting.")
        exit(1)

