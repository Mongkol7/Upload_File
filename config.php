<?php
require_once __DIR__ . '/vendor/autoload.php';

// Load environment variables using phpdotenv
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use Cloudinary\Cloudinary;

$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'] ?? '',
        'api_key' => $_ENV['CLOUDINARY_API_KEY'] ?? '',
        'api_secret' => $_ENV['CLOUDINARY_API_SECRET'] ?? '',
        'folder_name' => $_ENV['CLOUDINARY_FOLDER_NAME'] ?? 'Upload_ETEC_PHP_',
    ],
    'url' => [
        'secure' => true,
    ],
]);