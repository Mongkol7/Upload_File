/**
 * AI-Powered Search Functionality
 * Integrates with Google Gemini API for semantic search
 * Automatically activates when normal search finds no results
 */

let aiSearchTimeout = null;
let isAISearchAvailable = false;
let isAISearchActive = false;
window.isAISearching = false; // Track if AI search is currently in progress (global for gallery.js)

/**
 * Initialize AI search functionality
 */
function initAISearch() {
  const searchInput = document.getElementById('searchInput');
  
  if (!searchInput) {
    console.warn('Search input not found for AI search');
    return;
  }

  // Pre-warm AI service in the background (start immediately)
  preWarmAIService();

  // Check if AI search is available
  checkAISearchAvailability();

  // Enhanced search with automatic AI fallback
  searchInput.addEventListener('input', function() {
    const searchTerm = this.value.trim();
    
    // Clear existing timeout
    if (aiSearchTimeout) {
      clearTimeout(aiSearchTimeout);
    }

    // If search is empty, show all files and reset AI state
    if (searchTerm === '') {
      isAISearchActive = false;
      window.isAISearching = false;
      filterGallery();
      hideAISearchLoading();
      return;
    }

    // Debounce search
    aiSearchTimeout = setTimeout(() => {
      performSmartSearch(searchTerm);
    }, 500); // Wait 500ms after user stops typing
  });
}

/**
 * Pre-warm AI service in the background
 * This wakes up the Python Flask service and loads the CLIP model
 * so it's ready when the user actually searches
 */
function preWarmAIService() {
  console.log('Pre-warming AI service in background...');
  
  // Step 1: Wake up the service with a health check
  fetch('api/ai-search.php?q=warmup&prewarm=true')
    .then(response => {
      if (response.ok) {
        console.log('✅ AI service is warming up in background');
        isAISearchAvailable = true;
        
        // Step 2: Optionally make a tiny test request to fully load the model
        // This happens silently in the background
        setTimeout(() => {
          // Make a minimal test request to ensure model is loaded
          // Use a very short timeout so it doesn't block if service is slow
          fetch('api/ai-search.php?q=test&prewarm=true', {
            signal: AbortSignal.timeout(3000) // 3 second timeout
          })
          .then(() => {
            console.log('✅ AI service is ready and warmed up');
          })
          .catch(() => {
            // Silently fail - service might still be loading, that's okay
            console.log('AI service is still loading (this is normal)');
          });
        }, 1000); // Wait 1 second after health check
      } else {
        console.warn('AI service health check failed, but will retry on first search');
      }
    })
    .catch(error => {
      // Silently fail - service might not be running yet
      // It will be checked again when user actually searches
      console.log('AI service pre-warm: Service not ready yet (will start on first search)');
    });
}

/**
 * Check if AI search is available
 */
function checkAISearchAvailability() {
  fetch('api/ai-search.php?q=test')
    .then(response => response.json())
    .then(data => {
      if (data.error && data.error.includes('API key')) {
        console.warn('AI search not available: API key not configured');
        isAISearchAvailable = false;
      } else {
        isAISearchAvailable = true;
        console.log('AI search is available and will activate automatically');
      }
    })
    .catch(error => {
      console.warn('AI search not available:', error);
      isAISearchAvailable = false;
    });
}

/**
 * Perform smart search: normal search first, then AI if needed
 * @param {string} query - Search query
 */
