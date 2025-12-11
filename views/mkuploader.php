<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . '/../components/header.php' ?>
</head>
<body data-theme="dark">
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

                <form action="index.php" method="post" enctype="multipart/form-data" class="space-y-4">
                    <div class="p-8 rounded-2xl border-2 border-dashed border-green-500/50 bg-green-500/10 hover:bg-green-500/20 transition-all duration-300">
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
                <h2 class="text-2xl font-bold text-green-500 mb-6 text-center">Your Files Gallery</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <?php foreach ($galleryResult['files'] as $file): ?>
                        <div class="relative group">
                            <div class="backdrop-blur-lg bg-gray-900/80 rounded-2xl overflow-hidden border border-green-500/20 hover:border-green-500/40 transition-all duration-300">
                                <?php if ($file['resource_type'] === 'image'): ?>
                                    <img src="<?php echo htmlspecialchars($file['url']); ?>" 
                                         alt="Uploaded File" 
                                         class="w-full h-48 object-cover">
                                <?php elseif ($file['resource_type'] === 'video'): ?>
                                    <video src="<?php echo htmlspecialchars($file['url']); ?>" 
                                           class="w-full h-48 object-cover" 
                                           controls 
                                           poster="<?php echo htmlspecialchars($file['url']); ?>/video/w_400,h_300,f_auto,q_auto">
                                    </video>
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
                                <div class="p-4">
                                    <p class="text-xs text-gray-400 mb-2">
                                        <?php 
                                        $datetime = new DateTime($file['created_at']);
                                        $datetime->setTimezone(new DateTimeZone('Asia/Phnom_Penh'));
                                        echo $datetime->format('M d, Y - h:i A');
                                        ?>
                                    </p>
                                    <div class="flex justify-between items-center">
                                        <button onclick="copyToClipboard('<?php echo htmlspecialchars($file['url']); ?>')" 
                                                class="text-green-500 hover:text-green-400 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                        </button>
                                        <form action="index.php" method="post" class="inline">
                                            <input type="hidden" name="public_id" value="<?php echo htmlspecialchars($file['public_id']); ?>">
                                            <button type="button" 
                                                onclick="confirmDelete('<?php echo htmlspecialchars($file['public_id']); ?>')"
                                                class="text-red-500 hover:text-red-400 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if ($galleryResult['total_count'] > 0): ?>
                    <p class="text-center text-gray-400 mt-6">
                        Showing <?php echo $galleryResult['total_count']; ?> file(s)
                    </p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <script>
        function copyToClipboard(text) {
            console.log('copyToClipboard called with:', text);
            navigator.clipboard.writeText(text).then(function() {
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
                    timerProgressBar: true
                });
            }, function(err) {
                console.error('Could not copy text: ', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Copy Failed!',
                    text: 'Could not copy URL to clipboard',
                    background: '#1f2937',
                    color: '#f3f4f6',
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'OK'
                });
            });
        }

        function confirmDelete(publicId) {
            console.log('confirmDelete called with:', publicId);
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!',
                background: '#1f2937',
                color: '#f3f4f6'
            }).then((result) => {
                console.log('SweetAlert result:', result);
                if (result.isConfirmed) {
                    console.log('Submitting delete form for:', publicId);
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'index.php';
                    
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'delete_image';
                    input.value = '1';
                    
                    const publicIdInput = document.createElement('input');
                    publicIdInput.type = 'hidden';
                    publicIdInput.name = 'public_id';
                    publicIdInput.value = publicId;
                    
                    form.appendChild(input);
                    form.appendChild(publicIdInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        // Test if functions are loaded
        console.log('JavaScript functions loaded successfully');
        console.log('copyToClipboard function:', typeof copyToClipboard);
        console.log('confirmDelete function:', typeof confirmDelete);

        <?php if (isset($deleteResult)): ?>
            <?php if ($deleteResult['success']): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: '<?php echo htmlspecialchars($deleteResult['message']); ?>',
                    background: '#1f2937',
                    color: '#f3f4f6',
                    confirmButtonColor: '#22c55e',
                    confirmButtonText: 'OK',
                    timer: 2000,
                    timerProgressBar: true
                });
            <?php else: ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Delete Failed!',
                    text: '<?php echo htmlspecialchars($deleteResult['message']); ?>',
                    background: '#1f2937',
                    color: '#f3f4f6',
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'OK'
                });
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($uploadResult['message'] === 'success'): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'File uploaded successfully to Cloudinary',
                background: '#1f2937',
                color: '#f3f4f6',
                confirmButtonColor: '#22c55e',
                confirmButtonText: 'Great!'
            });
        <?php elseif ($uploadResult['message'] === 'error'): ?>
            Swal.fire({
                icon: 'error',
                title: 'Upload Failed!',
                text: 'Please try again',
                background: '#1f2937',
                color: '#f3f4f6',
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'OK'
            });
        <?php elseif ($uploadResult['message'] === 'warning'): ?>
            Swal.fire({
                icon: 'warning',
                title: 'Warning',
                text: 'Upload succeeded but no URL returned',
                background: '#1f2937',
                color: '#f3f4f6',
                confirmButtonColor: '#f59e0b',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>
    </script>
    <?php if ($uploadResult['success'] && $uploadResult['url']) { ?>
        <div id="successCard" class="fixed bottom-6 right-6 max-w-xs backdrop-blur-2xl bg-gray-800/70 rounded-2xl p-4 border border-green-500/40 shadow-xl shadow-green-500/20">
            <button onclick="document.getElementById('successCard').style.display='none'" class="absolute top-2 right-2 p-2 hover:bg-gray-700/50 rounded-lg transition-all">
                <svg class="w-5 h-5 text-gray-400 hover:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <div class="flex items-center gap-3 mb-3">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm font-semibold text-green-500">Upload Success!</p>
            </div>
            <?php if ($uploadResult['resource_type'] === 'image'): ?>
                <img src="<?php echo htmlspecialchars($uploadResult['url']); ?>" alt="Uploaded File" class="w-full h-auto rounded-xl shadow-md border border-green-500/30">
            <?php elseif ($uploadResult['resource_type'] === 'video'): ?>
                <video src="<?php echo htmlspecialchars($uploadResult['url']); ?>" class="w-full h-auto rounded-xl shadow-md border border-green-500/30" controls poster="<?php echo htmlspecialchars($uploadResult['url']); ?>/video/w_300,h_200,f_auto,q_auto"></video>
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

</script>

</body>
</html>
