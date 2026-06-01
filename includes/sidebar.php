<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$role = $_SESSION['role'] ?? null;
?>

<!-- MOBILE BACKDROP OVERLAY -->
<div id="backdrop" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm hidden z-[90] transition-all duration-300" onclick="toggleSidebar()"></div>

<!-- STYLISH SIDEBAR PANEL -->
<div id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-white border-r border-sky-100/80 shadow-xl transform -translate-x-full transition-transform duration-300 ease-in-out z-[100] flex flex-col justify-between">
    
    <div>
        <!-- HEADER SPECIFICATION -->
        <div class="h-20 px-6 border-b border-sky-50 flex items-center justify-between bg-sky-50/30">
            <div class="flex items-center gap-2.5">
                <span class="text-xl">🎛️</span>
                <span class="font-bold text-slate-800 tracking-tight text-sm uppercase tracking-wider">Control Panel</span>
            </div>
            <button onclick="toggleSidebar()" class="text-slate-400 hover:text-sky-600 text-lg transition p-1.5 rounded-lg hover:bg-sky-50">
                ✕
            </button>
        </div>

        <!-- ROLE-BASED APPLICATION LINKS -->
        <div class="p-4 space-y-1 text-sm font-medium">
            
            <!-- ADMIN ACCESS WORKSPACE -->
            <?php if ($role == "Admin") { ?>
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 px-3 pt-2 pb-1">System Core</p>
                <a href="/PawTrack/dashboard/index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50 transition duration-150">📊 Overview Hub</a>
                <a href="/PawTrack/cats/list.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50 transition duration-150">🐈 Manage Felines</a>
                <a href="/PawTrack/shelters/list.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50 transition duration-150">🏢 Partner Hubs</a>
                <a href="/PawTrack/adoption/list.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50 transition duration-150">🏠 Adoption Apps</a>
                
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 px-3 pt-4 pb-1">Spatial Metrics</p>
                <a href="/PawTrack/intake/map.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50 transition duration-150">🗺️ GIS Hotspot Map</a>
                <a href="/PawTrack/reports/index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50 transition duration-150">📈 System Reports</a>

            <!-- NEW: SHELTER MANAGER ACCESS WORKSPACE -->
            <?php } elseif ($role == "Manager") { ?>
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 px-3 pt-2 pb-1">Facility Control</p>
                <a href="/PawTrack/dashboard/index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50 transition duration-150">💼 Manager Console</a>
                <a href="/PawTrack/cats/list.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50 transition duration-150">🐱 Feline Registry</a>
                <a href="/PawTrack/adoption/list.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50 transition duration-150">⚖️ Adoption Pipelines</a>
                <a href="/PawTrack/reports/index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50 transition duration-150">📊 Shelter Analytics</a>

            <!-- OPERATIONAL STAFF ACCESS WORKSPACE -->
            <?php } elseif ($role == "Staff") { ?>
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 px-3 pt-2 pb-1">Operations</p>
                <a href="/PawTrack/dashboard/index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50 transition duration-150">📋 Console Main</a>
                <a href="/PawTrack/cats/list.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50 transition duration-150">🐱 Feline Registry</a>
                <a href="/PawTrack/cats/add.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50 transition duration-150">＋ Register Cat</a>
                
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 px-3 pt-4 pb-1">Healthcare</p>
                <a href="/PawTrack/vaccination/list.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50 transition duration-150">🛡️ Immunizations</a>
                <a href="/PawTrack/adoption/list.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50 transition duration-150">⚖️ Adoptions</a>

            <!-- PUBLIC ADOPTER ACCESS WORKSPACE -->
            <?php } elseif ($role == "Adopter") { ?>
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 px-3 pt-2 pb-1">My Account</p>
                <a href="/PawTrack/cats/list.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50 transition duration-150">🐈 Search Profiles</a>
                <a href="/PawTrack/adoption/list.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50 transition duration-150">📋 My Applications</a>
                <a href="/PawTrack/donations/add.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50 transition duration-150">💝 Send Donation</a>

            <!-- ANONYMOUS GUEST VISITOR -->
            <?php } else { ?>
                <div class="p-4 text-center space-y-3 bg-slate-50 rounded-2xl border border-sky-50 mt-2">
                    <p class="text-xs text-slate-500 leading-relaxed">Sign in to unlock interactive management features.</p>
                    <a href="/PawTrack/auth/login.php" class="block bg-sky-500 hover:bg-sky-600 text-white font-semibold py-2 rounded-xl text-xs shadow transition">
                        Login Here
                    </a>
                </div>
            <?php } ?>
            
        </div>
    </div>

    <!-- FIXED SIDEBAR MODULE FOOTER -->
    <?php if (isset($_SESSION['user_id'])) { ?>
        <div class="p-4 border-t border-sky-50 bg-sky-50/20">
            <div class="flex items-center justify-between bg-white p-3 rounded-xl border border-sky-100/60 shadow-sm">
                <span class="text-xs font-bold text-slate-700 truncate max-w-[120px]">👤 <?php echo htmlspecialchars($_SESSION['name'] ?? 'User'); ?></span>
                <a href="/PawTrack/auth/logout.php" class="text-[11px] font-bold text-rose-500 hover:text-rose-700 transition">Exit</a>
            </div>
        </div>
    <?php } ?>

</div>