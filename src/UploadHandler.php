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

        try {
            $uploadResult = $this->cloudinary->uploadApi()->upload($tmp, [
                'folder' => $this->cloudinary_folder,
                'resource_type' => 'auto' // Auto-detect file type
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

    public function getImages()
    {
        try {
            $allFiles = [];
            $nextCursor = null;
            
            do {
                $params = [
                    'type' => 'upload',
                    'prefix' => $this->cloudinary_folder,
                    'max_results' => 500 // Maximum per request
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
                    
                    $allFiles[] = [
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
                
                // Check if there are more files to fetch
                $nextCursor = isset($result['next_cursor']) ? $result['next_cursor'] : null;
                
            } while ($nextCursor); // Continue while there's a next cursor

            // Sort files by creation date (newest first)
            usort($allFiles, function($a, $b) {
                $dateA = new DateTime($a['created_at']);
                $dateB = new DateTime($b['created_at']);
                return $dateB <=> $dateA; // Newest to oldest
            });

            return [
                'success' => true,
                'files' => $allFiles,
                'total_count' => count($allFiles)
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'files' => [],
                'error' => $e->getMessage()
            ];
        }
    }

    public function deleteImage($publicId)
    {
        try {
            $result = $this->cloudinary->uploadApi()->destroy($publicId);
            return [
                'success' => $result['result'] === 'ok',
                'message' => $result['result'] === 'ok' ? 'Image deleted successfully' : 'Failed to delete image'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error deleting image: ' . $e->getMessage()
            ];
        }
    }
}
