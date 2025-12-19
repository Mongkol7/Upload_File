<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/UploadHandler.php';
require_once __DIR__ . '/../src/AIAnalyzer.php';

header('Content-Type: application/json');

// Check if CLIP service is available
$clipApiUrl = $_ENV['CLIP_API_URL'] ?? 'http://127.0.0.1:5000';

// Test connection to CLIP service
$ch = curl_init($clipApiUrl . '/health');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$healthCheck = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    echo json_encode([
        'success' => false,
        'error' => 'CLIP AI service is not running. Please start the AI service first (see ai-service/README.md)'
    ]);
    exit;
}

$uploadHandler = new UploadHandler($cloudinary, $cloudinary_folder);
$aiAnalyzer = new AIAnalyzer(); // No API key needed for local service

// Get search query
$query = $_GET['q'] ?? $_POST['q'] ?? '';
$isPrewarm = isset($_GET['prewarm']) && $_GET['prewarm'] === 'true';

// If this is a pre-warm request, just check health and return quickly
if ($isPrewarm && ($query === 'warmup' || $query === 'test')) {
    echo json_encode([
        'success' => true,
        'message' => 'AI service is ready',
        'prewarmed' => true
    ]);
    exit;
}

if (empty($query)) {
    echo json_encode([
        'success' => false,
        'error' => 'Search query is required'
    ]);
    exit;
}

try {
    // Get all files
    $galleryResult = $uploadHandler->getFiles();
    
    if (!$galleryResult['success'] || empty($galleryResult['files'])) {
        echo json_encode([
            'success' => true,
            'files' => []
        ]);
        exit;
    }

    $files = $galleryResult['files'];
    
    // Log search for debugging
    $mediaFiles = array_filter($files, function($file) {
        $type = $file['resource_type'] ?? '';
        return in_array($type, ['image', 'video']);
    });
    error_log("CLIP Search: Query='{$query}', Total files=" . count($files) . ", Media files=" . count($mediaFiles));
    
    // Perform semantic search using CLIP - analyzes actual image/video content
    $matchedFiles = $aiAnalyzer->semanticSearch($query, $files);
    
    error_log("CLIP Search: Found " . count($matchedFiles) . " matching files");
    
    // Debug: Log first few file URLs being sent
    if (!empty($mediaFiles)) {
        $sampleFiles = array_slice($mediaFiles, 0, 3);
        foreach ($sampleFiles as $file) {
            error_log("Sample file URL: " . ($file['url'] ?? 'no URL'));
        }
    }
    
    // Remove duplicates
    $uniqueFiles = [];
    $seenPublicIds = [];
    foreach ($matchedFiles as $file) {
        $publicId = $file['public_id'] ?? $file['url'] ?? '';
        if (!in_array($publicId, $seenPublicIds)) {
            $uniqueFiles[] = $file;
            $seenPublicIds[] = $publicId;
        }
    }
    
    echo json_encode([
        'success' => true,
        'files' => $uniqueFiles,
        'count' => count($uniqueFiles),
        'message' => 'AI analyzed ' . count($files) . ' files and found ' . count($uniqueFiles) . ' matches'
    ]);
    
} catch (Exception $e) {
    error_log("AI Search Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

