/**
 * Upload Progress Indicator - Per-file progress bars
 */

/**
 * Show upload progress
 * @param {string} fileName - File name
 * @param {number} progress - Progress percentage (0-100)
 */
function showUploadProgress(fileName, progress) {
    let progressBar = document.getElementById('uploadProgressBar');
    
    if (!progressBar) {
        progressBar = document.createElement('div');
        progressBar.id = 'uploadProgressBar';
        progressBar.className = 'fixed bottom-6 right-6 bg-gray-800/90 backdrop-blur-lg rounded-2xl p-4 border border-green-500/30 shadow-xl z-40 max-w-sm';
        progressBar.innerHTML = `
            <div class="flex items-center justify-between mb-2">
                <span class="text-white font-semibold text-sm">Uploading...</span>
                <button onclick="hideUploadProgress()" class="text-gray-400 hover:text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <p class="text-gray-400 text-xs mb-2 truncate" id="uploadFileName">${fileName}</p>
            <div class="w-full bg-gray-700 rounded-full h-2">
                <div id="uploadProgressFill" class="bg-green-500 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
            <p class="text-gray-400 text-xs mt-2 text-right" id="uploadProgressText">0%</p>
        `;
        document.body.appendChild(progressBar);
    }
    
    document.getElementById('uploadFileName').textContent = fileName;
    document.getElementById('uploadProgressFill').style.width = progress + '%';
    document.getElementById('uploadProgressText').textContent = Math.round(progress) + '%';
}

/**
 * Hide upload progress
 */
function hideUploadProgress() {
    const progressBar = document.getElementById('uploadProgressBar');
    if (progressBar) {
        progressBar.remove();
    }
}

// Simulate upload progress for form submissions (since we can't track actual progress with regular form submit)
document.addEventListener('DOMContentLoaded', function() {
    const uploadForm = document.getElementById('uploadForm');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            const fileInput = document.getElementById('fileToUpload');
            if (fileInput && fileInput.files.length > 0) {
                const fileName = fileInput.files[0].name;
                showUploadProgress(fileName, 0);
                
                // Simulate progress (since we can't track actual upload progress with form submit)
                let progress = 0;
                const interval = setInterval(() => {
                    progress += Math.random() * 15;
                    if (progress > 90) progress = 90; // Don't go to 100% until actual completion
                    showUploadProgress(fileName, progress);
                }, 200);
                
                // Clear interval after form submission (page will reload)
                setTimeout(() => clearInterval(interval), 5000);
            }
        });
    }
});

