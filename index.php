<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/src/UploadHandler.php';

$uploadHandler = new UploadHandler($cloudinary, $cloudinary_folder);
$uploadResult = $uploadHandler->handleUpload();
$renameResult = [];

// Get files from Cloudinary (no limit)
$galleryResult = $uploadHandler->getFiles();

// Handle delete request
if (isset($_POST['delete_file']) && !empty($_POST['public_id']) && !empty($_POST['resource_type'])) {
    $fileDeleteResult = $uploadHandler->deleteFile($_POST['public_id'], $_POST['resource_type']);
    if ($fileDeleteResult['success']) {
        // Refresh gallery after successful deletion
        $galleryResult = $uploadHandler->getFiles();
    }
}

// Handle rename request
if (isset($_POST['rename_file']) && !empty($_POST['public_id']) && !empty($_POST['new_filename']) && !empty($_POST['resource_type'])) {
    $renameResult = $uploadHandler->renameFile($_POST['public_id'], $_POST['new_filename'], $_POST['resource_type']);
    if ($renameResult['success']) {
        // Refresh gallery after successful rename
        $galleryResult = $uploadHandler->getFiles();
    }
}

// Load the view template
require_once __DIR__ . '/views/mkuploader.php';
?>