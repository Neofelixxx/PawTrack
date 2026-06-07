<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("../config/db.php");
include("../includes/header.php");

$role = $_SESSION['role'] ?? null;
if (!$role || ($role != "Admin" && $role != "Manager" && $role != "Staff")) {
    header("Location: /PawTrack/auth/login.php");
    exit;
}

/* ==========================================================================
   1. CORE DSS EARLY WARNING SYSTEM & DATA AGGREGATION
   ========================================================================== */
// Fetch comprehensive shelter operational parameters
$shelter_query = "
    SELECT 
        s.shelterid, s.name, s.capacity,
        COUNT(CASE WHEN c.status != 'Adopted' AND c.status != 'Deceased' THEN 1 END) as active_occupancy,
        COUNT(CASE WHEN c.status = 'Under Treatment' THEN 1 END) as treatment_count,
        COUNT(CASE WHEN c.status = 'Quarantined' THEN 1 END) as quarantined_count,
        COUNT(CASE WHEN c.status = 'Available' THEN 1 END) as available_count,
        COUNT(CASE WHEN c.status = 'Adopted' THEN 1 END) as adopted_count,
        COALESCE(SUM(m.cost), 0) as total_medical_cost,
        COUNT(DISTINCT a.adoptionid) filter (where a.status = 'Pending') as pending_adoptions
    FROM Shelter s
    LEFT JOIN Cat c ON s.shelterid = c.shelterid
    LEFT JOIN Medical_Record m ON c.catid = m.catid
    LEFT JOIN Adoption a ON c.catid = a.catid
    GROUP BY s.shelterid, s.name, s.capacity
    ORDER BY s.name ASC
";
$shelter_res = pg_query($conn, $shelter_query);

$shelters_data = [];
$early_warnings = [];
while ($row = pg_fetch_assoc($shelter_res)) {
    $capacity = (int)$row['capacity'];
    $occupancy = (int)$row['active_occupancy'];
    $treatment = (int)$row['treatment_count'];
    $quarantine = (int)$row['quarantined_count'];
    $pending_adopt = (int)$row['pending_adoptions'];
    $med_cost = (float)$row['total_medical_cost'];
    
    $occupancy_rate = $capacity > 0 ? round(($occupancy / $capacity) * 100, 1) : 0;
    
    // Calculate Total Intake Volume for performance matrix
    $total_cats_logged = $row['available_count'] + $row['adopted_count'] + $treatment + $quarantine;
    $adoption_rate = $total_cats_logged > 0 ? round(($row['adopted_count'] / $total_cats_logged) * 100, 1) : 0;

    /* RISK SCORING WEIGHT MATRIX CALCULATION ENGINE */
    $risk_score = 0;
    if ($occupancy_rate >= 90) { $risk_score += 40; }
    elseif ($occupancy_rate >= 75) { $risk_score += 20; }
    
    if ($med_cost > 2000) { $risk_score += 30; }
    elseif ($med_cost > 1000) { $risk_score += 15; }
    
    if ($quarantine > 5) { $risk_score += 20; }
    if ($pending_adopt > 8) { $risk_score += 10; }

    if ($risk_score >= 60) { $risk_level = "High Risk"; }
    elseif ($risk_score >= 30) { $risk_level = "Medium Risk"; }
    else { $risk_level = "Low Risk"; }

    /* LOAD GROUPING CLASSIFICATION */
    if ($occupancy_rate >= 85) { $load_status = "Critical"; }
    elseif ($occupancy_rate >= 60) { $load_status = "Warning"; }
    else { $load_status = "Stable"; }

    /* EVALUATE AUTOMATED EARLY WARNING INDICATORS */
    if ($occupancy_rate >= 90) {
        $early_warnings[] = "CRITICAL CAPACITY: " . htmlspecialchars($row['name']) . " operating at " . $occupancy_rate . "% bounds.";
    }
    if ($quarantine > 5) {
        $early_warnings[] = "ISOLATION PRESSURE: " . htmlspecialchars($row['name']) . " reports " . $quarantine . " active quarantine profiles.";
    }
    if ($med_cost > 3000) {
        $early_warnings[] = "EXPENDITURE SPIKE: " . htmlspecialchars($row['name']) . " financial load exceeds RM " . number_format($med_cost, 2);
    }
    if ($total_cats_logged > 0 && $adoption_rate < 25) {
        $early_warnings[] = "LOW PLACEMENT VELOCITY: " . htmlspecialchars($row['name']) . " adoption rate currently trailing at " . $adoption_rate . "%.";
    }

    $shelters_data[] = [
        'shelterid' => (int)$row['shelterid'],
        'name' => $row['name'],
        'capacity' => $capacity,
        'occupancy' => $occupancy,
        'occupancy_rate' => $occupancy_rate,
        'adoption_rate' => $adoption_rate,
        'intake_volume' => $total_cats_logged,
        'medical_cost' => $med_cost,
        'risk_level' => $risk_level,
        'load_status' => $load_status,
        'quarantine' => $quarantine,
        'treatment' => $treatment
    ];
}

