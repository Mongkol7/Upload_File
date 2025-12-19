# AI Search Troubleshooting Guide

## Issue: AI Search Shows "Searching" But No Results

### Check 1: Service is Running
Visit: `http://127.0.0.1:5000/health`
Should show: `{"status": "healthy", "model_loaded": true}`

### Check 2: Check Browser Console
1. Open browser Developer Tools (F12)
2. Go to Console tab
3. Search for something
4. Look for error messages or logs

### Check 3: Check PHP Error Logs
Look in your PHP error log (usually in XAMPP: `C:\xampp\php\logs\php_error_log`)
Look for lines starting with "CLIP Search:"

### Check 4: Check Python Service Logs
Look at the Python window running the service. You should see:
- "Searching X files for query: 'your query'"
- "Image ...: similarity=X.XXX, matches=True/False"
- "Found X matching files"

### Common Issues

#### Issue: No files being sent to AI
**Symptom**: Python logs show "Searching 0 files"
**Fix**: Check that you have images/videos in Cloudinary

#### Issue: Images not downloading
**Symptom**: Python logs show "Failed to fetch image"
**Fix**: 
- Check Cloudinary URLs are accessible
- Check internet connection
- Verify Cloudinary URLs are public

#### Issue: No matches found
**Symptom**: Python logs show files analyzed but "Found 0 matching files"
**Fix**: 
- Try a more general search term
- The similarity threshold is 0.20 (20% match)
- Some images may not match even if they seem related

#### Issue: Service timeout
**Symptom**: Request times out after 60 seconds
**Fix**:
- Reduce number of files analyzed (currently limited to 20)
- Check internet speed (images download from Cloudinary)
- Consider using GPU for faster processing

### Debug Steps

1. **Test the service directly:**
```bash
curl -X POST http://127.0.0.1:5000/search \
  -H "Content-Type: application/json" \
  -d '{"query": "car", "files": [{"url": "YOUR_CLOUDINARY_URL", "resource_type": "image"}]}'
```

2. **Check what files are being sent:**
Look in PHP error log for "Sample file URL:" messages

3. **Check similarity scores:**
Look in Python window for similarity scores. If all are below 0.20, no matches will be found.

### Adjusting Sensitivity

To get more results, lower the threshold in `ai-service/app.py`:
```python
threshold = 0.15  # Lower = more results
```

To get fewer, more accurate results:
```python
threshold = 0.25  # Higher = fewer but more accurate
```

### Still Not Working?

1. Check all logs (PHP, Python, Browser Console)
2. Verify service is running: `http://127.0.0.1:5000/health`
3. Test with a simple query like "car" or "person"
4. Make sure you have actual images in Cloudinary (not just filenames)

