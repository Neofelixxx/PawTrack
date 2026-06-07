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
$facility_name = "Combined Shelter Network";
if ($shelter_id) {
    $shelter_stmt = pg_query_params($conn, "SELECT Name FROM Shelter WHERE ShelterID = $1", array($shelter_id));
    if ($shelter_row = pg_fetch_assoc($shelter_stmt)) {
        $facility_name = $shelter_row['name'];
    }
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 mb-12">
    
    <div class="mb-8 border-b border-sky-100 pb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">Operations Dashboard</h2>
            <p class="text-slate-500 text-sm mt-1">
                Access Level: 
                <span class="bg-sky-50 text-sky-700 font-bold px-2.5 py-1 rounded-md border border-sky-100/60 ml-1 text-xs uppercase tracking-wide">
                    <?php echo htmlspecialchars($role); ?>
                </span>
                <?php if ($shelter_id) { ?>
                    <span class="text-xs text-slate-400 font-mono ml-2">Assigned Hub: <?php echo htmlspecialchars($facility_name); ?></span>
                <?php } ?>
            </p>
        </div>
        <div class="text-xs font-semibold text-slate-500 bg-white px-4 py-2 rounded-xl border border-sky-100/40 shadow-sm self-start sm:self-center">
            📍 Operation Node: Johor Bahru Network
        </div>
    </div>

    <?php if ($role == "Admin") { ?>
        <div class="space-y-6">
            <div class="p-4 bg-slate-900 rounded-2xl text-sm text-slate-200 shadow-sm flex items-center gap-3">
                <span class="text-xl">🔑</span>
                <div class="break-words">
                    <strong>System Administration Active:</strong> Global platform settings enabled. Access parameters permit the oversight of accounts, registration of shelter facility parameters, and evaluation of rescue mapping databases.
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <a href="../cats/list.php" class="bg-white p-6 h-64 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200 flex flex-col justify-start group">
                    <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center text-2xl group-hover:bg-sky-500 group-hover:text-white transition-all shadow-sm shrink-0">🐱</div>
                    <div class="mt-4 flex flex-col flex-1">
                        <h4 class="font-bold text-slate-800 group-hover:text-sky-600 transition duration-150">Cat Database</h4>
                        <p class="text-xs text-slate-500 leading-relaxed break-words mt-2">Review records and health parameters across all processing facilities.</p>
                    </div>
                </a>
                <a href="../shelters/list.php" class="bg-white p-6 h-64 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200 flex flex-col justify-start group">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl group-hover:bg-blue-500 group-hover:text-white transition-all shadow-sm shrink-0">🏢</div>
                    <div class="mt-4 flex flex-col flex-1">
                        <h4 class="font-bold text-slate-800 group-hover:text-blue-600 transition duration-150">Shelter Facilities</h4>
                        <p class="text-xs text-slate-500 leading-relaxed break-words mt-2">Register new buildings and allocate regional maximum capacity boundaries.</p>
                    </div>
                </a>
                <a href="../reports/index.php" class="bg-white p-6 h-64 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200 flex flex-col justify-start group">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl group-hover:bg-amber-500 group-hover:text-white transition-all shadow-sm shrink-0">📊</div>
                    <div class="mt-4 flex flex-col flex-1">
                        <h4 class="font-bold text-slate-800 group-hover:text-amber-600 transition duration-150">System Reports</h4>
                        <p class="text-xs text-slate-500 leading-relaxed break-words mt-2">Evaluate community donation records and regional shelter data totals.</p>
                    </div>
                </a>
                <a href="../intake/map.php" class="bg-white p-6 h-64 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200 flex flex-col justify-start group">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl mb-4 group-hover:bg-purple-500 group-hover:text-white transition-all shadow-sm shrink-0">🗺️</div>
                    <div class="mt-4 flex flex-col flex-1">
                        <h4 class="font-bold text-slate-800 group-hover:text-purple-600 transition duration-150">Rescue Map</h4>
                        <p class="text-xs text-slate-500 leading-relaxed break-words mt-2">Monitor geographical density indicators for incoming rescued strays.</p>
                    </div>
                </a>
            </div>
        </div>

    <?php } elseif ($role == "Manager") { ?>
        <div class="space-y-6">
            <div class="p-4 bg-amber-50 border border-amber-100 rounded-2xl text-sm text-amber-800 shadow-sm flex items-center gap-3">
                <span class="text-xl">💼</span>
                <div class="break-words">
                    <strong>Shelter Management Console:</strong> Authenticated as coordinator for <strong><?php echo htmlspecialchars($facility_name); ?></strong>. Permissions grant the authorization of applications, expense monitoring, and facility setup.
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <a href="../cats/list.php" class="bg-white p-6 h-64 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200 flex flex-col justify-start group">
                    <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center text-2xl group-hover:bg-sky-500 group-hover:text-white transition-all shadow-sm shrink-0">🐱</div>
                    <div class="mt-4 flex flex-col flex-1">
                        <h4 class="font-bold text-slate-800 group-hover:text-sky-600 transition duration-150">Feline Registry</h4>
                        <p class="text-xs text-slate-500 leading-relaxed break-words mt-2">Manage local feline profiles, healthcare details, and vaccination lists.</p>
                    </div>
                </a>
                <a href="../adoption/list.php" class="bg-white p-6 h-64 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200 flex flex-col justify-start group">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl group-hover:bg-emerald-500 group-hover:text-white transition-all shadow-sm shrink-0">⚖️</div>
                    <div class="mt-4 flex flex-col flex-1">
                        <h4 class="font-bold text-slate-800 group-hover:text-emerald-600 transition duration-150">Adoption Desk</h4>
                        <p class="text-xs text-slate-500 leading-relaxed break-words mt-2">Review screening details and issue final matching case approvals.</p>
                    </div>
                </a>
                <a href="../reports/index.php" class="bg-white p-6 h-64 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200 flex flex-col justify-start group">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl group-hover:bg-amber-500 group-hover:text-white transition-all shadow-sm shrink-0">📊</div>
                    <div class="mt-4 flex flex-col flex-1">
                        <h4 class="font-bold text-slate-800 group-hover:text-amber-600 transition duration-150">Management Reports</h4>
                        <p class="text-xs text-slate-500 leading-relaxed break-words mt-2">Track facility spending records and cage utilization parameters.</p>
                    </div>
                </a>
                <a href="../intake/map.php" class="bg-white p-6 h-64 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200 flex flex-col justify-start group">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl group-hover:bg-purple-500 group-hover:text-white transition-all shadow-sm shrink-0">🗺️</div>
                    <div class="mt-4 flex flex-col flex-1">
                        <h4 class="font-bold text-slate-800 group-hover:text-purple-600 transition duration-150">Area Analysis</h4>
                        <p class="text-xs text-slate-500 leading-relaxed break-words mt-2">Analyze geographic rescue distributions adjacent to the facility.</p>
                    </div>
                </a>
            </div>
        </div>

    <?php } elseif ($role == "Staff") { ?>
        <div class="space-y-6">
            <div class="p-4 bg-sky-50 rounded-2xl border border-sky-100/50 text-sm text-sky-800 shadow-sm flex items-center gap-3">
                <span class="text-xl">📋</span>
                <div class="break-words">
                    <strong>Shelter Operations Active:</strong> Access active at <strong><?php echo htmlspecialchars($facility_name); ?></strong>. Forms enable the log of rescued strays, administration of medical cases, and update of workflow files.
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <a href="../cats/list.php" class="bg-white p-6 h-64 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200 flex flex-col justify-start group">
                    <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center text-2xl group-hover:bg-sky-500 group-hover:text-white transition-all shadow-sm shrink-0">📝</div>
                    <div class="mt-4 flex flex-col flex-1">
                        <h4 class="font-bold text-slate-800 group-hover:text-sky-600 transition duration-150">Feline Database</h4>
                        <p class="text-xs text-slate-500 leading-relaxed break-words mt-2">Perform profile data entry, append healthcare values, and log actions.</p>
                    </div>
                </a>
                <a href="../adoption/list.php" class="bg-white p-6 h-64 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200 flex flex-col justify-start group">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl group-hover:bg-emerald-500 group-hover:text-white transition-all shadow-sm shrink-0">📋</div>
                    <div class="mt-4 flex flex-col flex-1">
                        <h4 class="font-bold text-slate-800 group-hover:text-emerald-500 transition duration-150">Pipeline Management</h4>
                        <p class="text-xs text-slate-500 leading-relaxed break-words mt-2">Filter visitor backgrounds and forward profiles for manager verification.</p>
                    </div>
                </a>
                <a href="../intake/map.php" class="bg-white p-6 h-64 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200 flex flex-col justify-start group">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl group-hover:bg-purple-500 group-hover:text-white transition-all shadow-sm shrink-0">📍</div>
                    <div class="mt-4 flex flex-col flex-1">
                        <h4 class="font-bold text-slate-800 group-hover:text-purple-600 transition duration-150">Plot Entry</h4>
                        <p class="text-xs text-slate-500 leading-relaxed break-words mt-2">Log geographic coordinates for newly processed strays.</p>
                    </div>
                </a>
            </div>
        </div>

    <?php } else { ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="../cats/list.php" class="bg-white p-6 h-64 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200 flex flex-col justify-start group">
                <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center text-2xl group-hover:bg-sky-500 group-hover:text-white transition-all shadow-sm shrink-0">🐱</div>
                <div class="mt-4 flex flex-col flex-1">
                    <h4 class="font-bold text-slate-800 group-hover:text-sky-600 transition duration-150">Browse Available Cats</h4>
                    <p class="text-xs text-slate-500 leading-relaxed break-words mt-2">Look through animal rescue lists, view medical updates, and apply for adoption matching.</p>
                </div>
            </a>
            <a href="../adoption/list.php" class="bg-white p-6 h-64 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200 flex flex-col justify-start group">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl group-hover:bg-emerald-500 group-hover:text-white transition-all shadow-sm shrink-0">📋</div>
                <div class="mt-4 flex flex-col flex-1">
                    <h4 class="font-bold text-slate-800 group-hover:text-emerald-600 transition duration-150">My Applications</h4>
                    <p class="text-xs text-slate-500 leading-relaxed break-words mt-2">Check evaluation milestones and review status updates regarding submitted matching requests.</p>
                </div>
            </a>
            <a href="../donations/add.php" class="bg-white p-6 h-64 rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200 flex flex-col justify-start group">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-2xl group-hover:bg-rose-500 group-hover:text-white transition-all shadow-sm shrink-0">💖</div>
                <div class="mt-4 flex flex-col flex-1">
                    <h4 class="font-bold text-slate-800 group-hover:text-rose-600 transition duration-150">Support Local Shelters</h4>
                    <p class="text-xs text-slate-500 leading-relaxed break-words mt-2">Sponsor local rescue work, provide item checklists, or view community metrics.</p>
                </div>
            </a>
        </div>
    <?php } ?>

</div>

<?php include("../includes/footer.php"); ?>