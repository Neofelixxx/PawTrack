<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

$role = $_SESSION['role'] ?? null;
if ($role !== 'Admin' && $role !== 'Staff' && $role !== 'Manager') {
    echo json_encode(['error' => 'Unauthorized access privileges']);
    exit;
}

if (!isset($_FILES['cat_photo']) || $_FILES['cat_photo']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['error' => 'No valid image file received']);
    exit;
}

$imagePath = $_FILES['cat_photo']['tmp_name'];
$imageData = base64_encode(file_get_contents($imagePath));

$requestJson = json_encode([
    'requests' => [
        [
            'image' => ['content' => $imageData],
            'features' => [['type' => 'LABEL_DETECTION', 'maxResults' => 20]]
        ]
    ]
]);

$ch = curl_init('https://vision.googleapis.com/v1/images:annotate');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $requestJson);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
curl_close($ch);

if (!$response) {
    echo json_encode(['error' => 'Remote vision engine timed out']);
    exit;
}

$responseData = json_decode($response, true);
$labels = $responseData['responses'][0]['labelAnnotations'] ?? [];

$detectedColor = 'Mixed Color';
$detectedPattern = 'Solid Pattern';
$detectedEyeColor = 'Unknown'; // Default fallback

$tagString = '';
foreach ($labels as $label) {
    $tagString .= strtolower($label['description']) . ' ';
}

// 1. Coat Pattern Mapping
if (strpos($tagString, 'tabby') !== false || strpos($tagString, 'striped') !== false) {
    $detectedPattern = 'Tabby';
} elseif (strpos($tagString, 'calico') !== false || strpos($tagString, 'tortoiseshell') !== false) {
    $detectedPattern = 'Calico / Tortoiseshell';
} elseif (strpos($tagString, 'pointed') !== false || strpos($tagString, 'siamese') !== false) {
    $detectedPattern = 'Pointed';
} elseif (strpos($tagString, 'bicolor') !== false || strpos($tagString, 'tuxedo cat') !== false) {
    $detectedPattern = 'Bi-Color';
}

// 2. Fur Color Mapping
if (strpos($tagString, 'ginger') !== false || strpos($tagString, 'orange cat') !== false) {
    $detectedColor = 'Orange / Ginger';
} elseif (strpos($tagString, 'black cat') !== false) {
    $detectedColor = 'Solid Black';
} elseif (strpos($tagString, 'white cat') !== false) {
    $detectedColor = 'Solid White';
} elseif (strpos($tagString, 'grey') !== false || strpos($tagString, 'gray') !== false) {
    $detectedColor = 'Grey / Blue';
} elseif (strpos($tagString, 'cream') !== false) {
    $detectedColor = 'Cream / Peach';
}

// 3. NEW: Eye Color Mapping
if (strpos($tagString, 'green eyes') !== false || strpos($tagString, 'green eye') !== false) {
    $detectedEyeColor = 'Green';
} elseif (strpos($tagString, 'blue eyes') !== false || strpos($tagString, 'blue eye') !== false) {
    $detectedEyeColor = 'Blue';
} elseif (strpos($tagString, 'yellow eyes') !== false || strpos($tagString, 'amber eyes') !== false) {
    $detectedEyeColor = 'Yellow / Amber';
} elseif (strpos($tagString, 'hazel eyes') !== false) {
    $detectedEyeColor = 'Hazel';
}

echo json_encode([
    'success' => true,
    'color' => $detectedColor,
    'pattern' => $detectedPattern,
    'eye_color' => $detectedEyeColor // Returning eye color to the form
]);