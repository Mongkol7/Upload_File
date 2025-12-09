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
            ]);

            if (isset($uploadResult['secure_url'])) {
                $result['success'] = true;
                $result['url'] = $uploadResult['secure_url'];
                $result['message'] = 'success';
            } else {
                $result['message'] = 'warning';
            }
        } catch (Exception $e) {
            $result['message'] = 'error';
        }

        return $result;
    }

    public function getImages($limit = 20)
    {
        try {
            $result = $this->cloudinary->adminApi()->assets([
                'type' => 'upload',
                'prefix' => $this->cloudinary_folder,
                'max_results' => $limit,
                'resource_type' => 'image'
            ]);

            $images = [];
            foreach ($result['resources'] as $resource) {
                $images[] = [
                    'url' => $resource['secure_url'],
                    'public_id' => $resource['public_id'],
                    'created_at' => $resource['created_at'],
                    'format' => $resource['format'],
                    'size' => $resource['bytes']
                ];
            }

            return [
                'success' => true,
                'images' => $images,
                'total_count' => count($images)
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'images' => [],
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
