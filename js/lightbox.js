/**
 * Image Lightbox/Modal Viewer
 */

let currentLightboxIndex = 0;
let lightboxFiles = [];

/**
 * Open lightbox with image
 * @param {string} url - Image URL
 * @param {string} publicId - File public ID
 * @param {Array} files - Array of all files for navigation
 */
function openLightbox(url, publicId, files = []) {
    lightboxFiles = files;
    currentLightboxIndex = files.findIndex(f => f.public_id === publicId);
    if (currentLightboxIndex === -1) currentLightboxIndex = 0;

    const lightbox = document.getElementById('lightbox');
    if (!lightbox) {
        createLightbox();
    }
    
    updateLightboxContent();
    
    // Show lightbox with animation
    const lightboxEl = document.getElementById('lightbox');
    lightboxEl.style.display = 'flex';
    
    // Trigger animation
    requestAnimationFrame(() => {
        lightboxEl.classList.remove('opacity-0');
        lightboxEl.classList.add('opacity-100', 'bg-black/95');
        lightboxEl.querySelector('.max-w-7xl').classList.remove('scale-95');
        lightboxEl.querySelector('.max-w-7xl').classList.add('scale-100');
        
        // Show navigation buttons with delay
        setTimeout(() => {
            const navButtons = lightboxEl.querySelectorAll('button:not(:first-child)');
            navButtons.forEach(btn => btn.classList.remove('opacity-0'));
        }, 200);
        
        // Show info with delay
        setTimeout(() => {
            const info = lightboxEl.querySelector('#lightboxInfo');
            if (info) info.classList.remove('opacity-0');
        }, 300);
    });
    
    document.body.style.overflow = 'hidden';
}

/**
 * Create lightbox HTML structure
 */
function createLightbox() {
    const lightbox = document.createElement('div');
    lightbox.id = 'lightbox';
    lightbox.className = 'fixed inset-0 bg-black/0 backdrop-blur-sm z-50 flex items-center justify-center opacity-0 transition-all duration-300 ease-out';
    lightbox.style.display = 'none';
    lightbox.innerHTML = `
        <button onclick="closeLightbox()" class="absolute top-4 right-4 text-white/80 hover:text-green-500 transition-all duration-200 transform hover:scale-110 z-10">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <button onclick="lightboxPrevious()" class="absolute left-4 text-white/80 hover:text-green-500 transition-all duration-200 transform hover:scale-110 z-10 p-2 bg-black/50 rounded-full opacity-0 hover:opacity-100">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
        <button onclick="lightboxNext()" class="absolute right-4 text-white/80 hover:text-green-500 transition-all duration-200 transform hover:scale-110 z-10 p-2 bg-black/50 rounded-full opacity-0 hover:opacity-100">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>
        <div class="max-w-7xl max-h-[90vh] p-4 transform scale-95 transition-all duration-300 ease-out">
            <img id="lightboxImage" src="" alt="Preview" class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl">
            <div id="lightboxInfo" class="mt-4 text-center text-white opacity-0 transition-opacity duration-300 delay-200"></div>
        </div>
    `;
    document.body.appendChild(lightbox);
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (document.getElementById('lightbox')?.style.display === 'flex') {
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowLeft') lightboxPrevious();
            if (e.key === 'ArrowRight') lightboxNext();
        }
    });
}

/**
 * Update lightbox content
 */
function updateLightboxContent() {
    if (lightboxFiles.length === 0) return;
    
    const file = lightboxFiles[currentLightboxIndex];
    const img = document.getElementById('lightboxImage');
    const info = document.getElementById('lightboxInfo');
    
    if (file.resource_type === 'image') {
        img.src = file.url;
        img.style.display = 'block';
    } else if (file.resource_type === 'video') {
        img.outerHTML = `<video id="lightboxImage" src="${file.url}" controls class="max-w-full max-h-[90vh] object-contain rounded-lg"></video>`;
    } else {
        img.style.display = 'none';
    }
    
    if (info) {
        info.innerHTML = `
            <p class="text-lg font-semibold">${file.filename}</p>
            <p class="text-sm text-gray-400">${formatFileSize(file.size)} • ${file.format?.toUpperCase() || 'FILE'}</p>
        `;
    }
}

/**
 * Close lightbox
 */
function closeLightbox() {
    const lightbox = document.getElementById('lightbox');
    if (lightbox) {
        // Start closing animation
        lightbox.classList.remove('opacity-100', 'bg-black/95');
        lightbox.classList.add('opacity-0', 'bg-black/0');
        lightbox.querySelector('.max-w-7xl').classList.remove('scale-100');
        lightbox.querySelector('.max-w-7xl').classList.add('scale-95');
        
        // Hide navigation buttons and info
        const navButtons = lightbox.querySelectorAll('button:not(:first-child)');
        navButtons.forEach(btn => btn.classList.add('opacity-0'));
        const info = lightbox.querySelector('#lightboxInfo');
        if (info) info.classList.add('opacity-0');
        
        // Hide lightbox after animation completes
        setTimeout(() => {
            lightbox.style.display = 'none';
            document.body.style.overflow = '';
        }, 300);
    }
}

/**
 * Navigate to previous image
 */
function lightboxPrevious() {
    if (lightboxFiles.length === 0) return;
    currentLightboxIndex = (currentLightboxIndex - 1 + lightboxFiles.length) % lightboxFiles.length;
    updateLightboxContent();
}

/**
 * Navigate to next image
 */
function lightboxNext() {
    if (lightboxFiles.length === 0) return;
    currentLightboxIndex = (currentLightboxIndex + 1) % lightboxFiles.length;
    updateLightboxContent();
}

