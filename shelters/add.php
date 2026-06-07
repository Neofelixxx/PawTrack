<?php
include("../config/db.php");
include("../includes/header.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['role'] ?? null;
if (!$role || ($role != "Admin" && $role != "Manager")) {
    header("Location: /PawTrack/auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $district = $_POST['district'];
    $description = $_POST['description'];
    $capacity = $_POST['capacity'];
    $imageName = "";

    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "../assets/images/shelters/" . $imageName);
    }

    $query = "INSERT INTO Shelter (Name, Address, District, Description, Capacity, Image) VALUES ($1, $2, $3, $4, $5, $6)";
    $result = pg_query_params($conn, $query, [$name, $address, $district, $description, $capacity, $imageName]);

    if ($result) {
        $_SESSION['message'] = "Shelter node registered successfully.";
        header("Location: list.php");
        exit;
    }
}
?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 mb-12">
    <div class="mb-8 border-b border-sky-100 pb-4">
        <h2 class="text-3xl font-black text-slate-900 tracking-tight">Register Shelter Hub</h2>
        <p class="text-slate-500 text-sm mt-1">Allocate new operational facilities into the system registry.</p>
    </div>

    <form method="POST" enctype="multipart/form-data" class="bg-white p-6 sm:p-8 rounded-3xl border border-sky-100/60 shadow-sm space-y-6">
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Facility Name</label>
            <input type="text" name="name" required class="w-full border border-slate-200 bg-slate-50 px-4 py-3 rounded-xl focus:outline-none focus:border-sky-500 text-sm transition">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Regional District</label>
                <input type="text" name="district" required class="w-full border border-slate-200 bg-slate-50 px-4 py-3 rounded-xl focus:outline-none focus:border-sky-500 text-sm transition">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Maximum Capacity</label>
                <input type="number" name="capacity" required class="w-full border border-slate-200 bg-slate-50 px-4 py-3 rounded-xl focus:outline-none focus:border-sky-500 text-sm font-mono transition">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Physical Address</label>
            <input type="text" name="address" required class="w-full border border-slate-200 bg-slate-50 px-4 py-3 rounded-xl focus:outline-none focus:border-sky-500 text-sm transition">
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Facility Description</label>
            <textarea name="description" rows="4" class="w-full border border-slate-200 bg-slate-50 px-4 py-3 rounded-xl focus:outline-none focus:border-sky-500 text-sm transition"></textarea>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Facility Photograph</label>
            <input type="file" name="image" class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100 transition">
        </div>

        <div class="flex gap-3 pt-4 border-t border-slate-100">
            <button type="submit" class="flex-1 bg-sky-500 hover:bg-sky-600 text-white font-bold py-3.5 rounded-xl shadow-md transition text-sm">
                Commit Registration
            </button>
            <a href="list.php" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-8 py-3.5 rounded-xl text-sm transition text-center">
                Cancel
            </a>
        </div>
    </form>
</div>

<?php include("../includes/footer.php"); ?>