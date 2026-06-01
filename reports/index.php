<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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
    SELECT c.shelterid, t.treatname as category, SUM(m.cost) as total_cost 
    FROM medical_record m
    JOIN cat c ON m.catid = c.catid
    JOIN treatment t ON m.treatid = t.treatid
    GROUP BY c.shelterid, t.treatname
");
$cost_data = [];
while ($row = pg_fetch_assoc($cost_raw)) {
    $cost_data[] = [
        'shelterid' => (int)$row['shelterid'],
        'category' => $row['category'],
        'total' => (float)$row['total_cost']
    ];
}

/* 4. OPERATIONAL KPIS */
$kpi_total_cats = pg_fetch_assoc(pg_query($conn, 'SELECT COUNT(*) AS total FROM "cat"'));
$kpi_adopted = pg_fetch_assoc(pg_query($conn, "SELECT COUNT(*) AS total FROM \"cat\" WHERE \"status\" = 'Adopted'"));
$kpi_pending_apps = pg_fetch_assoc(pg_query($conn, "SELECT COUNT(*) AS total FROM \"adoption\" WHERE \"status\" = 'Pending'"));
$kpi_total_costs = pg_fetch_assoc(pg_query($conn, 'SELECT SUM("cost") AS total FROM "medical_record"'));
$adoption_rate = $kpi_total_cats['total'] > 0 ? round(($kpi_adopted['total'] / $kpi_total_cats['total']) * 100, 1) : 0;

