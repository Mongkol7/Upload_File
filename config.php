<?php
require_once __DIR__ . '/vendor/autoload.php';

use Cloudinary\Cloudinary;

$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => getenv('CLOUDINARY_CLOUD_NAME') ?: '',
        'api_key' => getenv('CLOUDINARY_API_KEY') ?: '',
        'api_secret' => getenv('CLOUDINARY_API_SECRET') ?: '',
        'folder_name' => getenv('CLOUDINARY_FOLDER_NAME') ?: 'Upload_ETEC_PHP',
    ],
    'url' => [
        'secure' => true,
    ],
]);  