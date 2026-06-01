<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("config/db.php");
include("includes/header.php");

/* FETCH LIVE DATA COUNTS FOR SYSTEM REAL-TIME BALANCING */
$total_cats = pg_fetch_assoc(pg_query($conn, "SELECT COUNT(*) AS total FROM Cat WHERE Status = 'Available'"));
$total_shelters = pg_fetch_assoc(pg_query($conn, "SELECT COUNT(*) AS total FROM Shelter"));
?>

<div class="max-w-7xl mx-auto space-y-12 mt-2">
    
    <div class="bg-white border border-sky-100/80 rounded-3xl overflow-hidden shadow-sm grid grid-cols-1 lg:grid-cols-12 items-center">
        
        <div class="p-8 md:p-12 lg:col-span-7 space-y-6">
            <div class="space-y-3">
                <span class="inline-block bg-sky-50 text-sky-700 font-bold text-xs px-3 py-1.5 rounded-full uppercase tracking-wider border border-sky-100/60">
                    Johor Bahru Stray Feline Network
                </span>
                <h1 class="text-3xl md:text-5xl font-black text-slate-800 tracking-tight leading-tight">
                    Connecting Rescued Cats <br class="hidden md:inline">With Loving Homes
                </h1>
            </div>
            
            <p class="text-slate-500 text-sm md:text-base leading-relaxed max-w-xl">
                PawTrack operates as a spatial Decision Support System designed to unify fragmented animal rescue networks. By integrating advanced PostGIS geometric tracking with daily clinical logs, cage capacity tracking, and transparent financial channels, we provide municipal stakeholders and NGOs with a data-driven system to optimize stray management.
            </p>
            
            <div class="pt-2 flex flex-wrap gap-4">
                <a href="/PawTrack/cats/list.php" class="bg-slate-800 hover:bg-slate-900 text-white font-semibold px-6 py-3.5 rounded-xl text-sm shadow transition duration-150">
                    Browse Available Cats
                </a>
                <a href="/PawTrack/donations/add.php" class="bg-sky-500 hover:bg-sky-600 text-white font-semibold px-6 py-3.5 rounded-xl text-sm shadow-sm transition duration-150">
                    Support Our Wishlist
                </a>
            </div>
        </div>

        <div class="lg:col-span-5 h-64 lg:h-full min-h-[420px] bg-sky-50 relative overflow-hidden flex items-center justify-center">
            <div class="absolute inset-0 opacity-20 pointer-events-none bg-[radial-gradient(#0ea5e9_1px,transparent_1px)] [background-size:16px_16px]"></div>
            
            <img src="/PawTrack/assets/images/Homepage Kitty.jpg" 
                 alt="PawTrack Welcome Feline" 
                 class="w-full h-full object-cover relative z-10 transition duration-500 hover:scale-103"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                 
            <div class="hidden absolute inset-0 flex flex-col items-center justify-center text-sky-300">
                <svg class="w-16 h-16 opacity-40" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 14c-1.66 0-3 1.12-3 2.5 0 2.48 2.5 4.5 3 4.5s3-2.02 3-4.5c0-1.38-1.34-2.5-3-2.5zm-4.5-3c-.83 0-1.5.84-1.5 1.88 0 1.87 1.25 3.37 1.5 3.37s1.5-1.5 1.5-3.37c0-1.04-.67-1.88-1.5-1.88z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400">Integrated Core Architecture Modules</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <a href="/PawTrack/cats/list.php" class="group bg-white p-6 rounded-2xl border border-sky-100/60 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition duration-200 flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 bg-slate-50 border border-slate-100 text-slate-600 rounded-xl flex items-center justify-center mb-4 group-hover:bg-sky-500 group-hover:text-white group-hover:border-sky-500 transition duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <h4 class="text-md font-bold text-slate-800 group-hover:text-sky-600 transition">Search Feline Profiles</h4>
                    <p class="text-slate-400 text-xs mt-1 leading-relaxed">View active records, behavioral parameters, check daily cage management statuses, and verify medical validation readiness maps.</p>
                </div>
            </a>

            <a href="/PawTrack/donations/add.php" class="group bg-white p-6 rounded-2xl border border-sky-100/60 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition duration-200 flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 bg-slate-50 border border-slate-100 text-slate-600 rounded-xl flex items-center justify-center mb-4 group-hover:bg-sky-500 group-hover:text-white group-hover:border-sky-500 transition duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h4 class="text-md font-bold text-slate-800 group-hover:text-sky-600 transition">Community Support Channels</h4>
                    <p class="text-slate-400 text-xs mt-1 leading-relaxed">Access direct DuitNow QR channels, browse localized material wishlist checklists, and review anonymous funding logs securely.</p>
                </div>
            </a>

            <a href="/PawTrack/intake/map.php" class="group bg-white p-6 rounded-2xl border border-sky-100/60 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition duration-200 flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 bg-slate-50 border border-slate-100 text-slate-600 rounded-xl flex items-center justify-center mb-4 group-hover:bg-sky-500 group-hover:text-white group-hover:border-sky-500 transition duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h4 class="text-md font-bold text-slate-800 group-hover:text-sky-600 transition">GIS Intake Mapping</h4>
                    <p class="text-slate-400 text-xs mt-1 leading-relaxed">Analyze geometric rescue coordinates across Johor Bahru with raw PostGIS mapping engines to isolate localized hotspot clusters.</p>
                </div>
            </a>

        </div>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-sky-100/60 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4 text-xs">
        <div class="flex items-center gap-3">
            <span class="flex h-2.5 w-2.5 relative">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
            </span>
            <p class="font-semibold text-slate-500">
                Core Status Active: Monitoring <span class="text-slate-800 font-bold"><?php echo $total_shelters['total']; ?> shelter nodes</span> throughout the regional district.
            </p>
        </div>
        <div class="text-slate-400 font-medium">
            Currently sheltering <span class="text-sky-600 font-bold"><?php echo $total_cats['total']; ?> felines</span> awaiting adoption matching validation.
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>