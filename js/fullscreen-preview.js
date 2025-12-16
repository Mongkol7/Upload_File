/**
 * Fullscreen Preview - Show file in 80% fullscreen with blurred background
 */

/**
 * Open fullscreen preview
 * @param {string} url - File URL
 * @param {string} resourceType - Resource type (image, video, etc.)
 * @param {string} filename - File name
 */
function openFullscreenPreview(url, resourceType, filename) {
    // Remove existing preview if any
    const existing = document.getElementById('fullscreenPreview');
    if (existing) {
        existing.remove();
    }
    
    // Create preview overlay
    const preview = document.createElement('div');
    preview.id = 'fullscreenPreview';
    preview.className = 'fixed inset-0 z-50 flex items-center justify-center';
    preview.style.backdropFilter = 'blur(10px)';
    preview.style.backgroundColor = 'rgba(0, 0, 0, 0.8)';
    
    // Click on overlay to close
    preview.addEventListener('click', function(e) {
        if (e.target === preview) {
            closeFullscreenPreview();
        }
    });
    
    // Create content container (80% of screen)
    const container = document.createElement('div');
    container.className = 'relative w-[80vw] h-[80vh] max-w-[80vw] max-h-[80vh] flex items-center justify-center';
    container.style.pointerEvents = 'auto';
    container.addEventListener('click', function(e) {
        e.stopPropagation(); // Prevent closing when clicking on content
    });
    
    // Create close button
    const closeBtn = document.createElement('button');
    closeBtn.className = 'absolute top-4 right-4 z-10 text-white hover:text-green-500 transition-colors bg-black/50 rounded-full p-2';
    closeBtn.innerHTML = `
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    `;
    closeBtn.onclick = closeFullscreenPreview;
    
    // Create content based on file type
    let content;
    if (resourceType === 'image') {
        content = document.createElement('img');
        content.src = url;
        content.alt = filename;
        content.className = 'max-w-full max-h-full object-contain rounded-lg shadow-2xl';
    } else if (resourceType === 'video') {
        content = document.createElement('video');
        content.src = url;
        content.controls = true;
        content.className = 'max-w-full max-h-full object-contain rounded-lg shadow-2xl';
    } else {
        // For other file types, show a message
        content = document.createElement('div');
        content.className = 'bg-gray-800 rounded-lg p-8 text-center border border-green-500/30';
        content.innerHTML = `
            <svg class="w-24 h-24 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-xl text-white mb-2">${filename}</p>
            <p class="text-gray-400 mb-4">This file type cannot be previewed</p>
            <a href="${url}" target="_blank" class="inline-block px-6 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors">
                Open in New Tab
            </a>
        `;
    }
    
    // Assemble preview
    container.appendChild(closeBtn);
    container.appendChild(content);
    preview.appendChild(container);
    
    // Add to body
    document.body.appendChild(preview);
    document.body.style.overflow = 'hidden';
    
    // Add fade-in animation
    requestAnimationFrame(() => {
        preview.style.opacity = '0';
        preview.style.transition = 'opacity 0.3s ease-in-out';
        requestAnimationFrame(() => {
            preview.style.opacity = '1';
        });
    });
    
    // Close on Escape key
    const escapeHandler = function(e) {
        if (e.key === 'Escape') {
            closeFullscreenPreview();
            document.removeEventListener('keydown', escapeHandler);
        }
    };
    document.addEventListener('keydown', escapeHandler);
}

/**
 * Close fullscreen preview
 */
function closeFullscreenPreview() {
    const preview = document.getElementById('fullscreenPreview');
    if (preview) {
        preview.style.opacity = '0';
        preview.style.transition = 'opacity 0.3s ease-in-out';
        setTimeout(() => {
            preview.remove();
            document.body.style.overflow = '';
        }, 300);
    }
}

