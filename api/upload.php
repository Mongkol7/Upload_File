<?php
// Serverless function for Vercel: /api/upload.php
// Expects multipart/form-data with field name "fileToUpload"

require_once __DIR__ . '/../vendor/autoload.php';

use Cloudinary\Cloudinary;
use Dotenv\Dotenv;

// Load .env locally; on Vercel, getenv() is populated from project env vars
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->safeLoad();
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    if (!isset($_FILES['fileToUpload']) || !is_array($_FILES['fileToUpload']) || $_FILES['fileToUpload']['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'No file uploaded']);
        exit;
    }

    $cloudinary = new Cloudinary([
        'cloud' => [
            'cloud_name' => getenv('CLOUDINARY_CLOUD_NAME'),
            'api_key' => getenv('CLOUDINARY_API_KEY'),
            'api_secret' => getenv('CLOUDINARY_API_SECRET'),
        ],
        'url' => [
            'secure' => true,
        ],
    ]);

    $folder = getenv('CLOUDINARY_FOLDER_NAME') ?: 'Upload_ETEC_PHP';
    $tmp = $_FILES['fileToUpload']['tmp_name'];

    $result = $cloudinary->uploadApi()->upload($tmp, [
        'folder' => $folder,
    ]);

    if (!isset($result['secure_url'])) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'Upload failed']);
        exit;
    }

    echo json_encode([
        'ok' => true,
        'url' => $result['secure_url'],
        'public_id' => $result['public_id'] ?? null,
        'folder' => $folder,
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
