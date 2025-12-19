/**
 * Gallery Management and Animation Functions
 * Handles file gallery interactions, animations, and UI updates
 */

// ============================================
// Loading Overlay Functions
// ============================================

/**
 * Show loading overlay with custom message
 * @param {string} message - Loading message to display
 */
function showLoading(message = 'Processing...') {
  const overlay = document.getElementById('loadingOverlay');
  const loadingText = document.getElementById('loadingText');
  if (overlay && loadingText) {
    loadingText.textContent = message;
    overlay.style.display = 'flex';
  }
}

/**
 * Hide loading overlay
 */
function hideLoading() {
  const overlay = document.getElementById('loadingOverlay');
  if (overlay) {
    overlay.style.display = 'none';
  }
}

// ============================================
// Clipboard Functions
// ============================================

/**
 * Copy text to clipboard and show success/error notification
 * @param {string} text - Text to copy to clipboard
 */
function copyToClipboard(text) {
  console.log('copyToClipboard called with:', text);
  navigator.clipboard.writeText(text).then(
    function () {
      console.log('Copy successful');
      Swal.fire({
        icon: 'success',
        title: 'Copied!',
        text: 'File URL copied to clipboard',
        background: '#1f2937',
        color: '#f3f4f6',
        confirmButtonColor: '#22c55e',
        confirmButtonText: 'OK',
        timer: 2000,
        timerProgressBar: true,
      });
    },
    function (err) {
      console.error('Could not copy text: ', err);
      Swal.fire({
        icon: 'error',
        title: 'Copy Failed!',
        text: 'Could not copy URL to clipboard',
        background: '#1f2937',
        color: '#f3f4f6',
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'OK',
      });
    }
  );
}

// ============================================
// Delete Functions
// ============================================

/**
 * Confirm and delete a file
 * @param {string} publicId - Cloudinary public ID
 * @param {string} resourceType - Resource type (image, video, raw)
 */
function confirmDelete(publicId, resourceType) {
  console.log('confirmDelete called with:', publicId, resourceType);
  Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Yes, delete it!',
    background: '#1f2937',
    color: '#f3f4f6',
  }).then((result) => {
    console.log('SweetAlert result:', result);
    if (result.isConfirmed) {
      console.log('Submitting delete form for:', publicId);
      showLoading('Deleting file...');
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = 'index.php';

      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'delete_file';
      input.value = '1';

      const publicIdInput = document.createElement('input');
      publicIdInput.type = 'hidden';
      publicIdInput.name = 'public_id';
      publicIdInput.value = publicId;

      const resourceTypeInput = document.createElement('input');
      resourceTypeInput.type = 'hidden';
      resourceTypeInput.name = 'resource_type';
      resourceTypeInput.value = resourceType;

      form.appendChild(input);
      form.appendChild(publicIdInput);
      form.appendChild(resourceTypeInput);
      document.body.appendChild(form);
      form.submit();
    }
  });
}

// ============================================
// Gallery Toggle Functions
// ============================================

/**
 * Show skeleton loading
 */
function showGallerySkeleton() {
  const skeleton = document.getElementById('gallerySkeleton');
  if (skeleton) {
    skeleton.style.display = 'grid';
  }
}

/**
 * Hide skeleton loading
 */
function hideGallerySkeleton() {
  const skeleton = document.getElementById('gallerySkeleton');
  if (skeleton) {
    skeleton.style.display = 'none';
  }
}

/**
 * Toggle gallery visibility
 */
window.toggleGallery = function () {
  console.log('toggleGallery function called');
  const galleryContent = document.getElementById('galleryContent');
  const toggleIcon = document.getElementById('toggleIcon');
  const toggleText = document.getElementById('toggleText');

  if (!galleryContent) {
    console.error('Error: galleryContent element not found');
    return;
  }

  if (!toggleIcon || !toggleText) {
    console.error('Error: toggle elements not found');
    return;
  }

  console.log('Current display state:', galleryContent.style.display);

  if (
    galleryContent.style.display === 'none' ||
    galleryContent.style.display === ''
  ) {
    // Show skeleton while gallery is loading
    showGallerySkeleton();

    // Show gallery with a slight delay for smooth transition
    setTimeout(() => {
      galleryContent.style.display = 'grid';
      // Hide skeleton once gallery is shown
      hideGallerySkeleton();
      toggleText.textContent = 'Hide Gallery';
      toggleIcon.innerHTML =
        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>';
      console.log('Gallery shown');

      // Initialize scroll animations when gallery is shown
      setTimeout(() => {
        reinitScrollAnimations();
      }, 100);
    }, 500); // Show skeleton for 500ms for better UX
  } else {
    // Hide gallery
    galleryContent.style.display = 'none';
    hideGallerySkeleton();
    toggleText.textContent = 'Show Gallery';
    toggleIcon.innerHTML =
      '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>';
    console.log('Gallery hidden');
  }
};

