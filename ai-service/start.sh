#!/bin/bash
# Start script for AI service (Linux/Mac)

echo "Starting AI Image Analysis Service..."

# Check if virtual environment exists
if [ ! -d "venv" ]; then
    echo "Creating virtual environment..."
    python3 -m venv venv
fi

# Activate virtual environment
source venv/bin/activate

# Install dependencies
echo "Installing dependencies..."
pip install -r requirements.txt

# Start the service
echo "Starting Flask server..."
python app.py

