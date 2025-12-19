# Setup Self-Hosted AI Image Search

This guide will help you set up the self-hosted CLIP-based AI image search service.

## What Changed

- **Before**: Used Google Gemini API (cloud-based, requires API key)
- **Now**: Uses CLIP model running locally on your server (free, no API key needed)

## Benefits

✅ **Free** - No API costs  
✅ **Private** - All processing happens on your server  
✅ **Fast** - No network latency to external APIs  
✅ **Works offline** - Once model is downloaded  
✅ **Production-ready** - Can be deployed on any server

## Requirements

- Python 3.8 or higher
- 4GB+ RAM (8GB+ recommended)
- Optional: NVIDIA GPU for faster processing

## Quick Setup

### Step 1: Install Python Dependencies

**Windows:**

```bash
cd ai-service
start.bat
```

**Linux/Mac:**

```bash
cd ai-service
chmod +x start.sh
./start.sh
```

The script will:

1. Create a Python virtual environment
2. Install all required packages (including CLIP model)
3. Start the AI service on port 5000

### Step 2: Verify Service is Running

Open browser and go to: `http://127.0.0.1:5000/health`

You should see:

```json
{
  "status": "healthy",
  "model_loaded": true,
  "device": "cpu"
}
```

### Step 3: Configure PHP (Optional)

If your AI service runs on a different port or host, add to your `.env` file:

```env
CLIP_API_URL=http://127.0.0.1:5000
```

## Manual Setup

If the scripts don't work, follow these steps:

```bash
# 1. Navigate to ai-service folder
cd ai-service

# 2. Create virtual environment
python3 -m venv venv

# 3. Activate virtual environment
# Windows:
venv\Scripts\activate
# Linux/Mac:
source venv/bin/activate

# 4. Install dependencies
pip install -r requirements.txt

# 5. Start service
python app.py
```

## Running in Production

### Windows (Using NSSM)

1. Download NSSM: https://nssm.cc/download
2. Install as service:

```bash
nssm install ClipAIService
# Set path to: C:\path\to\website\ai-service\venv\Scripts\python.exe
# Set arguments to: app.py
# Set working directory to: C:\path\to\website\ai-service
```

### Linux (Using systemd)

Create `/etc/systemd/system/clip-ai.service`:

```ini
[Unit]
Description=CLIP AI Image Search Service
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/path/to/website/ai-service
Environment="PATH=/path/to/website/ai-service/venv/bin"
ExecStart=/path/to/website/ai-service/venv/bin/python app.py
Restart=always

[Install]
WantedBy=multi-user.target
```

Then:

```bash
sudo systemctl enable clip-ai
sudo systemctl start clip-ai
```

### Docker (Optional)

If you prefer Docker, create a `Dockerfile` in `ai-service/`:

```dockerfile
FROM python:3.9-slim

WORKDIR /app

COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

COPY . .

EXPOSE 5000

CMD ["python", "app.py"]
```

## Testing

Test the service:

```bash
curl -X POST http://127.0.0.1:5000/search \
  -H "Content-Type: application/json" \
  -d '{
    "query": "car",
    "files": [
      {"url": "https://example.com/car.jpg", "resource_type": "image"}
    ]
  }'
```

## Troubleshooting

### Service won't start

1. **Check Python version**: `python --version` (need 3.8+)
2. **Check port 5000**: Make sure it's not in use
3. **Check dependencies**: Run `pip install -r requirements.txt` again
4. **Check logs**: Look at console output for errors

### Model download fails

- Ensure internet connection (model downloads on first run, ~350MB)
- Check disk space (need ~1GB free)
- Try manual download: The model will be cached in `~/.cache/clip/`

### Out of memory

- Reduce batch size in `app.py` (line with `files[:20]`)
- Use CPU instead of GPU
- Process fewer images at once

### PHP can't connect

1. **Check service is running**: Visit `http://127.0.0.1:5000/health`
2. **Check firewall**: Port 5000 must be accessible
3. **Check URL in .env**: Verify `CLIP_API_URL` is correct
4. **Check PHP error logs**: Look for connection errors

## Performance Tips

1. **Use GPU**: If you have NVIDIA GPU, install CUDA for 10x speedup
2. **Increase batch size**: Edit `app.py` to process more images at once
3. **Cache results**: Consider caching analysis results for frequently searched images
4. **Use production server**: Use gunicorn or uwsgi instead of Flask dev server

## Model Information

- **Model**: CLIP ViT-B/32
- **Size**: ~350MB (downloads automatically)
- **Speed**:
  - CPU: ~1-2 seconds per image
  - GPU: ~0.1-0.3 seconds per image
- **Accuracy**: Excellent for semantic search

## Next Steps

1. Start the AI service
2. Test it works: `http://127.0.0.1:5000/health`
3. Try searching on your website
4. Set up as a service for production (see above)

The AI search will now work completely offline and privately on your server!
