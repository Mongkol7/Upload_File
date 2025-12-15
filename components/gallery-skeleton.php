<!-- Gallery Skeleton Loading Component -->
<div id="gallerySkeleton" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" style="display: none;">
    <?php 
    // Generate 8 skeleton cards
    for ($i = 0; $i < 8; $i++): 
    ?>
        <div class="backdrop-blur-lg bg-gray-900/80 rounded-2xl overflow-hidden border border-green-500/20">
            <!-- Image skeleton -->
            <div class="w-full h-48 bg-gray-800/50 skeleton-shimmer"></div>
            
            <!-- Content skeleton -->
            <div class="p-4 space-y-3">
                <!-- Filename skeleton -->
                <div class="flex items-center justify-between">
                    <div class="h-4 bg-gray-700/50 rounded skeleton-shimmer flex-1 mr-2"></div>
                    <div class="h-4 w-4 bg-gray-700/50 rounded skeleton-shimmer"></div>
                </div>
                
                <!-- Date skeleton -->
                <div class="h-3 bg-gray-700/30 rounded skeleton-shimmer w-2/3"></div>
                
                <!-- Actions skeleton -->
                <div class="flex justify-between items-center pt-2">
                    <div class="h-5 w-5 bg-gray-700/50 rounded skeleton-shimmer"></div>
                    <div class="h-5 w-5 bg-gray-700/50 rounded skeleton-shimmer"></div>
                </div>
            </div>
        </div>
    <?php endfor; ?>
</div>


