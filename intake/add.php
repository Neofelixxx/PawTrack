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
    $result = pg_query_params($conn, $query, [$catid, $date, $point, $desc]);
    if ($result) {
        $_SESSION['message'] = "Intake added successfully!";
        header("Location: list.php");
        exit;
    } else {
        echo "<div class='p-4 bg-rose-50 text-rose-600 rounded-xl mb-4'>Failed to add intake.</div>";
    }
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- HEADER CONSOLE -->
    <div class="mb-8 border-b border-sky-100 pb-4">
        <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Record Stray Cat Intake</h2>
        <p class="text-slate-500 text-sm mt-1">Log newly rescued cats with geometric location points to feed the district hotspot analysis engine[cite: 1, 2].</p>
    </div>

    <!-- SPLIT DASHBOARD LAYOUT -->
    <div class="flex flex-col lg:flex-row gap-8 items-stretch">
        
        <!-- LEFT PANEL: REGISTRATION FORM -->
        <div class="w-full lg:w-5/12 bg-white p-6 md:p-8 rounded-3xl border border-sky-100/60 shadow-sm flex flex-col justify-between">
            <form method="POST" class="space-y-5">
                
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Select Rescued Cat</label>
                    <select name="catid" required class="w-full border border-sky-100 bg-slate-50/50 px-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-sm transition">
                        <?php while ($c = pg_fetch_assoc($cats)) { ?>
                            <option value="<?php echo $c['catid']; ?>"><?php echo $c['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Intake Record Date</label>
                    <input type="date" name="date" required class="w-full border border-sky-100 bg-slate-50/50 px-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-sm transition">
                </div>

                <div class="w-full lg:w-7/12 relative min-h-[450px] lg:min-h-auto flex flex-col">
                    <div class="absolute inset-0 bg-white rounded-3xl border border-sky-100/60 shadow-sm overflow-hidden p-2 flex flex-col h-full !z-0">
                        <div id="map" style="height: 460px;" class="rounded-2xl border border-sky-100 shadow-inner !z-0"></div>
                    </div>
                </div>

                <!-- READONLY COORDINATE GEOMETRIES -->
                <div class="p-4 bg-sky-50/50 rounded-2xl border border-sky-100/40 mt-2">
                    <p class="text-xs font-bold text-sky-800 tracking-wide mb-3 flex items-center gap-1.5">
                        <span>📍</span> Selected Geographic Anchors
                    </p>
                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div>
                            <label class="block text-slate-400 font-semibold mb-1">Latitude Coordinate</label>
                            <input type="text" id="lat" name="lat" placeholder="Click map to lock" readonly required
                                   class="w-full bg-white border border-sky-100 px-3 py-2 rounded-xl text-slate-700 font-mono font-bold focus:outline-none text-center shadow-inner text-xs">
                        </div>
                        <div>
                            <label class="block text-slate-400 font-semibold mb-1">Longitude Coordinate</label>
                            <input type="text" id="lng" name="lng" placeholder="Click map to lock" readonly required
                                   class="w-full bg-white border border-sky-100 px-3 py-2 rounded-xl text-slate-700 font-mono font-bold focus:outline-none text-center shadow-inner text-xs">
                        </div>
                    </div>
                </div>

                <!-- CONTROL BUTTONS -->
                <div class="flex gap-3 pt-4 border-t border-slate-50 mt-6">
                    <button type="submit" class="flex-1 bg-sky-500 hover:bg-sky-600 text-white font-semibold py-3 rounded-xl text-sm shadow-md hover:shadow-lg transition duration-200">
                        Save GIS Intake
                    </button>
                    <a href="list.php" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold px-6 py-3 rounded-xl text-sm transition duration-200 flex items-center justify-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- RIGHT PANEL: INTERACTIVE MAP CANVAS -->
        <div class="w-full lg:w-7/12 relative min-h-[450px] lg:min-h-auto flex flex-col">
            <div class="absolute inset-0 bg-white rounded-3xl border border-sky-100/60 shadow-sm overflow-hidden p-2 flex flex-col h-full">
                <div id="map" style="height: 460px;" class="rounded-2xl border border-sky-100 shadow-inner z-10"></div>
            </div>
        </div>

    </div>
</div>

<!-- LEAFLET API INTEGRATIONS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // System Default Viewport Anchored to Johor Bahru District[cite: 2]
    var map = L.map('map', { zoomControl: false }).setView([1.4927, 103.7414], 12);
    
    // Modern Clean Map Canvas Overlay
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '© OpenStreetMap contributors © CARTO'
    }).addTo(map);

    // Reposition zoom controls neatly
    L.control.zoom({ position: 'topright' }).addTo(map);

    var marker;

    // Spatial Pointer Intercept Mapping Event
    map.on('click', function(e) {
        var lat = e.latlng.lat;
        var lng = e.latlng.lng;

        // Sync local forms natively
        document.getElementById('lat').value = lat.toFixed(6);
        document.getElementById('lng').value = lng.toFixed(6);

        // Reset tracking vector markers
        if (marker) {
            map.removeLayer(marker);
        }

        // Drop stylized pinpoint anchor[cite: 2]
        marker = L.marker([lat, lng], {
            icon: L.icon({
                iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
                iconSize: [30, 30],
                iconAnchor: [15, 30]
            })
        }).addTo(map);
        
        map.panTo([lat, lng]);
    });
</script>
<?php include("../includes/footer.php"); ?>