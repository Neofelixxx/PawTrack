<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Set execution timeout to unlimited so the AI vision loop doesn't snap halfway
set_time_limit(0);

include("../config/db.php");

// Only let staff/admin run this seed block
$role = $_SESSION['role'] ?? null;
if ($role !== 'Admin' && $role !== 'Staff') {
    // If you are testing locally without logging in, comment out the line below temporarily:
    die("Unauthorized access privileges.");
}

echo "<h2>🚀 PawTrack Massive Automated Seeding Engine</h2>";
flush();

/* ==========================================================================
   PHASE 1: FIX EXISTING CATS (Like Comel and Bunga)
   ========================================================================== */
echo "<h3>Phase 1: Analyzing existing records...</h3>";

$existing = pg_query($conn, "SELECT catid, image FROM Cat WHERE color IS NULL OR color = 'Mixed Color'");

while ($cat = pg_fetch_assoc($existing)) {
    $catid = $cat['catid'];
    $imageName = $cat['image'];
    $localPath = "../assets/images/cats/" . $imageName;

    if (!empty($imageName) && file_exists($localPath)) {
        echo "Processing existing Cat ID #{$catid} ({$imageName})... ";
        
        // Call our internal detection logic directly
        $traits = autoDetectTraits($localPath);
        
        pg_query_params($conn, 
            "UPDATE Cat SET color = $1, pattern = $2, eye_color = $3 WHERE catid = $4",
            [$traits['color'], $traits['pattern'], $traits['eye_color'], $catid]
        );
        echo "<span style='color:green;'>Updated!</span><br>";
    } else {
        echo "Cat ID #{$catid}: No local image file found to analyze.<br>";
    }
    flush();
}

/* ==========================================================================
   PHASE 2: BULK GENERATE HUNDREDS OF NEW RESCUE RECORDS
   ========================================================================== */
echo "<h3>Phase 2: Generating mass test data for lecturer requirements...</h3>";

// Arrays for generating organic-looking mock data
$mockNames = ['Mochi', 'Oyen', 'Luna', 'Simba', 'Bella', 'Kiko', 'Milo', 'Coco', 'Lily', 'Teh O', 'Kopi', 'Chiko', 'Tomi', 'Lulu', 'Mimi'];
$mockBreeds = ['Domestic Shorthair', 'Domestic Longhair', 'Persian Mix', 'Siamese Mix', 'Kampung Cat'];
$mockAges = ['Kitten', 'Juvenile', 'Adult', 'Senior'];
$mockGenders = ['Male', 'Female'];
$mockRemarks = [
    'Very playful, loves to chase laser pointers and response to clicker training.',
    'Loves to sleep on laps. Learned to give a high-five for treats.',
    'Quiet and independent. Highly observant, can open simple latch doors.',
    'Friendly rescue case. Loves playing with other kittens and running loops.',
    'Shy at first but extremely sweet once comfortable. Responds well to calm voices.'
];

// Gather valid shelters to distribute cats evenly
$shelterResult = pg_query($conn, "SELECT shelterid FROM Shelter");
$shelterIds = [];
while ($s = pg_fetch_assoc($shelterResult)) {
    $shelterIds[] = $s['shelterid'];
}

if (empty($shelterIds)) {
    die("<span style='color:red;'>Error: Please add at least one Shelter to your database before seeding cats.</span>");
}

// Read your image directory to find available pictures to assign
$imageFiles = array_diff(scandir("../assets/images/cats/"), array('.', '..'));
$imagePool = [];
foreach ($imageFiles as $file) {
    if (preg_match('/\.(jpg|jpeg|png|webp)$/i', $file)) {
        $imagePool[] = $file;
    }
}

// Adjust this number to generate exactly how many records your lecturer needs!
$totalCatsToCreate = 120; 
$insertedCount = 0;

