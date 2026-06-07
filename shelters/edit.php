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

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    die("Missing or invalid record identifier.");
}

// 1. PROCESS DATABASE CHANGES BEFORE ANY HTML IS OUTPUT
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $district = trim($_POST['district']);
    $description = trim($_POST['description']);
    $capacity = (int)$_POST['capacity'];

    $imgResult = pg_query_params($conn, "SELECT image FROM Shelter WHERE shelterid = $1", [$id]);
    $oldData = pg_fetch_assoc($imgResult);
    $imageName = $oldData['image'] ?? "";

    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../assets/images/shelters/";
        
        // Automated fallback check: Create directory programmatically if missing
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $imageName = time() . "_" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $targetDir . $imageName);
    }

    $query = "UPDATE Shelter SET name = $1, address = $2, district = $3, description = $4, capacity = $5, image = $6 WHERE shelterid = $7";
    $result = pg_query_params($conn, $query, [$name, $address, $district, $description, $capacity, $imageName, $id]);

    if ($result) {
        $_SESSION['message'] = "Shelter parameters successfully modified.";
        header("Location: list.php");
        exit;
    }
}

// 2. NOW FETCH ACTIVE PREFILL DATA AND LOAD THE VISUAL LAYOUT
$dataResult = pg_query_params($conn, "SELECT * FROM Shelter WHERE shelterid = $1", [$id]);
$data = pg_fetch_assoc($dataResult);

if (!$data) {
    die("Facility record could not be located.");
}

// Include layout files only when redirects are safely bypassed
include("../includes/header.php");
?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 mb-12">
    <div class="mb-8 border-b border-sky-100 pb-4">
        <h2 class="text-3xl font-black text-slate-900 tracking-tight">Modify Facility Data</h2>
        <p class="text-slate-500 text-sm mt-1">Update operational parameters for the selected shelter hub.</p>
    </div>

    <form method="POST" enctype="multipart/form-data" class="bg-white p-6 sm:p-8 rounded-3xl border border-sky-100/60 shadow-sm space-y-6">
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Facility Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($data['name']); ?>" required class="w-full border border-slate-200 bg-slate-50 px-4 py-3 rounded-xl focus:outline-none focus:border-sky-500 text-sm transition">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Regional District</label>
                <input type="text" name="district" value="<?php echo htmlspecialchars($data['district']); ?>" required class="w-full border border-slate-200 bg-slate-50 px-4 py-3 rounded-xl focus:outline-none focus:border-sky-500 text-sm transition">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Maximum Capacity</label>
                <input type="number" name="capacity" value="<?php echo htmlspecialchars($data['capacity']); ?>" required class="w-full border border-slate-200 bg-slate-50 px-4 py-3 rounded-xl focus:outline-none focus:border-sky-500 text-sm font-mono transition">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Physical Address</label>
            <input type="text" name="address" value="<?php echo htmlspecialchars($data['address']); ?>" required class="w-full border border-slate-200 bg-slate-50 px-4 py-3 rounded-xl focus:outline-none focus:border-sky-500 text-sm transition">
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Facility Description</label>
            <textarea name="description" rows="4" class="w-full border border-slate-200 bg-slate-50 px-4 py-3 rounded-xl focus:outline-none focus:border-sky-500 text-sm transition"><?php echo htmlspecialchars($data['description']); ?></textarea>
        </div>

        <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl flex flex-col sm:flex-row items-center gap-6">
            <?php if ($data['image']) { ?>
                <img src="../assets/images/shelters/<?php echo htmlspecialchars($data['image']); ?>" class="w-24 h-24 object-cover rounded-lg border border-slate-200 shadow-sm">
            <?php } ?>
            <div class="flex-1 w-full">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Update Photograph</label>
                <input type="file" name="image" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-sky-100 file:text-sky-700 hover:file:bg-sky-200 transition">
            </div>
        </div>

        <div class="flex gap-3 pt-4 border-t border-slate-100">
            <button type="submit" class="flex-1 bg-sky-500 hover:bg-sky-600 text-white font-bold py-3.5 rounded-xl shadow-md transition text-sm">
                Commit Modifications
            </button>
            <a href="list.php" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-8 py-3.5 rounded-xl text-sm transition text-center">
                Cancel
            </a>
        </div>
    </form>
</div>

<?php include("../includes/footer.php"); ?>