/**
 * File Details Modal - Show metadata, size, dimensions, format
 */

/**
 * Open file details modal
 * @param {Object} file - File object with metadata
 */
function openFileDetails(file) {
    const modal = document.getElementById('fileDetailsModal');
    if (!modal) {
        createFileDetailsModal();
    }
    
    updateFileDetailsContent(file);
    document.getElementById('fileDetailsModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

/**
 * Create file details modal
 */
function createFileDetailsModal() {
    const modal = document.createElement('div');
    modal.id = 'fileDetailsModal';
    modal.className = 'fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4';
    modal.style.display = 'none';
    modal.innerHTML = `
        <div class="bg-gray-800 rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto border border-green-500/30">
            <div class="sticky top-0 bg-gray-800/95 backdrop-blur-sm border-b border-green-500/30 p-6 flex justify-between items-center">
                <h3 class="text-2xl font-bold text-green-500">File Details</h3>
                <button onclick="closeFileDetails()" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="fileDetailsContent" class="p-6"></div>
        </div>
    `;
    document.body.appendChild(modal);
    
    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && document.getElementById('fileDetailsModal')?.style.display === 'flex') {
            closeFileDetails();
        }
    });
}

/**
 * Update file details content
 */
function updateFileDetailsContent(file) {
    const content = document.getElementById('fileDetailsContent');
    if (!content) return;
    
    const date = new Date(file.created_at);
    const formattedDate = date.toLocaleString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    content.innerHTML = `
        <div class="space-y-6">
            ${file.resource_type === 'image' ? `
                <div class="flex justify-center">
                    <img src="${file.url}" alt="${file.filename}" class="max-w-full max-h-96 rounded-lg border border-green-500/30">
                </div>
            ` : file.resource_type === 'video' ? `
                <div class="flex justify-center">
                    <video src="${file.url}" controls class="max-w-full max-h-96 rounded-lg border border-green-500/30"></video>
                </div>
            ` : ''}
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-700/50 rounded-lg p-4">
                    <p class="text-gray-400 text-sm mb-1">Filename</p>
                    <p class="text-white font-semibold">${file.filename}</p>
                </div>
                
                <div class="bg-gray-700/50 rounded-lg p-4">
                    <p class="text-gray-400 text-sm mb-1">File Size</p>
                    <p class="text-white font-semibold">${formatFileSize(file.size)}</p>
                </div>
                
                <div class="bg-gray-700/50 rounded-lg p-4">
                    <p class="text-gray-400 text-sm mb-1">File Type</p>
                    <p class="text-white font-semibold">${file.format?.toUpperCase() || 'N/A'}</p>
                </div>
                
                <div class="bg-gray-700/50 rounded-lg p-4">
                    <p class="text-gray-400 text-sm mb-1">Resource Type</p>
                    <p class="text-white font-semibold capitalize">${file.resource_type}</p>
                </div>
                
                <div class="bg-gray-700/50 rounded-lg p-4 md:col-span-2">
                    <p class="text-gray-400 text-sm mb-1">Upload Date</p>
                    <p class="text-white font-semibold">${formattedDate}</p>
                </div>
                
                <div class="bg-gray-700/50 rounded-lg p-4 md:col-span-2">
                    <p class="text-gray-400 text-sm mb-1">URL</p>
                    <div class="flex gap-2">
                        <input type="text" value="${file.url}" readonly class="flex-1 px-3 py-2 bg-gray-600 text-white rounded-lg text-sm">
                        <button onclick="copyToClipboard('${file.url}')" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors">
                            Copy
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="flex gap-3 pt-4">
                <button onclick="downloadFile('${file.url}', '${file.filename}')" class="flex-1 px-4 py-3 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors font-semibold">
                    Download
                </button>
                <button onclick="shareFile('${file.url}', '${file.filename}')" class="flex-1 px-4 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors font-semibold">
                    Share
                </button>
            </div>
        </div>
    `;
}

/**
 * Close file details modal
 */
function closeFileDetails() {
    const modal = document.getElementById('fileDetailsModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

/**
 * Format file size
 */
function formatFileSize(bytes) {
    if (!bytes || bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

