<?php
include("../config/db.php");
include("../includes/header.php");

if (!isset($_SESSION['user'])) {
    header("Location: /PawTrack/auth/login.php");
    exit;
}
$role = $_SESSION['role'];
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- DASHBOARD WELCOME HEADER -->
    <div class="mb-8 border-b border-sky-100 pb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">Management Console</h2>
            <p class="text-slate-500 text-sm mt-1">
                Logged in as: <span class="bg-sky-50 text-sky-700 font-bold px-2.5 py-1 rounded-md border border-sky-100/60 ml-1 text-xs uppercase tracking-wider"><?php echo $role; ?></span>
            </p>
        </div>
        <div class="text-xs font-semibold text-slate-400 bg-white px-4 py-2 rounded-xl border border-sky-100/40 shadow-sm shrink-0 self-start sm:self-center">
            📍 District Area: Johor Bahru[cite: 2]
        </div>
    </div>

    <!-- ==================== ADMIN PANEL WORKSPACE ==================== -->
    <?php if ($role == "Admin") { ?>
        <div class="space-y-6">
            <div class="p-4 bg-sky-50/50 rounded-2xl border border-sky-100/40 text-sm text-sky-800">
                🛠️ <b>System Administrator Privileges Active:</b> You have full authorization over data parameters, system reports, and PostGIS location configurations[cite: 2, 4].
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- MENU CARD: MANAGE CATS -->
                <a href="../cats/list.php" class="bg-white p-6 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between group">
                    <div class="w-12 h-12 rounded-2xl bg-sky-50 flex items-center justify-center text-2xl mb-4 text-sky-600 shadow-inner">🐈</div>
                    <div>
                        <h4 class="font-bold text-slate-800 group-hover:text-sky-600 transition duration-150">Manage Felines</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Update status settings, medical registries, and assignment listings[cite: 2, 4].</p>
                    </div>
                </a>

                <!-- MENU CARD: MANAGE SHELTERS -->
                <a href="../shelters/list.php" class="bg-white p-6 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between group">
                    <div class="w-12 h-12 rounded-2xl bg-sky-50 flex items-center justify-center text-2xl mb-4 text-sky-600 shadow-inner">📍</div>
                    <div>
                        <h4 class="font-bold text-slate-800 group-hover:text-sky-600 transition duration-150">Manage Hubs</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Monitor unit accommodations and district coordinate specifications[cite: 2, 4].</p>
                    </div>
                </a>

                <!-- MENU CARD: ADOPTIONS -->
                <a href="../adoption/list.php" class="bg-white p-6 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between group">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50/60 flex items-center justify-center text-2xl mb-4 text-emerald-600 shadow-inner">🏠</div>
                    <div>
                        <h4 class="font-bold text-slate-800 group-hover:text-emerald-500 transition duration-150">Adoption Matrix</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Review incoming adopter background apps and process validation updates[cite: 2, 4].</p>
                    </div>
                </a>

                <!-- MENU CARD: GIS SPATIAL MAP -->
                <a href="../intake/map.php" class="bg-white p-6 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between group">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 flex items-center justify-center text-2xl mb-4 text-purple-600 shadow-inner">🗺️</div>
                    <div>
                        <h4 class="font-bold text-slate-800 group-hover:text-purple-600 transition duration-150">GIS Intake Map</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Analyze geometric location metrics and localized hotspot clusters[cite: 2, 4].</p>
                    </div>
                </a>
            </div>
        </div>

    <!-- ==================== STAFF PANEL WORKSPACE ==================== -->
    <?php } elseif ($role == "Staff") { ?>
        <div class="space-y-6">
            <div class="p-4 bg-sky-50/50 rounded-2xl border border-sky-100/40 text-sm text-sky-800">
                📋 <b>Operational Staff Console Active:</b> Use these operational modules to update daily intake data, medical records, and adoption requests[cite: 2, 4].
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <a href="../cats/list.php" class="bg-white p-6 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between group">
                    <div class="w-12 h-12 rounded-2xl bg-sky-50 flex items-center justify-center text-2xl mb-4 text-sky-600 shadow-inner">🔍</div>
                    <div>
                        <h4 class="font-bold text-slate-800 group-hover:text-sky-600 transition duration-150">View Felines</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Browse the real-time active registry[cite: 2].</p>
                    </div>
                </a>

                <a href="../cats/add.php" class="bg-white p-6 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between group">
                    <div class="w-12 h-12 rounded-2xl bg-sky-50 flex items-center justify-center text-2xl mb-4 text-sky-600 shadow-inner">＋</div>
                    <div>
                        <h4 class="font-bold text-slate-800 group-hover:text-sky-600 transition duration-150">Add Feline</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Register newly rescued cats into the system[cite: 2, 4].</p>
                    </div>
                </a>

                <a href="../adoption/list.php" class="bg-white p-6 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between group">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50/60 flex items-center justify-center text-2xl mb-4 text-emerald-600 shadow-inner">⚖️</div>
                    <div>
                        <h4 class="font-bold text-slate-800 group-hover:text-emerald-500 transition duration-150">Process Applications</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Manage public adoption workflow pipelines[cite: 2, 4].</p>
                    </div>
                </a>

                <a href="../intake/map.php" class="bg-white p-6 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between group">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 flex items-center justify-center text-2xl mb-4 text-purple-600 shadow-inner">🗺️</div>
                    <div>
                        <h4 class="font-bold text-slate-800 group-hover:text-purple-600 transition duration-150">View Spatial Map</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">View localized incoming coordinates[cite: 2, 4].</p>
                    </div>
                </a>
            </div>
        </div>

    <!-- ==================== ADOPTER PANEL WORKSPACE ==================== -->
    <?php } elseif ($role == "Adopter") { ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="../cats/list.php" class="bg-white p-6 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between group">
                <div class="w-12 h-12 rounded-2xl bg-sky-50 flex items-center justify-center text-2xl mb-4 text-sky-600 shadow-inner">🐈</div>
                <div>
                    <h4 class="font-bold text-slate-800 group-hover:text-sky-600 transition duration-150">Browse Cats</h4>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Search profiles and request adoption approvals[cite: 2, 4].</p>
                </div>
            </a>

            <a href="../adoption/list.php" class="bg-white p-6 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between group">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50/60 flex items-center justify-center text-2xl mb-4 text-emerald-600 shadow-inner">📋</div>
                <div>
                    <h4 class="font-bold text-slate-800 group-hover:text-emerald-500 transition duration-150">My Status</h4>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Monitor your application updates in real time[cite: 2, 4].</p>
                </div>
            </a>

            <a href="../intake/map.php" class="bg-white p-6 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between group">
                <div class="w-12 h-12 rounded-2xl bg-purple-50 flex items-center justify-center text-2xl mb-4 text-purple-600 shadow-inner">📍</div>
                <div>
                    <h4 class="font-bold text-slate-800 group-hover:text-purple-600 transition duration-150">Hub Map</h4>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Find local shelter buildings throughout the district[cite: 1, 2].</p>
                </div>
            </a>
        </div>
    <?php } ?>

</div>

<?php include("../includes/footer.php"); ?>