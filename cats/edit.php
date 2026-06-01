<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("../config/db.php");

$id = $_GET['id'] ?? null;
if (!$id) {
    die("Invalid Cat ID");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $breed = trim($_POST['breed']);
    $birthdate = $_POST['birthdate'];
    $agecategory = $_POST['agecategory'];
    $gender = $_POST['gender'];
    $description = trim($_POST['description']);
    $status = $_POST['status'];
    $shelterid = $_POST['shelterid'];

    $cat_query = pg_query_params($conn, "SELECT image FROM Cat WHERE catid = $1", [$id]);
    $old = pg_fetch_assoc($cat_query);
    $imageName = $old['image'];

    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $target = "../assets/images/cats/" . $imageName;
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    }

    $query = "UPDATE Cat SET name=$1, breed=$2, birthdate=$3, agecategory=$4, gender=$5, description=$6, status=$7, shelterid=$8, image=$9 WHERE catid=$10";
    $result = pg_query_params($conn, $query, [$name, $breed, $birthdate ?: null, $agecategory, $gender, $description, $status, $shelterid, $imageName, $id]);

    if ($result) {
        header("Location: list.php");
        exit;
    }
}

$cat = pg_fetch_assoc(pg_query_params($conn, "SELECT * FROM Cat WHERE catid = $1", [$id]));
$shelters = pg_query($conn, "SELECT shelterid, name FROM Shelter ORDER BY name");
include("../includes/header.php");
?>

<div class="max-w-2xl mx-auto px-4 mt-2">
    <div class="mb-6">
        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Modify Profile File</h2>
        <p class="text-slate-800 font-semibold text-sm">Update telemetry, behavioral conditions, or spatial placement configurations for this feline entry.</p>
    </div>

    <form method="POST" enctype="multipart/form-data" class="bg-white p-8 rounded-3xl border border-sky-100 shadow-sm space-y-5 text-xs font-bold text-slate-500 uppercase tracking-wider">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block mb-1.5">Feline Given Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($cat['name']); ?>" required class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm font-medium text-slate-800 tracking-normal normal-case">
            </div>
            <div>
                <label class="block mb-1.5">Breed / Genetic Mix</label>
                <input type="text" name="breed" value="<?php echo htmlspecialchars($cat['breed']); ?>" required class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm font-medium text-slate-800 tracking-normal normal-case">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block mb-1.5">Estimated Birthdate</label>
                <input type="date" name="birthdate" value="<?php echo $cat['birthdate']; ?>" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm font-medium text-slate-800 tracking-normal">
            </div>
            <div>
                <label class="block mb-1.5">Age Classification</label>
                <select name="agecategory" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm font-semibold text-slate-700">
                    <option value="Kitten" <?php if($cat['agecategory']=='Kitten') echo 'selected'; ?>>Kitten</option>
                    <option value="Juvenile" <?php if($cat['agecategory']=='Juvenile') echo 'selected'; ?>>Juvenile</option>
                    <option value="Adult" <?php if($cat['agecategory']=='Adult') echo 'selected'; ?>>Adult</option>
                    <option value="Senior" <?php if($cat['agecategory']=='Senior') echo 'selected'; ?>>Senior</option>
                    <option value="Unknown" <?php if($cat['agecategory']=='Unknown') echo 'selected'; ?>>Unknown</option>
                </select>
            </div>
            <div>
                <label class="block mb-1.5">Gender Sex</label>
                <select name="gender" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm font-semibold text-slate-700">
                    <option value="Male" <?php if($cat['gender']=='Male') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if($cat['gender']=='Female') echo 'selected'; ?>>Female</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block mb-1.5">Operational Care Status</label>
                <select name="status" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm font-semibold text-slate-700">
                    <?php
                    $statuses = ['Available', 'Adopted', 'Under Treatment', 'Quarantined', 'Deceased', 'Transferred'];
                    foreach ($statuses as $s) {
                        $selected = ($cat['status'] == $s) ? "selected" : "";
                        echo "<option $selected>$s</option>";
                    }
                    ?>
                </select>
            </div>
            <div>
                <label class="block mb-1.5">Housing Operations Base</label>
                <select name="shelterid" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm font-semibold text-slate-700">
                    <?php while ($s = pg_fetch_assoc($shelters)) { ?>
                        <option value="<?php echo $s['shelterid']; ?>" <?php if ($s['shelterid'] == $cat['shelterid']) echo "selected"; ?>><?php echo htmlspecialchars($s['name']); ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div>
            <label class="block mb-1.5">Case History Background Description</label>
            <textarea name="description" rows="4" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm font-medium text-slate-800 tracking-normal normal-case"><?php echo htmlspecialchars($cat['description']); ?></textarea>
        </div>

        <div class="flex items-center gap-4 bg-slate-50 p-3 rounded-2xl border border-slate-200">
            <?php if ($cat['image']) { ?>
                <img src="../assets/images/cats/<?php echo htmlspecialchars($cat['image']); ?>" class="w-20 h-20 object-cover rounded-xl border">
            <?php } ?>
            <div>
                <label class="block mb-1">Replace Photo Asset</label>
                <input type="file" name="image" class="text-slate-600 font-medium text-xs">
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-sky-500 hover:bg-sky-600 text-white font-bold px-6 py-3 rounded-xl text-sm transition shadow-sm">
                Save Parameter Updates
            </button>
            <a href="list.php" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-6 py-3 rounded-xl text-sm transition text-center">
                Cancel
            </a>
        </div>
    </form>
</div>

<?php include("../includes/footer.php"); ?>