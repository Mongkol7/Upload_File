# Quick Start Guide - AI Search & Feedback

## 🚀 Quick Setup (2 minutes)

### Step 1: Get Your Free Gemini API Key
1. Visit: https://makersuite.google.com/app/apikey
2. Sign in with Google
3. Click "Create API Key"
4. Copy the key

### Step 2: Add to .env File
Add this line to your `.env` file:
```
GEMINI_API_KEY=your_api_key_here
```

### Step 3: Done! 🎉
- **AI Search**: Click the AI icon (💡) next to the search box to enable semantic search
- **Feedback**: Click the message icon (💬) at bottom right to send feedback

## ✨ Features

### AI-Powered Search
- **Regular Mode**: Searches by filename (default)
- **AI Mode**: Understands meaning and content of your search
- Toggle with the lightbulb icon next to search box
- Works with images, videos, and documents

### Feedback Form
- Round button at bottom right corner
- Sends directly to: thoeungsereymongkol@gmail.com
- Name and email are optional

## 📝 Example Usage

**AI Search Examples:**
- Search "sunset" → Finds all sunset images
- Search "document about taxes" → Finds tax-related PDFs
- Search "video of cat" → Finds cat videos

**Feedback:**
- Click 💬 button
- Type your message
- Click "Send Feedback"
- Done!

## ⚠️ Troubleshooting

**AI Search not working?**
- Check `.env` file has `GEMINI_API_KEY`
- Check browser console for errors
- Make sure API key is valid

**Feedback not sending?**
- For local development, PHP mail() may need SMTP configuration
- Check PHP error logs
- For production, consider using PHPMailer with SMTP

## 📚 More Info

See `SETUP_AI.md` for detailed documentation.

