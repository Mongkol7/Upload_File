<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . '/../components/header.php' ?>
    <!-- Custom CSS for animations -->
    <link rel="stylesheet" href="css/animations.css">
</head>
<body data-theme="dark">
    <!-- Loading Overlay Component -->
    <?php include __DIR__ . '/../components/loading.php' ?>

    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-slate-800 to-gray-900 flex items-center justify-center p-4">
        <!-- Animated background blobs -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-96 h-96 bg-gradient-to-br from-green-600/10 to-green-500/5 rounded-full mix-blend-screen filter blur-3xl opacity-30 animate-pulse"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-gradient-to-br from-green-500/10 to-green-600/5 rounded-full mix-blend-screen filter blur-3xl opacity-30 animate-pulse" style="animation-delay: 2s;"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-gradient-to-br from-green-400/8 to-green-600/3 rounded-full mix-blend-overlay filter blur-3xl opacity-25 animate-pulse" style="animation-delay: 4s;"></div>
        </div>
        <div class="relative w-full max-w-md">
            <div class="backdrop-blur-2xl bg-gray-800/70 rounded-3xl p-8 border border-green-500/30 shadow-2xl shadow-green-500/20">
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-green-500 to-green-600 mb-4 shadow-lg shadow-green-500/50">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-green-500">MK UPLOADER</h1>
                    <p class="text-gray-400 text-sm mt-2">Upload any file to Cloudinary</p>
                </div>

                <form action="index.php" method="post" enctype="multipart/form-data" class="space-y-4" id="uploadForm" onsubmit="showLoading('Uploading file...')">
                    <div class="p-8 rounded-2xl border-2 border-dashed border-green-500/50 bg-green-500/10 hover:bg-green-500/20 transition-all duration-300 upload-area" id="uploadArea">
                        <label for="fileToUpload" class="flex flex-col items-center justify-center cursor-pointer">
                            <svg class="w-10 h-10 text-green-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                            <span class="text-gray-100 font-semibold">Choose File</span>
                            <span class="text-gray-400 text-xs mt-1">All file types supported</span>
                        </label>
                        <input type="file" name="fileToUpload" id="fileToUpload" class="hidden">
                    </div>

                    <button type="submit" name="submit" class="btn w-full rounded-xl font-semibold bg-green-500 hover:bg-green-600 border-0 text-gray-900">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Upload File
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- File Gallery Section -->
    <?php if ($galleryResult['success'] && !empty($galleryResult['files'])): ?>
        <div class="container mx-auto px-4 py-8">
            <div class="backdrop-blur-2xl bg-gray-800/70 rounded-3xl p-8 border border-green-500/30 shadow-2xl shadow-green-500/20">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <h2 class="text-2xl font-bold text-green-500 text-center sm:text-left">Your Files Gallery</h2>
                    <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                        <!-- Search Box -->
                        <div class="relative">
                            <input type="text" 
                                   id="searchInput" 
                                   placeholder="Search files... (Ctrl+F)" 
                                   class="px-4 py-2 pl-10 rounded-lg bg-gray-700/50 text-gray-300 placeholder-gray-500 border border-green-500/20 focus:border-green-500/40 focus:outline-none transition-all duration-300 w-full sm:w-64">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        
                        <!-- Sort Options -->
                        <div class="relative">
                            <select id="sortSelect" onchange="sortGallery(this.value)" class="appearance-none w-full px-4 py-2 rounded-lg bg-gray-700/50 text-gray-300 border border-green-500/20 focus:border-green-500/40 focus:outline-none transition-all duration-300 pr-8">
                                <option value="date">Sort by Date</option>
                                <option value="name">Sort by Name</option>
                                <option value="size">Sort by Size</option>
                                <option value="type">Sort by Type</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                            </div>
                        </div>
                        
                        <!-- Modern Category Filter -->
                        <div class="relative">
                            <select id="categoryFilter" class="appearance-none w-full px-4 py-2 rounded-lg bg-gray-700/50 text-gray-300 border border-green-500/20 focus:border-green-500/40 focus:outline-none transition-all duration-300 pr-8">
                                <option value="all">All Files</option>
                                <option value="image">Images</option>
                                <option value="video">Videos</option>
                                <option value="pdf">PDFs</option>
                                <option value="other">Other Files</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                            </div>
                        </div>
                        
                        <!-- Modern Toggle Button -->
                        <button onclick="toggleGallery()" 
                                id="galleryToggle"
                                class="w-full sm:w-auto px-4 py-2 rounded-lg bg-gray-700/50 text-gray-300 border border-green-500/20 hover:border-green-500/40 focus:outline-none transition-all duration-300 flex items-center justify-center gap-2">
                            <svg id="toggleIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            <span id="toggleText">Show Gallery</span>
                        </button>
                    </div>
                </div>
                <?php if ($galleryResult['total_count'] > 0): ?>
                    <p id="gallery-file-count" class="text-center text-gray-400 mt-3 mb-8">
                        Showing <?php echo $galleryResult['total_count']; ?> file(s)
                    </p>
                <?php endif; ?>
                
                <!-- Gallery Skeleton Loading -->
                <?php include __DIR__ . '/../components/gallery-skeleton.php' ?>
                
                <!-- Bulk Actions Bar (will be created by JS) -->
                
                <!-- Gallery Content -->
                <div id="galleryContent" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" style="display: none;">
                    <?php 
                    // Store files array for JavaScript
                    $filesJson = json_encode($galleryResult['files']);
                    ?>
                    <?php foreach ($galleryResult['files'] as $file): ?>
                        <div class="relative group gallery-item-scroll" 
                             data-filename="<?php echo htmlspecialchars(strtolower($file['filename'])); ?>"
                             data-public-id="<?php echo htmlspecialchars($file['public_id']); ?>"
                             data-url="<?php echo htmlspecialchars($file['url']); ?>"
                             data-size="<?php echo htmlspecialchars($file['size'] ?? 0); ?>"
                             data-created-at="<?php echo htmlspecialchars($file['created_at']); ?>"
                             data-category="<?php
                                $category = 'other'; // Default category
                                if (isset($file['format']) && $file['format'] === 'pdf') {
                                    $category = 'pdf';
                                } elseif ($file['resource_type'] === 'image') {
                                    $category = 'image';
                                } elseif ($file['resource_type'] === 'video') {
                                    $category = 'video';
                                }
                                echo htmlspecialchars($category);
                             ?>"
                             data-type="<?php echo htmlspecialchars($file['resource_type']); ?>"
                             data-format="<?php echo htmlspecialchars($file['format']); ?>">
                            <!-- Checkbox for bulk selection -->
                            <div class="absolute top-2 left-2 z-10">
                                <input type="checkbox" 
                                       class="file-checkbox w-5 h-5 rounded border-green-500 text-green-500 focus:ring-green-500"
                                       data-public-id="<?php echo htmlspecialchars($file['public_id']); ?>"
                                       onclick="event.stopPropagation(); toggleFileSelection('<?php echo htmlspecialchars($file['public_id']); ?>')">
                            </div>
                            
                            <div class="backdrop-blur-lg bg-gray-900/80 rounded-2xl overflow-hidden border border-green-500/20 hover:border-green-500/40 transition-all duration-300">
                                 <!-- Preview area - clickable for images/videos -->
                                 <div class="cursor-pointer" onclick="openFullscreenPreview('<?php echo htmlspecialchars($file['url']); ?>', '<?php echo htmlspecialchars($file['resource_type']); ?>', '<?php echo htmlspecialchars($file['filename']); ?>')">
                                     <?php if (isset($file['format']) && $file['format'] === 'pdf'): ?>
                                         <div class="w-full h-48 flex items-center justify-center bg-gray-800">
                                             <div class="text-center">
                                                 <svg class="w-16 h-16 text-red-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                 </svg>
                                                 <p class="text-xs text-gray-400">PDF</p>
                                             </div>
                                         </div>
                                     <?php elseif ($file['resource_type'] === 'image'): ?>
                                         <img src="<?php echo htmlspecialchars($file['url']); ?>" 
                                              alt="Uploaded File" 
                                              class="w-full h-48 object-cover">
                                     <?php elseif ($file['resource_type'] === 'video'): ?>
                                         <video src="<?php echo htmlspecialchars($file['url']); ?>" 
                                                class="w-full h-48 object-cover" 
                                                controls></video>
                                     <?php else: ?>
                                         <div class="w-full h-48 flex items-center justify-center bg-gray-800">
                                             <div class="text-center">
                                                 <svg class="w-16 h-16 text-green-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                 </svg>
                                                 <p class="text-xs text-gray-400"><?php echo htmlspecialchars(strtoupper($file['format'])); ?></p>
                                             </div>
                                         </div>
                                     <?php endif; ?>
                                 </div>
                                 <!-- Detail section - not clickable for preview -->
                                 <div class="p-4" onclick="event.stopPropagation()">
                                    <div id="filename-view-<?php echo htmlspecialchars($file['public_id']); ?>">
                                        <div class="flex items-center justify-between">
                                            <p class="gallery-item-filename text-sm text-gray-300 font-medium mb-1 truncate" title="<?php echo htmlspecialchars($file['filename']); ?>">
                                                <?php echo htmlspecialchars($file['filename']); ?>
                                            </p>
                                            <button onclick="toggleEditMode('<?php echo htmlspecialchars($file['public_id']); ?>', true)" class="text-gray-400 hover:text-white">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L16.732 3.732z"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div id="filename-edit-<?php echo htmlspecialchars($file['public_id']); ?>" style="display: none;">
                                        <form action="index.php" method="post" onsubmit="showLoading('Renaming file...')">
                                            <input type="hidden" name="rename_file" value="1">
                                            <input type="hidden" name="public_id" value="<?php echo htmlspecialchars($file['public_id']); ?>">
                                            <input type="hidden" name="resource_type" value="<?php echo htmlspecialchars($file['resource_type']); ?>">
                                            <input type="text" name="new_filename" value="<?php echo htmlspecialchars($file['filename']); ?>" class="w-full px-2 py-1 rounded bg-gray-700 text-white border border-green-500/50">
                                            <div class="flex justify-end gap-2 mt-2">
                                                <button type="button" onclick="toggleEditMode('<?php echo htmlspecialchars($file['public_id']); ?>', false)" class="text-xs text-gray-400 hover:text-white">Cancel</button>
                                                <button type="submit" class="text-xs text-green-500 hover:text-green-400">Save</button>
                                            </div>
                                        </form>
                                    </div>
                                    <p class="text-xs text-gray-400 mb-1">
                                        <?php 
                                        $datetime = new DateTime($file['created_at']);
                                        $datetime->setTimezone(new DateTimeZone('Asia/Phnom_Penh'));
                                        echo $datetime->format('M d, Y - h:i A');
                                        ?>
                                    </p>
                                    <p class="text-xs text-gray-500 mb-2">
                                        <?php 
                                        $size = $file['size'] ?? 0;
                                        $units = ['B', 'KB', 'MB', 'GB'];
                                        $unitIndex = 0;
                                        while ($size >= 1024 && $unitIndex < count($units) - 1) {
                                            $size /= 1024;
                                            $unitIndex++;
                                        }
                                        echo round($size, 2) . ' ' . $units[$unitIndex];
                                        ?>
                                    </p>
                                    <div class="flex justify-between items-center">
                                        <div class="flex gap-2">
                                            <button onclick="event.stopPropagation(); downloadFile('<?php echo htmlspecialchars($file['url']); ?>', '<?php echo htmlspecialchars($file['filename']); ?>')" 
                                                    class="text-blue-500 hover:text-blue-400 transition-colors" title="Download">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                </svg>
                                            </button>
                                            <button onclick="event.stopPropagation(); openFileDetails(<?php echo htmlspecialchars(json_encode($file)); ?>)" 
                                                    class="text-purple-500 hover:text-purple-400 transition-colors" title="Details">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </button>
                                            <button onclick="event.stopPropagation(); copyToClipboard('<?php echo htmlspecialchars($file['url']); ?>')" 
                                                    class="text-green-500 hover:text-green-400 transition-colors" title="Copy URL">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <button onclick="event.stopPropagation(); confirmDelete('<?php echo htmlspecialchars($file['public_id']); ?>', '<?php echo htmlspecialchars($file['resource_type']); ?>')"
                                                class="text-red-500 hover:text-red-400 transition-colors" title="Delete">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
            </div>
        </div>
    <?php endif; ?>

    <!-- Pass PHP variables to JavaScript -->
    <script>
        // Make PHP variables available to external JavaScript
        window.galleryTotalFiles = <?php echo $galleryResult['total_count'] ?? 0; ?>;
        window.galleryFiles = <?php echo isset($galleryResult['files']) ? json_encode($galleryResult['files']) : '[]'; ?>;
        
        // Pass result data for alerts
        <?php if (isset($renameResult)): ?>
        window.renameResult = {
            success: <?php echo $renameResult['success'] ? 'true' : 'false'; ?>,
            message: <?php echo json_encode($renameResult['message'] ?? ''); ?>
        };
        <?php endif; ?>
        
        <?php if (isset($fileDeleteResult)): ?>
        window.fileDeleteResult = {
            success: <?php echo $fileDeleteResult['success'] ? 'true' : 'false'; ?>,
            message: <?php echo json_encode($fileDeleteResult['message'] ?? ''); ?>
        };
        <?php endif; ?>
        
        <?php if (isset($uploadResult)): ?>
        window.uploadResult = {
            message: <?php echo json_encode($uploadResult['message'] ?? ''); ?>,
            success: <?php echo isset($uploadResult['success']) && $uploadResult['success'] ? 'true' : 'false'; ?>,
            url: <?php echo isset($uploadResult['url']) ? json_encode($uploadResult['url']) : 'null'; ?>
        };
        <?php endif; ?>
    </script>
    
    <!-- External JavaScript files -->
    <script src="js/fullscreen-preview.js"></script>
    <script src="js/bulk-operations.js"></script>
    <script src="js/file-details.js"></script>
    <script src="js/sort.js"></script>
    <script src="js/keyboard-shortcuts.js"></script>
    <script src="js/download.js"></script>
    <script src="js/share.js"></script>
    <script src="js/upload-progress.js"></script>
    <script src="js/gallery.js"></script>
    <script src="js/alerts.js"></script>
    <?php if ($uploadResult['success'] && $uploadResult['url']) { ?>
        <div id="successCard" class="fixed bottom-6 right-6 max-w-xs backdrop-blur-2xl bg-gray-800/70 rounded-2xl p-4 border border-green-500/40 shadow-xl shadow-green-500/20">
            <button onclick="document.getElementById('successCard').style.display='none'" class="absolute top-2 right-2 p-2 hover:bg-gray-700/50 rounded-lg transition-all">
                <svg class="w-5 h-5 text-gray-400 hover:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <div class="flex items-center gap-3 mb-3">
                <?php if ($uploadResult['resource_type'] === 'image'): ?>
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                <?php elseif ($uploadResult['resource_type'] === 'video'): ?>
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                <?php elseif (isset($uploadResult['format']) && strtolower($uploadResult['format']) === 'pdf'): ?>
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-5L9 2H4z" clip-rule="evenodd"/>
                    </svg>
                <?php else: ?>
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                <?php endif; ?>
                <p class="text-sm font-semibold text-green-500">Upload Success!</p>
            </div>
            <?php if ($uploadResult['resource_type'] === 'image'): ?>
                <img src="<?php echo htmlspecialchars($uploadResult['url']); ?>" alt="Uploaded File" class="w-full h-auto rounded-xl shadow-md border border-green-500/30">
            <?php elseif ($uploadResult['resource_type'] === 'video'): ?>
                <video src="<?php echo htmlspecialchars($uploadResult['url']); ?>" class="w-full h-auto rounded-xl shadow-md border border-green-500/30" controls></video>
            <?php else: ?>
                <div class="w-full h-32 flex items-center justify-center bg-gray-800 rounded-xl border border-green-500/30">
                    <div class="text-center">
                        <svg class="w-12 h-12 text-green-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-xs text-gray-400"><?php echo htmlspecialchars(strtoupper($uploadResult['format'])); ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php } ?>

</body>
</html>
