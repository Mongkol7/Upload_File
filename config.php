<?php
require_once __DIR__ . '/vendor/autoload.php';

// Load environment variables from .env file
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($value));
        }
    }
}

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

// Define folder name from environment
$cloudinary_folder = getenv('CLOUDINARY_FOLDER_NAME') ?: 'Upload_ETEC_PHP';