/* ==========================================================================
   2. 12-MONTH HISTORICAL TREND CALCULATIONS
   ========================================================================== */
$trend_query = "
    SELECT 
        TO_CHAR(m.month, 'Mon YYYY') as label,
        COUNT(DISTINCT i.intakeid) as intake_count,
        COUNT(DISTINCT a.adoptionid) filter (where a.status = 'Approved') as adoption_count,
        COALESCE(SUM(med.cost), 0) as medical_cost
    FROM generate_series(CURRENT_DATE - INTERVAL '11 months', CURRENT_DATE, '1 month') AS m(month)
    LEFT JOIN Intake i ON TO_CHAR(i.intakedate, 'Mon YYYY') = TO_CHAR(m.month, 'Mon YYYY')
    LEFT JOIN Adoption a ON TO_CHAR(a.adoptiondate, 'Mon YYYY') = TO_CHAR(m.month, 'Mon YYYY')
    LEFT JOIN Medical_Record med ON TO_CHAR(med.treatmentdate, 'Mon YYYY') = TO_CHAR(m.month, 'Mon YYYY')
    GROUP BY m.month
    ORDER BY m.month ASC
";
$trend_res = pg_query($conn, $trend_query);

$labels = []; $intake_trends = []; $adoption_trends = []; $cost_trends = [];
while ($t = pg_fetch_assoc($trend_res)) {
    $labels[] = $t['label'];
    $intake_trends[] = (int)$t['intake_count'];
    $adoption_trends[] = (int)$t['adoption_count'];
    $cost_trends[] = (float)$t['medical_cost'];
}

/* ==========================================================================
   3. SYSTEM-WIDE FINANCIAL PRESSURE ANALYSIS
   ========================================================================== */
