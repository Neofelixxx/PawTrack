<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$role = $_SESSION['role'] ?? null;
$base_path = $base_path ?? (($_SERVER['HTTP_HOST'] === 'pawtrack.local') ? '/' : '/PawTrack/');
?>

<div id="backdrop" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm hidden z-[90] transition-all duration-300" onclick="toggleSidebar()"></div>

<div id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-white border-r border-sky-100/80 shadow-xl transform -translate-x-full transition-transform duration-300 ease-in-out z-[100] flex flex-col justify-between overflow-y-auto">
    
    <div>
        <div class="h-20 px-6 border-b border-sky-50 flex items-center justify-between bg-sky-50/30">
            <div class="flex items-center gap-2.5">
                <span class="text-xl">⚙️</span>
                <span class="font-bold text-slate-800 tracking-tight text-sm uppercase tracking-wider">Control Panel</span>
            </div>
            <button onclick="toggleSidebar()" class="text-slate-400 hover:text-sky-600 text-lg transition p-1.5 rounded-lg hover:bg-sky-50">✕</button>
        </div>

        <div class="p-4 space-y-1 text-sm font-medium">
            
            <?php if ($role == "Admin") { ?>
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 px-3 pt-2 pb-1">System Core</p>
                <a href="<?php echo $base_path; ?>users/list.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50">User Accounts</a>
                <a href="<?php echo $base_path; ?>shelters/list.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50">Shelter Nodes</a>
                
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 px-3 pt-4 pb-1">Operations</p>
                <a href="<?php echo $base_path; ?>cats/list.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50">Cat Database</a>
                <a href="<?php echo $base_path; ?>medical/list.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50">Medical Logs</a>
                <a href="<?php echo $base_path; ?>vaccination/list.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50">Manage Vaccines</a>
                <a href="<?php echo $base_path; ?>adoption/list.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50">Adoption</a>
                <a href="<?php echo $base_path; ?>donations/list.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50">Donations</a>
                <a href="<?php echo $base_path; ?>cages/list.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50">Cage Tracking Board</a>
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 px-3 pt-4 pb-1">Analytics</p>
                <a href="<?php echo $base_path; ?>intake/map.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50">Map</a>
                <a href="<?php echo $base_path; ?>reports/index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50">Reports</a>

            <?php } elseif ($role == "Manager") { ?>
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 px-3 pt-2 pb-1">Facility Control</p>
                <a href="<?php echo $base_path; ?>shelters/list.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50">Manage Shelters</a>
                <a href="<?php echo $base_path; ?>cats/list.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50">Manage Cat</a>
                <a href="<?php echo $base_path; ?>medical/list.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50">Medical Records</a>
                <a href="<?php echo $base_path; ?>vaccination/list.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50">Manage Vaccines</a>
                <a href="<?php echo $base_path; ?>adoption/list.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50">Adoptions</a>
                <a href="<?php echo $base_path; ?>reports/index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50">Analytics</a>
                <a href="<?php echo $base_path; ?>cages/list.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50">Cage Tracking Board</a>

            <?php } elseif ($role == "Staff") { ?>
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 px-3 pt-2 pb-1">Operations</p>
                <a href="<?php echo $base_path; ?>cats/list.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50">Cat Management</a>
                <a href="<?php echo $base_path; ?>cats/add.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sky-600 font-bold bg-sky-50 border border-sky-100">Register Cat</a>
                <a href="<?php echo $base_path; ?>medical/list.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50">Medical Logs</a>
                <a href="<?php echo $base_path; ?>vaccination/list.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50">Manage Vaccines</a>
                <a href="<?php echo $base_path; ?>adoption/list.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50">Adoption</a>
                <a href="<?php echo $base_path; ?>intake/map.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50">Map</a>
                <a href="<?php echo $base_path; ?>cages/list.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50">Cage Tracking Board</a>

            <?php } elseif ($role == "Adopter") { ?>
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 px-3 pt-2 pb-1">Account Dashboard</p>
                <a href="<?php echo $base_path; ?>cats/list.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50">Search Profiles</a>
                <a href="<?php echo $base_path; ?>adoption/list.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50">Submitted Applications</a>
                <a href="<?php echo $base_path; ?>donations/add.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:text-sky-600 hover:bg-sky-50">Contributions</a>

            <?php } else { ?>
                <div class="p-4 text-center space-y-3 bg-slate-50 rounded-2xl border border-sky-50 mt-2">
                    <p class="text-xs text-slate-500 leading-relaxed">Authentication required to access interactive tracking parameters.</p>
                    <a href="<?php echo $base_path; ?>auth/login.php" class="block bg-sky-500 hover:bg-sky-600 text-white font-semibold py-2 rounded-xl text-xs shadow transition">
                        System Login
                    </a>
                </div>
            <?php } ?>
            
        </div>
    </div>

    <?php if (isset($_SESSION['user_id'])) { ?>
        <div class="p-4 border-t border-sky-50 bg-sky-50/20">
            <div class="flex items-center justify-between bg-white p-3 rounded-xl border border-sky-100/60 shadow-sm">
                <span class="text-xs font-bold text-slate-700 truncate max-w-[120px]">👤 <?php echo htmlspecialchars($_SESSION['name'] ?? 'User'); ?></span>
                <a href="<?php echo $base_path; ?>auth/logout.php" class="text-[11px] font-bold text-rose-500 hover:text-rose-700 transition">Disconnect</a>
            </div>
        </div>
    <?php } ?>

</div>