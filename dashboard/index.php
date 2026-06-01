<?php
// Start session if not explicitly invoked by header
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../config/db.php");
include("../includes/header.php");

// Define role-state mappings (Supporting Admin, Manager, Staff, Adopter/Registered User, Public)
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Public';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$shelter_id = isset($_SESSION['shelter_id']) ? $_SESSION['shelter_id'] : null;

// Fetch shelter metadata context if bound to a facility (Managers & Staff)
$facility_name = "Global District Network";
if ($shelter_id) {
    $shelter_stmt = pg_query_params($conn, "SELECT Name FROM Shelter WHERE ShelterID = $1", array($shelter_id));
    if ($shelter_row = pg_fetch_assoc($shelter_stmt)) {
        $facility_name = $shelter_row['name'];
    }
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
    
    <!-- DASHBOARD WELCOME HEADER -->
    <div class="mb-8 border-b border-sky-100 pb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">Operations Dashboard</h2>
            <p class="text-slate-500 text-sm mt-1">
                Security Profile: 
                <span class="bg-sky-50 text-sky-700 font-bold px-2.5 py-1 rounded-md border border-sky-100/60 ml-1 text-xs uppercase tracking-wider">
                    <?php echo $role; ?>
                </span>
                <?php if ($shelter_id) { ?>
                    <span class="text-xs text-slate-400 font-mono ml-2">Assigned to: <?php echo $facility_name; ?></span>
                <?php } ?>
            </p>
        </div>
        <div class="text-xs font-semibold text-slate-500 bg-white px-4 py-2 rounded-xl border border-sky-100/40 shadow-sm shrink-0 self-start sm:self-center">
            📍 Operation Node: Johor Bahru Network
        </div>
    </div>

    <!-- ==================== 1. SYSTEM ADMIN PANEL WORKSPACE ==================== -->
    <?php if ($role == "Admin") { ?>
        <div class="space-y-6">
            <div class="p-4 bg-slate-900 rounded-2xl text-sm text-slate-200 shadow-sm flex items-center gap-3">
                <span class="text-xl">🔑</span>
                <div>
                    <strong>Infrastructure Root Control Active:</strong> Global platform oversight framework. You are authorized to manage cross-shelter user provisioning, drop database tables, and track raw PostGIS/QGIS geometric parameters.
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <a href="../cats/list.php" class="bg-white p-6 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between group">
                    <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center text-2xl mb-4 group-hover:bg-sky-500 group-hover:text-white transition-all duration-200 shadow-sm">🐱</div>
                    <div>
                        <h4 class="font-bold text-slate-800 group-hover:text-sky-600 transition duration-150">Feline Registry</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Global profile system override auditing all operational hubs.</p>
                    </div>
                </a>

                <a href="../shelters/list.php" class="bg-white p-6 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between group">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl mb-4 group-hover:bg-blue-500 group-hover:text-white transition-all duration-200 shadow-sm">🏢</div>
                    <div>
                        <h4 class="font-bold text-slate-800 group-hover:text-blue-600 transition duration-150">Facility Provisioning</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Register new shelter buildings and scale resource capacity benchmarks.</p>
                    </div>
                </a>

                <a href="../reports/index.php" class="bg-white p-6 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between group">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl mb-4 group-hover:bg-amber-500 group-hover:text-white transition-all duration-200 shadow-sm">📊</div>
                    <div>
                        <h4 class="font-bold text-slate-800 group-hover:text-amber-600 transition duration-150">System Metrics Engine</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Review cross-hub resource distributions and system audit parameters.</p>
                    </div>
                </a>

                <a href="../intake/map.php" class="bg-white p-6 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between group">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl mb-4 group-hover:bg-purple-500 group-hover:text-white transition-all duration-200 shadow-sm">🗺️</div>
                    <div>
                        <h4 class="font-bold text-slate-800 group-hover:text-purple-600 transition duration-150">GIS Intake Hotspots</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Review advanced PostGIS spatial models and raw geographic geometries.</p>
                    </div>
                </a>
            </div>
        </div>

    <!-- ==================== 2. NEW: SHELTER MANAGER WORKSPACE ==================== -->
    <?php } elseif ($role == "Manager") { ?>
        <div class="space-y-6">
            <div class="p-4 bg-amber-50/70 rounded-2xl border border-amber-100 text-sm text-amber-800 shadow-sm flex items-center gap-3">
                <span class="text-xl">💼</span>
                <div>
                    <strong>Shelter Management Control Terminal:</strong> Logged in as chief administrative supervisor for <strong><?php echo $facility_name; ?></strong>. You have authorization to approve adoptions, allocate expenditures, and manage localized resource configurations.
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <a href="../cats/list.php" class="bg-white p-6 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between group">
                    <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center text-2xl mb-4 group-hover:bg-sky-500 group-hover:text-white transition-all duration-200 shadow-sm">🐱</div>
                    <div>
                        <h4 class="font-bold text-slate-800 group-hover:text-sky-600 transition duration-150">Inventory Inventory</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Audit localized feline details, healthcare classifications, and clinical schedules.</p>
                    </div>
                </a>

                <a href="../adoption/list.php" class="bg-white p-6 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between group">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl mb-4 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-200 shadow-sm">⚖️</div>
                    <div>
                        <h4 class="font-bold text-slate-800 group-hover:text-emerald-600 transition duration-150">Adoption Pipeline</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Verify background applications and issue final matching approvals for your hub.</p>
                    </div>
                </a>

                <a href="../reports/index.php" class="bg-white p-6 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between group">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl mb-4 group-hover:bg-amber-500 group-hover:text-white transition-all duration-200 shadow-sm">📊</div>
                    <div>
                        <h4 class="font-bold text-slate-800 group-hover:text-amber-600 transition duration-150">Decision Analytics</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Monitor clinical spending metrics and cage utilization rates at your location.</p>
                    </div>
                </a>

                <a href="../intake/map.php" class="bg-white p-6 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between group">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl mb-4 group-hover:bg-purple-500 group-hover:text-white transition-all duration-200 shadow-sm">🗺️</div>
                    <div>
                        <h4 class="font-bold text-slate-800 group-hover:text-purple-600 transition duration-150">Spatial Clusters</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">View spatial incoming trends surrounding your operational zone.</p>
                    </div>
                </a>
            </div>
        </div>

    <!-- ==================== 3. OPERATIONAL STAFF WORKSPACE ==================== -->
    <?php } elseif ($role == "Staff") { ?>
        <div class="space-y-6">
            <div class="p-4 bg-sky-50/50 rounded-2xl border border-sky-100/40 text-sm text-sky-800 shadow-sm flex items-center gap-3">
                <span class="text-xl">🏃‍♂️</span>
                <div>
                    <strong>Ground Operations Console Active:</strong> Stationed at <strong><?php echo $facility_name; ?></strong>. Use the consolidated links below to record intakes, update medical registries, or adjust ongoing cage housing assignments.
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <a href="../cats/list.php" class="bg-white p-6 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between group">
                    <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center text-2xl mb-4 group-hover:bg-sky-500 group-hover:text-white transition-all duration-200 shadow-sm">📝</div>
                    <div>
                        <h4 class="font-bold text-slate-800 group-hover:text-sky-600 transition duration-150">Feline Master List</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Perform daily data entry, track treatment logs, and update status codes.</p>
                    </div>
                </a>

                <a href="../adoption/list.php" class="bg-white p-6 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between group">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl mb-4 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-200 shadow-sm">📋</div>
                    <div>
                        <h4 class="font-bold text-slate-800 group-hover:text-emerald-500 transition duration-150">Process Workflows</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Screen visitor profiles and update the pipeline status for manager verification.</p>
                    </div>
                </a>

                <a href="../intake/map.php" class="bg-white p-6 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between group">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl mb-4 group-hover:bg-purple-500 group-hover:text-white transition-all duration-200 shadow-sm">📍</div>
                    <div>
                        <h4 class="font-bold text-slate-800 group-hover:text-purple-600 transition duration-150">Intake GIS Entry</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Plot coordinate structures for newly processed strays.</p>
                    </div>
                </a>
            </div>
        </div>

    <!-- ==================== 4. REGISTERED ADOPTER / USER WORKSPACE ==================== -->
    <?php } elseif ($role == "Adopter") { ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="../cats/list.php" class="bg-white p-6 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between group">
                <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center text-2xl mb-4 group-hover:bg-sky-500 group-hover:text-white transition-all duration-200 shadow-sm">🐈</div>
                <div>
                    <h4 class="font-bold text-slate-800 group-hover:text-sky-600 transition duration-150">Browse Available Cats</h4>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Search detailed rescue profiles, view clinical validation histories, and submit requests.</p>
                </div>
            </a>

            <a href="../adoption/list.php" class="bg-white p-6 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between group">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl mb-4 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-200 shadow-sm">📜</div>
                <div>
                    <h4 class="font-bold text-slate-800 group-hover:text-emerald-500 transition duration-150">My Application Ledger</h4>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Track the real-time review updates of your pending adoption match requests.</p>
                </div>
            </a>

            <a href="../support/index.php" class="bg-white p-6 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between group">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-2xl mb-4 group-hover:bg-rose-500 group-hover:text-white transition-all duration-200 shadow-sm">💖</div>
                <div>
                    <h4 class="font-bold text-slate-800 group-hover:text-rose-600 transition duration-150">Public Support Center</h4>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Sponsor specific felines, review community milestones, and view the supporter wall.</p>
                </div>
            </a>
        </div>

    <!-- ==================== 5. PUBLIC GUEST PORTAL WORKSPACE ==================== -->
    <?php } else { ?>
        <div class="bg-white border border-sky-100/80 rounded-3xl p-8 text-center max-w-3xl mx-auto shadow-sm">
            <div class="w-16 h-16 bg-sky-50 text-sky-500 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4 shadow-inner">🌐</div>
            <h3 class="text-xl font-bold text-slate-800">Public Access Portal</h3>
            <p class="text-slate-500 text-sm mt-2 mb-6 max-w-xl mx-auto leading-relaxed">
                Welcome to PawTrack. Register an official account to unlock adoption applications, track personal match pipelines, or securely sponsor long-term medical treatments across our rescue network.
            </p>
            <div class="flex justify-center gap-4">
                <a href="../cats/list.php" class="bg-slate-800 hover:bg-slate-900 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition shadow-sm">
                    Browse Rescues
                </a>
                <a href="../auth/login.php" class="bg-sky-500 hover:bg-sky-600 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition shadow-sm">
                    Authenticate Session
                </a>
            </div>
        </div>
    <?php } ?>

</div>

<?php include("../includes/footer.php"); ?>