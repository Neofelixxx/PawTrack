<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("../config/db.php");
include("../includes/header.php");

// 1. GET INTAKE POINTS WITH CAT IMAGES INDICES
$intakes = pg_query($conn, "
    SELECT
        i.intakeid,
        i.locationdesc,
        ST_X(i.location::geometry) AS lng,
        ST_Y(i.location::geometry) AS lat,
        c.name AS cat_name,
        c.image AS cat_image
    FROM Intake i
    JOIN Cat c ON i.catid = c.catid
    WHERE i.location IS NOT NULL
");

// 2. GET SHELTER POINTS WITH FACILITY IMAGES
$shelters = pg_query($conn, "
    SELECT
        shelterid,
        name,
        image AS shelter_image,
        ST_X(location::geometry) AS lng,
        ST_Y(location::geometry) AS lat
    FROM Shelter
    WHERE location IS NOT NULL
");
?>

<div class="mb-4 border-b border-sky-200 pb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h2 class="text-3xl font-black text-slate-900 tracking-tight">📍 Geospatial Hotspot Distribution Engine</h2>
        <p class="text-slate-700 font-semibold text-sm mt-1">Interactive visual tracking interface displaying safe shelter nodes alongside wild rescue intake origins.</p>
    </div>
</div>

<div class="bg-white p-4 rounded-3xl border border-sky-100 shadow-sm">
    <div id="map" class="rounded-2xl shadow-inner !z-0" style="height: 650px;"></div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// Initialize map centered around Johor Bahru operations coordinate footprint
var map = L.map('map', {
    center: [1.4927, 103.7414],
    zoom: 12,
    zoomControl: true
});

// Load standard OpenStreetMap texture layer template
var baseMap = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

// Create separate feature layers to hold our interactive markers
var catLayer = L.layerGroup();
var shelterLayer = L.layerGroup();

// Initialize dynamic system path configurations passed down from PHP
var basePath = "<?php echo $base_path; ?>";

/* ==========================================================================
   POPULATE INTAKE RESCUE MARKERS LAYER
   ========================================================================== */
<?php while ($row = pg_fetch_assoc($intakes)) { 
    // Construct absolute dynamic target path for uploaded cat profile pictures
    $catImgPath = !empty($row['cat_image']) ? $base_path . "assets/images/cats/" . $row['cat_image'] : "";
?>
    (function() {
        var marker = L.marker([<?php echo $row['lat']; ?>, <?php echo $row['lng']; ?>]);
        
        var popupContent = "<div style='font-family: sans-serif; width: 180px;'>";
        popupContent += "<b style='font-size: 14px; color: #0369a1;'>🐱 <?php echo htmlspecialchars($row['cat_name']); ?></b><br>";
        popupContent += "<p style='margin: 4px 0 8px 0; font-size: 11px; color: #475569;'>📍 <?php echo htmlspecialchars($row['locationdesc']); ?></p>";
        
        <?php if (!empty($catImgPath)) { ?>
            popupContent += "<img src='<?php echo $catImgPath; ?>' style='width: 100%; height: 110px; object-cover; border-radius: 8px; border: 1px solid #e2e8f0;' alt='Cat Patient Photo'>";
        <?php } else { ?>
            popupContent += "<div style='width: 100%; height: 60px; background: #f0f9ff; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #0284c7; font-weight: bold;'>NO PROFILE UPLOADED</div>";
        <?php } ?>
        popupContent += "</div>";

        marker.bindPopup(popupContent);
        catLayer.addLayer(marker);
    })();
<?php } ?>

/* ==========================================================================
   POPULATE SHELTER NODE LAYERS WITH CUSTOM PIN GRAPHICS
   ========================================================================== */
<?php while ($s = pg_fetch_assoc($shelters)) { 
    // Facilities images might sit directly under root assets/images folder mapping 
    $shelterImgPath = !empty($s['shelter_image']) ? $base_path . "assets/images/" . $s['shelter_image'] : "";
?>
    (function() {
        var marker = L.marker([<?php echo $s['lat']; ?>, <?php echo $s['lng']; ?>], {
            icon: L.icon({
                iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
                iconSize: [30, 30],
                iconAnchor: [15, 30],
                popupAnchor: [0, -30]
            })
        });

        var popupContent = "<div style='font-family: sans-serif; width: 200px;'>";
        popupContent += "<b style='font-size: 14px; color: #0f172a;'>🏢 <?php echo htmlspecialchars($s['name']); ?></b><br>";
        popupContent += "<p style='margin: 4px 0 8px 0; font-size: 11px; color: #64748b;'>Operational Distribution Hub Node</p>";
        
        <?php if (!empty($shelterImgPath)) { ?>
            popupContent += "<img src='<?php echo $shelterImgPath; ?>' style='width: 100%; height: 110px; object-cover; border-radius: 8px; border: 1px solid #e2e8f0;' alt='Shelter Facility Photo'>";
        <?php } ?>
        popupContent += "</div>";

        marker.bindPopup(popupContent);
        shelterLayer.addLayer(marker);
    })();
<?php } ?>

// Add all data layers onto the active map console viewport by default
catLayer.addTo(map);
shelterLayer.addTo(map);

/* ==========================================================================
   NATIVE INTERACTIVE FILTER TOGGLE CONTROL LAYER
   ========================================================================== */
var overlayMaps = {
    "<span style='font-weight: bold; font-size: 12px; color: #0369a1;'>🐱 Feline Rescue Locations</span>": catLayer,
    "<span style='font-weight: bold; font-size: 12px; color: #0f172a;'>🏢 Active Shelter Nodes</span>": shelterLayer
};

// Inject filter checkbox interface container onto top-right area window canvas
L.control.layers(null, overlayMaps, { collapsed: false, position: 'topright' }).addTo(map);
</script>

<?php include("../includes/footer.php"); ?>