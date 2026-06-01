<?php

include("../config/db.php");
include("../includes/header.php");

$cats = pg_query($conn, "SELECT * FROM Cat ORDER BY name");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $catid = $_POST['catid'];
    $date = $_POST['date'];
    $desc = $_POST['desc'];
    $lat = $_POST['lat'];
    $lng = $_POST['lng'];

    $point = "SRID=4326;POINT($lng $lat)";

    $query = "
        INSERT INTO Intake
        (CatID, IntakeDate, Location, LocationDesc)
        VALUES
        ($1, $2, ST_GeographyFromText($3), $4)
    ";

    $result = pg_query_params(
        $conn,
        $query,
        [$catid, $date, $point, $desc]
    );

    if ($result) {
        $_SESSION['message'] = "Intake added successfully!";
        header("Location: list.php");
        exit;
    } else {
        echo "Failed to add intake.";
    }
}

?>

<h2 class="text-2xl font-bold mb-6">Add Intake</h2>

<form method="POST" class="bg-white p-6 rounded shadow">

    <!-- CAT -->
    <div class="mb-3">
        <label class="font-semibold">Cat</label>
        <select name="catid" class="w-full border p-2 rounded">
            <?php while ($c = pg_fetch_assoc($cats)) { ?>
                <option value="<?php echo $c['catid']; ?>">
                    <?php echo $c['name']; ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <!-- DATE -->
    <div class="mb-3">
        <label class="font-semibold">Date</label>
        <input type="date" name="date"
               class="w-full border p-2 rounded">
    </div>

    <!-- DESCRIPTION -->
    <div class="mb-3">
        <label class="font-semibold">Location Description</label>
        <input type="text" name="desc"
               class="w-full border p-2 rounded"
               placeholder="e.g. near school / market">
    </div>

    <!-- MAP -->
    <div class="mb-3">
        <label class="font-semibold">Select Location on Map</label>

        <div id="map" style="height: 400px;"
             class="rounded border mt-2"></div>
    </div>

    <!-- LAT LNG -->
    <div class="grid grid-cols-2 gap-3 mb-3">

        <div>
            <label>Latitude</label>
            <input type="text" id="lat" name="lat"
                   class="w-full border p-2 rounded" readonly>
        </div>

        <div>
            <label>Longitude</label>
            <input type="text" id="lng" name="lng"
                   class="w-full border p-2 rounded" readonly>
        </div>

    </div>

    <!-- BUTTONS -->
    <div class="flex gap-3">

        <button type="submit"
                class="bg-green-500 text-white px-4 py-2 rounded">
            Save Intake
        </button>

        <a href="list.php"
           class="bg-gray-400 text-white px-4 py-2 rounded">
            Cancel
        </a>

    </div>

</form>

<!-- LEAFLET -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>

// Default center (Johor Bahru)
var map = L.map('map').setView([1.4927, 103.7414], 12);

// Base map
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(map);

// Marker variable
var marker;

// Click event
map.on('click', function(e) {

    var lat = e.latlng.lat;
    var lng = e.latlng.lng;

    // Fill inputs
    document.getElementById('lat').value = lat;
    document.getElementById('lng').value = lng;

    // Remove old marker
    if (marker) {
        map.removeLayer(marker);
    }

    // Add new marker
    marker = L.marker([lat, lng]).addTo(map);

});

</script>

<?php include("../includes/footer.php"); ?>