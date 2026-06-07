<?php
include("../config/db.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['role'] ?? null;

// Enforce role-based access control parameters before any output
if (!$role || ($role != "Admin" && $role != "Staff" && $role != "Manager")) {
    header("Location: /PawTrack/auth/login.php");
    exit;
}

// 1. PROCESS DATABASE LOGGING BEFORE ANY HTML IS OUPUT
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $catid = $_POST['catid'];
    $vaccineid = $_POST['vaccineid'];
    $date = $_POST['date'];
    $cost = $_POST['cost'];

    $query = "
    INSERT INTO Vaccination_Record (CatID, VaccineID, Date, Cost)
    VALUES ($1, $2, $3, $4)
    ";

    $result = pg_query_params($conn, $query, [$catid, $vaccineid, $date, $cost]);

    if ($result) {
        $_SESSION['message'] = "Vaccination record logged successfully.";
        header("Location: /PawTrack/vaccination/list.php");
        exit;
    }
}

// 2. FETCH REFERENCE SEEDS ONLY AFTERA SAFELY BYPASSING REDIRECT LAYERS
// Administration users access all cat records for clinic logs; others filter by available status
if ($role == "Admin" || $role == "Manager" || $role == "Staff") {
    $cats = pg_query($conn, "SELECT CatID, Name, Status FROM Cat ORDER BY Name");
} else {
    $cats = pg_query($conn, "SELECT CatID, Name, Status FROM Cat WHERE Status = 'Available' ORDER BY Name");
}

$vaccines = pg_query($conn, "SELECT VaccineID, VaccineName, Description FROM Vaccination ORDER BY VaccineName");

// Safe rendering location for layout files
include("../includes/header.php");
?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 mb-12">
    
    <div class="mb-8 border-b border-sky-100 pb-4 text-center sm:text-left">
        <h2 class="text-3xl font-black text-slate-900 tracking-tight">Log Vaccination</h2>
        <p class="text-slate-500 text-sm mt-1">Record core cat vaccines and related medical costs.</p>
    </div>

    <form method="POST" class="bg-white p-6 sm:p-8 rounded-3xl border border-sky-100/60 shadow-sm space-y-6">
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Cat Patient</label>
                <select name="catid" required class="w-full border border-slate-200 bg-slate-50 px-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-sm font-medium text-slate-700 transition">
                    <?php while ($cat = pg_fetch_assoc($cats)) { ?>
                        <option value="<?php echo $cat['catid']; ?>">
                            🐱 <?php echo htmlspecialchars($cat['name']); ?> 
                            (<?php echo htmlspecialchars($cat['status']); ?>)
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Administered Vaccine</label>
                <select name="vaccineid" required class="w-full border border-slate-200 bg-slate-50 px-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-sm font-medium text-slate-700 transition">
                    <?php while ($v = pg_fetch_assoc($vaccines)) { ?>
                        <option value="<?php echo $v['vaccineid']; ?>">💉 <?php echo htmlspecialchars($v['vaccinename']); ?> (<?php echo htmlspecialchars($v['description']); ?>)</option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Administration Date</label>
                <input type="date" name="date" required class="w-full border border-slate-200 bg-slate-50 px-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-sm font-medium text-slate-700 transition">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Vaccine Cost (RM)</label>
                <input type="number" step="0.01" name="cost" placeholder="0.00" required
                       class="w-full border border-slate-200 bg-slate-50 px-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-sm font-mono font-bold text-slate-700 transition">
            </div>
        </div>

        <div class="flex gap-3 pt-4 border-t border-slate-100">
            <button type="submit" class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3.5 rounded-xl shadow-md transition duration-150 text-sm">
                Save Vaccination Record
            </button>
            <a href="/PawTrack/vaccination/list.php" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-8 py-3.5 rounded-xl text-sm transition duration-150 text-center">
                Cancel
            </a>
        </div>
    </form>
</div>

<?php include("../includes/footer.php"); ?>