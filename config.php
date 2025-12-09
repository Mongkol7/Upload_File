<?php
require_once __DIR__ . '/vendor/autoload.php';

// Load environment variables using dotenv
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use Cloudinary\Cloudinary;

$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'] ?? '',
        'api_key' => $_ENV['CLOUDINARY_API_KEY'] ?? '',
        'api_secret' => $_ENV['CLOUDINARY_API_SECRET'] ?? '',
    ],
    'url' => [
        'secure' => true,
    ],
]);

// Define folder name from environment
$cloudinary_folder = $_ENV['CLOUDINARY_FOLDER_NAME'] ?? 'Upload_ETEC_PHP_';