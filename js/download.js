/**
 * Download Functionality
 */

/**
 * Download a single file
 * @param {string} url - File URL
 * @param {string} filename - Filename for download
 */
function downloadFile(url, filename) {
    const a = document.createElement('a');
    a.href = url;
    a.download = filename || url.split('/').pop();
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    
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