// ============================================
// Edit Mode Functions
// ============================================

/**
 * Toggle edit mode for filename
 * @param {string} publicId - Cloudinary public ID
 * @param {boolean} showEdit - Whether to show edit mode
 */
window.toggleEditMode = function (publicId, showEdit) {
  const filenameDisplay = document.getElementById('filename-view-' + publicId);
  const filenameEdit = document.getElementById('filename-edit-' + publicId);

  if (!filenameDisplay || !filenameEdit) {
    console.error('Error: Filename elements not found for', publicId);
    console.log(
      'Looking for: filename-view-' +
        publicId +
        ' and filename-edit-' +
        publicId
    );
    return;
  }

  if (showEdit) {
    filenameDisplay.style.display = 'none';
    filenameEdit.style.display = 'block';
    // Focus on the input field
    const inputField = filenameEdit.querySelector('input[type="text"]');
    if (inputField) {
      inputField.focus();
      inputField.select();
    }
  } else {
    filenameDisplay.style.display = 'block';
    filenameEdit.style.display = 'none';
  }
};

// ============================================
// Search and Filter Functions
// ============================================

/**
 * Filter gallery items based on search term and category
 * This is the normal filename-based search
 */
function filterGallery() {
  const searchInput = document.getElementById('searchInput');
  if (!searchInput) return;
  
  const searchTerm = searchInput.value.trim();
  const category = document.getElementById('categoryFilter')?.value || 'all';
  const fileItems = document.querySelectorAll(
    '#galleryContent .relative.group'
  );

  let visibleCount = 0;

  fileItems.forEach((item) => {
    const filename = item.dataset.filename || '';
    const fileCategory = item.dataset.category;
    const filenameElement = item.querySelector('.gallery-item-filename');

    // If search is empty, show all files (respecting category filter)
    const matchesSearch = searchTerm === '' || filename.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesCategory = category === 'all' || fileCategory === category;

    if (matchesSearch && matchesCategory) {
      item.style.display = 'block';
      visibleCount++;
      // Reset animation state for newly visible items
      item.classList.remove('animate-in');

      if (searchTerm !== '') {
        // Highlight search term in filename
        const regex = new RegExp(searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'gi');
        const newHtml = filename.replace(
          regex,
          '<span class="bg-green-500 text-white">$&</span>'
        );
        if (filenameElement) {
          filenameElement.innerHTML = newHtml;
        }
      } else {
        // Reset to original filename when search is cleared
        if (filenameElement) {
          filenameElement.textContent = filename;
        }
      }
    } else {
      item.style.display = 'none';
      item.classList.remove('animate-in');
      // Reset filename when hidden
      if (filenameElement) {
        filenameElement.textContent = filename;
      }
    }
  });

  // Only update count if AI is not searching (to avoid overriding loading animation)
  if (typeof window.isAISearching === 'undefined' || !window.isAISearching) {
    updateFileCount(visibleCount, false);
  }
  
  // Re-initialize animations
  setTimeout(() => {
    reinitScrollAnimations();
  }, 50);
}

/**
 * Update file count display
 * @param {number} count - Number of visible files
 * @param {boolean} isAISearching - Whether AI search is in progress
 */
