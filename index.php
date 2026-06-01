<?php
include("config/db.php");
include("includes/header.php");

/* FETCH LIVE DATA COUNTS FOR HERO METRICS */
$total_cats = pg_fetch_assoc(pg_query($conn, "SELECT COUNT(*) AS total FROM Cat WHERE Status = 'Available'"));
$total_shelters = pg_fetch_assoc(pg_query($conn, "SELECT COUNT(*) AS total FROM Shelter"));
?>

<div class="relative bg-gradient-to-tr from-sky-400 via-sky-300 to-blue-50/40 rounded-3xl p-8 md:p-12 overflow-hidden shadow-sm mb-12">
    <div class="relative z-10 max-w-2xl">
        <span class="inline-block bg-white/60 backdrop-blur-sm text-sky-700 font-bold text-xs px-3 py-1.5 rounded-full uppercase tracking-wider mb-4">
            🐈 Johor Bahru Stray Feline Network
        </span>
        <h1 class="text-4xl md:text-5xl font-extrabold text-slate-800 tracking-tight leading-tight">
            Connecting Rescued Cats With Loving Homes
        </h1>
        <p class="text-slate-600 font-medium text-sm md:text-base mt-3 mb-8 leading-relaxed">
            PawTrack operates as a spatial Decision Support System helping shelters analyze population hotspots, streamline medical workflows, and manage feline placement across the district.
        </p>
        <div class="flex flex-wrap gap-4">
            <a href="/PawTrack/cats/list.php" class="bg-slate-800 hover:bg-slate-900 text-white font-semibold px-6 py-3.5 rounded-xl text-sm shadow transition">
                Browse Available Cats
            </a>
            <a href="/PawTrack/intake/map.php" class="bg-white hover:bg-slate-50 text-slate-700 font-semibold px-6 py-3.5 rounded-xl text-sm shadow-sm transition border border-sky-100">
                View GIS Hotspot Map
            </a>
        </div>
    </div>
    
    <div class="absolute right-0 bottom-0 translate-y-12 translate-x-12 opacity-10 text-[180px] pointer-events-none select-none">🐾</div>
    <div class="absolute right-1/4 top-8 opacity-20 text-4xl pointer-events-none select-none">🐈</div>
</div>

<div class="mb-12">
    <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-6">Operational Control Modules</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <a href="/PawTrack/cats/list.php" class="group bg-white p-6 rounded-2xl border border-sky-100/60 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition duration-200">
            <div class="w-12 h-12 bg-sky-50 text-sky-600 rounded-xl flex items-center justify-center text-xl font-bold mb-4 group-hover:bg-sky-500 group-hover:text-white transition duration-200">
                🐱
            </div>
            <h4 class="text-md font-bold text-slate-800">Search Feline Profiles</h4>
            <p class="text-slate-400 text-xs mt-1 leading-relaxed">View active records, behavioral notes, and adoption readiness profiles for registered rescues.</p>
        </a>

        <a href="/PawTrack/reports/index.php" class="group bg-white p-6 rounded-2xl border border-sky-100/60 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition duration-200">
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-xl font-bold mb-4 group-hover:bg-amber-500 group-hover:text-white transition duration-200">
                📊
            </div>
            <h4 class="text-md font-bold text-slate-800">Decision Analytics Hub</h4>
            <p class="text-slate-400 text-xs mt-1 leading-relaxed">Analyze shelter medical cost metrics, historical data counts, and administrative summaries.</p>
        </a>

        <a href="/PawTrack/intake/map.php" class="group bg-white p-6 rounded-2xl border border-sky-100/60 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition duration-200">
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl font-bold mb-4 group-hover:bg-emerald-500 group-hover:text-white transition duration-200">
                🗺️
            </div>
            <h4 class="text-md font-bold text-slate-800">GIS Intake Mapping</h4>
            <p class="text-slate-400 text-xs mt-1 leading-relaxed">Track rescue locations across Johor Bahru with PostGIS geometric coordinate mapping tools.</p>
        </a>

    </div>
</div>

<div class="bg-white rounded-2xl p-6 border border-sky-100/60 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <span class="flex h-3 w-3 relative">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
        </span>
        <p class="text-xs font-semibold text-slate-500">
            System Live: Monitoring <span class="text-slate-800 font-bold"><?php echo $total_shelters['total']; ?> shelter locations</span> across the district.
        </p>
    </div>
    <div class="text-xs text-slate-400 font-medium">
        Currently hosting <span class="text-sky-600 font-bold"><?php echo $total_cats['total']; ?> felines</span> waiting for adoption.
    </div>
</div>

<?php include("includes/footer.php"); ?>