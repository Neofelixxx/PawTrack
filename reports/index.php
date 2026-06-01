<?php
include("../config/db.php");
include("../includes/header.php");

/* BASIC ANALYTICS QUERIES — MATCHING YOUR EXACT UPGRADED SCHEMA */
$cats = pg_fetch_assoc(pg_query($conn, "SELECT COUNT(*) AS total FROM Cat"));
$shelters = pg_fetch_assoc(pg_query($conn, "SELECT COUNT(*) AS total FROM Shelter"));
$adoptions = pg_fetch_assoc(pg_query($conn, "SELECT COUNT(*) AS total FROM Adoption"));

// CHANGED: Swapped out the non-existent Donations table for your active Cage inventory metrics!
$cages = pg_fetch_assoc(pg_query($conn, "SELECT COUNT(*) AS total FROM Cage"));
?>

<!-- QUICK INSIGHT CARDS DISPLAY -->
<div class="max-w-7xl mx-auto px-4 mt-6">
    <h2 class="text-2xl font-bold text-slate-800 mb-6">📊 System Overview Summary</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- CATS CARD -->
        <div class="bg-white p-6 rounded-2xl border border-sky-100 shadow-sm">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Registered Felines</p>
            <h3 class="text-3xl font-extrabold text-slate-800 mt-2"><?php echo $cats['total']; ?></h3>
        </div>

        <!-- SHELTERS CARD -->
        <div class="bg-white p-6 rounded-2xl border border-sky-100 shadow-sm">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Active Shelter Hubs</p>
            <h3 class="text-3xl font-extrabold text-slate-800 mt-2"><?php echo $shelters['total']; ?></h3>
        </div>

        <!-- ADOPTIONS CARD -->
        <div class="bg-white p-6 rounded-2xl border border-sky-100 shadow-sm">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Adoption Claims</p>
            <h3 class="text-3xl font-extrabold text-slate-800 mt-2"><?php echo $adoptions['total']; ?></h3>
        </div>

        <!-- CAGES CARD -->
        <div class="bg-white p-6 rounded-2xl border border-sky-100 shadow-sm">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Managed Facility Cages</p>
            <h3 class="text-3xl font-extrabold text-slate-800 mt-2"><?php echo $cages['total']; ?></h3>
        </div>
    </div>

    <!-- POWER BI REDIRECT BENCHMARK NOTE -->
    <div class="mt-8 p-6 bg-sky-50/50 border border-sky-100 rounded-2xl text-center">
        <p class="text-sm text-slate-600 font-medium">
            💡 Detailed cost distributions and interactive data metrics are managed via your <strong>Power BI Executive Dashboard</strong> workbook.
        </p>
    </div>
</div>

<?php include("../includes/footer.php"); ?>