/**
 * SweetAlert Notifications Handler
 * Handles all result notifications from PHP operations (upload, rename, delete)
 */

// ============================================
// Alert Configuration
// ============================================

const ALERT_CONFIG = {
  background: '#1f2937',
  color: '#f3f4f6',
  success: {
    confirmButtonColor: '#22c55e',
    confirmButtonText: 'OK',
    timer: 2000,
    timerProgressBar: true,
  },
  error: {
    confirmButtonColor: '#ef4444',
    confirmButtonText: 'OK',
  },
  warning: {
    confirmButtonColor: '#f59e0b',
    confirmButtonText: 'OK',
  },
};

// ============================================
// Alert Functions
// ============================================

/**
 * Show success alert
 * @param {string} title - Alert title
 * @param {string} text - Alert message
 * @param {object} options - Additional options
 */
function showSuccessAlert(title, text, options = {}) {
  Swal.fire({
    icon: 'success',
    title: title,
    text: text,
    background: ALERT_CONFIG.background,
    color: ALERT_CONFIG.color,
    confirmButtonColor: ALERT_CONFIG.success.confirmButtonColor,
    confirmButtonText:
      options.confirmButtonText || ALERT_CONFIG.success.confirmButtonText,
    timer:
      options.timer !== undefined ? options.timer : ALERT_CONFIG.success.timer,
    timerProgressBar:
      options.timerProgressBar !== undefined
        ? options.timerProgressBar
        : ALERT_CONFIG.success.timerProgressBar,
    ...options,
  });
}

/**
 * Show error alert
 * @param {string} title - Alert title
 * @param {string} text - Alert message
 * @param {object} options - Additional options
 */
function showErrorAlert(title, text, options = {}) {
  Swal.fire({
    icon: 'error',
    title: title,
    text: text,
    background: ALERT_CONFIG.background,
    color: ALERT_CONFIG.color,
    confirmButtonColor: ALERT_CONFIG.error.confirmButtonColor,
    confirmButtonText:
      options.confirmButtonText || ALERT_CONFIG.error.confirmButtonText,
    ...options,
  });
}

/**
 * Show warning alert
 * @param {string} title - Alert title
 * @param {string} text - Alert message
 * @param {object} options - Additional options
 */
function showWarningAlert(title, text, options = {}) {
  Swal.fire({
    icon: 'warning',
    title: title,
    text: text,
    background: ALERT_CONFIG.background,
    color: ALERT_CONFIG.color,
    confirmButtonColor: ALERT_CONFIG.warning.confirmButtonColor,
    confirmButtonText:
      options.confirmButtonText || ALERT_CONFIG.warning.confirmButtonText,
    ...options,
  });
}

// ============================================
// Result Handlers
// ============================================

/**
 * Handle rename result
 */
function handleRenameResult() {
  if (window.renameResult) {
    if (window.renameResult.success) {
      showSuccessAlert('Renamed!', window.renameResult.message);
    } else {
      showErrorAlert('Rename Failed!', window.renameResult.message);
    }
  }
}

/**
 * Handle delete result
 */
function handleDeleteResult() {
  if (window.fileDeleteResult) {
    if (window.fileDeleteResult.success) {
      showSuccessAlert('Deleted!', window.fileDeleteResult.message);
    } else {
      showErrorAlert('Delete Failed!', window.fileDeleteResult.message);
    }
  }
}

/**
 * Handle upload result
 */
function handleUploadResult() {
  if (window.uploadResult) {
    if (window.uploadResult.message === 'success') {
      showSuccessAlert('Success!', 'File uploaded successfully to Cloudinary', {
        confirmButtonText: 'Great!',
      });
    } else if (window.uploadResult.message === 'error') {
      showErrorAlert('Upload Failed!', 'Please try again');
    } else if (window.uploadResult.message === 'warning') {
      showWarningAlert('Warning', 'Upload succeeded but no URL returned');
    }
  }
}

// ============================================
// Initialization
// ============================================

/**
 * Initialize all result handlers when DOM is ready
 */
document.addEventListener('DOMContentLoaded', function () {
  handleRenameResult();
  handleDeleteResult();
  handleUploadResult();
});
