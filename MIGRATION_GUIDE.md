# Migration from Google Gemini to Self-Hosted CLIP

## What Changed

Your website now uses a **self-hosted CLIP AI service** instead of Google Gemini API.

### Benefits

✅ **No API costs** - Completely free  
✅ **Privacy** - All processing happens on your server  
✅ **No rate limits** - Process as many images as you want  
✅ **Works offline** - Once model is downloaded  
✅ **Production-ready** - Deploy on any server

## Quick Start

### 1. Start the AI Service

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

### 2. Verify It's Running

Visit: `http://127.0.0.1:5000/health`

You should see:

```json
{ "status": "healthy", "model_loaded": true }
```

### 3. That's It!

Your website will now use the local CLIP service automatically. No configuration needed!

## Configuration (Optional)

If your AI service runs on a different port, add to `.env`:

```env
CLIP_API_URL=http://127.0.0.1:5000
```

## What You No Longer Need

- ❌ `GEMINI_API_KEY` in `.env` (can be removed)
- ❌ Google Gemini API account
- ❌ Internet connection for AI search (after initial model download)

## What You Need Now

- ✅ Python 3.8+ installed
- ✅ AI service running (see `ai-service/README.md`)
- ✅ 4GB+ RAM (8GB+ recommended)

## Testing

1. Start the AI service
2. Go to your website
3. Search for "car" (or any image description)
4. You should see the loading animation, then results!

## Troubleshooting

**Service won't start?**

- Check Python is installed: `python --version`
- Check port 5000 is free
- See `ai-service/README.md` for details

**PHP can't connect?**

- Make sure service is running: `http://127.0.0.1:5000/health`
- Check `.env` has correct `CLIP_API_URL`
- Check PHP error logs

**Slow performance?**

- First run downloads model (~350MB) - be patient
- Use GPU if available (automatic)
- Reduce batch size in `ai-service/app.py` if needed

## Production Deployment

See `SETUP_LOCAL_AI.md` for production deployment instructions using:

- Windows: NSSM
- Linux: systemd
- Docker: Optional

## Performance

- **CPU**: ~1-2 seconds per image
- **GPU**: ~0.1-0.3 seconds per image
- **Model size**: ~350MB (downloads once)

The AI search will work exactly the same as before, but now it's completely self-hosted!
