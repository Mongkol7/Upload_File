/**
 * File Preview on Hover - Larger thumbnail preview tooltip
 */

let previewTooltip = null;
let previewTimeout = null;

/**
 * Show preview tooltip
 * @param {Event} e - Mouse event
 * @param {string} url - File URL
 * @param {string} resourceType - Resource type
 */
function showFilePreview(e, url, resourceType) {
    // Clear any existing timeout
    if (previewTimeout) {
        clearTimeout(previewTimeout);
    }
    
    // Remove existing tooltip
    if (previewTooltip) {
        previewTooltip.remove();
        previewTooltip = null;
    }
    
    if (resourceType !== 'image') return; // Only show preview for images
    
    // Add a small delay to prevent showing on accidental hover
    previewTimeout = setTimeout(() => {
        previewTooltip = document.createElement('div');
        previewTooltip.className = 'fixed z-50 pointer-events-none opacity-0 transition-all duration-200 ease-out';
        previewTooltip.style.display = 'block';
        
        const img = document.createElement('img');
        img.src = url;
        img.className = 'max-w-md max-h-96 rounded-lg shadow-2xl border-2 border-green-500/50';
        img.style.transform = 'scale(0.8)';
        img.style.transition = 'transform 0.2s ease-out';
        
        previewTooltip.appendChild(img);
        document.body.appendChild(previewTooltip);
        
        updatePreviewPosition(e);
        
        // Animate in
        requestAnimationFrame(() => {
            previewTooltip.classList.remove('opacity-0');
            previewTooltip.classList.add('opacity-100');
            img.style.transform = 'scale(1)';
        });
    }, 300); // 300ms delay
}

/**
 * Update preview position
 */
function updatePreviewPosition(e) {
    if (!previewTooltip) return;
    
    const tooltip = previewTooltip.querySelector('img');
    if (!tooltip) return;
    
    const rect = tooltip.getBoundingClientRect();
    const x = e.clientX + 20;
    const y = e.clientY + 20;
    
    // Adjust if tooltip goes off screen
    const maxX = window.innerWidth - rect.width - 20;
    const maxY = window.innerHeight - rect.height - 20;
    
    previewTooltip.style.left = Math.min(x, maxX) + 'px';
    previewTooltip.style.top = Math.min(y, maxY) + 'px';
}

/**
 * Hide preview tooltip
 */
function hideFilePreview() {
    // Clear any pending timeout
    if (previewTimeout) {
        clearTimeout(previewTimeout);
        previewTimeout = null;
    }
    
    if (previewTooltip) {
        // Animate out
        previewTooltip.classList.remove('opacity-100');
        previewTooltip.classList.add('opacity-0');
        
        const img = previewTooltip.querySelector('img');
        if (img) {
            img.style.transform = 'scale(0.8)';
        }
        
        // Remove after animation completes
        setTimeout(() => {
            if (previewTooltip && previewTooltip.parentNode) {
                previewTooltip.remove();
            }
            previewTooltip = null;
        }, 200);
    }
}

// Add hover listeners to gallery images
document.addEventListener('DOMContentLoaded', function() {
    // Use event delegation for dynamically added images
    document.addEventListener('mouseenter', function(e) {
        const img = e.target.closest('.gallery-item-scroll img');
        if (img && img.parentElement) {
            const card = img.closest('.gallery-item-scroll');
            if (card) {
                const url = card.dataset.url;
                const resourceType = card.dataset.type;
                if (url && resourceType === 'image') {
                    // Add event listeners directly to this image
                    img.addEventListener('mousemove', function(ev) {
                        showFilePreview(ev, url, resourceType);
                    });
                    img.addEventListener('mouseleave', hideFilePreview);
                    img.addEventListener('mouseenter', function(ev) {
                        showFilePreview(ev, url, resourceType);
                    });
                }
            }
        }
    }, true);
    
    // Also handle mouseleave on the entire gallery item to cleanup
    document.addEventListener('mouseleave', function(e) {
        const galleryItem = e.target.closest('.gallery-item-scroll');
        if (galleryItem) {
            hideFilePreview();
        }
    }, true);
});

