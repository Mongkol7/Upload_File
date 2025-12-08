<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Image</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.4.19/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body data-theme="dark">
    <!-- to run: http://localhost/website/uploadfile.php -->
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-slate-800 to-gray-900 flex items-center justify-center p-4">
        <div class="relative w-full max-w-md">
            <div class="backdrop-blur-2xl bg-gray-800/70 rounded-3xl p-8 border border-green-500/30 shadow-2xl shadow-green-500/20">
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-green-500 to-green-600 mb-4 shadow-lg shadow-green-500/50">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-green-500">MK UPLOADER</h1>
                    <p class="text-gray-400 text-sm mt-2">Select an image to upload into Cloudinary</p>
                </div>

                <form action="uploadfile.php" method="post" enctype="multipart/form-data" class="space-y-4">
                    <div class="p-8 rounded-2xl border-2 border-dashed border-green-500/50 bg-green-500/10 hover:bg-green-500/20 transition-all duration-300">
                        <label for="fileToUpload" class="flex flex-col items-center justify-center cursor-pointer">
                            <svg class="w-10 h-10 text-green-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <span class="text-gray-100 font-semibold">Choose Image</span>
                            <span class="text-gray-400 text-xs mt-1">JPG, PNG, GIF, WebP</span>
                        </label>
                        <input type="file" name="fileToUpload" id="fileToUpload" accept="image/*" class="hidden">
                    </div>

                    <button type="submit" name="submit" class="btn w-full rounded-xl font-semibold bg-green-500 hover:bg-green-600 border-0 text-gray-900">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Upload Image
                    </button>
                </form>
            </div>
        </div>
    </div>

    <?php
        include 'config.php';
        if (class_exists('Dotenv\\Dotenv')) {
            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
            $dotenv->safeLoad();
        }
        $cloud_url = null;
        $upload_status = null;
        
        if (isset($_POST['submit'])) {
            if (isset($_FILES['fileToUpload']) && is_array($_FILES['fileToUpload']) && $_FILES['fileToUpload']['error'] === 0) {
                $file = $_FILES['fileToUpload'];
                $tmp = $file['tmp_name'];
                try {
                    $folder = getenv('FOLDER_NAME') ?: 'Upload_ETEC_PHP';
                    $result = $cloudinary->uploadApi()->upload($tmp, [
                        'folder' => $folder,
                        // 'resource_type' => 'image', // optional; auto-detected
                    ]);
                    if (isset($result['secure_url'])) {
                        $cloud_url = $result['secure_url'];
                        $upload_status = 'success';
                    } else {
                        $upload_status = 'warning';
                    }
                } catch (Exception $e) {
                    $upload_status = 'error';
                }
            } else {
                $upload_status = 'error';
            }
        }
    ?>

    <script>
        <?php if ($upload_status === 'success'): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'File uploaded successfully to Cloudinary',
                background: '#1f2937',
                color: '#f3f4f6',
                confirmButtonColor: '#22c55e',
                confirmButtonText: 'Great!'
            });
        <?php elseif ($upload_status === 'error'): ?>
            Swal.fire({
                icon: 'error',
                title: 'Upload Failed!',
                text: 'Please try again',
                background: '#1f2937',
                color: '#f3f4f6',
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'OK'
            });
        <?php elseif ($upload_status === 'warning'): ?>
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
    <?php if ($cloud_url) { ?>
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
            <img src="<?php echo htmlspecialchars($cloud_url); ?>" alt="Uploaded Image" class="w-full h-auto rounded-xl shadow-md border border-green-500/30">
        </div>
    <?php } ?>

</body>
</html>