// Top 5 most expensive single cases
$expensive_cases = pg_query($conn, "
    SELECT m.medrecordid, c.name as cat_name, t.treatname, m.cost, s.name as shelter_name
    FROM Medical_Record m
    JOIN Cat c ON m.catid = c.catid
    JOIN Treatment t ON m.treatid = t.treatid
    JOIN Shelter s ON c.shelterid = s.shelterid
    ORDER BY m.cost DESC LIMIT 5
");

// Most common treatment types
$common_treatments = pg_query($conn, "
    SELECT t.treatname, COUNT(m.medrecordid) as frequency, SUM(m.cost) as total_cost
    FROM Medical_Record m
    JOIN Treatment t ON m.treatid = t.treatid
    GROUP BY t.treatname
    ORDER BY frequency DESC LIMIT 5
");

// Cat lifecycle flow data counts
$flow_rescue = pg_fetch_assoc(pg_query($conn, "SELECT COUNT(*) as total FROM Cat"));
$flow_treatment = pg_fetch_assoc(pg_query($conn, "SELECT COUNT(*) as total FROM Cat WHERE status = 'Under Treatment'"));
$flow_available = pg_fetch_assoc(pg_query($conn, "SELECT COUNT(*) as total FROM Cat WHERE status = 'Available'"));
$flow_adopted = pg_fetch_assoc(pg_query($conn, "SELECT COUNT(*) as total FROM Cat WHERE status = 'Adopted'"));
?>

<style>
@media print {
    nav, #sidebar, #backdrop, .no-print, footer, button, select { display: none !important; }
    body, html { background: #ffffff !important; color: #000000 !important; font-size: 10pt !important; }
    .print-container { width: 100% !important; max-width: 100% !important; padding: 0 !important; margin: 0 !important; }
    .chart-grid-wrapper { grid-template-columns: 1fr !important; }
    .print-page-break { page-break-after: always !important; break-after: always !important; }
    .report-card { page-break-inside: avoid !important; break-inside: avoid !important; border: 1px solid #cbd5e1 !important; border-radius: 12px !important; padding: 16px !important; margin-bottom: 20px !important; }
    canvas { max-width: 100% !important; height: 220px !important; }
    table { width: 100% !important; border-collapse: collapse !important; }
    th { background-color: #f1f5f9 !important; color: #0f172a !important; border-bottom: 2px solid #cbd5e1 !important; }
    td { border-bottom: 1px solid #e2e8f0 !important; }
}
</style>

<div class="print-container max-w-7xl mx-auto px-4 mt-4 space-y-8 mb-16">
    
    <div class="border-b border-sky-100 pb-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">📊 Decision Support System (DSS) Analytical Audit</h2>
            <p class="text-slate-500 text-sm mt-1">Strategic audit evaluating facility capacity limits, operational pressure vectors, and risk score metrics.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3 no-print">
            <button onclick="window.print()" class="bg-slate-800 hover:bg-slate-900 text-white font-bold px-5 py-2.5 rounded-xl shadow-sm text-xs transition">
                Print Executive Audit File
            </button>
        </div>
    </div>

    <?php if (!empty($early_warnings)) { ?>
        <div class="bg-rose-50 border border-rose-200 text-rose-800 p-5 rounded-2xl space-y-2 shadow-sm animate-pulse">
            <div class="flex items-center gap-2 font-black text-xs uppercase tracking-wider text-rose-700">
                <span>⚠️ Proactive System Early Warning Warnings</span>
            </div>
            <ul class="list-disc pl-5 text-xs font-semibold space-y-1">
                <?php foreach ($early_warnings as $warning) { ?>
                    <li><?php echo $warning; ?></li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>

    <div class="bg-white border border-sky-100/80 p-6 rounded-3xl shadow-sm space-y-4">
        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400">Cat Lifecycle Operational Velocity Flow</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
            <div class="bg-slate-50 p-4 rounded-2xl border">
                <span class="text-slate-400 font-bold text-[10px] uppercase block">1. Total Rescued</span>
                <div class="text-xl font-black text-slate-800 mt-1"><?php echo (int)$flow_rescue['total']; ?> Profiles</div>
            </div>
            <div class="bg-amber-50/60 p-4 rounded-2xl border border-amber-100 text-amber-900">
                <span class="text-amber-600 font-bold text-[10px] uppercase block">2. In Treatment</span>
                <div class="text-xl font-black mt-1"><?php echo (int)$flow_treatment['total']; ?> Profiles</div>
            </div>
            <div class="bg-sky-50/60 p-4 rounded-2xl border border-sky-100 text-sky-900">
                <span class="text-sky-600 font-bold text-[10px] uppercase block">3. Available to Rehome</span>
                <div class="text-xl font-black mt-1"><?php echo (int)$flow_available['total']; ?> Profiles</div>
            </div>
            <div class="bg-emerald-50/60 p-4 rounded-2xl border border-emerald-100 text-emerald-900">
                <span class="text-emerald-600 font-bold text-[10px] uppercase block">4. Final Placement</span>
                <div class="text-xl font-black mt-1"><?php echo (int)$flow_adopted['total']; ?> Placed 🎉</div>
            </div>
        </div>
    </div>

    <div class="bg-white border border-sky-100/80 p-6 rounded-3xl shadow-sm space-y-4">
        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400">Shelter Risk Profiles & Load Classifications</h3>
        <div class="overflow-x-auto rounded-xl border">
            <table class="w-full border-collapse text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider text-[10px] border-b">
                        <th class="p-3.5 pl-5">Facility Location Node</th>
                        <th class="p-3.5 text-center">Load Group</th>
                        <th class="p-3.5 text-center">Occupancy Capacity</th>
                        <th class="p-3.5 text-center">Quarantine Status</th>
                        <th class="p-3.5 text-right">Medical Expenses</th>
                        <th class="p-3.5 pr-5 text-right">DSS Action Priority Risk</th>
                    </tr>
                </thead>
                <tbody class="divide-y text-slate-600 font-medium">
                    <?php foreach ($shelters_data as $s) { 
                        $risk_class = $s['risk_level'] === 'High Risk' ? 'bg-red-50 text-red-700 border-red-100' : ($s['risk_level'] === 'Medium Risk' ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-emerald-50 text-emerald-700 border-emerald-100');
                        $load_class = $s['load_status'] === 'Critical' ? 'text-red-600 font-bold' : ($s['load_status'] === 'Warning' ? 'text-amber-600 font-bold' : 'text-emerald-600');
                    ?>
                        <tr class="hover:bg-slate-50/40 cursor-pointer" onclick="window.location.href='../intake/map.php?shelterid=<?php echo $s['shelterid']; ?>'">
                            <td class="p-3.5 pl-5 font-bold text-slate-900">🏢 <?php echo htmlspecialchars($s['name']); ?> <span class="no-print text-[10px] text-sky-600 font-normal underline ml-1">(View Spatial Points)</span></td>
                            <td class="p-3.5 text-center <?php echo $load_class; ?>"><?php echo $s['load_status']; ?></td>
                            <td class="p-3.5 text-center font-mono"><?php echo $s['occupancy']; ?> / <?php echo $s['capacity']; ?> (<?php echo $s['occupancy_rate']; ?>%)</td>
                            <td class="p-3.5 text-center font-mono"><?php echo $s['quarantine']; ?> Isolation / <?php echo $s['treatment']; ?> Clinic</td>
                            <td class="p-3.5 text-right font-mono">RM <?php echo number_format($s['medical_cost'], 2); ?></td>
                            <td class="p-3.5 pr-5 text-right">
                                <span class="px-2.5 py-1 border rounded-lg text-[10px] font-black uppercase <?php echo $risk_class; ?>">
                                    <?php echo $s['risk_level']; ?>
                                </span>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="chart-grid-wrapper grid grid-cols-1 lg:grid-cols-3 gap-6 print-page-break">
        <div class="report-card bg-white p-5 border border-sky-100 rounded-3xl shadow-sm h-72 flex flex-col justify-between">
            <div><h4 class="text-xs font-bold text-slate-900 uppercase tracking-wide">Intake Volumetric Trends</h4></div>
            <div class="flex-1 relative mt-2"><canvas id="intakeTrendChart"></canvas></div>
        </div>
        <div class="report-card bg-white p-5 border border-sky-100 rounded-3xl shadow-sm h-72 flex flex-col justify-between">
            <div><h4 class="text-xs font-bold text-slate-900 uppercase tracking-wide">Adoption Placement Trends</h4></div>
            <div class="flex-1 relative mt-2"><canvas id="adoptionTrendChart"></canvas></div>
        </div>
        <div class="report-card bg-white p-5 border border-sky-100 rounded-3xl shadow-sm h-72 flex flex-col justify-between">
            <div><h4 class="text-xs font-bold text-slate-900 uppercase tracking-wide">Medical Expenditure Fluctuations</h4></div>
            <div class="flex-1 relative mt-2"><canvas id="medicalTrendChart"></canvas></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
        <div class="bg-white border border-sky-100/80 p-6 rounded-3xl shadow-sm space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400">Top 5 Financial Expenditure Incidents</h3>
            <div class="overflow-x-auto rounded-xl border text-xs">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="bg-slate-50 text-slate-700 font-bold uppercase text-[10px] border-b">
                            <th class="p-3 pl-4">Patient Code</th>
                            <th class="p-3">Assigned Procedure</th>
                            <th class="p-3">Shelter Location</th>
                            <th class="p-3 pr-4 text-right">Cost Charged</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-slate-600 font-medium">
                        <?php while ($row = pg_fetch_assoc($expensive_cases)) { ?>
                            <tr class="hover:bg-slate-50/40">
                                <td class="p-3 pl-4 font-bold text-slate-900">🐈 <?php echo htmlspecialchars($row['cat_name']); ?> (#MED-<?php echo $row['medrecordid']; ?>)</td>
                                <td class="p-3"><?php echo htmlspecialchars($row['treatname']); ?></td>
                                <td class="p-3"><?php echo htmlspecialchars($row['shelter_name']); ?></td>
                                <td class="p-3 pr-4 text-right font-mono font-bold text-rose-600">RM <?php echo number_format($row['cost'], 2); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white border border-sky-100/80 p-6 rounded-3xl shadow-sm space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400">Clinical Treatment Classification Distribution</h3>
            <div class="overflow-x-auto rounded-xl border text-xs">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="bg-slate-50 text-slate-700 font-bold uppercase text-[10px] border-b">
                            <th class="p-3 pl-4">Procedure Description</th>
                            <th class="p-3 text-center">Incident Count Frequency</th>
                            <th class="p-3 pr-4 text-right">Cumulative Costs</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-slate-600 font-medium">
                        <?php while ($row = pg_fetch_assoc($common_treatments)) { ?>
                            <tr class="hover:bg-slate-50/40">
                                <td class="p-3 pl-4 font-bold text-slate-900">🩺 <?php echo htmlspecialchars($row['treatname']); ?></td>
                                <td class="p-3 text-center font-mono font-bold text-slate-800"><?php echo $row['frequency']; ?> Cases Filed</td>
                                <td class="p-3 pr-4 text-right font-mono font-bold text-slate-700">RM <?php echo number_format($row['total_cost'], 2); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Data arrays extracted securely from Postgres execution lines
const trendLabels = <?php echo json_encode($labels); ?>;
const intakeData = <?php echo json_encode($intake_trends); ?>;
const adoptionData = <?php echo json_encode($adoption_trends); ?>;
const medicalCostData = <?php echo json_encode($cost_trends); ?>;

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
        y: { beginAtZero: true, ticks: { font: { weight: 'bold', size: 10 } } },
        x: { ticks: { font: { weight: 'bold', size: 10 } } }
    }
};

// 1. INTAKE TREND LINE CHART
new Chart(document.getElementById('intakeTrendChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: trendLabels,
        datasets: [{
            data: intakeData,
            borderColor: 'rgb(14, 165, 233)',
            backgroundColor: 'rgba(14, 165, 233, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.2
        }]
    },
    options: chartOptions
});

// 2. ADOPTION TREND LINE CHART
new Chart(document.getElementById('adoptionTrendChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: trendLabels,
        datasets: [{
            data: adoptionData,
            borderColor: 'rgb(16, 185, 129)',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.2
        }]
    },
    options: chartOptions
});

// 3. MEDICAL SPENDING BAR CHART
new Chart(document.getElementById('medicalTrendChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: trendLabels,
        datasets: [{
            data: medicalCostData,
            backgroundColor: 'rgba(244, 63, 94, 0.8)',
            borderColor: 'rgb(225, 29, 72)',
            borderWidth: 1.5,
            borderRadius: 4
        }]
    },
    options: chartOptions
});
</script>

<?php include("../includes/footer.php"); ?>