function updateFileCount(count, isAISearching = false) {
  const countElement = document.getElementById('gallery-file-count');
  const totalFiles = window.galleryTotalFiles || 0;
  if (countElement) {
    // Show loading animation if AI is searching
    if (isAISearching) {
      countElement.innerHTML = `
        <div style="text-align: center; padding: 1rem 0;">
          <div style="color: #22c55e; margin-bottom: 1.5rem; font-size: 1rem; font-weight: 500;">AI is analyzing images...</div>
          <!-- From Uiverse.io by mobinkakei -->
          <div class="loader-3" style="margin: 0 auto;">
            <div class="circle"></div>
            <div class="circle"></div>
            <div class="circle"></div>
            <div class="circle"></div>
            <div class="circle"></div>
          </div>
        </div>
      `;
      return;
    }
    
    if (totalFiles === 0) {
      countElement.textContent = 'No files found.';
    } else if (count === 0) {
      countElement.textContent = 'No files found matching your filters';
    } else if (count === totalFiles) {
      countElement.textContent = `Showing ${count} file${
        count !== 1 ? 's' : ''
      }`;
    } else {
      countElement.textContent = `Showing ${count} of ${totalFiles} file(s)`;
    }
  }
}

// ============================================
// Scroll Animation Functions
// ============================================

let scrollObserver = null;

/**
 * Initialize scroll-triggered animations using Intersection Observer
 */
function initScrollAnimations() {
  // Disconnect existing observer if any
  if (scrollObserver) {
    scrollObserver.disconnect();
  }

  // Get only visible gallery items
  const galleryItems = Array.from(
    document.querySelectorAll('.gallery-item-scroll')
  ).filter((item) => {
    return (
      item.style.display !== 'none' &&
      window.getComputedStyle(item).display !== 'none'
    );
  });

  // Create Intersection Observer
  scrollObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          // Add animate-in class when item enters viewport
          entry.target.classList.add('animate-in');
          // Unobserve after animation to prevent re-triggering
          scrollObserver.unobserve(entry.target);
        }
      });
    },
    {
      // Trigger when item is 20% visible
      threshold: 0.2,
      // Start animation slightly before item enters viewport
      rootMargin: '0px 0px -50px 0px',
    }
  );

  // Observe only visible gallery items
  galleryItems.forEach((item) => {
    // Only observe items that haven't been animated yet
    if (!item.classList.contains('animate-in')) {
      scrollObserver.observe(item);
    }
  });
}

/**
 * Re-initialize animations when gallery is toggled or filtered
 */
function reinitScrollAnimations() {
  const galleryItems = document.querySelectorAll('.gallery-item-scroll');
  galleryItems.forEach((item) => {
    // Only reset animation for visible items
    if (
      item.style.display !== 'none' &&
      window.getComputedStyle(item).display !== 'none'
    ) {
      item.classList.remove('animate-in');
    }
  });

  // Small delay to ensure DOM is updated
  setTimeout(() => {
    initScrollAnimations();
  }, 100);
}

// ============================================
// Initialization
// ============================================

/**
 * Initialize all event listeners and animations when DOM is ready
 */
document.addEventListener('DOMContentLoaded', function () {
  console.log('DOMContentLoaded event fired.');
  const searchInput = document.getElementById('searchInput');
  const categoryFilter = document.getElementById('categoryFilter');

  // Hide skeleton on page load (files are already loaded server-side)
  hideGallerySkeleton();

  // Note: Search input event listener is now handled by ai-search.js
  // This allows for AI-powered semantic search
  if (searchInput) {
    console.log('Search input found. AI search will handle it.');
  } else {
    console.warn('Search input not found.');
  }

  if (categoryFilter) {
    console.log('Adding event listener to category filter.');
    categoryFilter.addEventListener('change', function () {
      filterGallery();
      // Re-initialize animations after filtering
      setTimeout(() => {
        reinitScrollAnimations();
      }, 50);
    });
  } else {
    console.warn('Category filter not found.');
  }

  // Initialize file count
  console.log('Initializing file count');
  const totalFiles = document.querySelectorAll(
    '#galleryContent .relative.group'
  ).length;
  // Use PHP-provided total or fallback to DOM count
  if (window.galleryTotalFiles === undefined) {
    window.galleryTotalFiles = totalFiles;
  }
  updateFileCount(totalFiles);
  console.log('File count initialized');

  // Initialize scroll animations
  initScrollAnimations();

  // Test if functions are loaded
  console.log('JavaScript functions loaded successfully');
  console.log('copyToClipboard function:', typeof copyToClipboard);
  console.log('confirmDelete function:', typeof confirmDelete);
});
