<?php
include("../config/db.php");
include("../includes/header.php");

/* 1. FETCH ALL SHELTERS FOR THE FILTER DROPDOWN */
$shelter_list = pg_query($conn, "SELECT shelterid, name FROM Shelter ORDER BY name");

/* 2. FETCH RAW BREAKDOWNS OF ALL CAT STATUSES + SHELTER ID */
$cat_raw = pg_query($conn, "SELECT shelterid, status, COUNT(*) as count FROM Cat GROUP BY shelterid, status");
$cat_data = [];
while ($row = pg_fetch_assoc($cat_raw)) {
    $cat_data[] = [
        'shelterid' => (int)$row['shelterid'],
        'status' => $row['status'],
        'count' => (int)$row['count']
    ];
}

/* 3. FETCH RAW BREAKDOWNS OF ALL MEDICAL COSTS + SHELTER ID */
$cost_raw = pg_query($conn, "
    SELECT c.shelterid, COALESCE(m.category, 'Uncategorized') as category, SUM(m.cost) as total_cost 
    FROM Medical_Record m
    JOIN Cat c ON m.catid = c.catid
    GROUP BY c.shelterid, m.category
");
$cost_data = [];
while ($row = pg_fetch_assoc($cost_raw)) {
    $cost_data[] = [
        'shelterid' => (int)$row['shelterid'],
        'category' => $row['category'],
        'total' => (float)$row['total_cost']
    ];
}
?>

<div class="max-w-7xl mx-auto px-4 mt-2">
    <div class="mb-8 border-b border-sky-100 pb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-bold text-slate-800 tracking-tight">📊 Decision Support Analytics Hub</h2>
            <p class="text-slate-500 text-sm mt-1">Live management overview filtering localized database metrics natively.</p>
        </div>
        
        <div class="flex items-center gap-3 bg-white px-4 py-2 rounded-2xl border border-sky-100/80 shadow-sm max-w-xs w-full">
            <span class="text-lg">🏢</span>
            <select id="shelterFilter" onchange="updateDashboardFilters()" class="w-full bg-transparent font-semibold text-slate-700 text-sm focus:outline-none">
                <option value="all">All Shelter Hubs Combined</option>
                <?php while ($s = pg_fetch_assoc($shelter_list)) { ?>
                    <option value="<?php echo $s['shelterid']; ?>"><?php echo $s['name']; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white p-6 rounded-3xl border border-sky-100/70 shadow-sm flex flex-col justify-between">
            <div>
                <h4 class="text-md font-bold text-slate-800">Feline Capacity & Operational Status</h4>
                <p class="text-xs text-slate-400 mb-4">Real-time allocation of stray cats across active clinical and adoption workflows.</p>
            </div>
            <div class="h-72 relative flex items-center justify-center">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-sky-100/70 shadow-sm flex flex-col justify-between">
            <div>
                <h4 class="text-md font-bold text-slate-800">Clinical Expenditure Distributions (RM)</h4>
                <p class="text-xs text-slate-400 mb-4">Financial metrics tracking total resource costs grouped by treatment category.</p>
            </div>
            <div class="h-72 relative flex items-center justify-center">
                <canvas id="costChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Raw database rows compiled into clean JavaScript Arrays
const rawCatData = <?php echo json_encode($cat_data); ?>;
const rawCostData = <?php echo json_encode($cost_data); ?>;

let statusChart, costChart;

function buildCharts(filteredStatuses, filteredCosts) {
    // 📊 CHART 1: CAT STATUSES
    const ctxStatus = document.getElementById('statusChart').getContext('2d');
    if (statusChart) statusChart.destroy(); // Wipe out old chart instance safely
    statusChart = new Chart(ctxStatus, {
        type: 'bar',
        data: {
            labels: Object.keys(filteredStatuses),
            datasets: [{
                data: Object.values(filteredStatuses),
                backgroundColor: 'rgba(56, 189, 248, 0.6)',
                borderColor: 'rgb(14, 165, 233)',
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    // 🍩 CHART 2: MEDICAL COSTS
    const ctxCost = document.getElementById('costChart').getContext('2d');
    if (costChart) costChart.destroy(); // Wipe out old chart instance safely
    costChart = new Chart(ctxCost, {
        type: 'doughnut',
        data: {
            labels: Object.keys(filteredCosts),
            datasets: [{
                data: Object.values(filteredCosts),
                backgroundColor: ['rgba(244, 63, 94, 0.6)', 'rgba(245, 158, 11, 0.6)', 'rgba(16, 185, 129, 0.6)', 'rgba(99, 102, 241, 0.6)'],
                borderColor: ['rgb(225, 29, 72)', 'rgb(217, 119, 6)', 'rgb(5, 150, 105)', 'rgb(79, 70, 229)'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'right' } }
        }
    });
}

function updateDashboardFilters() {
    const selectedShelter = document.getElementById('shelterFilter').value;
    
    // Process and aggregate status numbers dynamically
    let statuses = {};
    rawCatData.forEach(item => {
        if (selectedShelter === 'all' || item.shelterid === parseInt(selectedShelter)) {
            statuses[item.status] = (statuses[item.status] || 0) + item.count;
        }
    });

    // Process and aggregate financial variables dynamically
    let costs = {};
    rawCostData.forEach(item => {
        if (selectedShelter === 'all' || item.shelterid === parseInt(selectedShelter)) {
            costs[item.category] = (costs[item.category] || 0) + item.total;
        }
    });

    // Redraw the canvas panels with refreshed metrics animation
    buildCharts(statuses, costs);
}

// Initial initialization on window paint load
updateDashboardFilters();
</script>

<?php include("../includes/footer.php"); ?>