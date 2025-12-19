<!-- Feedback Form Component -->
<div id="feedbackWidget" class="fixed bottom-6 right-6 z-50">
    <!-- Feedback Button -->
    <button id="feedbackButton" 
            onclick="toggleFeedbackForm()"
            class="w-14 h-14 rounded-full bg-gradient-to-br from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white shadow-lg shadow-green-500/50 hover:shadow-xl hover:shadow-green-500/60 transition-all duration-300 flex items-center justify-center group">
        <svg id="feedbackIcon" class="w-6 h-6 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
        <svg id="closeIcon" class="w-6 h-6 transition-transform duration-300 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>

    <!-- Feedback Form -->
    <div id="feedbackForm" class="hidden absolute bottom-20 right-0 w-80 md:w-96 backdrop-blur-2xl bg-gray-800/90 rounded-2xl p-6 border border-green-500/30 shadow-2xl shadow-green-500/20">
        <h3 class="text-xl font-bold text-green-500 mb-4">Send Feedback</h3>
        <form id="feedbackFormElement" onsubmit="submitFeedback(event)">
            <div class="space-y-4">
                <div>
                    <label for="feedbackName" class="block text-sm font-medium text-gray-300 mb-2">Name (Optional)</label>
                    <input type="text" 
                           id="feedbackName" 
                           name="name"
                           class="w-full px-4 py-2 rounded-lg bg-gray-700/50 text-gray-300 placeholder-gray-500 border border-green-500/20 focus:border-green-500/40 focus:outline-none transition-all duration-300"
                           placeholder="Your name">
                </div>
                <div>
                    <label for="feedbackEmail" class="block text-sm font-medium text-gray-300 mb-2">
                        Email (Optional)
                        <span id="emailAutoDetected" class="text-xs text-green-400 ml-2 hidden">
                            <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Auto-detected
                        </span>
                    </label>
                    <input type="email" 
                           id="feedbackEmail" 
                           name="email"
                           autocomplete="email"
                           class="w-full px-4 py-2 rounded-lg bg-gray-700/50 text-gray-300 placeholder-gray-500 border border-green-500/20 focus:border-green-500/40 focus:outline-none transition-all duration-300"
                           placeholder="your.email@example.com">
                </div>
                <div>
                    <label for="feedbackMessage" class="block text-sm font-medium text-gray-300 mb-2">Message <span class="text-red-500">*</span></label>
                    <textarea id="feedbackMessage" 
                              name="message"
                              rows="4"
                              required
                              class="w-full px-4 py-2 rounded-lg bg-gray-700/50 text-gray-300 placeholder-gray-500 border border-green-500/20 focus:border-green-500/40 focus:outline-none transition-all duration-300 resize-none"
                              placeholder="Your feedback, suggestions, or questions..."></textarea>
                </div>
                <button type="submit" 
                        id="feedbackSubmitBtn"
                        class="w-full px-4 py-2 rounded-lg bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold transition-all duration-300 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Send Feedback
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleFeedbackForm() {
    const form = document.getElementById('feedbackForm');
    const icon = document.getElementById('feedbackIcon');
    const closeIcon = document.getElementById('closeIcon');
    
    if (form.classList.contains('hidden')) {
        form.classList.remove('hidden');
        icon.classList.add('hidden');
        closeIcon.classList.remove('hidden');
        
        // Auto-detect email when form opens
        autoDetectEmail();
    } else {
        form.classList.add('hidden');
        icon.classList.remove('hidden');
        closeIcon.classList.add('hidden');
    }
}

// Auto-detect email address
async function autoDetectEmail() {
    const emailInput = document.getElementById('feedbackEmail');
    const autoDetectedBadge = document.getElementById('emailAutoDetected');
    
    // Don't override if user already typed something
    if (emailInput.value.trim()) {
        return;
    }
    
    let detectedEmail = null;
    
    // Method 1: Try Credential Management API (if available)
    if (navigator.credentials && navigator.credentials.get) {
        try {
            const credential = await navigator.credentials.get({
                password: true,
                mediation: 'silent'
            });
            if (credential && credential.id && credential.id.includes('@')) {
                detectedEmail = credential.id;
            }
        } catch (e) {
            // Silently fail - not all browsers support this
        }
    }
    
    // Method 2: Try to get from browser autocomplete
    // Trigger autocomplete by focusing the field briefly
    if (!detectedEmail) {
        emailInput.focus();
        // Check if browser autofilled
        setTimeout(() => {
            if (emailInput.value && !detectedEmail) {
                detectedEmail = emailInput.value;
            }
        }, 100);
    }
    
    // Method 3: Check localStorage for previously saved email
    if (!detectedEmail) {
        const savedEmail = localStorage.getItem('feedback_email');
        if (savedEmail && savedEmail.includes('@')) {
            detectedEmail = savedEmail;
        }
    }
    
    // Method 4: Try to detect from Google account (if signed in)
    // Note: This is limited by browser security, but we can try
    if (!detectedEmail && window.gapi) {
        try {
            const authInstance = window.gapi.auth2.getAuthInstance();
            if (authInstance && authInstance.isSignedIn) {
                const profile = authInstance.currentUser.get().getBasicProfile();
                if (profile) {
                    detectedEmail = profile.getEmail();
                }
            }
        } catch (e) {
            // Google API not available
        }
    }
    
    // Set the detected email
    if (detectedEmail && !emailInput.value.trim()) {
        emailInput.value = detectedEmail;
        autoDetectedBadge.classList.remove('hidden');
        
        // Add a subtle animation
        emailInput.classList.add('ring-2', 'ring-green-500/50');
        setTimeout(() => {
            emailInput.classList.remove('ring-2', 'ring-green-500/50');
        }, 1000);
        
        // Save to localStorage for future use
        localStorage.setItem('feedback_email', detectedEmail);
    }
}

function submitFeedback(event) {
    event.preventDefault();
    
    const form = event.target;
    const submitBtn = document.getElementById('feedbackSubmitBtn');
    const originalText = submitBtn.innerHTML;
    
    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Sending...';
    
    const formData = new FormData(form);
    
    fetch('api/send-feedback.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Save email to localStorage for future auto-detection
            const emailInput = document.getElementById('feedbackEmail');
            if (emailInput.value.trim()) {
                localStorage.setItem('feedback_email', emailInput.value.trim());
            }
            
            Swal.fire({
                icon: 'success',
                title: 'Thank You!',
                text: data.message,
                background: '#1f2937',
                color: '#f3f4f6',
                confirmButtonColor: '#22c55e',
                confirmButtonText: 'OK'
            });
            form.reset();
            toggleFeedbackForm();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.error || 'Failed to send feedback. Please try again.',
                background: '#1f2937',
                color: '#f3f4f6',
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to send feedback. Please try again.',
            background: '#1f2937',
            color: '#f3f4f6',
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'OK'
        });
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

// Close form when clicking outside
document.addEventListener('click', function(event) {
    const widget = document.getElementById('feedbackWidget');
    const form = document.getElementById('feedbackForm');
    const button = document.getElementById('feedbackButton');
    
    if (!widget.contains(event.target) && !form.classList.contains('hidden')) {
        toggleFeedbackForm();
    }
});
</script>

