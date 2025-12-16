/**
 * Sort Options - Sort by name, date, size, type
 */

let currentSort = {
    field: 'date',
    order: 'desc' // 'asc' or 'desc'
};

/**
 * Sort gallery files
 * @param {string} field - Sort field (name, date, size, type)
 */
function sortGallery(field) {
    if (currentSort.field === field) {
        // Toggle order if same field
        currentSort.order = currentSort.order === 'asc' ? 'desc' : 'asc';
    } else {
        currentSort.field = field;
        currentSort.order = 'desc';
    }
    
    applySort();
    updateSortUI();
    saveSortPreference();
}

/**
 * Apply sorting to gallery
 */
function applySort() {
    const galleryContent = document.getElementById('galleryContent');
    if (!galleryContent) return;
    
    const items = Array.from(galleryContent.querySelectorAll('.relative.group'));
    
    items.sort((a, b) => {
        let aVal, bVal;
        
        switch (currentSort.field) {
            case 'name':
                aVal = a.dataset.filename?.toLowerCase() || '';
                bVal = b.dataset.filename?.toLowerCase() || '';
                break;
            case 'date':
                aVal = new Date(a.dataset.createdAt || 0);
                bVal = new Date(b.dataset.createdAt || 0);
                break;
            case 'size':
                aVal = parseInt(a.dataset.size || 0);
                bVal = parseInt(b.dataset.size || 0);
                break;
            case 'type':
                aVal = a.dataset.format?.toLowerCase() || '';
                bVal = b.dataset.format?.toLowerCase() || '';
                break;
            default:
                return 0;
        }
        
        if (aVal < bVal) return currentSort.order === 'asc' ? -1 : 1;
        if (aVal > bVal) return currentSort.order === 'asc' ? 1 : -1;
        return 0;
    });
    
    // Reorder DOM elements
    items.forEach(item => galleryContent.appendChild(item));
    
    // Reinitialize animations
    setTimeout(() => {
        reinitScrollAnimations();
    }, 100);
}

/**
 * Update sort UI
 */
function updateSortUI() {
    document.querySelectorAll('.sort-btn').forEach(btn => {
        const field = btn.dataset.sortField;
        if (field === currentSort.field) {
            btn.classList.add('bg-green-500/20', 'border-green-500/50');
            const icon = btn.querySelector('.sort-icon');
            if (icon) {
                icon.innerHTML = currentSort.order === 'asc' 
                    ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>'
                    : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>';
            }
        } else {
            btn.classList.remove('bg-green-500/20', 'border-green-500/50');
        }
    });
}

/**
 * Save sort preference
 */
function saveSortPreference() {
    localStorage.setItem('gallerySort', JSON.stringify(currentSort));
}

/**
 * Load sort preference
 */
function loadSortPreference() {
    const saved = localStorage.getItem('gallerySort');
    if (saved) {
        try {
            const parsed = JSON.parse(saved);
            currentSort = { ...currentSort, ...parsed };
        } catch (e) {
            console.error('Error loading sort preference:', e);
        }
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadSortPreference();
    applySort();
});