function performSmartSearch(query) {
  // First, perform normal filename search
  filterGallery();
  
  // Count visible results after normal search
  setTimeout(() => {
    const visibleItems = document.querySelectorAll('#galleryContent .relative.group[style*="block"], #galleryContent .relative.group:not([style*="none"])');
    let actualVisibleCount = 0;
    
    visibleItems.forEach(item => {
      const style = window.getComputedStyle(item);
      if (style.display !== 'none') {
        actualVisibleCount++;
      }
    });
    
    // If no results or very few results (< 3), automatically try AI search
    // Also try AI if query seems descriptive (longer than 2 words or contains descriptive words)
    const isDescriptiveQuery = query.split(/\s+/).length > 2 || 
                               /(looks?|shows?|contains?|has|with|about|like|appears?|seems?|depicts?|features?)/i.test(query);
    
    if (isAISearchAvailable && query.length > 2 && (actualVisibleCount === 0 || (actualVisibleCount < 3 && isDescriptiveQuery))) {
      console.log(`Normal search found ${actualVisibleCount} results. Activating AI search...`);
      isAISearchActive = true;
      window.isAISearching = true;
      updateFileCount(0, true); // Show loading animation
      performAISearch(query, true); // true = combine with normal results
    } else {
      isAISearchActive = false;
      window.isAISearching = false;
      hideAISearchLoading();
    }
  }, 100);
}

/**
 * Perform AI-powered semantic search
 * @param {string} query - Search query
 * @param {boolean} combineWithNormal - Whether to combine with normal search results
 */
function performAISearch(query, combineWithNormal = false) {
  const searchInput = document.getElementById('searchInput');
  if (searchInput) {
    searchInput.classList.add('opacity-75');
  }

  // Show loading indicator
  showAISearchLoading();
  window.isAISearching = true;
  updateFileCount(0, true); // Show loading animation

  console.log('Starting AI search for query:', query);

  fetch(`api/ai-search.php?q=${encodeURIComponent(query)}`)
    .then(response => {
      console.log('AI search response status:', response.status);
      return response.json();
    })
    .then(data => {
      console.log('AI search response data:', data);
      window.isAISearching = false;
      
      if (data.success && data.files) {
        console.log('AI search found', data.files.length, 'files');
        if (combineWithNormal) {
          // Combine AI results with normal search results
          displayCombinedSearchResults(data.files, query);
        } else {
          // Use only AI results
          displayAISearchResults(data.files, query);
        }
      } else {
        console.error('AI search error:', data.error || 'Unknown error');
        window.isAISearching = false;
        // Keep normal search results if combining, otherwise show nothing
        if (!combineWithNormal) {
          filterGallery();
        } else {
          updateFileCount(0, false);
        }
      }
    })
    .catch(error => {
      console.error('AI search request failed:', error);
      window.isAISearching = false;
      // Keep normal search results if combining, otherwise show nothing
      if (!combineWithNormal) {
        filterGallery();
      } else {
        updateFileCount(0, false);
      }
    })
    .finally(() => {
      if (searchInput) {
        searchInput.classList.remove('opacity-75');
      }
      hideAISearchLoading();
      window.isAISearching = false;
    });
}

/**
 * Display AI search results
 * @param {Array} files - Filtered files from AI search
 * @param {string} query - Search query
 */
function displayAISearchResults(files, query) {
  const fileItems = document.querySelectorAll('#galleryContent .relative.group');
  
  // Create sets for faster lookup
  const matchedPublicIds = new Set(files.map(f => (f.public_id || '').toLowerCase()));
  const matchedUrls = new Set(files.map(f => (f.url || '').toLowerCase()));
  const matchedFilenames = new Set(files.map(f => (f.filename || '').toLowerCase()));
  
  let visibleCount = 0;

  fileItems.forEach((item) => {
    const publicId = (item.dataset.publicId || '').toLowerCase();
    const url = (item.dataset.url || '').toLowerCase();
    const filename = (item.dataset.filename || '').toLowerCase();
    const category = document.getElementById('categoryFilter')?.value || 'all';
    const fileCategory = item.dataset.category;
    
    // Check if this file matches AI results (check public_id, url, or filename)
    const matchesAI = matchedPublicIds.has(publicId) || 
                     matchedUrls.has(url) || 
                     matchedFilenames.has(filename);
    const matchesCategory = category === 'all' || fileCategory === category;

    if (matchesAI && matchesCategory) {
      item.style.display = 'block';
      visibleCount++;
      item.classList.remove('animate-in');
      
      // Highlight search term in filename
      const filenameElement = item.querySelector('.gallery-item-filename');
      if (filenameElement && query) {
        const originalFilename = item.dataset.filename || '';
        const regex = new RegExp(query.split(/\s+/)[0].replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'gi');
        const newHtml = originalFilename.replace(
          regex,
          '<span class="bg-green-500 text-white">$&</span>'
        );
        filenameElement.innerHTML = newHtml;
      }
    } else {
      item.style.display = 'none';
      item.classList.remove('animate-in');
    }
  });

  console.log('AI search displayed', visibleCount, 'files out of', files.length, 'matched files');
  updateFileCount(visibleCount, false);
  
  // Re-initialize animations
  setTimeout(() => {
    reinitScrollAnimations();
  }, 50);
}

