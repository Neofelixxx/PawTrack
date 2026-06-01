<?php
include("../config/db.php");
include("../includes/header.php");

/* 1. FETCH SUMMARY COUNTS */
$cats = pg_fetch_assoc(pg_query($conn, "SELECT COUNT(*) AS total FROM Cat"));
$shelters = pg_fetch_assoc(pg_query($conn, "SELECT COUNT(*) AS total FROM Shelter"));
$adoptions = pg_fetch_assoc(pg_query($conn, "SELECT COUNT(*) AS total FROM Adoption"));
$cages = pg_fetch_assoc(pg_query($conn, "SELECT COUNT(*) AS total FROM Cage"));

/* 2. FETCH DATA FOR CHART 1: CATS BY STATUS */
$status_query = pg_query($conn, "SELECT status, COUNT(*) as count FROM Cat GROUP BY status");
$status_labels = [];
$status_counts = [];
while ($row = pg_fetch_assoc($status_query)) {
    $status_labels[] = $row['status'];
    $status_counts[] = (int)$row['count'];
}

/* 3. FETCH DATA FOR CHART 2: MEDICAL COSTS BY CATEGORY */
$cost_query = pg_query($conn, "SELECT COALESCE(category, 'Uncategorized') as category, SUM(cost) as total_cost FROM Medical_Record GROUP BY category");
$cost_labels = [];
$cost_amounts = [];
while ($row = pg_fetch_assoc($cost_query)) {
    $cost_labels[] = $row['category'];
    $cost_amounts[] = (float)$row['total_cost'];
}
?>

<div class="max-w-7xl mx-auto px-4 mt-2">
    <div class="mb-8 border-b border-sky-100 pb-4">
        <h2 class="text-3xl font-bold text-slate-800 tracking-tight">📊 Decision Support Analytics Hub</h2>
        <p class="text-slate-500 text-sm mt-1">Live management overview pulling directly from your localized PostgreSQL database core.</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        <div class="bg-white p-6 rounded-2xl border border-sky-100/70 shadow-sm">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Registered Felines</p>
            <h3 class="text-3xl font-extrabold text-slate-800 mt-2"><?php echo $cats['total']; ?></h3>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-sky-100/70 shadow-sm">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Active Shelter Hubs</p>
            <h3 class="text-3xl font-extrabold text-slate-800 mt-2"><?php echo $shelters['total']; ?></h3>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-sky-100/70 shadow-sm">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Adoption Claims</p>
            <h3 class="text-3xl font-extrabold text-slate-800 mt-2"><?php echo $adoptions['total']; ?></h3>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-sky-100/70 shadow-sm">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Managed Facility Cages</p>
            <h3 class="text-3xl font-extrabold text-slate-800 mt-2"><?php echo $cages['total']; ?></h3>
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
// 📊 CHART 1 INITIALIZATION: CAT STATUS BREAKDOWN
const ctxStatus = document.getElementById('statusChart').getContext('2d');
new Chart(ctxStatus, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($status_labels); ?>,
        datasets: [{
            label: 'Number of Cats',
            data: <?php echo json_encode($status_counts); ?>,
            backgroundColor: 'rgba(56, 189, 248, 0.6)', // Tailwind sky-400 with opacity
            borderColor: 'rgb(14, 165, 233)',          // Tailwind sky-500
            borderWidth: 2,
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1, color: '#94a3b8' }, grid: { color: '#f1f5f9' } },
            x: { ticks: { color: '#64748b' }, grid: { display: false } }
        }
    }
});

// 🍩 CHART 2 INITIALIZATION: COST SUMMARY DISTRIBUTION
const ctxCost = document.getElementById('costChart').getContext('2d');
new Chart(ctxCost, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode($cost_labels); ?>,
        datasets: [{
            data: <?php echo json_encode($cost_amounts); ?>,
            backgroundColor: [
                'rgba(244, 63, 94, 0.6)',  // Rose
                'rgba(245, 158, 11, 0.6)', // Amber
                'rgba(16, 185, 129, 0.6)', // Emerald
                'rgba(99, 102, 241, 0.6)'  // Indigo
            ],
            borderColor: [
                'rgb(225, 29, 72)',
                'rgb(217, 119, 6)',
                'rgb(5, 150, 105)',
                'rgb(79, 70, 229)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'right', labels: { boxWidth: 12, font: { size: 11 }, color: '#475569' } }
        }
    }
});
</script>

<?php include("../includes/footer.php"); ?>