for ($i = 0; $i < $totalCatsToCreate; $i++) {
    $name = $mockNames[array_rand($mockNames)] . " " . ($i + 1);
    $breed = $mockBreeds[array_rand($mockBreeds)];
    $age = $mockAges[array_rand($mockAges)];
    $gender = $mockGenders[array_rand($mockGenders)];
    $remarks = $mockRemarks[array_rand($mockRemarks)];
    $shelterid = $shelterIds[array_rand($shelterIds)];
    
    // Pick a random image from your pool, fallback to empty string if no images are inside the folder
    $imageName = !empty($imagePool) ? $imagePool[array_rand($imagePool)] : "";
    
    $color = 'Mixed Color';
    $pattern = 'Solid Pattern';
    $eye_color = 'Unknown';
    
    // If an image is assigned, analyze it to extract genuine characteristics
    if (!empty($imageName)) {
        $localPath = "../assets/images/cats/" . $imageName;
        if (file_exists($localPath)) {
            $traits = autoDetectTraits($localPath);
            $color = $traits['color'];
            $pattern = $traits['pattern'];
            $eye_color = $traits['eye_color'];
        }
    }

    // Dynamic birthdate generator based on chosen age classification
    $daysAgo = ($age === 'Kitten') ? rand(30, 120) : (($age === 'Juvenile') ? rand(150, 360) : rand(730, 3000));
    $birthdate = date('Y-m-d', strtotime("-{$daysAgo} days"));
    $description = "A lovely {$gender} {$breed} rescued safely within the Johor Bahru municipal region.";

    $query = "INSERT INTO Cat (ShelterID, Name, Breed, BirthDate, AgeCategory, Gender, Description, Image, Status, color, pattern, eye_color, special_remarks) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, 'Available', $9, $10, $11, $12)";
    
    $res = pg_query_params($conn, $query, [
        $shelterid, $name, $breed, $birthdate, $age, $gender, $description, $imageName,
        $color, $pattern, $eye_color, $remarks
    ]);

    if ($res) {
        $insertedCount++;
    }
}

echo "<br><span style='color:green; font-weight:bold;'>Success! Generated {$insertedCount} total cat records with automated coat and eye detection analytics!</span><br>";
echo "<a href='list.php'>Click here to return to your clean Available Cats list.</a>";

/* ==========================================================================
   HELPER SIMULATOR OF YOUR AI VISION CORE
   ========================================================================== */
function autoDetectTraits($filePath) {
    // Fallbacks
    $res = ['color' => 'Mixed Color', 'pattern' => 'Solid Pattern', 'eye_color' => 'Unknown'];
    
    // Skip remote requests if file is completely empty to prevent crashes
    if (filesize($filePath) === 0) return $res;

    $imageData = base64_encode(file_get_contents($filePath));
    $requestJson = json_encode([
        'requests' => [[
            'image' => ['content' => $imageData],
            'features' => [['type' => 'LABEL_DETECTION', 'maxResults' => 20]]
        ]]
    ]);

    $ch = curl_init('https://vision.googleapis.com/v1/images:annotate');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $requestJson);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 10 second safety limit per image

    $response = curl_exec($ch);
    curl_close($ch);

    if (!$response) return $res;

    $responseData = json_decode($response, true);
    $labels = $responseData['responses'][0]['labelAnnotations'] ?? [];

    $tagString = '';
    foreach ($labels as $label) {
        $tagString .= strtolower($label['description']) . ' ';
    }

    // Pattern maps
    if (strpos($tagString, 'tabby') !== false || strpos($tagString, 'striped') !== false) $res['pattern'] = 'Tabby';
    elseif (strpos($tagString, 'calico') !== false || strpos($tagString, 'tortoiseshell') !== false) $res['pattern'] = 'Calico';
    elseif (strpos($tagString, 'pointed') !== false || strpos($tagString, 'siamese') !== false) $res['pattern'] = 'Pointed';
    elseif (strpos($tagString, 'bicolor') !== false || strpos($tagString, 'tuxedo cat') !== false) $res['pattern'] = 'Bi-Color';

    // Color maps
    if (strpos($tagString, 'ginger') !== false || strpos($tagString, 'orange cat') !== false) $res['color'] = 'Orange / Ginger';
    elseif (strpos($tagString, 'black cat') !== false) $res['color'] = 'Solid Black';
    elseif (strpos($tagString, 'white cat') !== false) $res['color'] = 'Solid White';
    elseif (strpos($tagString, 'grey') !== false || strpos($tagString, 'gray') !== false) $res['color'] = 'Grey / Blue';
    elseif (strpos($tagString, 'cream') !== false) $res['color'] = 'Cream / Peach';

    // Eye color maps
    if (strpos($tagString, 'green eyes') !== false || strpos($tagString, 'green eye') !== false) $res['eye_color'] = 'Green';
    elseif (strpos($tagString, 'blue eyes') !== false || strpos($tagString, 'blue eye') !== false) $res['eye_color'] = 'Blue';
    elseif (strpos($tagString, 'yellow eyes') !== false || strpos($tagString, 'amber eyes') !== false) $res['eye_color'] = 'Yellow / Amber';
    elseif (strpos($tagString, 'hazel eyes') !== false) $res['eye_color'] = 'Hazel';

    return $res;
}
?>