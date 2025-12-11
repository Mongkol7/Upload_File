<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/src/UploadHandler.php';

$uploadHandler = new UploadHandler($cloudinary, $cloudinary_folder);
$uploadResult = $uploadHandler->handleUpload();

// Get files from Cloudinary (no limit)
$galleryResult = $uploadHandler->getImages();

// Handle delete request
if (isset($_POST['delete_image']) && !empty($_POST['public_id'])) {
    $deleteResult = $uploadHandler->deleteImage($_POST['public_id']);
    if ($deleteResult['success']) {
        // Refresh gallery after successful deletion
        $galleryResult = $uploadHandler->getImages();
    }
}

// Load the view template
require_once __DIR__ . '/views/mkuploader.php';
?>
