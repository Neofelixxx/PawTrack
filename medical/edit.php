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
    die("Missing or invalid record identifier parameter request.");
}

// Process modifications first to ensure header redirects resolve properly
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $catid = $_POST['catid'];
    $treatid = $_POST['treatid'];
    $category = trim($_POST['category']);
    $cost = $_POST['cost'];
    $date = $_POST['date'];
    $notes = trim($_POST['notes']);

    $query = "
        UPDATE Medical_Record 
        SET CatID = $1, TreatID = $2, Category = $3, Cost = $4, TreatmentDate = $5, Notes = $6 
        WHERE medrecordid = $7
    ";
    $result = pg_query_params($conn, $query, [$catid, $treatid, $category, $cost, $date, $notes, $id]);

    if ($result) {
        $_SESSION['message'] = "Medical profile data parameters modified successfully.";
        header("Location: list.php");
        exit;
    }
}

// Fetch entry context for dropdown select pre-fills
$data = pg_fetch_assoc(pg_query_params($conn, "SELECT * FROM Medical_Record WHERE medrecordid = $1", [$id]));
if (!$data) {
    die("Medical profile target record could not be found.");
}

$cats = pg_query($conn, "SELECT CatID, Name FROM Cat ORDER BY Name");
$treatments = pg_query($conn, "SELECT TreatID, TreatName FROM Treatment ORDER BY TreatName");

include("../includes/header.php");
?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 mb-12">
    <div class="mb-8 border-b border-sky-100 pb-4 text-center sm:text-left">
        <h2 class="text-3xl font-black text-slate-900 tracking-tight">Modify Medical Entry</h2>
        <p class="text-slate-500 text-sm mt-1">Adjust documentation records, categories, and expenditures related to this health record.</p>
    </div>

    <form method="POST" class="bg-white p-6 sm:p-8 rounded-3xl border border-sky-100/60 shadow-sm space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Feline Patient</label>
                <select name="catid" required class="w-full border border-slate-200 bg-slate-50 px-4 py-3 rounded-xl focus:outline-none focus:border-sky-500 text-sm font-medium text-slate-700 transition">
                    <?php while ($cat = pg_fetch_assoc($cats)) { ?>
                        <option value="<?php echo $cat['catid']; ?>" <?php echo ($cat['catid'] == $data['catid']) ? 'selected' : ''; ?>>
                            🐱 <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Assigned Treatment</label>
                <select name="treatid" required class="w-full border border-slate-200 bg-slate-50 px-4 py-3 rounded-xl focus:outline-none focus:border-sky-500 text-sm font-medium text-slate-700 transition">
                    <?php while ($t = pg_fetch_assoc($treatments)) { ?>
                        <option value="<?php echo $t['treatid']; ?>" <?php echo ($t['treatid'] == $data['treatid']) ? 'selected' : ''; ?>>
                            🩺 <?php echo htmlspecialchars($t['treatname']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Clinical Category</label>
                <input type="text" name="category" value="<?php echo htmlspecialchars($data['category']); ?>" class="w-full border border-slate-200 bg-slate-50 px-4 py-3 rounded-xl focus:outline-none focus:border-sky-500 text-sm font-medium text-slate-700 transition">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Operational Expense (RM)</label>
                <input type="number" step="0.01" name="cost" value="<?php echo htmlspecialchars($data['cost']); ?>" required class="w-full border border-slate-200 bg-slate-50 px-4 py-3 rounded-xl focus:outline-none focus:border-sky-500 text-sm font-mono font-bold text-slate-700 transition">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Filing Date</label>
            <input type="date" name="date" value="<?php echo htmlspecialchars($data['treatmentdate']); ?>" required class="w-full border border-slate-200 bg-slate-50 px-4 py-3 rounded-xl focus:outline-none focus:border-sky-500 text-sm font-medium text-slate-700 transition">
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Clinical Assessment Notes</label>
            <textarea name="notes" rows="4" class="w-full border border-slate-200 bg-slate-50 px-4 py-3 rounded-xl focus:outline-none focus:border-sky-500 text-sm font-medium text-slate-700 transition"><?php echo htmlspecialchars($data['notes']); ?></textarea>
        </div>

        <div class="flex gap-3 pt-4 border-t border-slate-100">
            <button type="submit" class="flex-1 bg-sky-500 hover:bg-sky-600 text-white font-bold py-3.5 rounded-xl shadow-md transition text-sm">
                Commit Changes
            </button>
            <a href="/PawTrack/medical/list.php" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-8 py-3.5 rounded-xl text-sm transition text-center">
                Cancel
            </a>
        </div>
    </form>
</div>

<?php include("../includes/footer.php"); ?>