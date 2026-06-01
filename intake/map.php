<?php

include("../config/db.php");
include("../includes/header.php");

// Get intake points
$intakes = pg_query($conn, "
    SELECT
        i.intakeid,
        i.locationdesc,
        ST_X(i.location::geometry) AS lng,
        ST_Y(i.location::geometry) AS lat,
        c.name AS cat_name
    FROM Intake i
    JOIN Cat c ON i.catid = c.catid
    WHERE i.location IS NOT NULL
");

// Get shelter points
$shelters = pg_query($conn, "
    SELECT
        shelterid,
        name,
        ST_X(location::geometry) AS lng,
        ST_Y(location::geometry) AS lat
    FROM Shelter
    WHERE location IS NOT NULL
");

?>

<h2 class="text-2xl font-bold mb-4">📍 Intake Map</h2>

<div id="map" class="rounded shadow !z-0" style="height: 600px;"></div>

<!-- Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>

// Initialize map (Johor Bahru)
var map = L.map('map').setView([1.4927, 103.7414], 12);

// Base map layer
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

<?php while ($row = pg_fetch_assoc($intakes)) { ?>

    L.marker([<?php echo $row['lat']; ?>, <?php echo $row['lng']; ?>])
        .addTo(map)
        .bindPopup(
            "<b>🐱 <?php echo $row['cat_name']; ?></b><br>" +
            "<?php echo $row['locationdesc']; ?>"
        );

<?php } ?>

<?php while ($s = pg_fetch_assoc($shelters)) { ?>

    L.marker([<?php echo $s['lat']; ?>, <?php echo $s['lng']; ?>], {
        icon: L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
            iconSize: [25, 25]
        })
    })
    .addTo(map)
    .bindPopup("<b>🏠 <?php echo $s['name']; ?></b>");

<?php } ?>

</script>

<?php include("../includes/footer.php"); ?>