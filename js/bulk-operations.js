/**
 * Bulk Operations - Select multiple files, bulk delete/download
 */

let selectedFiles = new Set();

/**
 * Toggle file selection
 * @param {string} publicId - File public ID
 */
function toggleFileSelection(publicId) {
    if (selectedFiles.has(publicId)) {
        selectedFiles.delete(publicId);
    } else {
        selectedFiles.add(publicId);
    }
    
    updateSelectionUI();
    updateBulkActionsBar();
}

/**
 * Select all visible files
 */
function selectAllFiles() {
    const checkboxes = document.querySelectorAll('.file-checkbox:not(:disabled)');
    checkboxes.forEach(cb => {
        const publicId = cb.dataset.publicId;
        selectedFiles.add(publicId);
        cb.checked = true;
    });
    updateBulkActionsBar();
}

/**
 * Deselect all files
 */
function deselectAllFiles() {
    selectedFiles.clear();
    document.querySelectorAll('.file-checkbox').forEach(cb => cb.checked = false);
    updateSelectionUI();
    updateBulkActionsBar();
}

/**
 * Update selection UI
 */
function updateSelectionUI() {
    document.querySelectorAll('.file-checkbox').forEach(cb => {
        const publicId = cb.dataset.publicId;
        cb.checked = selectedFiles.has(publicId);
    });
}

/**
 * Update bulk actions bar visibility
 */
function updateBulkActionsBar() {
    const bar = document.getElementById('bulkActionsBar');
    if (!bar) return;
    
    if (selectedFiles.size > 0) {
        bar.style.display = 'flex';
        document.getElementById('selectedCount').textContent = selectedFiles.size;
    } else {
        bar.style.display = 'none';
    }
}

/**
 * Bulk delete selected files
 */
function bulkDelete() {
    if (selectedFiles.size === 0) return;
    
    Swal.fire({
        title: 'Delete Selected Files?',
        text: `Are you sure you want to delete ${selectedFiles.size} file(s)?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete them!',
        background: '#1f2937',
        color: '#f3f4f6'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading(`Deleting ${selectedFiles.size} file(s)...`);
            
            // Get file data for each selected file
            const filesData = Array.from(selectedFiles).map(publicId => {
                const card = document.querySelector(`[data-public-id="${publicId}"]`);
                if (card) {
                    return {
                        public_id: publicId,
                        resource_type: card.dataset.type || 'image'
                    };
                }
                return { public_id: publicId, resource_type: 'image' };
            });
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'index.php';
            
            form.appendChild(createHiddenInput('bulk_delete', '1'));
            form.appendChild(createHiddenInput('selected_files', JSON.stringify(filesData)));
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}

/**
 * Bulk download selected files
 */
function bulkDownload() {
    if (selectedFiles.size === 0) return;
    
    showLoading(`Preparing download for ${selectedFiles.size} file(s)...`);
    
    // Get file URLs
    const files = Array.from(selectedFiles).map(publicId => {
        const card = document.querySelector(`[data-public-id="${publicId}"]`);
        return card ? card.dataset.url : null;
    }).filter(Boolean);
    
    // Download files one by one (browser limitation)
    files.forEach((url, index) => {
        setTimeout(() => {
            const a = document.createElement('a');
            a.href = url;
            a.download = url.split('/').pop();
            a.click();
        }, index * 200);
    });
    
    setTimeout(() => {
        hideLoading();
        Swal.fire({
            icon: 'success',
            title: 'Download Started',
            text: `Downloading ${files.length} file(s)...`,
            background: '#1f2937',
            color: '#f3f4f6',
            confirmButtonColor: '#22c55e',
            timer: 2000
        });
    }, files.length * 200);
}

/**
 * Create hidden input element
 */
function createHiddenInput(name, value) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = value;
    return input;
}

// Initialize bulk operations
document.addEventListener('DOMContentLoaded', function() {
    // Create bulk actions bar if it doesn't exist
    if (!document.getElementById('bulkActionsBar')) {
        const bar = document.createElement('div');
        bar.id = 'bulkActionsBar';
        bar.className = 'fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-gray-800/90 backdrop-blur-lg rounded-2xl p-4 border border-green-500/30 shadow-xl z-40';
        bar.style.display = 'none';
        bar.innerHTML = `
            <div class="flex items-center gap-4">
                <span class="text-white font-semibold"><span id="selectedCount">0</span> file(s) selected</span>
                <button onclick="bulkDownload()" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors">
                    Download
                </button>
                <button onclick="bulkDelete()" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">
                    Delete
                </button>
                <button onclick="deselectAllFiles()" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    Clear
                </button>
            </div>
        `;
        document.body.appendChild(bar);
    }
});

