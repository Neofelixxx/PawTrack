<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("../config/db.php");
include("../includes/header.php");

// 1. GET ENHANCED INTAKE DATA WITH BREED, STATUS, AND AGE FOR ADVANCED FILTERS
$intakes = pg_query($conn, "
    SELECT
        i.intakeid,
        i.locationdesc,
        ST_X(i.location::geometry) AS lng,
        ST_Y(i.location::geometry) AS lat,
        c.name AS cat_name,
        c.image AS cat_image,
        c.status AS cat_status,
        c.breed AS cat_breed,
        c.agecategory AS cat_age
    FROM Intake i
    JOIN Cat c ON i.catid = c.catid
    WHERE i.location IS NOT NULL
");

$intake_array = [];
while ($row = pg_fetch_assoc($intakes)) {
    $intake_array[] = $row;
}

// 2. GET ENHANCED SHELTER CAPACITY DATA
$shelters = pg_query($conn, "
    SELECT
        s.shelterid, s.name, s.image AS shelter_image, s.capacity,
        COUNT(c.catid) as current_occupancy,
        ST_X(s.location::geometry) AS lng, ST_Y(s.location::geometry) AS lat
    FROM Shelter s
    LEFT JOIN Cat c ON s.shelterid = c.shelterid AND c.status != 'Adopted' AND c.status != 'Deceased'
    WHERE s.location IS NOT NULL
    GROUP BY s.shelterid, s.name, s.image, s.capacity, s.location
");

$shelter_array = [];
while ($s = pg_fetch_assoc($shelters)) {
    $pct = $s['capacity'] > 0 ? round(($s['current_occupancy'] / $s['capacity']) * 100) : 0;
    $s['saturation_pct'] = $pct;
    $shelter_array[] = $s;
}

// Fetch unique categories for dynamic drop-downs
$breeds_query = pg_query($conn, "SELECT DISTINCT breed FROM Cat WHERE breed IS NOT NULL AND breed != '' ORDER BY breed");
$statuses_query = pg_query($conn, "SELECT DISTINCT status FROM Cat ORDER BY status");
?>

<!-- ONE-SCREEN VIEWPORT CONTAINER (Prevents page-level scrolling entirely) -->
<div class="w-full flex flex-col lg:flex-row gap-6 h-[calc(100vh-140px)] overflow-hidden">
    
    <!-- LEFT SIDE: SIMPLE USER CONTROL PANEL -->
    <div class="w-full lg:w-1/4 flex flex-col justify-between bg-white p-6 rounded-3xl border border-sky-100 shadow-sm overflow-y-auto">
        <div class="space-y-6">
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">📍 Rescue & Shelter Map</h2>
                <p class="text-slate-500 text-xs mt-1">Use this map to find where stray cats were rescued and check available spaces at our shelters.</p>
            </div>

            <!-- EASY-TO-UNDERSTAND DROPDOWNS -->
            <div class="space-y-4 text-xs font-bold text-slate-400">
                <div>
                    <label class="block uppercase tracking-wider mb-1.5">Map View</label>
                    <select id="mapMode" onchange="applyDssFilters()" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl text-slate-700 text-sm focus:outline-none focus:border-sky-400 cursor-pointer">
                        <option value="markers">Show cats as individual pins</option>
                        <option value="heatmap">Show areas with high cat density</option>
                    </select>
                </div>
                
                <div>
                    <label class="block uppercase tracking-wider mb-1.5">Cat Condition</label>
                    <select id="statusFilter" onchange="applyDssFilters()" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl text-slate-700 text-sm focus:outline-none focus:border-sky-400 cursor-pointer">
                        <option value="all">Show all cats</option>
                        <option value="Available">Healthy & Ready for adoption</option>
                        <option value="Under Treatment">Sick / Injured cats receiving care</option>
                        <option value="Quarantined">New arrivals in isolation</option>
                    </select>
                </div>

                <div>
                    <label class="block uppercase tracking-wider mb-1.5">Cat Breed</label>
                    <select id="breedFilter" onchange="applyDssFilters()" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl text-slate-700 text-sm focus:outline-none focus:border-sky-400 cursor-pointer">
                        <option value="all">Show all breeds</option>
                        <?php while($br = pg_fetch_assoc($breeds_query)) { ?>
                            <option value="<?php echo $br['breed']; ?>"><?php echo htmlspecialchars($br['breed']); ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div>
                    <label class="block uppercase tracking-wider mb-1.5">Shelter Space Available</label>
                    <select id="capacityFilter" onchange="applyDssFilters()" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl text-slate-700 text-sm focus:outline-none focus:border-sky-400 cursor-pointer">
                        <option value="all">Show all shelters</option>
                        <option value="high">Shelters that are nearly full (Over 80%)</option>
                        <option value="safe">Shelters with plenty of open space</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- SIMPLE MAP LEGEND -->
        <div class="mt-6 pt-4 border-t border-slate-100 text-[11px] text-slate-500 space-y-2 font-medium">
            <div class="flex items-center gap-2">
                <span class="inline-block w-3 h-3 bg-blue-600 rounded-full"></span>
                <span>Where a cat was rescued</span>
            </div>
            <div class="flex items-center gap-2">
                <img src="https://cdn-icons-png.flaticon.com/512/684/684908.png" class="w-3.5 h-3.5" alt="Blue Hub">
                <span>Shelter (Has open space)</span>
            </div>
            <div class="flex items-center gap-2">
                <img src="https://cdn-icons-png.flaticon.com/512/564/564619.png" class="w-3.5 h-3.5" alt="Red Warning">
                <span>Shelter is nearly full (Over 80%)</span>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE: THE MAP WINDOW CANVAS -->
    <div class="flex-1 bg-white p-3 rounded-3xl border border-sky-100 shadow-sm h-full relative">
        <div id="map" class="w-full h-full rounded-2xl shadow-inner !z-0"></div>
    </div>

</div>

<!-- LEAFLET AND DENSITY LAYER DISPATCHERS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://leaflet.github.io/Leaflet.heat/dist/leaflet-heat.js"></script>

<script>
// Clamping geographic look boundaries to Johor Bahru coordinates footprint
var johorBahruBounds = L.latLngBounds(L.latLng([1.4000, 103.5500]), L.latLng([1.6200, 103.9500]));

var map = L.map('map', {
    center: [1.4927, 103.7414],
    zoom: 12,
    minZoom: 11,
    maxBounds: johorBahruBounds,
    maxBoundsViscosity: 1.0,
    zoomControl: true
});

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    bounds: johorBahruBounds
}).addTo(map);

// Data matrix variables parsed from backend arrays safely
var intakesData = <?php echo json_encode($intake_array); ?>;
var sheltersData = <?php echo json_encode($shelter_array); ?>;
var basePath = "<?php echo $base_path; ?>";

var catMarkerLayer = L.layerGroup().addTo(map);
var shelterLayer = L.layerGroup().addTo(map);
var heatmapLayer = L.heatLayer([], { radius: 25, blur: 15, maxZoom: 13 });

// Visual bounding framework rectangle overlay indicator
L.rectangle(johorBahruBounds, { color: "#0284c7", weight: 1, fill: false, dashArray: "4, 8" }).addTo(map);

function applyDssFilters() {
    var mapMode = document.getElementById('mapMode').value;
    var statusSel = document.getElementById('statusFilter').value;
    var breedSel = document.getElementById('breedFilter').value;
    var capSel = document.getElementById('capacityFilter').value;

    catMarkerLayer.clearLayers();
    shelterLayer.clearLayers();
    map.removeLayer(heatmapLayer);

    /* ==========================================================================
       POPULATE SHELTERS
       ========================================================================== */
    sheltersData.forEach(function(s) {
        var matchesCap = (capSel === 'all') || 
                         (capSel === 'high' && s.saturation_pct >= 80) || 
                         (capSel === 'safe' && s.saturation_pct < 80);

        if (matchesCap) {
            var pinIcon = (s.saturation_pct >= 80) ? 
                'https://cdn-icons-png.flaticon.com/512/564/564619.png' : 
                'https://cdn-icons-png.flaticon.com/512/684/684908.png';
                
            var marker = L.marker([parseFloat(s.lat), parseFloat(s.lng)], {
                icon: L.icon({ iconUrl: pinIcon, iconSize: [30, 30], iconAnchor: [15, 30], popupAnchor: [0, -30] })
            });

            var shelterImgPath = s.shelter_image ? basePath + "assets/images/" + s.shelter_image : "";
            var popupContent = "<div style='width:200px; font-family:sans-serif;'>";
            popupContent += "<b style='color:#0f172a;'>🏢 " + escapeHtml(s.name) + "</b><br>";
            popupContent += "<span style='font-size:11px; font-weight:bold; color:" + (s.saturation_pct >= 80 ? "#b91c1c" : "#15803d") + ";'>";
            popupContent += "Space Capacity: " + s.saturation_pct + "% (" + s.current_occupancy + "/" + s.capacity + " Cats)</span><br>";
            if (shelterImgPath) popupContent += "<img src='" + shelterImgPath + "' style='width:100%; height:100px; object-fit:cover; border-radius:6px; margin-top:5px;'>";
            popupContent += "</div>";

            marker.bindPopup(popupContent);
            shelterLayer.addLayer(marker);
        }
    });

    /* ==========================================================================
       POPULATE CAT INTAKE LOCATIONS WITH REVISED "FOUND..." PROSE DESCRIPTIONS
       ========================================================================== */
    var activeHeatPoints = [];

    intakesData.forEach(function(row) {
        var matchesStatus = (statusSel === 'all') || (row.cat_status === statusSel);
        var matchesBreed = (breedSel === 'all') || (row.cat_breed === breedSel);

        if (matchesStatus && matchesBreed) {
            var lat = parseFloat(row.lat);
            var lng = parseFloat(row.lng);

            activeHeatPoints.push([lat, lng, 1.0]);

            var marker = L.marker([lat, lng]);
            var catImgPath = row.cat_image ? basePath + "assets/images/cats/" + row.cat_image : "";
            
            // RESOLVED CONTENT PROSE: Prefixed with "Found..." to contextualize coordinate logic cleanly
            var popupContent = "<div style='width:180px; font-family:sans-serif;'>";
            popupContent += "<b style='color:#0369a1;'>🐱 Rescued Feline: " + escapeHtml(row.cat_name) + "</b><br>";
            popupContent += "<span style='font-size:10px; font-weight:bold; background-color:#f1f5f9; padding:2px 6px; border-radius:4px; display:inline-block; margin:4px 0;'>Current Status: " + row.cat_status + "</span><br>";
            popupContent += "<p style='font-size:11px; color:#475569; margin-top:2px;'>📌 <b>Found:</b> " + escapeHtml(row.locationdesc) + "</p>";
            if (catImgPath) popupContent += "<img src='" + catImgPath + "' style='width:100%; height:110px; object-fit:cover; border-radius:8px; margin-top:5px;'>";
            popupContent += "</div>";
            
            marker.bindPopup(popupContent);
            catMarkerLayer.addLayer(marker);
        }
    });

    if (mapMode === 'heatmap') {
        heatmapLayer.setLatLngs(activeHeatPoints);
        heatmapLayer.addTo(map);
    } else {
        catMarkerLayer.addTo(map);
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}

applyDssFilters();
</script>

<?php include("../includes/footer.php"); ?>