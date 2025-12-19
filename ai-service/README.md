# Self-Hosted AI Image Analysis Service

This service provides semantic image search using CLIP (Contrastive Language-Image Pre-training) model. It runs locally and can be called from PHP.

## Features

- **Semantic Image Search**: Find images by describing their content (e.g., "car", "sunset", "person smiling")
- **Self-Hosted**: Runs entirely on your server, no external API calls
- **Fast**: Uses CLIP ViT-B/32 model (good balance of speed and accuracy)
- **GPU Support**: Automatically uses GPU if available, falls back to CPU

## Requirements

- Python 3.8 or higher
- 4GB+ RAM (8GB+ recommended)
- Optional: NVIDIA GPU with CUDA support (for faster processing)

## Installation

### Windows

1. Open Command Prompt in the `ai-service` folder
2. Run `start.bat` (it will create virtual environment and install dependencies)

### Linux/Mac

1. Open terminal in the `ai-service` folder
2. Make script executable: `chmod +x start.sh`
3. Run: `./start.sh`

### Manual Installation

```bash
# Create virtual environment
python3 -m venv venv

# Activate virtual environment
# Windows:
venv\Scripts\activate
# Linux/Mac:
source venv/bin/activate

# Install dependencies
pip install -r requirements.txt

# Start server
python app.py
```

## Usage

The service runs on `http://127.0.0.1:5000` by default.

### Health Check
```bash
curl http://127.0.0.1:5000/health
```

### Search Images
```bash
curl -X POST http://127.0.0.1:5000/search \
  -H "Content-Type: application/json" \
  -d '{
    "query": "car",
    "files": [
      {"url": "https://example.com/image1.jpg", "resource_type": "image"},
      {"url": "https://example.com/image2.jpg", "resource_type": "image"}
    ]
  }'
```

## API Endpoints

### POST /search
Search multiple images for a query.

**Request:**
```json
{
  "query": "car",
  "files": [
    {
      "url": "https://...",
      "resource_type": "image",
      "public_id": "...",
      "filename": "..."
    }
  ]
}
```

**Response:**
```json
{
  "success": true,
  "files": [...],
  "count": 5
}
```

### POST /analyze
Analyze a single image.

**Request:**
```json
{
  "url": "https://...",
  "query": "car"
}
```

**Response:**
```json
{
  "success": true,
  "matches": true,
  "similarity": 0.75
}
```

## Configuration

Create a `.env` file to configure:

```env
PORT=5000
HOST=127.0.0.1
```

## Performance

- **CPU**: ~1-2 seconds per image
- **GPU**: ~0.1-0.3 seconds per image
- **Batch processing**: Analyzes up to 20 images per request

## Troubleshooting

### Model download fails
The CLIP model will be downloaded automatically on first run (~350MB). Ensure you have internet connection.

### Out of memory
If you get memory errors:
- Reduce batch size in `app.py` (currently 20)
- Use CPU instead of GPU
- Process fewer images at once

### Service won't start
- Check Python version: `python --version` (need 3.8+)
- Check if port 5000 is available
- Check error logs in console

## Production Deployment

For production, use a process manager like:
- **Windows**: NSSM (Non-Sucking Service Manager)
- **Linux**: systemd or supervisor
- **Docker**: See Dockerfile (if provided)

## License

Uses CLIP model from OpenAI (MIT License)

