<!-- Loading Overlay Component -->
<div id="loadingOverlay" class="fixed inset-0 bg-gray-900/90 backdrop-blur-sm z-50 flex items-center justify-center" style="display: none;">
    <div class="text-center">
        <div class="inline-block relative">
            <!-- Spinning ring -->
            <div class="w-16 h-16 border-4 border-green-500/30 border-t-green-500 rounded-full animate-spin"></div>
            <!-- Inner pulsing dot -->
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
        </div>
        <p id="loadingText" class="mt-6 text-green-500 font-semibold text-lg">Processing...</p>
        <p class="mt-2 text-gray-400 text-sm">Please wait</p>
    </div>
</div>



