<?php
include("../config/db.php");
include("../includes/header.php");

/* BASIC ANALYTICS QUERIES */
$cats = pg_fetch_assoc(pg_query($conn, "SELECT COUNT(*) AS total FROM Cat"));
$shelters = pg_fetch_assoc(pg_query($conn, "SELECT COUNT(*) AS total FROM Shelter"));
$adoptions = pg_fetch_assoc(pg_query($conn, "SELECT COUNT(*) AS total FROM Adoption"));
$donations = pg_fetch_assoc(pg_query($conn, "SELECT COUNT(*) AS total FROM \"Donations\""));
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- CONSOLE HEADER -->
    <div class="mb-8 border-b border-sky-100 pb-5">
        <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">System Analytics & Reports</h2>
        <p class="text-slate-500 text-sm mt-1">Real-time data insights supporting humane shelter operations and resource management across the district[cite: 1].</p>
    </div>

    <!-- METRICS DISPLAY GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        
        <!-- CARD 1: TOTAL CATS -->
        <div class="bg-white p-6 rounded-3xl border border-sky-100/70 shadow-sm hover:shadow-md transition duration-200 flex items-center justify-between group">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Felines</p>
                <h3 class="text-4xl font-extrabold text-slate-800 mt-2 tracking-tight group-hover:text-sky-500 transition duration-200">
                    <?php echo $cats['total']; ?>
                </h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-sky-50 flex items-center justify-center text-2xl shadow-inner text-sky-600">🐈</div>
        </div>

        <!-- CARD 2: SHELTERS -->
        <div class="bg-white p-6 rounded-3xl border border-sky-100/70 shadow-sm hover:shadow-md transition duration-200 flex items-center justify-between group">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Active Shelters</p>
                <h3 class="text-4xl font-extrabold text-slate-800 mt-2 tracking-tight group-hover:text-sky-500 transition duration-200">
                    <?php echo $shelters['total']; ?>
                </h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-sky-50 flex items-center justify-center text-2xl shadow-inner text-sky-600">📍</div>
        </div>

        <!-- CARD 3: ADOPTIONS -->
        <div class="bg-white p-6 rounded-3xl border border-sky-100/70 shadow-sm hover:shadow-md transition duration-200 flex items-center justify-between group">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Successful Adoptions</p>
                <h3 class="text-4xl font-extrabold text-slate-800 mt-2 tracking-tight group-hover:text-emerald-500 transition duration-200">
                    <?php echo $adoptions['total']; ?>
                </h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50/60 flex items-center justify-center text-2xl shadow-inner text-emerald-600">🏠</div>
        </div>

        <!-- CARD 4: DONATIONS -->
        <div class="bg-white p-6 rounded-3xl border border-sky-100/70 shadow-sm hover:shadow-md transition duration-200 flex items-center justify-between group">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Fund Contributions</p>
                <h3 class="text-4xl font-extrabold text-slate-800 mt-2 tracking-tight group-hover:text-amber-500 transition duration-200">
                    <?php echo $donations['total']; ?>
                </h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50/60 flex items-center justify-center text-2xl shadow-inner text-amber-500">💝</div>
        </div>

    </div>

    <!-- DOUBLE CONSOLE WORKSPACE Panels -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- INSIGHTS SUMMARY (Left 2 Columns) -->
        <div class="lg:col-span-2 bg-white p-8 rounded-3xl border border-sky-100/60 shadow-sm">
            <h3 class="text-xl font-bold text-slate-800 tracking-tight mb-4 flex items-center gap-2">
                <span>📊</span> Key Management Insights
            </h3>
            <p class="text-slate-500 text-sm mb-6 leading-relaxed">
                PawTrack unifies multi-shelter metrics to eliminate fragmented tracking inconsistencies, allowing administrators to optimize animal care lifecycles effectively[cite: 1].
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-4 bg-slate-50/50 rounded-2xl border border-sky-50">
                    <span class="text-sky-500 text-lg">🧬</span>
                    <h4 class="font-bold text-slate-700 text-sm mt-2">Medical & Sterilization</h4>
                    <p class="text-xs text-slate-500 mt-1">Tracks custom medical histories, expenses, and vaccination compliance thresholds safely[cite: 1, 2].</p>
                </div>
                <div class="p-4 bg-slate-50/50 rounded-2xl border border-sky-50">
                    <span class="text-sky-500 text-lg">🗺️</span>
                    <h4 class="font-bold text-slate-700 text-sm mt-2">GIS Hotspot Analysis</h4>
                    <p class="text-xs text-slate-500 mt-1">Links geometric intake variables to localize regional stray animal density patterns efficiently[cite: 1, 2].</p>
                </div>
            </div>
        </div>

        <!-- PROJECT SPECIFICATIONS (Right 1 Column) -->
        <div class="bg-gradient-to-br from-sky-500 to-sky-600 p-8 rounded-3xl text-white shadow-lg relative overflow-hidden">
            <div class="absolute inset-0 opacity-10 pointer-events-none bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:12px_12px]"></div>
            
            <div class="relative z-10 flex flex-col justify-between h-full">
                <div>
                    <span class="bg-white/20 backdrop-blur-md text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full">Decision Support</span>
                    <h3 class="text-2xl font-bold tracking-tight mt-4">SDG Alignment Matrix</h3>
                    <p class="text-sky-100/90 text-xs mt-2 leading-relaxed">
                        PawTrack directly aligns with **UN Sustainable Development Goal 11** (Sustainable Cities) and **Goal 15** (Life on Land) through data-driven urban animal welfare planning[cite: 1].
                    </p>
                </div>
                <div class="pt-6 border-t border-white/20 mt-6 flex items-center justify-between text-xs font-medium text-sky-100">
                    <span>Scope: Single District[cite: 1]</span>
                    <span class="bg-white text-sky-600 font-bold px-2.5 py-1 rounded-lg shadow">PostGIS Ready[cite: 2]</span>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include("../includes/footer.php"); ?>