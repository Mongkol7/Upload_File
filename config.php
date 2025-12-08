<?php
require_once __DIR__ . '/vendor/autoload.php';

use Cloudinary\Cloudinary;

$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => 'dnuqw4ctr',
        'api_key' => '314143141236428',
        'api_secret' => 'au4nElzt3DiUHs8L7u0TXl0CwIo',
        'folder_name' => 'Upload_ETEC_PHP',
    ],
    'url' => [
        'secure' => true,
    ],
]);  