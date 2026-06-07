<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$role = $_SESSION['role'] ?? null;
$name = $_SESSION['name'] ?? null;
// Ensure base path is available if navbar is loaded independently
$base_path = $base_path ?? (($_SERVER['HTTP_HOST'] === 'pawtrack.local') ? '/' : '/PawTrack/');
?>

<nav class="sticky top-0 bg-white/95 backdrop-blur-md border-b border-sky-100/60 z-40 transition-all no-print">
    <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
        
        <div class="flex items-center gap-4">
            <button onclick="toggleSidebar()" class="text-sky-700 hover:text-sky-900 text-2xl transition p-2 rounded-xl hover:bg-sky-50">
                ☰
            </button>
            <a href="<?php echo $base_path; ?>index.php" class="flex items-center gap-3 group">
                <img src="<?php echo $base_path; ?>assets/images/Cat Logo.png" 
                     alt="PawTrack Logo" 
                     class="w-9 h-9 object-cover rounded-full border border-sky-200 shadow-sm group-hover:scale-105 transition duration-300"
                     onerror="this.onerror=null; this.src='../assets/images/Cat Logo.png';">
                <span class="text-2xl font-extrabold tracking-tight text-sky-600 group-hover:text-sky-700 transition hidden sm:block">
                    PawTrack
                </span>
            </a>
        </div>

        <div class="hidden md:flex items-center gap-1 font-medium text-slate-600">
            <?php if ($role === 'Admin' || $role === 'Manager' || $role === 'Staff') { ?>
                <a href="<?php echo $base_path; ?>cats/list.php" class="px-4 py-2 rounded-xl hover:text-sky-600 hover:bg-sky-50 transition">Cats</a>
                <a href="<?php echo $base_path; ?>adoption/list.php" class="px-4 py-2 rounded-xl hover:text-sky-600 hover:bg-sky-50 transition">Adoption</a>
                <a href="<?php echo $base_path; ?>intake/map.php" class="px-4 py-2 rounded-xl hover:text-sky-600 hover:bg-sky-50 transition">Map</a>
                <?php if ($role === 'Admin' || $role === 'Manager') { ?>
                    <a href="<?php echo $base_path; ?>reports/index.php" class="px-4 py-2 rounded-xl hover:text-sky-600 hover:bg-sky-50 transition">Reports</a>
                <?php } ?>
            <?php } else { ?>
                <a href="<?php echo $base_path; ?>cats/list.php" class="px-4 py-2 rounded-xl hover:text-sky-600 hover:bg-sky-50 transition">Adopt</a>
                <a href="<?php echo $base_path; ?>donations/add.php" class="px-4 py-2 rounded-xl hover:text-sky-600 hover:bg-sky-50 transition">Donate</a>
                <?php if ($role === 'Adopter') { ?>
                    <a href="<?php echo $base_path; ?>adoption/list.php" class="px-4 py-2 rounded-xl hover:text-sky-600 hover:bg-sky-50 transition">Apps</a>
                <?php } ?>
            <?php } ?>
        </div>

        <div class="flex items-center gap-4">
            <?php if ($role) { ?>
                <span class="text-xs font-mono font-bold bg-slate-100 text-slate-600 px-2 py-1 rounded-md uppercase hidden sm:block">
                    <?php echo htmlspecialchars($role); ?>
                </span>
                <a href="<?php echo $base_path; ?>auth/logout.php" class="bg-slate-800 hover:bg-rose-600 text-white font-semibold px-5 py-2.5 rounded-xl shadow-sm transition-all duration-200 text-sm">
                    Logout
                </a>
            <?php } else { ?>
                <a href="<?php echo $base_path; ?>auth/login.php" class="text-sm font-semibold text-slate-600 hover:text-sky-600 transition px-3 py-2">
                    Login
                </a>
                <a href="<?php echo $base_path; ?>auth/register.php" class="bg-sky-500 hover:bg-sky-600 text-white font-semibold px-5 py-2.5 rounded-xl shadow-md transition-all duration-200 text-sm">
                    Register
                </a>
            <?php } ?>
        </div>
    </div>
</nav>