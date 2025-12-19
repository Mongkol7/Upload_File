<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/AIAnalyzer.php';

header('Content-Type: application/json');

// Get Gemini API key from environment
$geminiApiKey = $_ENV['GEMINI_API_KEY'] ?? '';

if (empty($geminiApiKey)) {
    echo json_encode([
        'success' => false,
        'error' => 'Gemini API key not configured'
    ]);
    exit;
}

$aiAnalyzer = new AIAnalyzer($geminiApiKey);

// Get file details
$fileUrl = $_POST['url'] ?? $_GET['url'] ?? '';
$resourceType = $_POST['resource_type'] ?? $_GET['resource_type'] ?? 'image';
$format = $_POST['format'] ?? $_GET['format'] ?? '';

if (empty($fileUrl)) {
    echo json_encode([
        'success' => false,
        'error' => 'File URL is required'
    ]);
    exit;
}

try {
    $analysis = $aiAnalyzer->analyzeFile($fileUrl, $resourceType, $format);
    
    echo json_encode([
        'success' => true,
        'analysis' => $analysis
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

