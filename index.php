<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/src/UploadHandler.php';

$uploadHandler = new UploadHandler($cloudinary, $cloudinary_folder);
$uploadResult = $uploadHandler->handleUpload();
$renameResult = null;
$fileDeleteResult = null;

// Get files from Cloudinary (no limit)
$galleryResult = $uploadHandler->getFiles();

// Handle bulk delete request
if (isset($_POST['bulk_delete']) && !empty($_POST['selected_files'])) {
    $selectedFiles = json_decode($_POST['selected_files'], true);
    $bulkDeleteResult = ['success' => true, 'deleted' => 0, 'failed' => 0];
    
    if (is_array($selectedFiles)) {
        foreach ($selectedFiles as $fileData) {
            // File data might be just public_id or an object
            if (is_array($fileData)) {
                $publicId = $fileData['public_id'] ?? $fileData;
                $resourceType = $fileData['resource_type'] ?? 'image';
            } else {
                // Try to get resource type from gallery files
                $publicId = $fileData;
                $resourceType = 'image'; // Default, will try to detect
            }
            
            $result = $uploadHandler->deleteFile($publicId, $resourceType);
            if ($result['success']) {
                $bulkDeleteResult['deleted']++;
            } else {
                $bulkDeleteResult['failed']++;
            }
        }
        
        if ($bulkDeleteResult['deleted'] > 0) {
            // Refresh gallery after successful deletion
            $galleryResult = $uploadHandler->getFiles();
        }
        
        $fileDeleteResult = [
            'success' => $bulkDeleteResult['failed'] === 0,
            'message' => "Deleted {$bulkDeleteResult['deleted']} file(s)" . ($bulkDeleteResult['failed'] > 0 ? ", {$bulkDeleteResult['failed']} failed" : "")
        ];
    }
}

// Handle single delete request
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