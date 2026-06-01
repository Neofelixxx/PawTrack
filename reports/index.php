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

<!-- POWER BI EXECUTIVE LAUNCHER FRAME -->
<div class="mt-8 p-8 bg-white border border-sky-100 rounded-3xl shadow-sm text-center max-w-2xl mx-auto">
    <div class="w-16 h-16 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-between justify-center text-2xl mx-auto mb-4 font-bold shadow-sm">
        📊
    </div>
    <h4 class="text-xl font-bold text-slate-800">Advanced Decision Support Dashboard</h4>
    <p class="text-slate-500 text-sm mt-1 mb-6 leading-relaxed">
        Launch the real-time Power BI engine to manipulate financial metrics, cost distributions, and cross-filter resource allocation data across the shelter network.
    </p>
    
    <!-- LINK TO YOUR REPOSITORY BI FILE -->
    <a href="/PawTrack/reports/Reports.pbix" 
       download
       class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white font-semibold px-6 py-3 rounded-xl text-sm shadow-md hover:shadow-lg transition duration-200">
        <span>🚀</span> Open Power BI Analytics Workspace
    </a>
</div>

<?php include("../includes/footer.php"); ?>