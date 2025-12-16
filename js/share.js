/**
 * Share Functionality - Generate shareable links and QR codes
 */

/**
 * Share file
 * @param {string} url - File URL
 * @param {string} filename - Filename
 */
function shareFile(url, filename) {
    const shareModal = document.getElementById('shareModal');
    if (!shareModal) {
        createShareModal();
    }
    
    updateShareContent(url, filename);
    document.getElementById('shareModal').style.display = 'flex';
}

/**
 * Create share modal
 */
function createShareModal() {
    const modal = document.createElement('div');
    modal.id = 'shareModal';
    modal.className = 'fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4';
    modal.style.display = 'none';
    modal.innerHTML = `
        <div class="bg-gray-800 rounded-2xl max-w-md w-full border border-green-500/30">
            <div class="p-6 border-b border-green-500/30 flex justify-between items-center">
                <h3 class="text-xl font-bold text-green-500">Share File</h3>
                <button onclick="closeShareModal()" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="shareContent" class="p-6 space-y-4"></div>
        </div>
    `;
    document.body.appendChild(modal);
}

/**
 * Update share content
 */
function updateShareContent(url, filename) {
    const content = document.getElementById('shareContent');
    if (!content) return;
    
    content.innerHTML = `
        <div class="space-y-4">
            <div>
                <label class="text-gray-400 text-sm mb-2 block">Share Link</label>
                <div class="flex gap-2">
                    <input type="text" id="shareUrl" value="${url}" readonly class="flex-1 px-3 py-2 bg-gray-700 text-white rounded-lg text-sm">
                    <button onclick="copyToClipboard('${url}')" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg">
                        Copy
                    </button>
                </div>
            </div>
            
            <div class="flex gap-2">
                <button onclick="shareToSocial('facebook', '${url}', '${filename}')" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                    Facebook
                </button>
                <button onclick="shareToSocial('twitter', '${url}', '${filename}')" class="flex-1 px-4 py-2 bg-blue-400 hover:bg-blue-500 text-white rounded-lg">
                    Twitter
                </button>
                <button onclick="shareToSocial('whatsapp', '${url}', '${filename}')" class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">
                    WhatsApp
                </button>
            </div>
            
            <div id="qrCodeContainer" class="flex justify-center pt-4">
                <div class="bg-white p-4 rounded-lg">
                    <div id="qrcode"></div>
                </div>
            </div>
        </div>
    `;
    
    // Generate QR code (using a simple API or library)
    generateQRCode(url);
}

/**
 * Share to social media
 */
function shareToSocial(platform, url, filename) {
    const text = encodeURIComponent(`Check out this file: ${filename}`);
    const shareUrl = encodeURIComponent(url);
    
    let shareLink = '';
    switch (platform) {
        case 'facebook':
            shareLink = `https://www.facebook.com/sharer/sharer.php?u=${shareUrl}`;
            break;
        case 'twitter':
            shareLink = `https://twitter.com/intent/tweet?text=${text}&url=${shareUrl}`;
            break;
        case 'whatsapp':
            shareLink = `https://wa.me/?text=${text}%20${shareUrl}`;
            break;
    }
    
    if (shareLink) {
        window.open(shareLink, '_blank', 'width=600,height=400');
    }
}

/**
 * Generate QR code
 */
function generateQRCode(url) {
    // Using QR Server API (free, no library needed)
    const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(url)}`;
    const qrImg = document.createElement('img');
    qrImg.src = qrUrl;
    qrImg.alt = 'QR Code';
    const container = document.getElementById('qrcode');
    if (container) {
        container.innerHTML = '';
        container.appendChild(qrImg);
    }
}

/**
 * Close share modal
 */
function closeShareModal() {
    const modal = document.getElementById('shareModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