/**
 * Display combined search results (normal + AI)
 * @param {Array} aiFiles - Filtered files from AI search
 * @param {string} query - Search query
 */
function displayCombinedSearchResults(aiFiles, query) {
  const fileItems = document.querySelectorAll('#galleryContent .relative.group');
  
  // Create sets for faster lookup
  const matchedPublicIds = new Set(aiFiles.map(f => (f.public_id || '').toLowerCase()));
  const matchedUrls = new Set(aiFiles.map(f => (f.url || '').toLowerCase()));
  const matchedFilenames = new Set(aiFiles.map(f => (f.filename || '').toLowerCase()));
  
  const searchTerm = query.toLowerCase();
  let visibleCount = 0;

  fileItems.forEach((item) => {
    const publicId = (item.dataset.publicId || '').toLowerCase();
    const url = (item.dataset.url || '').toLowerCase();
    const filename = (item.dataset.filename || '').toLowerCase();
    const category = document.getElementById('categoryFilter')?.value || 'all';
    const fileCategory = item.dataset.category;
    
    // Check if matches normal search (filename) OR AI search
    const matchesNormal = filename.includes(searchTerm);
    const matchesAI = matchedPublicIds.has(publicId) || 
                     matchedUrls.has(url) || 
                     matchedFilenames.has(filename);
    const matchesCategory = category === 'all' || fileCategory === category;
    
    // Show if matches either normal or AI search
    if ((matchesNormal || matchesAI) && matchesCategory) {
      item.style.display = 'block';
      visibleCount++;
      item.classList.remove('animate-in');
      
      // Highlight search term in filename
      const filenameElement = item.querySelector('.gallery-item-filename');
      if (filenameElement && query) {
        const originalFilename = item.dataset.filename || '';
        const firstWord = query.split(/\s+/)[0].replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const regex = new RegExp(firstWord, 'gi');
        const newHtml = originalFilename.replace(
          regex,
          '<span class="bg-green-500 text-white">$&</span>'
        );
        filenameElement.innerHTML = newHtml;
      }
    } else {
      item.style.display = 'none';
      item.classList.remove('animate-in');
    }
  });

  console.log('Combined search displayed', visibleCount, 'files (AI found', aiFiles.length, 'files)');
  updateFileCount(visibleCount, false);
  
  // Re-initialize animations
  setTimeout(() => {
    reinitScrollAnimations();
  }, 50);
}

/**
 * Show AI search loading indicator
 */
function showAISearchLoading() {
  const searchInput = document.getElementById('searchInput');
  if (!searchInput) return;

  // Add loading spinner
  let loadingIndicator = document.getElementById('aiSearchLoading');
  if (!loadingIndicator) {
    loadingIndicator = document.createElement('div');
    loadingIndicator.id = 'aiSearchLoading';
    loadingIndicator.className = 'absolute right-3 top-2.5 z-10';
    loadingIndicator.innerHTML = `
      <svg class="animate-spin w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    `;
    searchInput.parentElement.appendChild(loadingIndicator);
  }
  loadingIndicator.style.display = 'block';
}

/**
 * Hide AI search loading indicator
 */
function hideAISearchLoading() {
  const loadingIndicator = document.getElementById('aiSearchLoading');
  if (loadingIndicator) {
    loadingIndicator.style.display = 'none';
  }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initAISearch);
} else {
  initAISearch();
}