/* 5. SHELTER RESOURCE CAPACITY UTILIZATION */
$shelter_util_query = pg_query($conn, '
    SELECT s."shelterid", s."name", s."capacity", COUNT(c."catid") as current_occupancy
    FROM "shelter" s
    LEFT JOIN "cat" c ON s."shelterid" = c."shelterid" AND c."status" != \'Adopted\' AND c."status" != \'Deceased\'
    GROUP BY s."shelterid", s."name", s."capacity"
    ORDER BY s."name"
');
$shelter_util_data = [];
while ($row = pg_fetch_assoc($shelter_util_query)) {
    $shelter_util_data[] = $row;
}

/* 6. DETAILED MEDICAL TRANSACTION LEDGER */
$medical_ledger_query = pg_query($conn, '
    SELECT m."medrecordid" AS recordid, c."name" as cat_name, t."treatname" as category, m."cost", m."treatmentdate", s."name" as shelter_name, c."shelterid"
    FROM "medical_record" m
    JOIN "cat" c ON m."catid" = c."catid"
    JOIN "treatment" t ON m."treatid" = t."treatid"
    JOIN "shelter" s ON c."shelterid" = s."shelterid"
    ORDER BY m."treatmentdate" DESC
    LIMIT 10
');
$medical_ledger_data = [];
while ($row = pg_fetch_assoc($medical_ledger_query)) {
    $medical_ledger_data[] = $row;
}
?>

<style>
@media print {
    /* 1. Kill web layouts, menus, buttons and header bars */
    nav, #sidebar, #backdrop, .no-print, footer, button, .sticky {
        display: none !important;
    }
    
    /* 2. Force document page to use white backing with deep black text */
    body, html {
        background-color: #ffffff !important;
        background: #ffffff !important;
        color: #000000 !important;
        font-size: 11pt !important;
    }

    /* 3. Drop maximum container bounds to use full width paper margins */
    .print-container {
        width: 100% !important;
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
        box-shadow: none !important;
        border: none !important;
    }

    /* 4. Force a clean A4 split matrix (Charts break onto their own pages gracefully) */
    .chart-grid-wrapper {
        grid-template-columns: 1fr !important; /* Stack vertically instead of crushing side-by-side */
    }
    
    .print-page-break {
        page-break-after: always !important;
        break-after: always !important;
    }

    .report-card {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
        border: 1px solid #94a3b8 !important;
        box-shadow: none !important;
        background: #ffffff !important;
        border-radius: 12px !important;
        padding: 20px !important;
        margin-bottom: 24px !important;
    }

    /* 5. Force text charts labels to sit flat */
    canvas {
        max-width: 100% !important;
        height: 280px !important;
    }

    /* 6. Clean up tables for clear black ink legibility */
    table {
        width: 100% !important;
        border-collapse: collapse !important;
    }
    th {
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
        border-bottom: 2px solid #cbd5e1 !important;
    }
    td {
        border-bottom: 1px solid #e2e8f0 !important;
    }
}
</style>

<div class="print-container max-w-7xl mx-auto px-4 mt-2 space-y-8">
    
    <div class="mb-4 border-b border-sky-200 pb-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">📊 Relational Decision Support System (DSS) Analytics</h2>
            <p class="text-slate-800 font-semibold text-sm mt-1">Comprehensive audit report evaluating population indices, logistical capacity loads, and healthcare asset distributions.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3 no-print">
            <button onclick="window.print()" class="bg-slate-800 hover:bg-slate-900 text-white font-bold px-5 py-2.5 rounded-xl shadow-md transition duration-150 text-sm flex items-center gap-2">
                Print Executive Audit File
            </button>
            <div class="flex items-center gap-2 bg-white px-4 py-2.5 rounded-xl border border-sky-200 shadow-sm">
                <select id="shelterFilter" onchange="updateDashboardFilters()" class="bg-transparent font-bold text-slate-700 text-sm focus:outline-none cursor-pointer">
                    <option value="all">All Regional Shelter Nodes Combined</option>
                    <?php 
                    pg_result_seek($shelter_list, 0);
                    while ($s = pg_fetch_assoc($shelter_list)) { ?>
                        <option value="<?php echo $s['shelterid']; ?>"><?php echo htmlspecialchars($s['name']); ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-sky-100 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Intake Cataloged</span>
            <div class="text-2xl font-black text-slate-900 mt-1"><?php echo number_format($kpi_total_cats['total'] ?? 0); ?> Rescues</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-sky-100 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Adoption Placement Rate</span>
            <div class="text-2xl font-black text-emerald-700 mt-1"><?php echo $adoption_rate; ?>% Success</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-sky-100 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Cumulative Expenditures</span>
            <div class="text-2xl font-black text-slate-900 mt-1">RM <?php echo number_format($kpi_total_costs['total'] ?? 0, 2); ?></div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-sky-100 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Pending Applications</span>
            <div class="text-2xl font-black text-amber-700 mt-1"><?php echo number_format($kpi_pending_apps['total'] ?? 0); ?> Apps</div>
        </div>
    </div>

    <div class="chart-grid-wrapper grid grid-cols-1 lg:grid-cols-2 gap-8 print-page-break">
        <div class="report-card bg-white p-6 rounded-3xl border border-sky-100 shadow-sm flex flex-col justify-between">
            <div>
                <h4 class="text-md font-bold text-slate-900 uppercase tracking-wide">Feline Capacity & Operational Status</h4>
                <p class="text-xs text-slate-600 font-medium mb-4">Dynamic bar distribution charting allocation layers across active workflow nodes.</p>
            </div>
            <div class="h-64 relative">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <div class="report-card bg-white p-6 rounded-3xl border border-sky-100 shadow-sm flex flex-col justify-between">
            <div>
                <h4 class="text-md font-bold text-slate-900 uppercase tracking-wide">Clinical Expenditure Distributions</h4>
                <p class="text-xs text-slate-600 font-medium mb-4">Proportional cost breakdown tracking treatment resource streams natively.</p>
            </div>
            <div class="h-64 relative">
                <canvas id="costChart"></canvas>
            </div>
        </div>
    </div>

    <div class="report-card bg-white p-6 rounded-3xl border border-sky-100 shadow-sm space-y-4 print-page-break">
        <div>
            <h4 class="text-md font-bold text-slate-900 uppercase tracking-wide">Shelter Capacity Constraints & Load Allocation</h4>
            <p class="text-xs text-slate-600 font-medium">Real-time resource evaluation warning management when facility spaces breach thresholds.</p>
        </div>
        <div class="overflow-x-auto rounded-xl border border-slate-100 text-xs">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-900 font-bold uppercase tracking-wider border-b border-slate-100 text-[10px]">
                        <th class="p-3.5 pl-5">Facility Location Name</th>
                        <th class="p-3.5 text-center">Maximum Capacity</th>
                        <th class="p-3.5 text-center">Active Occupancy Load</th>
                        <th class="p-3.5 pr-5 text-right">Saturation Index Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                    <?php foreach ($shelter_util_data as $row) { 
                        $pct = $row['capacity'] > 0 ? round(($row['current_occupancy'] / $row['capacity']) * 100) : 0;
                        $badge_class = $pct >= 85 ? 'bg-red-50 text-red-700 border-red-100' : ($pct >= 50 ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-emerald-50 text-emerald-700 border-emerald-100');
                    ?>
                        <tr class="hover:bg-slate-50/50 shelter-row" data-shelterid="<?php echo $row['shelterid']; ?>">
                            <td class="p-3.5 pl-5 font-bold text-slate-900">🏢 <?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="p-3.5 text-center font-mono"><?php echo (int)$row['capacity']; ?> Units</td>
                            <td class="p-3.5 text-center font-mono font-bold text-slate-800"><?php echo (int)$row['current_occupancy']; ?> Felines</td>
                            <td class="p-3.5 pr-5 text-right">
                                <span class="px-2.5 py-1 rounded-lg border text-[11px] font-bold <?php echo $badge_class; ?>">
                                    <?php echo $pct; ?>% Cap Load
                                </span>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="report-card bg-white p-6 rounded-3xl border border-sky-100 shadow-sm space-y-4">
        <div>
            <h4 class="text-md font-bold text-slate-900 uppercase tracking-wide">Live Healthcare Expenditure Stream Ledger</h4>
            <p class="text-xs text-slate-600 font-medium">Detailed tracking logging the most recent medical itemizations, surgical entries, and processing costs.</p>
        </div>
        <div class="overflow-x-auto rounded-xl border border-slate-100 text-xs">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-900 font-bold uppercase tracking-wider border-b border-slate-100 text-[10px]">
                        <th class="p-3.5 pl-5">Record Link Code</th>
                        <th class="p-3.5">Feline Patient</th>
                        <th class="p-3.5">Treatment Category</th>
                        <th class="p-3.5">Assigned Hub Location</th>
                        <th class="p-3.5 text-center">Filing Timestamp</th>
                        <th class="p-3.5 pr-5 text-right">Cost Charged</th>
                    </tr>
                </thead>
                <tbody id="ledgerTableBody" class="divide-y divide-slate-100 text-slate-700 font-medium">
                    <?php foreach ($medical_ledger_data as $row) { ?>
                        <tr class="hover:bg-slate-50/50 ledger-row" data-shelterid="<?php echo $row['shelterid']; ?>">
                            <td class="p-3.5 pl-5 font-mono text-[11px] text-slate-400">#MED-<?php echo $row['recordid']; ?></td>
                            <td class="p-3.5 font-bold text-slate-900">🐈 <?php echo htmlspecialchars($row['cat_name']); ?></td>
                            <td class="p-3.5">
                                <span class="bg-slate-100 text-slate-700 text-[10px] font-bold px-2 py-0.5 rounded uppercase">
                                    <?php echo htmlspecialchars($row['category']); ?>
                                </span>
                            </td>
                            <td class="p-3.5 text-slate-600">🏢 <?php echo htmlspecialchars($row['shelter_name']); ?></td>
                            <td class="p-3.5 text-center text-slate-500 font-mono text-[11px]"><?php echo date("d M Y", strtotime($row['treatmentdate'])); ?></td>
                            <td class="p-3.5 pr-5 text-right font-mono font-bold text-emerald-700">RM <?php echo number_format($row['cost'], 2); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const rawCatData = <?php echo json_encode($cat_data); ?>;
const rawCostData = <?php echo json_encode($cost_data); ?>;

let statusChart, costChart;

function buildCharts(filteredStatuses, filteredCosts) {
    const ctxStatus = document.getElementById('statusChart').getContext('2d');
    if (statusChart) statusChart.destroy();
    statusChart = new Chart(ctxStatus, {
        type: 'bar',
        data: {
            labels: Object.keys(filteredStatuses),
            datasets: [{
                data: Object.values(filteredStatuses),
                backgroundColor: 'rgba(14, 165, 233, 0.8)',
                borderColor: 'rgb(2, 132, 199)',
                borderWidth: 2,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { 
                y: { beginAtZero: true, ticks: { color: '#0f172a', font: { weight: 'bold' } } },
                x: { ticks: { color: '#0f172a', font: { weight: 'bold' } } }
            }
        }
    });

    const ctxCost = document.getElementById('costChart').getContext('2d');
    if (costChart) costChart.destroy();
    costChart = new Chart(ctxCost, {
        type: 'doughnut',
        data: {
            labels: Object.keys(filteredCosts),
            datasets: [{
                data: Object.values(filteredCosts),
                backgroundColor: ['rgb(225, 29, 72)', 'rgb(217, 119, 6)', 'rgb(5, 150, 105)', 'rgb(79, 70, 229)'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'right', labels: { color: '#0f172a', font: { weight: 'bold' } } } }
        }
    });
}

function updateDashboardFilters() {
    const selectedShelter = document.getElementById('shelterFilter').value;
    
    let statuses = {};
    rawCatData.forEach(item => {
        if (selectedShelter === 'all' || item.shelterid === parseInt(selectedShelter)) {
            statuses[item.status] = (statuses[item.status] || 0) + item.count;
        }
    });

    let costs = {};
    rawCostData.forEach(item => {
        if (selectedShelter === 'all' || item.shelterid === parseInt(selectedShelter)) {
            costs[item.category] = (costs[item.category] || 0) + item.total;
        }
    });

    buildCharts(statuses, costs);

    document.querySelectorAll('.shelter-row').forEach(row => {
        const id = row.getAttribute('data-shelterid');
        if (selectedShelter === 'all' || id === selectedShelter) {
            row.classList.remove('hidden');
        } else {
            row.classList.add('hidden');
        }
    });

    document.querySelectorAll('.ledger-row').forEach(row => {
        const id = row.getAttribute('data-shelterid');
        if (selectedShelter === 'all' || id === selectedShelter) {
            row.classList.remove('hidden');
        } else {
            row.classList.add('hidden');
        }
    });
}

updateDashboardFilters();
</script>

<?php include("../includes/footer.php"); ?>