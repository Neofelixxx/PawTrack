<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("config/db.php");
include("includes/header.php");

/* Fetch dynamic animal population parameters for the summary section */
$total_cats = pg_fetch_assoc(pg_query($conn, "SELECT COUNT(*) AS total FROM Cat WHERE Status = 'Available'"));
$total_shelters = pg_fetch_assoc(pg_query($conn, "SELECT COUNT(*) AS total FROM Shelter"));
?>

<div class="max-w-7xl mx-auto space-y-16 mt-4 mb-16 px-4 sm:px-6 lg:px-8">
    
    <!-- HERO SECTION WITH AN EMOTIONAL TOUCH -->
    <div class="bg-gradient-to-br from-white to-sky-50/60 border border-sky-100/70 rounded-3xl overflow-hidden shadow-sm grid grid-cols-1 lg:grid-cols-12 items-center transition-all duration-300">
        
        <!-- LEFT: TEXT AND CALL TO ACTIONS -->
        <div class="p-8 md:p-12 lg:col-span-7 space-y-6">
            <div class="space-y-4">
                <span class="inline-block bg-sky-50 text-sky-700 font-semibold text-xs px-4 py-1.5 rounded-full uppercase tracking-wide border border-sky-100/60">
                    Johor Bahru Stray Cat Rescue
                </span>
                <h1 class="text-3xl md:text-5xl font-black text-slate-800 leading-tight tracking-tight">
                    Helping Stray Cats Find <br class="hidden md:inline"><span class="text-sky-600">Safety, Care, and Homes</span>
                </h1>
            </div>
            
            <p class="text-slate-600 text-sm md:text-base leading-relaxed max-w-xl">
                PawTrack connects rescue efforts, shelters, and donors to help stray cats get medical care and find loving families. Track active rescues, support local shelters, and protect cats in need across our local neighborhoods.
            </p>
            
            <div class="pt-2 flex flex-wrap gap-4">
                <a href="/PawTrack/cats/list.php" class="bg-slate-800 hover:bg-slate-900 text-white font-bold px-6 py-3.5 rounded-xl text-sm shadow transition duration-150 flex items-center gap-2">
                    🐾 Meet the Cats
                </a>
                <a href="/PawTrack/donations/add.php" class="bg-sky-500 hover:bg-sky-600 text-white font-bold px-6 py-3.5 rounded-xl text-sm shadow-sm transition duration-150 flex items-center gap-2">
                    💖 Help a Cat Today
                </a>
            </div>
        </div>

        <!-- RIGHT: PICTURE AND CAPTION OVERLAY -->
        <div class="lg:col-span-5 h-64 lg:h-full min-h-[440px] bg-sky-50 relative overflow-hidden flex flex-col justify-end">
            <!-- Background Accent Layer -->
            <div class="absolute inset-0 opacity-10 pointer-events-none bg-[radial-gradient(#0ea5e9_1px,transparent_1px)] [background-size:16px_16px]"></div>
            
            <img src="<?php echo $base_path; ?>assets/images/Homepage Kitty.jpg" 
                 alt="Rescued Feline Welcome" 
                 class="absolute inset-0 w-full h-full object-cover transition duration-700 hover:scale-105"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
         
            <div class="hidden absolute inset-0 flex-col items-center justify-center text-sky-300">
                 <span class="text-6xl">🐈</span>
            </div>

            <!-- Soft Overlay text -->
            <div class="relative z-20 bg-gradient-to-t from-slate-900/90 via-slate-900/40 to-transparent p-6 text-white text-center sm:text-left">
                <p class="text-xs font-bold tracking-widest uppercase text-sky-300 mb-0.5">Rescue • Care • Rehome</p>
                <p class="text-xs font-medium text-slate-200">Every rescued cat starts here — with care and hope.</p>
            </div>
        </div>
    </div>

    <!-- FEATURE MODULES SECTION -->
    <div class="space-y-6">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-sky-50 pb-2">
            Ways to Get Involved
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- CARD 1: MEET THE CATS -->
            <a href="/PawTrack/cats/list.php" class="bg-white p-6 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-md transition duration-200 group flex flex-col justify-between">
                <div class="space-y-3">
                    <span class="text-3xl block">🐱</span>
                    <h4 class="text-lg font-bold text-slate-800 group-hover:text-sky-600 transition">Meet the Cats</h4>
                    <p class="text-slate-600 text-xs leading-relaxed">See cats currently under shelter care who are healthy, vaccinated, and ready to meet prospective adoption families.</p>
                </div>
                <span class="text-xs font-bold text-sky-600 mt-4 block group-hover:underline">View Profiles &rarr;</span>
            </a>

            <!-- CARD 2: SUPPORT A CAT -->
            <a href="/PawTrack/donations/add.php" class="bg-white p-6 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-md transition duration-200 group flex flex-col justify-between">
                <div class="space-y-3">
                    <span class="text-3xl block">💖</span>
                    <h4 class="text-lg font-bold text-slate-800 group-hover:text-sky-600 transition">Support a Cat</h4>
                    <p class="text-slate-600 text-xs leading-relaxed">Help provide nutritious food, critical veterinary treatments, and secure housing by contributing materials or financial funds.</p>
                </div>
                <span class="text-xs font-bold text-sky-600 mt-4 block group-hover:underline">Send Contribution &rarr;</span>
            </a>

            <!-- CARD 3: RESCUE MAP -->
            <a href="/PawTrack/intake/map.php" class="bg-white p-6 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-md transition duration-200 group flex flex-col justify-between">
                <div class="space-y-3">
                    <span class="text-3xl block">📍</span>
                    <h4 class="text-lg font-bold text-slate-800 group-hover:text-sky-600 transition">Rescue Map</h4>
                    <p class="text-slate-600 text-xs leading-relaxed">Explore documented areas where street cats were successfully rescued, helping identify community areas that need support.</p>
                </div>
                <div class="flex items-center justify-between mt-4">
                    <span class="text-xs font-bold text-sky-600 block group-hover:underline">View Rescue Map &rarr;</span>
                </div>
            </a>

        </div>
    </div>

    <!-- COMMUNITY STATUS FOOTER METRICS -->
    <div class="bg-white rounded-3xl p-6 border border-sky-100/60 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-medium text-slate-600">
        <div class="flex items-center gap-3">
            <span class="flex h-2.5 w-2.5 relative">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
            </span>
            <p>
                Active local care network connecting <span class="text-slate-900 font-bold"><?php echo (int)$total_shelters['total']; ?> shelters</span> across regional areas.
            </p>
        </div>
        <div class="bg-sky-50 text-sky-800 px-3 py-1.5 rounded-xl font-bold border border-sky-100/40">
            💙 Together, we are caring for <?php echo (int)$total_cats['total']; ?> rescued cats awaiting homes
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>