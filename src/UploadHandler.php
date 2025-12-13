<?php

class UploadHandler
{
    private $cloudinary;
    private $cloudinary_folder;

    public function __construct($cloudinary, $cloudinary_folder)
    {
        $this->cloudinary = $cloudinary;
        $this->cloudinary_folder = $cloudinary_folder;
    }

    public function handleUpload()
    {
        $result = [
            'success' => false,
            'url' => null,
            'message' => null
        ];

        if (!isset($_POST['submit'])) {
            return $result;
        }

        if (!isset($_FILES['fileToUpload']) || !is_array($_FILES['fileToUpload']) || $_FILES['fileToUpload']['error'] !== 0) {
            $result['message'] = 'error';
            return $result;
        }

        $file = $_FILES['fileToUpload'];
        $tmp = $file['tmp_name'];
        $originalName = pathinfo($file['name'], PATHINFO_FILENAME);

        try {
            $uploadResult = $this->cloudinary->uploadApi()->upload($tmp, [
                'folder' => $this->cloudinary_folder,
                'resource_type' => 'auto', // Auto-detect file type
                'public_id' => $originalName, // Use original filename
                'use_filename' => true, // Use the original filename
                'unique_filename' => false, // Don't add random suffix
                'overwrite' => true // Allow overwriting existing files
            ]);

            if (isset($uploadResult['secure_url'])) {
                $result['success'] = true;
                $result['url'] = $uploadResult['secure_url'];
                $result['resource_type'] = $uploadResult['resource_type'];
                $result['format'] = $uploadResult['format'];
                $result['message'] = 'success';
            } else {
                $result['message'] = 'warning';
            }
        } catch (Exception $e) {
            $result['message'] = 'error';
        }

        return $result;
    }

    public function getFiles()
    {
        try {
            $allResources = [];

            // Resource types to fetch
            $resourceTypes = ['image', 'video', 'raw'];

            foreach ($resourceTypes as $resourceType) {
                $nextCursor = null;
                do {
                    $params = [
                        'type' => 'upload',
                        'prefix' => $this->cloudinary_folder,
                        'max_results' => 500,
                        'resource_type' => $resourceType
                    ];
                    
                    if ($nextCursor) {
                        $params['next_cursor'] = $nextCursor;
                    }
                    
                    $result = $this->cloudinary->adminApi()->assets($params);
                    
                    foreach ($result['resources'] as $resource) {
                        // Extract file name from public_id (remove folder path)
                        $fileName = basename($resource['public_id']);
                        if (isset($resource['format'])) {
                            $fileName .= '.' . $resource['format'];
                        }
                        
                        $allResources[] = [
                            'url' => $resource['secure_url'],
                            'public_id' => $resource['public_id'],
                            'created_at' => $resource['created_at'],
                            'format' => $resource['format'],
                            'size' => $resource['bytes'],
                            'resource_type' => $resource['resource_type'],
                            'type' => $resource['type'],
                            'filename' => $fileName
                        ];
                    }
                    
                    $nextCursor = isset($result['next_cursor']) ? $result['next_cursor'] : null;
                    
                } while ($nextCursor);
            }

            // Sort all fetched files by creation date (newest first)
            usort($allResources, function($a, $b) {
                $dateA = new DateTime($a['created_at']);
                $dateB = new DateTime($b['created_at']);
                return $dateB <=> $dateA; // Newest to oldest
            });

            return [
                'success' => true,
                'files' => $allResources,
                'total_count' => count($allResources)
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'files' => [],
                'error' => $e->getMessage()
            ];
        }
    }

    public function deleteFile($publicId, $resourceType = 'image')
    {
        try {
            $options = ['resource_type' => $resourceType];
            $result = $this->cloudinary->uploadApi()->destroy($publicId, $options);
            
            if ($result['result'] === 'ok') {
                return [
                    'success' => true,
                    'message' => 'File deleted successfully.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to delete file.'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error deleting file: ' . $e->getMessage()
            ];
        }
    }

    public function renameFile($publicId, $newFilename, $resourceType = 'image')
    {
        try {
            // The new public_id should not contain the file extension
            $newFilenameWithoutExt = pathinfo($newFilename, PATHINFO_FILENAME);

            // Construct the new public_id, preserving the folder structure
            $folderPath = dirname($publicId);
            if ($folderPath === '.') {
                $newPublicId = $newFilenameWithoutExt;
            } else {
                $newPublicId = $folderPath . '/' . $newFilenameWithoutExt;
            }

            // Debug logging
            error_log("Rename attempt: '$publicId' -> '$newPublicId' (type: $resourceType)");

            // Use the correct rename method with proper parameters
            $result = $this->cloudinary->uploadApi()->rename($publicId, $newPublicId, [
                'resource_type' => $resourceType,
                'invalidate' => true
            ]);
            
            // Debug logging
            error_log("Rename result: " . print_r($result, true));

            // Check if the rename was successful - Cloudinary returns the updated asset info
            if (isset($result['public_id'])) {
                return [
                    'success' => true,
                    'message' => 'File renamed successfully.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Rename failed. The file may already exist or the name is invalid.'
                ];
            }
        } catch (Exception $e) {
            error_log("Rename exception: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Rename failed: ' . $e->getMessage()
            ];
        }
    }
}
