<?php
include("../config/db.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$role = $_SESSION['role'] ?? null;
if (!$role || ($role != "Admin" && $role != "Manager")) {
    header("Location: /PawTrack/auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shelterid = $_POST['shelterid'];
    $cagenumber = trim($_POST['cagenumber']);
    $section = trim($_POST['section']);
    $capacity = (int)$_POST['capacity'];

    $query = "INSERT INTO Cage (ShelterID, CageNumber, Section, Capacity) VALUES ($1, $2, $3, $4)";
    $result = pg_query_params($conn, $query, [$shelterid, $cagenumber, $section, $capacity]);

    if ($result) {
        header("Location: list.php");
        exit;
    }
}

$shelters = pg_query($conn, "SELECT shelterid, name FROM Shelter ORDER BY name");
include("../includes/header.php");
?>

<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 mb-12">
    <div class="mb-8 border-b pb-4">
        <h2 class="text-3xl font-black text-slate-900 tracking-tight">Register Unit Cage Space</h2>
        <p class="text-slate-500 text-sm mt-1">Log internal housing assets into the selected shelter infrastructure network.</p>
    </div>

    <form method="POST" class="bg-white p-6 sm:p-8 rounded-3xl border border-sky-100 shadow-sm space-y-6 text-xs font-bold text-slate-500 uppercase tracking-wide">
        <div>
            <label class="block mb-1.5">Shelter Location Link</label>
            <select name="shelterid" required class="w-full border border-slate-200 bg-slate-50 px-4 py-3 rounded-xl text-slate-700 text-sm font-medium focus:outline-none focus:border-sky-500 normal-case">
                <?php while ($s = pg_fetch_assoc($shelters)) { ?>
                    <option value="<?php echo $s['shelterid']; ?>"><?php echo htmlspecialchars($s['name']); ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="sm:col-span-2">
                <label class="block mb-1.5">Cage Number / Identifier Code</label>
                <input type="text" name="cagenumber" required placeholder="e.g. C-101, ICU-04" class="w-full border border-slate-200 bg-slate-50 px-4 py-3 rounded-xl text-slate-700 text-sm font-medium focus:outline-none focus:border-sky-500 normal-case">
            </div>
            <div>
                <label class="block mb-1.5">Maximum Capacity</label>
                <input type="number" name="capacity" min="1" required placeholder="1" class="w-full border border-slate-200 bg-slate-50 px-4 py-3 rounded-xl text-slate-700 text-sm font-mono focus:outline-none focus:border-sky-500">
            </div>
        </div>

        <div>
            <label class="block mb-1.5">Functional Room Section Block</label>
            <select name="section" required class="w-full border border-slate-200 bg-slate-50 px-4 py-3 rounded-xl text-slate-700 text-sm font-medium focus:outline-none focus:border-sky-500 normal-case">
                <option value="General Population">General Population</option>
                <option value="Quarantine Room">Quarantine Room</option>
                <option value="Intensive Care Unit (ICU)">Intensive Care Unit (ICU)</option>
                <option value="Isolation Ward">Isolation Ward</option>
            </select>
        </div>

        <div class="flex gap-3 pt-4 border-t">
            <button type="submit" class="flex-1 bg-sky-500 hover:bg-sky-600 text-white font-bold py-3.5 rounded-xl text-sm normal-case">
                Commit Infrastructure Space
            </button>
            <a href="list.php" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-6 py-3.5 rounded-xl text-sm normal-case flex items-center justify-center">Cancel</a>
        </div>
    </form>
</div>

<?php include("../includes/footer.php"); ?>