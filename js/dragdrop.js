/**
 * Drag and Drop Upload Functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('fileToUpload');
    const uploadForm = document.getElementById('uploadForm');
    const uploadContainer = uploadForm?.querySelector('.upload-area');
    
    if (!uploadArea || !uploadForm) return;

    // Create drag overlay if it doesn't exist
    let dragOverlay = document.getElementById('dragOverlay');
    if (!dragOverlay) {
        dragOverlay = document.createElement('div');
        dragOverlay.id = 'dragOverlay';
        dragOverlay.className = 'fixed inset-0 bg-green-500/20 backdrop-blur-sm z-40 flex items-center justify-center pointer-events-none opacity-0 transition-opacity duration-300';
        dragOverlay.innerHTML = `
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-green-500/30 border-4 border-green-500 border-dashed mb-4">
                    <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-green-500">Drop files here to upload</p>
            </div>
        `;
        document.body.appendChild(dragOverlay);
    }

    // Prevent default drag behaviors
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        document.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    // Highlight drop area when item is dragged over it
    ['dragenter', 'dragover'].forEach(eventName => {
        document.addEventListener(eventName, function(e) {
            dragOverlay.style.opacity = '1';
            dragOverlay.style.pointerEvents = 'auto';
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        document.addEventListener(eventName, function(e) {
            dragOverlay.style.opacity = '0';
            dragOverlay.style.pointerEvents = 'none';
        }, false);
    });

    // Handle dropped files
    document.addEventListener('drop', function(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        if (files.length > 0) {
            uploadArea.files = files;
            // Trigger form submission
            showLoading('Uploading files...');
            uploadForm.submit();
        }
    }, false);
});

