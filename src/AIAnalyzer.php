<?php

/**
 * AI Analyzer Service using Self-Hosted CLIP API
 * Analyzes images and videos using local CLIP model
 */
class AIAnalyzer
{
    private $apiUrl;
    private $timeout = 60; // Increased timeout for image analysis

    public function __construct($apiKey = null)
    {
        // Use local CLIP service instead of Google Gemini
        $this->apiUrl = $_ENV['CLIP_API_URL'] ?? 'http://127.0.0.1:5000';
    }

    /**
     * Analyze an image using CLIP (kept for compatibility, but uses search endpoint)
     * @param string $imageUrl - URL of the image to analyze
     * @return array - Analysis result with description and tags
     */
    public function analyzeImage($imageUrl)
    {
        try {
            // Fetch image content
            $imageContent = @file_get_contents($imageUrl);
            if ($imageContent === false) {
                error_log("Failed to fetch image from URL: $imageUrl");
                return ['description' => '', 'tags' => []];
            }

            // Detect MIME type
            $imageInfo = @getimagesizefromstring($imageContent);
            $mimeType = $imageInfo ? $imageInfo['mime'] : 'image/jpeg';

            $prompt = "Analyze this image and provide: 1) A detailed description of what you see, 2) Key objects, people, or elements, 3) Colors and style, 4) Any text visible in the image, 5) Context or setting. Format the response as a JSON object with 'description', 'objects', 'colors', 'text', and 'context' fields.";

            $data = [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ],
                            [
                                'inline_data' => [
                                    'mime_type' => $mimeType,
                                    'data' => base64_encode($imageContent)
                                ]
                            ]
                        ]
                    ]
                ]
            ];

            $response = $this->makeRequest($data);
            
            if ($response && isset($response['candidates'][0]['content']['parts'][0]['text'])) {
                $analysisText = $response['candidates'][0]['content']['parts'][0]['text'];
                return $this->parseAnalysis($analysisText);
            }

            return ['description' => '', 'tags' => []];
        } catch (Exception $e) {
            error_log("AI Image Analysis Error: " . $e->getMessage());
            return ['description' => '', 'tags' => []];
        }
    }

    /**
     * Analyze a video (using thumbnail/first frame)
     * @param string $videoUrl - URL of the video
     * @return array - Analysis result
     */
    public function analyzeVideo($videoUrl)
    {
        try {
            // For videos, we'll analyze a thumbnail or first frame
            // Cloudinary can generate thumbnails
            $thumbnailUrl = str_replace('/upload/', '/upload/w_400,h_300,c_fill/', $videoUrl);
            
            // Fetch thumbnail content
            $thumbnailContent = @file_get_contents($thumbnailUrl);
            if ($thumbnailContent === false) {
                error_log("Failed to fetch video thumbnail from URL: $thumbnailUrl");
                return ['description' => '', 'tags' => []];
            }

            $prompt = "Analyze this video thumbnail and provide: 1) A detailed description of the video content, 2) Key subjects or actions, 3) Setting or location, 4) Any visible text or graphics. Format as JSON with 'description', 'subjects', 'setting', and 'text' fields.";

            $data = [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ],
                            [
                                'inline_data' => [
                                    'mime_type' => 'image/jpeg',
                                    'data' => base64_encode($thumbnailContent)
                                ]
                            ]
                        ]
                    ]
                ]
            ];

            $response = $this->makeRequest($data);
            
            if ($response && isset($response['candidates'][0]['content']['parts'][0]['text'])) {
                $analysisText = $response['candidates'][0]['content']['parts'][0]['text'];
                return $this->parseAnalysis($analysisText);
            }

            return ['description' => '', 'tags' => []];
        } catch (Exception $e) {
            error_log("AI Video Analysis Error: " . $e->getMessage());
            return ['description' => '', 'tags' => []];
        }
    }

    /**
     * Analyze a document (PDF, text files)
     * @param string $documentUrl - URL of the document
     * @param string $mimeType - MIME type of the document
     * @return array - Analysis result
     */
    public function analyzeDocument($documentUrl, $mimeType = 'application/pdf')
    {
        try {
            // For PDFs, we'll extract text first if possible
            // For now, we'll use a simplified approach
            $prompt = "Analyze this document and provide: 1) Main topic or subject, 2) Key points or summary, 3) Document type, 4) Important keywords. Format as JSON with 'topic', 'summary', 'type', and 'keywords' fields.";

            // Try to get text content from PDF
            $textContent = '';
            if ($mimeType === 'application/pdf') {
                // For PDF, we'd need a PDF parser library
                // For now, we'll use a generic approach
                $textContent = "PDF Document";
            } else {
                $textContent = file_get_contents($documentUrl);
            }

            $data = [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt . "\n\nDocument content preview: " . substr($textContent, 0, 1000)
                            ]
                        ]
                    ]
                ]
            ];

            $response = $this->makeRequest($data);
            
            if ($response && isset($response['candidates'][0]['content']['parts'][0]['text'])) {
                $analysisText = $response['candidates'][0]['content']['parts'][0]['text'];
                return $this->parseAnalysis($analysisText);
            }

            return ['description' => '', 'tags' => []];
        } catch (Exception $e) {
            error_log("AI Document Analysis Error: " . $e->getMessage());
            return ['description' => '', 'tags' => []];
        }
    }

    /**
     * Analyze any file based on its type
     * @param string $fileUrl - URL of the file
     * @param string $resourceType - Type: image, video, raw
     * @param string $format - File format
     * @return array - Analysis result
     */
    public function analyzeFile($fileUrl, $resourceType, $format = '')
    {
        if ($resourceType === 'image') {
            return $this->analyzeImage($fileUrl);
        } elseif ($resourceType === 'video') {
            return $this->analyzeVideo($fileUrl);
        } elseif ($format === 'pdf' || $resourceType === 'raw') {
            return $this->analyzeDocument($fileUrl);
        }
        
        return ['description' => '', 'tags' => []];
    }

    /**
     * Search files using AI semantic search by analyzing actual image/video content
     * Uses self-hosted CLIP service
     * @param string $query - Search query
     * @param array $files - Array of files to search through
     * @return array - Array of matching file objects
     */
    public function semanticSearch($query, $files)
    {
        try {
            // Filter to only images and videos (CLIP can analyze these)
            $mediaFiles = array_filter($files, function($file) {
                $type = $file['resource_type'] ?? '';
                return in_array($type, ['image', 'video']);
            });
            
            if (empty($mediaFiles)) {
                return [];
            }

            // Prepare files for CLIP API - limit to 20 for performance
            $filesForApi = array_map(function($file) {
                return [
                    'url' => $file['url'] ?? '',
                    'resource_type' => $file['resource_type'] ?? 'image',
                    'public_id' => $file['public_id'] ?? '',
                    'filename' => $file['filename'] ?? ''
                ];
            }, array_slice(array_values($mediaFiles), 0, 20)); // Limit to 20 files

            // Call local CLIP service
            $data = [
                'query' => $query,
                'files' => $filesForApi
            ];

            $ch = curl_init($this->apiUrl . '/search');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                error_log("CLIP API cURL Error: " . $curlError);
                return [];
            }

            if ($httpCode !== 200) {
                error_log("CLIP API Error: HTTP $httpCode - $response");
                return [];
            }

            $result = json_decode($response, true);
            
            if ($result && isset($result['success']) && $result['success']) {
                // Remove similarity score from response (not needed by PHP)
                $matchedFiles = array_map(function($file) {
                    unset($file['similarity']);
                    return $file;
                }, $result['files'] ?? []);
                
                error_log("CLIP search found " . count($matchedFiles) . " matches for query: '$query'");
                return $matchedFiles;
            }

            error_log("CLIP API returned error: " . ($result['error'] ?? 'Unknown error'));
            return [];
            
        } catch (Exception $e) {
            error_log("AI Semantic Search Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Analyze multiple files in batch to check if they match the query
     * @param array $files - Array of files to analyze
     * @param string $query - Search query
     * @return array - Array of matching files
     */
    private function batchAnalyzeFiles($files, $query)
    {
        $matchedFiles = [];
        
        // Process files in smaller batches (5 at a time) to avoid token limits
        $batchSize = 5;
        $batches = array_chunk($files, $batchSize);
        
        foreach ($batches as $batch) {
            $batchResults = $this->analyzeBatch($batch, $query);
            $matchedFiles = array_merge($matchedFiles, $batchResults);
        }
        
        return $matchedFiles;
    }
    
    /**
     * Analyze a batch of files together
     * @param array $files - Batch of files (max 5)
     * @param string $query - Search query
     * @return array - Matching files from this batch
     */
    private function analyzeBatch($files, $query)
    {
        try {
            $parts = [];
            $fileIndex = 0;
            $fileMap = []; // Map index to file object
            
            // Build prompt with all images
            $prompt = "I will show you " . count($files) . " images/videos. For each one, determine if it contains or shows: '{$query}'. Look at the actual visual content, objects, subjects, scenes, text, colors, and context.\n\n";
            $prompt .= "Respond with a JSON array where each element is 'yes' or 'no' corresponding to each image in order. Example: [\"yes\", \"no\", \"yes\"]\n\n";
            $prompt .= "Images:";
            
            $parts[] = ['text' => $prompt];
            
            foreach ($files as $file) {
                $fileUrl = $file['url'] ?? '';
                $resourceType = $file['resource_type'] ?? 'image';
                
                if (empty($fileUrl)) {
                    error_log("Skipping file with empty URL");
                    continue;
                }
                
                // Get image content
                if ($resourceType === 'image') {
                    $imageContent = @file_get_contents($fileUrl);
                    if ($imageContent === false) {
                        error_log("Failed to fetch image from: $fileUrl");
                        continue;
                    }
                    
                    $imageInfo = @getimagesizefromstring($imageContent);
                    $mimeType = $imageInfo ? $imageInfo['mime'] : 'image/jpeg';
                    
                    // Map this file to the current index
                    $fileMap[$fileIndex] = $file;
                    
                    $parts[] = [
                        'text' => "\nImage " . ($fileIndex + 1) . ":"
                    ];
                    $parts[] = [
                        'inline_data' => [
                            'mime_type' => $mimeType,
                            'data' => base64_encode($imageContent)
                        ]
                    ];
                    
                    $fileIndex++;
                } elseif ($resourceType === 'video') {
                    // For videos, use thumbnail
                    $thumbnailUrl = str_replace('/upload/', '/upload/w_400,h_300,c_fill/', $fileUrl);
                    $thumbnailContent = @file_get_contents($thumbnailUrl);
                    if ($thumbnailContent === false) {
                        error_log("Failed to fetch video thumbnail from: $thumbnailUrl");
                        continue;
                    }
                    
                    // Map this file to the current index
                    $fileMap[$fileIndex] = $file;
                    
                    $parts[] = [
                        'text' => "\nVideo " . ($fileIndex + 1) . " (thumbnail):"
                    ];
                    $parts[] = [
                        'inline_data' => [
                            'mime_type' => 'image/jpeg',
                            'data' => base64_encode($thumbnailContent)
                        ]
                    ];
                    
                    $fileIndex++;
                } else {
                    // Skip non-image/video files
                    continue;
                }
            }
            
            if (empty($fileMap)) {
                return [];
            }
            
            // Make API request
            $data = [
                'contents' => [
                    [
                        'parts' => $parts
                    ]
                ]
            ];
            
            $response = $this->makeRequest($data);
            
            if (!$response) {
                error_log("No response from Gemini API");
                return [];
            }
            
            if (isset($response['error'])) {
                error_log("Gemini API error: " . json_encode($response['error']));
                return [];
            }
            
            if (isset($response['candidates'][0]['content']['parts'][0]['text'])) {
                $resultText = $response['candidates'][0]['content']['parts'][0]['text'];
                error_log("Gemini response text: " . substr($resultText, 0, 200));
                
                // Extract JSON array - try multiple patterns
                $jsonPatterns = [
                    '/\[[\s\S]*?\]/',  // Standard JSON array
                    '/\[.*?\]/s',      // With newlines
                ];
                
                $results = null;
                foreach ($jsonPatterns as $pattern) {
                    if (preg_match($pattern, $resultText, $matches)) {
                        $decoded = json_decode($matches[0], true);
                        if (is_array($decoded) && count($decoded) > 0) {
                            $results = $decoded;
                            break;
                        }
                    }
                }
                
                // If no JSON found, try to parse yes/no from text
                if ($results === null) {
                    error_log("Could not parse JSON, trying text parsing");
                    $lines = explode("\n", $resultText);
                    $results = [];
                    foreach ($lines as $line) {
                        $lineLower = strtolower(trim($line));
                        if (strpos($lineLower, 'yes') !== false || strpos($lineLower, 'true') !== false) {
                            $results[] = 'yes';
                        } elseif (strpos($lineLower, 'no') !== false || strpos($lineLower, 'false') !== false) {
                            $results[] = 'no';
                        }
                    }
                }
                
                if (is_array($results) && count($results) > 0) {
                    $matchedFiles = [];
                    $resultIndex = 0;
                    foreach ($fileMap as $index => $file) {
                        if ($resultIndex < count($results)) {
                            $result = $results[$resultIndex];
                            $resultLower = strtolower(trim($result));
                            if ($resultLower === 'yes' || 
                                $resultLower === 'true' ||
                                strpos($resultLower, 'yes') !== false) {
                                $matchedFiles[] = $file;
                                error_log("File matched: " . ($file['filename'] ?? 'unknown'));
                            }
                            $resultIndex++;
                        }
                    }
                    error_log("Batch analysis found " . count($matchedFiles) . " matches");
                    return $matchedFiles;
                }
            }
            
            error_log("Could not parse Gemini response");
            return [];
        } catch (Exception $e) {
            error_log("Batch analysis error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Check if a file's content matches the search query
     * @param string $fileUrl - URL of the file
     * @param string $resourceType - Type: image, video, raw
     * @param string $query - Search query
     * @param string $format - File format
     * @return bool - True if file matches query
     */
    private function doesFileMatchQuery($fileUrl, $resourceType, $query, $format = '')
    {
        try {
            $prompt = "Does this image/video contain or show: '{$query}'? Look at the actual visual content, objects, subjects, scenes, text, colors, and context. Respond with ONLY 'yes' or 'no'.";
            
            $parts = [
                ['text' => $prompt]
            ];
            
            // Add image/video content
            if ($resourceType === 'image') {
                $imageContent = @file_get_contents($fileUrl);
                if ($imageContent === false) {
                    return false;
                }
                
                $imageInfo = @getimagesizefromstring($imageContent);
                $mimeType = $imageInfo ? $imageInfo['mime'] : 'image/jpeg';
                
                $parts[] = [
                    'inline_data' => [
                        'mime_type' => $mimeType,
                        'data' => base64_encode($imageContent)
                    ]
                ];
            } elseif ($resourceType === 'video') {
                // For videos, analyze thumbnail
                $thumbnailUrl = str_replace('/upload/', '/upload/w_400,h_300,c_fill/', $fileUrl);
                $thumbnailContent = @file_get_contents($thumbnailUrl);
                if ($thumbnailContent === false) {
                    return false;
                }
                
                $parts[] = [
                    'inline_data' => [
                        'mime_type' => 'image/jpeg',
                        'data' => base64_encode($thumbnailContent)
                    ]
                ];
            } else {
                // For other files, can't analyze visually
                return false;
            }
            
            $data = [
                'contents' => [
                    [
                        'parts' => $parts
                    ]
                ]
            ];
            
            $response = $this->makeRequest($data);
            
            if ($response && isset($response['candidates'][0]['content']['parts'][0]['text'])) {
                $resultText = strtolower(trim($response['candidates'][0]['content']['parts'][0]['text']));
                // Check if response indicates a match
                return strpos($resultText, 'yes') !== false || 
                       strpos($resultText, 'true') !== false ||
                       strpos($resultText, 'match') !== false ||
                       strpos($resultText, 'contains') !== false;
            }
            
            return false;
        } catch (Exception $e) {
            error_log("File match check error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Make API request to Gemini
     * @param array $data - Request data
     * @return array|null - API response
     */
    private function makeRequest($data)
    {
        $url = $this->apiUrl . '?key=' . $this->apiKey;
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            return json_decode($response, true);
        }

        error_log("Gemini API Error: HTTP $httpCode - $response");
        return null;
    }

    /**
     * Parse AI analysis text into structured format
     * @param string $analysisText - Raw analysis text from AI
     * @return array - Parsed analysis
     */
    private function parseAnalysis($analysisText)
    {
        // Try to extract JSON from the response
        preg_match('/\{.*\}/s', $analysisText, $matches);
        if (!empty($matches)) {
            $json = json_decode($matches[0], true);
            if ($json) {
                // Extract tags from all fields
                $tags = [];
                foreach ($json as $key => $value) {
                    if (is_string($value)) {
                        $words = preg_split('/\s+/', strtolower($value));
                        $tags = array_merge($tags, $words);
                    } elseif (is_array($value)) {
                        $tags = array_merge($tags, $value);
                    }
                }
                $json['tags'] = array_unique(array_filter($tags));
                return $json;
            }
        }

        // Fallback: extract keywords from text
        $tags = [];
        $words = preg_split('/\s+/', strtolower($analysisText));
        $tags = array_unique(array_filter($words, function($word) {
            return strlen($word) > 3;
        }));

        return [
            'description' => $analysisText,
            'tags' => array_slice($tags, 0, 20) // Limit to 20 tags
        ];
    }
}

