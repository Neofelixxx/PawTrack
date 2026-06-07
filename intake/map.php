<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("../config/db.php");
include("../includes/header.php");

// 1. FETCH ENHANCED INTAKE PROFILE RECORDS
$intakes = pg_query($conn, "
    SELECT
        i.intakeid,
        i.locationdesc,
        i.intakedate,
        ST_X(i.location::geometry) AS lng,
        ST_Y(i.location::geometry) AS lat,
        c.name AS cat_name,
        c.image AS cat_image,
        c.status AS cat_status,
        c.breed AS cat_breed,
        c.agecategory AS cat_age,
        s.name AS shelter_name
    FROM Intake i
    JOIN Cat c ON i.catid = c.catid
    LEFT JOIN Shelter s ON c.shelterid = s.shelterid
    WHERE i.location IS NOT NULL
");

$intake_array = [];
while ($row = pg_fetch_assoc($intakes)) {
    $intake_array[] = $row;
}

// 2. FETCH ENHANCED SHELTER OCCUPANCY PARAMETERS
$shelters = pg_query($conn, "
    SELECT
        s.shelterid, s.name, s.image AS shelter_image, s.capacity, s.district, s.address,
        COUNT(CASE WHEN c.status != 'Adopted' AND c.status != 'Deceased' THEN 1 END) as current_occupancy,
        COUNT(CASE WHEN c.status = 'Available' THEN 1 END) as available_count,
        COUNT(CASE WHEN c.status = 'Under Treatment' THEN 1 END) as treatment_count,
        COUNT(CASE WHEN c.status = 'Quarantined' THEN 1 END) as quarantined_count,
        ST_X(s.location::geometry) AS lng, ST_Y(s.location::geometry) AS lat
    FROM Shelter s
    LEFT JOIN Cat c ON s.shelterid = c.shelterid
    WHERE s.location IS NOT NULL
    GROUP BY s.shelterid, s.name, s.image, s.capacity, s.district, s.address, s.location
");

$shelter_array = [];
while ($s = pg_fetch_assoc($shelters)) {
    $pct = $s['capacity'] > 0 ? round(($s['current_occupancy'] / $s['capacity']) * 100) : 0;
    $s['saturation_pct'] = $pct;
    $shelter_array[] = $s;
}

$breeds_query = pg_query($conn, "SELECT DISTINCT breed FROM Cat WHERE breed IS NOT NULL AND breed != '' ORDER BY breed");
$districts_query = pg_query($conn, "SELECT DISTINCT district FROM Shelter WHERE district IS NOT NULL AND district != '' ORDER BY district");
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 mb-8 space-y-4">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-4 rounded-2xl border border-sky-100/80 shadow-sm">
        <div class="flex items-center gap-3">
            <button onclick="toggleMetricsPanel()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-4 py-2 rounded-xl text-xs transition flex items-center gap-1.5">
                <span>📊</span> <span id="metricsToggleText">Show Overview Metrics</span>
            </button>
            <button onclick="toggleFocusMode()" class="bg-sky-50 hover:bg-sky-100 border border-sky-200 text-sky-700 font-bold px-4 py-2 rounded-xl text-xs transition flex items-center gap-1.5">
                <span>🔍</span> <span id="focusToggleText">Enable Focus Mode</span>
            </button>
        </div>
        <div class="w-full sm:w-auto">
            <select id="analysisType" onchange="switchAnalysisWorkflow()" class="w-full sm:w-64 bg-slate-50 border border-slate-200 p-2.5 rounded-xl text-slate-800 text-xs font-bold focus:outline-none cursor-pointer">
                <option value="rescue_locations">Perspective: Rescue Locations</option>
                <option value="rescue_hotspots">Perspective: Rescue Hotspots (Heatmap)</option>
                <option value="shelter_capacity">Perspective: Shelter Capacity Levels</option>
            </select>
        </div>
    </div>

    <div id="metricsOverviewGrid" class="hidden grid grid-cols-2 lg:grid-cols-4 gap-4 transition-all duration-300">
        <div class="bg-white p-4 rounded-2xl border border-sky-100 shadow-sm">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Rescue Placements Listed</span>
            <div class="text-xl font-black text-slate-800 mt-0.5" id="cardRescuesCount">0</div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-sky-100 shadow-sm">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Active Facility Nodes</span>
            <div class="text-xl font-black text-slate-800 mt-0.5" id="cardSheltersCount">0</div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-sky-100 shadow-sm">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Highest Density Area</span>
            <div class="text-xl font-black text-rose-600 mt-0.5" id="cardAffectedArea">None</div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-sky-100 shadow-sm">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Average Capacity Load</span>
            <div class="text-xl font-black text-sky-700 mt-0.5" id="cardAvgUtilization">0%</div>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-6 items-stretch">
        
        <div id="dashboardSidebar" class="w-full lg:w-1/5 flex flex-col bg-white rounded-3xl border border-sky-100 shadow-sm overflow-hidden shrink-0 transition-all duration-300 min-h-[550px]">
            <div class="flex bg-slate-50 border-b text-center text-xs font-bold text-slate-400">
                <button onclick="switchSidebarTab('controls')" id="tabBtn-controls" class="flex-1 py-3 border-r text-sky-600 bg-white border-b-2 border-b-sky-500">Controls</button>
                <button onclick="switchSidebarTab('insights')" id="tabBtn-insights" class="flex-1 py-3 border-r hover:bg-slate-100/70 transition">Insights</button>
                <button onclick="switchSidebarTab('legend')" id="tabBtn-legend" class="flex-1 py-3 hover:bg-slate-100/70 transition">Legend</button>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-4">
                <div id="tabContent-controls" class="tab-panel space-y-3">
                    <div id="groupStatus" class="border border-slate-100 rounded-xl overflow-hidden shadow-2xs">
                        <button type="button" onclick="toggleAccordion('acc-status')" class="w-full bg-slate-50/60 px-3 py-2.5 text-left text-xs font-bold text-slate-700 flex justify-between items-center border-b">
                            <span>🐱 Feline Status</span>
                            <span id="arrow-acc-status">&darr;</span>
                        </button>
                        <div id="acc-status" class="p-2.5 bg-white space-y-2 hidden">
                            <select id="statusFilter" onchange="runDssEngine()" class="w-full border border-slate-200 bg-slate-50 p-2 rounded-lg text-xs text-slate-700 focus:outline-none">
                                <option value="all">All Statuses</option>
                                <option value="Available">Available</option>
                                <option value="Under Treatment">Under Treatment</option>
                                <option value="Quarantined">Quarantined</option>
                                <option value="Adopted">Adopted</option>
                                <option value="Deceased">Deceased</option>
                            </select>
                        </div>
                    </div>

                    <div id="groupDemographics" class="border border-slate-100 rounded-xl overflow-hidden shadow-2xs">
                        <button type="button" onclick="toggleAccordion('acc-demo')" class="w-full bg-slate-50/60 px-3 py-2.5 text-left text-xs font-bold text-slate-700 flex justify-between items-center border-b">
                            <span>📊 Demographics</span>
                            <span id="arrow-acc-demo">&darr;</span>
                        </button>
                        <div id="acc-demo" class="p-2.5 bg-white space-y-2 hidden">
                            <div>
                                <label class="block text-[9px] text-slate-400 font-bold uppercase mb-1">Age Group</label>
                                <select id="ageFilter" onchange="runDssEngine()" class="w-full border border-slate-200 bg-slate-50 p-1.5 rounded-lg text-xs text-slate-700 focus:outline-none">
                                    <option value="all">All Ages</option>
                                    <option value="Kitten">Kitten</option>
                                    <option value="Juvenile">Juvenile</option>
                                    <option value="Adult">Adult</option>
                                    <option value="Senior">Senior</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[9px] text-slate-400 font-bold uppercase mb-1">Breed Type</label>
                                <select id="breedFilter" onchange="runDssEngine()" class="w-full border border-slate-200 bg-slate-50 p-1.5 rounded-lg text-xs text-slate-700 focus:outline-none">
                                    <option value="all">All Breeds</option>
                                    <?php while($br = pg_fetch_assoc($breeds_query)) { ?>
                                        <option value="<?php echo htmlspecialchars($br['breed']); ?>"><?php echo htmlspecialchars($br['breed']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="groupGeography" class="border border-slate-100 rounded-xl overflow-hidden shadow-2xs">
                        <button type="button" onclick="toggleAccordion('acc-geo')" class="w-full bg-slate-50/60 px-3 py-2.5 text-left text-xs font-bold text-slate-700 flex justify-between items-center border-b">
                            <span>📍 Geography Bounds</span>
                            <span id="arrow-acc-geo">&darr;</span>
                        </button>
                        <div id="acc-geo" class="p-2.5 bg-white space-y-2 hidden">
                            <select id="districtFilter" onchange="runDssEngine()" class="w-full border border-slate-200 bg-slate-50 p-2 rounded-lg text-xs text-slate-700 focus:outline-none">
                                <option value="all">All Districts</option>
                                <?php pg_result_seek($districts_query, 0); while($ds = pg_fetch_assoc($districts_query)) { ?>
                                    <option value="<?php echo htmlspecialchars($ds['district']); ?>"><?php echo htmlspecialchars($ds['district']); ?></option>
                                <?php } ?>
                            </select>
                            <div class="pt-1">
                                <input type="text" id="shelterSearch" oninput="findShelterFocus()" placeholder="Search facility name..." class="w-full border border-slate-200 bg-slate-50 p-2 rounded-lg text-xs text-slate-700 focus:outline-none">
                            </div>
                        </div>
                    </div>

                    <div id="groupTime" class="border border-slate-100 rounded-xl overflow-hidden shadow-2xs hidden">
                        <button type="button" onclick="toggleAccordion('acc-time')" class="w-full bg-slate-50/60 px-3 py-2.5 text-left text-xs font-bold text-slate-700 flex justify-between items-center border-b">
                            <span>📅 Chronological Limit</span>
                            <span id="arrow-acc-time">&darr;</span>
                        </button>
                        <div id="acc-time" class="p-2.5 bg-white hidden">
                            <select id="timeFilter" onchange="runDssEngine()" class="w-full border border-slate-200 bg-slate-50 p-2 rounded-lg text-xs text-slate-700 focus:outline-none">
                                <option value="all">All Records</option>
                                <option value="30">Last 30 Days</option>
                                <option value="90">Last 90 Days</option>
                                <option value="year">This Calendar Year</option>
                            </select>
                        </div>
                    </div>

                    <div id="groupCapacity" class="border border-slate-100 rounded-xl overflow-hidden shadow-2xs hidden">
                        <button type="button" onclick="toggleAccordion('acc-cap')" class="w-full bg-slate-50/60 px-3 py-2.5 text-left text-xs font-bold text-slate-700 flex justify-between items-center border-b">
                            <span>📦 Capacity Ranges</span>
                            <span id="arrow-acc-cap">&darr;</span>
                        </button>
                        <div id="acc-cap" class="p-2.5 bg-white hidden">
                            <select id="capacityFilter" onchange="runDssEngine()" class="w-full border border-slate-200 bg-slate-50 p-2 rounded-lg text-xs text-slate-700 focus:outline-none">
                                <option value="all">All Thresholds</option>
                                <option value="safe">Available Space (&lt;80%)</option>
                                <option value="warning">Nearly Full Housed (80%-99%)</option>
                                <option value="full">Over-Capacity (100%+)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div id="tabContent-insights" class="tab-panel hidden space-y-4">
                    <div id="hotspotStatsPanel" class="bg-slate-50 border p-4 rounded-2xl text-xs space-y-2.5">
                        <h4 class="font-bold text-sky-700 uppercase text-[10px] tracking-wider mb-1">Live Analytics</h4>
                        <div class="flex justify-between border-b pb-1.5">
                            <span class="text-slate-400">Records Found:</span>
                            <span id="statTotalRecords" class="font-black text-slate-800">0</span>
                        </div>
                        <div class="flex justify-between pb-0.5">
                            <span class="text-slate-400">Peak Cluster Point:</span>
                            <span id="statPeakArea" class="font-black text-slate-800">None</span>
                        </div>
                    </div>
                    <div id="heatmapExplanationBox" class="hidden bg-sky-50/70 border border-sky-100 p-4 rounded-2xl text-xs text-slate-600 leading-relaxed space-y-2">
                        <span class="font-bold text-sky-800 block text-[10px] uppercase tracking-wide">💡 Density Levels</span>
                        <p>Denser red heat vectors isolate micro-incident hotspots requiring localized intervention resources.</p>
                    </div>
                </div>

                <div id="tabContent-legend" class="tab-panel hidden text-xs text-slate-500 font-medium space-y-3">
                    <div id="legendRescueItem" class="flex items-center gap-2.5">
                        <span class="inline-block w-3 h-3 bg-blue-600 rounded-full shadow-sm"></span>
                        <span>Rescue Marker Incident Node</span>
                    </div>
                    <div id="legendCapacityBlock" class="space-y-2.5">
                        <div class="flex items-center gap-2.5">
                            <span class="inline-block w-3 h-3 rounded-full bg-[#16a34a]"></span>
                            <span>Healthy Bound (0% - 59%)</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <span class="inline-block w-3 h-3 rounded-full bg-[#ca8a04]"></span>
                            <span>Moderate Loading (60% - 79%)</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <span class="inline-block w-3 h-3 rounded-full bg-[#ea580c]"></span>
                            <span>Nearly Full Housed (80% - 99%)</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <span class="inline-block w-3 h-3 rounded-full bg-[#dc2626]"></span>
                            <span>Over-Occupied (100%+)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-1 bg-white p-2 rounded-3xl border border-sky-100 shadow-sm h-[550px] relative overflow-hidden">
            <div id="map" class="w-full h-full rounded-2xl shadow-inner !z-0"></div>

            <div class="absolute bottom-5 right-5 bg-white/95 backdrop-blur-md border border-slate-200 p-4 rounded-2xl shadow-lg z-[400] text-[11px] text-slate-600 space-y-1.5 w-56 font-medium pointer-events-none transition-all duration-200">
                <div class="text-xs font-black text-slate-900 border-b pb-1 flex items-center gap-1.5">
                    <span class="h-2 w-2 rounded-full bg-sky-500 animate-pulse"></span>
                    <span id="ctxWorkflowText">Rescue Locations</span>
                </div>
                <div>Filter Context: <span id="ctxFiltersSummary" class="text-slate-900 font-bold font-mono">Global Matrix</span></div>
                <div class="flex justify-between border-t pt-1.5 mt-1 font-bold">
                    <span>Rendered Tally: <span id="ctxResultsCount" class="text-sky-700 font-mono">0</span></span>
                    <span id="ctxPeakAreaLabel" class="text-rose-600 truncate max-w-[90px]">None</span>
                </div>
            </div>
        </div>

    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://leaflet.github.io/Leaflet.heat/dist/leaflet-heat.js"></script>

<script>
var johorBahruBounds = L.latLngBounds(L.latLng([1.4000, 103.5500]), L.latLng([1.6200, 103.9500]));

var map = L.map('map', {
    center: [1.4927, 103.7414],
    zoom: 12,
    minZoom: 11,
    maxBounds: johorBahruBounds,
    maxBoundsViscosity: 0.5 // FIXED: Lowered stiffness configuration to protect background image tile snapping
});

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

var intakesData = <?php echo json_encode($intake_array); ?>;
var sheltersData = <?php echo json_encode($shelter_array); ?>;
var basePath = "<?php echo $base_path; ?>";

var rescueMarkerLayer = L.layerGroup().addTo(map);
var shelterMarkerLayer = L.layerGroup().addTo(map);
var heatmapLayer = L.heatLayer([], { radius: 30, blur: 18, maxZoom: 13 });

L.rectangle(johorBahruBounds, { color: "#0284c7", weight: 1, fill: false, dashArray: "4, 8" }).addTo(map);

function switchSidebarTab(tabName) {
    document.querySelectorAll('.tab-panel').forEach(function(p) { p.classList.add('hidden'); });
    document.querySelectorAll('[id^="tabBtn-"]').forEach(function(b) { 
        b.className = "flex-1 py-3 border-r hover:bg-slate-100/70 text-slate-400 font-bold transition"; 
    });
    document.getElementById('tabContent-' + tabName).classList.remove('hidden');
    document.getElementById('tabBtn-' + tabName).className = "flex-1 py-3 border-r text-sky-600 bg-white border-b-2 border-b-sky-500 font-bold";
}

function toggleAccordion(accId) {
    var panel = document.getElementById(accId);
    var arrow = document.getElementById('arrow-' + accId);
    if (panel.classList.contains('hidden')) {
        panel.classList.remove('hidden');
        arrow.innerHTML = "&uarr;";
    } else {
        panel.classList.add('hidden');
        arrow.innerHTML = "&darr;";
    }
}

function toggleMetricsPanel() {
    var grid = document.getElementById('metricsOverviewGrid');
    var txt = document.getElementById('metricsToggleText');
    if (grid.classList.contains('hidden')) {
        grid.classList.remove('hidden');
        txt.innerText = "Hide Overview Metrics";
    } else {
        grid.classList.add('hidden');
        txt.innerText = "Show Overview Metrics";
    }
}

function toggleFocusMode() {
    var sidebar = document.getElementById('dashboardSidebar');
    var txt = document.getElementById('focusToggleText');
    if (sidebar.classList.contains('hidden')) {
        sidebar.classList.remove('hidden');
        txt.innerText = "Enable Focus Mode";
    } else {
        sidebar.classList.add('hidden');
        txt.innerText = "Exit Focus Mode";
    }
    setTimeout(function() { map.invalidateSize(); }, 320);
}

function switchAnalysisWorkflow() {
    var view = document.getElementById('analysisType').value;
    
    document.getElementById('groupStatus').classList.remove('hidden');
    document.getElementById('groupDemographics').classList.remove('hidden');
    document.getElementById('groupTime').classList.add('hidden');
    document.getElementById('groupCapacity').classList.add('hidden');
    document.getElementById('heatmapExplanationBox').classList.add('hidden');
    document.getElementById('legendRescueItem').classList.remove('hidden');
    document.getElementById('legendCapacityBlock').classList.remove('hidden');

    if (view === 'rescue_locations') {
        document.getElementById('legendCapacityBlock').classList.add('hidden');
        document.getElementById('ctxWorkflowText').innerText = "Rescue Locations";
    } else if (view === 'rescue_hotspots') {
        document.getElementById('groupDemographics').classList.add('hidden');
        document.getElementById('groupTime').classList.remove('hidden');
        document.getElementById('heatmapExplanationBox').classList.remove('hidden');
        document.getElementById('legendCapacityBlock').classList.add('hidden');
        document.getElementById('ctxWorkflowText').innerText = "Rescue Hotspots (Heatmap)";
    } else if (view === 'shelter_capacity') {
        document.getElementById('groupStatus').classList.add('hidden');
        document.getElementById('groupDemographics').classList.add('hidden');
        document.getElementById('groupCapacity').classList.remove('hidden');
        document.getElementById('legendRescueItem').classList.add('hidden');
        document.getElementById('ctxWorkflowText').innerText = "Shelter Capacity Levels";
    }
    
    runDssEngine();
}

function runDssEngine() {
    var workflow = document.getElementById('analysisType').value;
    var statusFilter = document.getElementById('statusFilter').value;
    var ageFilter = document.getElementById('ageFilter').value;
    var breedFilter = document.getElementById('breedFilter').value;
    var timeFilter = document.getElementById('timeFilter').value;
    var capacityFilter = document.getElementById('capacityFilter').value;
    var districtFilter = document.getElementById('districtFilter').value;

    rescueMarkerLayer.clearLayers();
    shelterMarkerLayer.clearLayers();
    map.removeLayer(heatmapLayer);

    var filteredRescuesCount = 0;
    var heatPoints = [];
    var districtFrequency = {};

    document.getElementById('ctxFiltersSummary').innerText = districtFilter !== 'all' ? districtFilter : "Global Network";

    if (workflow !== 'shelter_capacity') {
        intakesData.forEach(function(row) {
            if (statusFilter !== 'all' && row.cat_status !== statusFilter) return;
            if (workflow === 'rescue_locations') {
                if (ageFilter !== 'all' && row.cat_age !== ageFilter) return;
                if (breedFilter !== 'all' && row.cat_breed !== breedFilter) return;
            }
            if (districtFilter !== 'all' && row.locationdesc.toLowerCase().indexOf(districtFilter.toLowerCase()) === -1) return;

            if (workflow === 'rescue_hotspots' && timeFilter !== 'all' && row.intakedate) {
                var intakeDate = new Date(row.intakedate);
                var cutoffDate = new Date();
                if (timeFilter === '30') { cutoffDate.setDate(cutoffDate.getDate() - 30); if (intakeDate < cutoffDate) return; }
                else if (timeFilter === '90') { cutoffDate.setDate(cutoffDate.getDate() - 90); if (intakeDate < cutoffDate) return; }
                else if (timeFilter === 'year') { if (intakeDate.getFullYear() !== cutoffDate.getFullYear()) return; }
            }

            filteredRescuesCount++;
            var lat = parseFloat(row.lat);
            var lng = parseFloat(row.lng);

            var matchingDistrict = "Johor Bahru";
            sheltersData.forEach(function(s) { if (row.locationdesc.toLowerCase().includes(s.district.toLowerCase())) { matchingDistrict = s.district; } });
            districtFrequency[matchingDistrict] = (districtFrequency[matchingDistrict] || 0) + 1;

            if (workflow === 'rescue_locations') {
                var marker = L.marker([lat, lng]);
                var imgPath = row.cat_image ? basePath + "assets/images/cats/" + row.cat_image : "";
                
                var content = "<div class='text-xs space-y-1 text-slate-700' style='width:220px; font-family:sans-serif;'>";
                content += "<div class='font-black text-sm text-sky-700 border-b pb-1 mb-1'>🐱 Cat Name: " + escapeHtml(row.cat_name) + "</div>";
                content += "<div><b>Status:</b> <span class='text-slate-900 font-semibold'>" + row.cat_status + "</span></div>";
                content += "<div><b>Age Category:</b> " + row.cat_age + "</div>";
                content += "<div><b>Breed:</b> " + escapeHtml(row.cat_breed) + "</div>";
                content += "<div class='bg-slate-50 border p-1.5 rounded my-1'>📍 <b>Found At:</b> " + escapeHtml(row.locationdesc) + "</div>";
                content += "<div><b>Rescued On:</b> " + row.intakedate + "</div>";
                content += "<div><b>Current Shelter:</b> " + escapeHtml(row.shelter_name || "Processing Hub") + "</div>";
                if (imgPath) content += "<img src='" + imgPath + "' class='w-full h-24 object-cover rounded-xl mt-2 border shadow-sm'>";
                content += "</div>";

                // FIXED: Disabled auto-panning behavior to protect canvas structural offsets clean
                marker.bindPopup(content, { autoPan: false });
                rescueMarkerLayer.addLayer(marker);
            } else if (workflow === 'rescue_hotspots') {
                heatPoints.push([lat, lng, 1.0]);
            }
        });
    }

    if (workflow === 'rescue_hotspots') {
        heatmapLayer.setLatLngs(heatPoints);
        heatmapLayer.addTo(map);
    }

    var totalSheltersCount = 0;
    var totalUtilizationSum = 0;

    sheltersData.forEach(function(s) {
        if (districtFilter !== 'all' && s.district !== districtFilter) return;
        var load = s.saturation_pct;
        
        if (workflow === 'shelter_capacity') {
            if (capacityFilter === 'safe' && load >= 80) return;
            if (capacityFilter === 'warning' && (load < 80 || load > 99)) return;
            if (capacityFilter === 'full' && load < 100) return;
        }

        totalSheltersCount++;
        totalUtilizationSum += load;

        var markerColor = "#16a34a"; 
        if (load >= 60 && load <= 79) markerColor = "#ca8a04"; 
        else if (load >= 80 && load <= 99) markerColor = "#ea580c"; 
        else if (load >= 100) markerColor = "#b91c1c"; 

        if (workflow !== 'rescue_hotspots') {
            var sMarker = L.circleMarker([parseFloat(s.lat), parseFloat(s.lng)], {
                radius: 10, fillColor: markerColor, color: "#ffffff", weight: 2, fillOpacity: 0.9
            });

            var shelterImg = s.shelter_image ? basePath + "assets/images/" + s.shelter_image : "";
            var sContent = "<div class='text-xs space-y-1.5 text-slate-700' style='width:240px; font-family:sans-serif;'>";
            sContent += "<div class='font-black text-sm text-slate-900 border-b pb-1'>🏢 " + escapeHtml(s.name) + "</div>";
            sContent += "<div class='grid grid-cols-2 gap-1.5 font-bold text-center text-[11px] my-2'>";
            sContent += "<div class='p-1 bg-slate-100 rounded text-slate-600'>Capacity<br><span class='text-slate-900 font-mono'>" + s.capacity + "</span></div>";
            sContent += "<div class='p-1 rounded " + (load >= 80 ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700') + "'>Occupancy<br><span class='font-mono'>" + s.current_occupancy + " (" + load + "%)</span></div>";
            sContent += "</div>";
            sContent += "<div class='space-y-0.5 border-t pt-1.5'>";
            sContent += "<div>• Available Cats: <b>" + s.available_count + "</b></div>";
            sContent += "<div>• Under Treatment: <b>" + s.treatment_count + "</b></div>";
            sContent += "<div>• Quarantined Cats: <b>" + s.quarantined_count + "</b></div>";
            sContent += "</div>";
            if (shelterImg) sContent += "<img src='" + shelterImg + "' class='w-full h-24 object-cover rounded-xl mt-2 border shadow-sm'>";
            sContent += "</div>";

            // FIXED: Disabled auto-panning behavior here as well to secure system state
            sMarker.bindPopup(sContent, { autoPan: false });
            shelterMarkerLayer.addLayer(sMarker);
        }
    });

    var peakArea = "None"; var maxCount = 0;
    for (var loc in districtFrequency) { if (districtFrequency[loc] > maxCount) { maxCount = districtFrequency[loc]; peakArea = loc; } }

    document.getElementById('cardRescuesCount').innerText = filteredRescuesCount;
    document.getElementById('cardSheltersCount').innerText = totalSheltersCount;
    document.getElementById('cardAffectedArea').innerText = peakArea + " (" + maxCount + ")";
    document.getElementById('cardAvgUtilization').innerText = totalSheltersCount > 0 ? Math.round(totalUtilizationSum / totalSheltersCount) + "%" : "0%";

    document.getElementById('statTotalRecords').innerText = filteredRescuesCount;
    document.getElementById('statPeakArea').innerText = peakArea + " (" + maxCount + " cases)";
    
    document.getElementById('ctxResultsCount').innerText = workflow === 'shelter_capacity' ? totalSheltersCount : filteredRescuesCount;
    document.getElementById('ctxPeakAreaLabel').innerText = peakArea !== "None" ? "🔥 " + peakArea : "";
}

function findShelterFocus() {
    var searchVal = document.getElementById('shelterSearch').value.toLowerCase();
    if (!searchVal) return;
    sheltersData.forEach(function(s) { if (s.name.toLowerCase().includes(searchVal)) { map.setView([parseFloat(s.lat), parseFloat(s.lng)], 14); } });
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}

switchAnalysisWorkflow();
setTimeout(function() { map.invalidateSize(); }, 250);
</script>

<?php include("../includes/footer.php"); ?>