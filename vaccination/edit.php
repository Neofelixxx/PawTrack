<?php
include("../config/db.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['role'] ?? null;
if (!$role || ($role != "Admin" && $role != "Staff" && $role != "Manager")) {
    header("Location: /PawTrack/auth/login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    die("Missing or invalid target transaction record identifier.");
}

// Intercept transaction parameters first to safeguard header execution structures
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $catid = $_POST['catid'];
    $vaccineid = $_POST['vaccineid'];
    $date = $_POST['date'];
    $cost = $_POST['cost'];

    $query = "
        UPDATE Vaccination_Record 
        SET CatID = $1, VaccineID = $2, Date = $3, Cost = $4 
        WHERE vacrecordid = $5
    ";
    $result = pg_query_params($conn, $query, [$catid, $vaccineid, $date, $cost, $id]);

    if ($result) {
        $_SESSION['message'] = "Vaccination record updated successfully.";
        header("Location: list.php");
        exit;
    }
}

// Pull active record values to map into form input fields safely
$data = pg_fetch_assoc(pg_query_params($conn, "SELECT * FROM Vaccination_Record WHERE vacrecordid = $1", [$id]));
if (!$data) {
    die("Vaccination target entry parameters could not be found.");
}

$cats = pg_query($conn, "SELECT CatID, Name FROM Cat ORDER BY Name");
$vaccines = pg_query($conn, "SELECT VaccineID, VaccineName FROM Vaccination ORDER BY VaccineName");

include("../includes/header.php");
?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 mb-12">
    <div class="mb-8 border-b border-sky-100 pb-4 text-center sm:text-left">
        <h2 class="text-3xl font-black text-slate-900 tracking-tight">Modify Vaccination Entry</h2>
        <p class="text-slate-500 text-sm mt-1">Adjust documentation metrics and cost factors related to this immunization log.</p>
    </div>

    <form method="POST" class="bg-white p-6 sm:p-8 rounded-3xl border border-sky-100/60 shadow-sm space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Cat Patient</label>
                <select name="catid" required class="w-full border border-slate-200 bg-slate-50 px-4 py-3 rounded-xl focus:outline-none focus:border-sky-500 text-sm font-medium text-slate-700 transition">
                    <?php while ($cat = pg_fetch_assoc($cats)) { ?>
                        <option value="<?php echo $cat['catid']; ?>" <?php echo ($cat['catid'] == $data['catid']) ? 'selected' : ''; ?>>
                            🐱 <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Administered Vaccine Type</label>
                <select name="vaccineid" required class="w-full border border-slate-200 bg-slate-50 px-4 py-3 rounded-xl focus:outline-none focus:border-sky-500 text-sm font-medium text-slate-700 transition">
                    <?php while ($v = pg_fetch_assoc($vaccines)) { ?>
                        <option value="<?php echo $v['vaccineid']; ?>" <?php echo ($v['vaccineid'] == $data['vaccineid']) ? 'selected' : ''; ?>>
                            💉 <?php echo htmlspecialchars($v['vaccinename']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Administration Date</label>
                <input type="date" name="date" value="<?php echo htmlspecialchars($data['date']); ?>" required class="w-full border border-slate-200 bg-slate-50 px-4 py-3 rounded-xl focus:outline-none focus:border-sky-500 text-sm font-medium text-slate-700 transition">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Vaccine Cost (RM)</label>
                <input type="number" step="0.01" name="cost" value="<?php echo htmlspecialchars($data['cost']); ?>" required class="w-full border border-slate-200 bg-slate-50 px-4 py-3 rounded-xl focus:outline-none focus:border-sky-500 text-sm font-mono font-bold text-slate-700 transition">
            </div>
        </div>

        <div class="flex gap-3 pt-4 border-t border-slate-100">
            <button type="submit" class="flex-1 bg-sky-500 hover:bg-sky-600 text-white font-bold py-3.5 rounded-xl shadow-md transition text-sm">
                Commit Changes
            </button>
            <a href="/PawTrack/vaccination/list.php" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-8 py-3.5 rounded-xl text-sm transition text-center">
                Cancel
            </a>
        </div>
    </form>
</div>

<?php include("../includes/footer.php"); ?>