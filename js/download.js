/**
 * Download Functionality
 */

/**
 * Download a single file
 * @param {string} url - File URL
 * @param {string} filename - Filename for download
 */
function downloadFile(url, filename) {
    // Check if the URL is from the same origin or external
    const isSameOrigin = url.startsWith(window.location.origin);
    const isCloudinary = url.includes('cloudinary.com');
    
    // For cross-origin files (Cloudinary), we need to force download differently
    if (!isSameOrigin || isCloudinary) {
        // Method 1: Try to use download attribute with cross-origin handling
        downloadWithCrossOriginHandling(url, filename);
    } else {
        // For same-origin files, use standard download
        const a = document.createElement('a');
        a.href = url;
        a.download = filename || url.split('/').pop();
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }
    
    showDownloadNotification();
}

/**
 * Handle cross-origin downloads with fallback methods
 * @param {string} url - File URL
 * @param {string} filename - Filename for download
 */
function downloadWithCrossOriginHandling(url, filename) {
    // Method 1: Try to add download parameters to Cloudinary URL
    let downloadUrl = url;
    if (url.includes('cloudinary.com')) {
        // Add Cloudinary download parameters
        const separator = url.includes('?') ? '&' : '?';
        downloadUrl = `${url}${separator}dl=1`;
    }
    
    // Create download link with forced download
    const a = document.createElement('a');
    a.href = downloadUrl;
    a.download = filename || url.split('/').pop();
    a.target = '_blank'; // Open in new tab as fallback
    a.rel = 'noopener noreferrer';
    
    // Try to trigger download
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    
    // For videos, the above should work, but for images/PDFs we may need fallback
    setTimeout(() => {
        if (isImageOrPdf(url)) {
            downloadAsNewTab(url, filename);
        }
    }, 500);
}

/**
 * Download image/PDF by opening in new tab with save dialog hint
 * @param {string} url - File URL
 * @param {string} filename - Filename for download
 */
function downloadAsNewTab(url, filename) {
    // Create a new window/tab that will show the save dialog
    const newWindow = window.open(url, '_blank');
    
    // Show instruction to user
    Swal.fire({
        icon: 'info',
        title: 'Download Instructions',
        html: `For ${isImageOrPdf(url) ? 'images and PDFs' : 'this file'}, right-click on the opened file and select "Save image as..." or "Save link as..."`,
        background: '#1f2937',
        color: '#f3f4f6',
        confirmButtonColor: '#22c55e',
        timer: 4000,
        timerProgressBar: true
    });
}

/**
 * Check if URL points to an image or PDF
 * @param {string} url - File URL
 * @returns {boolean} - True if image or PDF
 */
function isImageOrPdf(url) {
    const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
    const urlLower = url.toLowerCase();
    
    // Check for PDF
    if (urlLower.includes('.pdf') || urlLower.includes('format=pdf')) {
        return true;
    }
    
    // Check for image extensions
    return imageExtensions.some(ext => urlLower.includes(`.${ext}`));
}

/**
 * Show download notification
 */
function showDownloadNotification() {
    Swal.fire({
        icon: 'success',
        title: 'Download Started',
        text: 'Your file is downloading...',
        background: '#1f2937',
        color: '#f3f4f6',
        confirmButtonColor: '#22c55e',
        timer: 2000,
        timerProgressBar: true
    });
}

/**
 * Download multiple files as ZIP (client-side limitation - downloads individually)
 * @param {Array} files - Array of file objects with url and filename
 */
function downloadMultipleFiles(files) {
    if (!files || files.length === 0) return;
    
    showLoading(`Preparing ${files.length} file(s) for download...`);
    
    files.forEach((file, index) => {
        setTimeout(() => {
            downloadFile(file.url, file.filename);
        }, index * 300);
    });
    
    setTimeout(() => {
        hideLoading();
    }, files.length * 300);
}

