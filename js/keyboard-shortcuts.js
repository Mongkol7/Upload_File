/**
 * Keyboard Shortcuts
 */

document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + F - Focus search
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault();
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.focus();
            searchInput.select();
        }
    }
    
    // Delete key - Delete selected files
    if (e.key === 'Delete' && selectedFiles && selectedFiles.size > 0) {
        e.preventDefault();
        bulkDelete();
    }
    
    // Escape - Close modals/lightbox
    if (e.key === 'Escape') {
        const lightbox = document.getElementById('lightbox');
        const fileDetails = document.getElementById('fileDetailsModal');
        
        if (lightbox && lightbox.style.display === 'flex') {
            closeLightbox();
        } else if (fileDetails && fileDetails.style.display === 'flex') {
            closeFileDetails();
        } else if (selectedFiles && selectedFiles.size > 0) {
            deselectAllFiles();
        }
    }
    
    // Arrow keys for lightbox navigation (handled in lightbox.js)
    // Ctrl/Cmd + A - Select all (when in gallery)
    if ((e.ctrlKey || e.metaKey) && e.key === 'a' && document.getElementById('galleryContent')) {
        const isInputFocused = document.activeElement.tagName === 'INPUT' || 
                              document.activeElement.tagName === 'TEXTAREA';
        if (!isInputFocused) {
            e.preventDefault();
            selectAllFiles();
        }
    }
    
    // G - Toggle gallery
    if (e.key === 'g' && !e.ctrlKey && !e.metaKey && !e.shiftKey) {
        const isInputFocused = document.activeElement.tagName === 'INPUT' || 
                              document.activeElement.tagName === 'TEXTAREA';
        if (!isInputFocused) {
            e.preventDefault();
            if (typeof toggleGallery === 'function') {
                toggleGallery();
            }
        }
    }
});

