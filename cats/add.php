<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("../config/db.php");
include("../includes/header.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $breed = trim($_POST['breed']);
    $birthdate = $_POST['birthdate'];
    $agecategory = $_POST['agecategory'];
    $gender = $_POST['gender'];
    $description = trim($_POST['description']);
    $shelterid = $_POST['shelterid'];

    $imageName = "";
    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $target = "../assets/images/cats/" . $imageName;
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    }

    $query = "INSERT INTO Cat (ShelterID, Name, Breed, BirthDate, AgeCategory, Gender, Description, Image, Status) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, 'Available')";
    $result = pg_query_params($conn, $query, [$shelterid, $name, $breed, $birthdate ?: null, $agecategory, $gender, $description, $imageName]);

    if ($result) { 
        header("Location: list.php");
        exit;
    }
}
$shelters = pg_query($conn, "SELECT shelterid, name FROM Shelter ORDER BY name");
?>

<div class="max-w-2xl mx-auto px-4 mt-2">
    <div class="mb-6">
        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Register New Feline</h2>
        <p class="text-slate-800 font-semibold text-sm">Provision a new rescue case profile file into the municipal management grid node.</p>
    </div>

    <form method="POST" enctype="multipart/form-data" class="bg-white p-8 rounded-3xl border border-sky-100 shadow-sm space-y-5 text-xs font-bold text-slate-500 uppercase tracking-wider">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block mb-1.5">Feline Given Name</label>
                <input type="text" name="name" required class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm font-medium text-slate-800 tracking-normal normal-case">
            </div>
            <div>
                <label class="block mb-1.5">Breed / Genetic Mix</label>
                <input type="text" name="breed" required class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm font-medium text-slate-800 tracking-normal normal-case">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block mb-1.5">Estimated Birthdate</label>
                <input type="date" name="birthdate" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm font-medium text-slate-800 tracking-normal">
            </div>
            <div>
                <label class="block mb-1.5">Age Classification</label>
                <select name="agecategory" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm font-semibold text-slate-700">
                    <option value="Kitten">Kitten</option>
                    <option value="Juvenile">Juvenile</option>
                    <option value="Adult">Adult</option>
                    <option value="Senior">Senior</option>
                    <option value="Unknown">Unknown</option>
                </select>
            </div>
            <div>
                <label class="block mb-1.5">Gender Sex</label>
                <select name="gender" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm font-semibold text-slate-700">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block mb-1.5">Assigned Target Shelter</label>
            <select name="shelterid" required class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm font-semibold text-slate-700">
                <?php while ($shelter = pg_fetch_assoc($shelters)) { ?>
                    <option value="<?php echo $shelter['shelterid']; ?>"><?php echo htmlspecialchars($shelter['name']); ?></option>
                <?php } ?>
            </select>
        </div>

        <div>
            <label class="block mb-1.5">Case History Background Description</label>
            <textarea name="description" rows="4" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm font-medium text-slate-800 tracking-normal normal-case"></textarea>
        </div>

        <div>
            <label class="block mb-1.5">Profile Photo File Attachment</label>
            <input type="file" name="image" class="w-full text-slate-600 font-medium text-sm mt-1">
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-sky-500 hover:bg-sky-600 text-white font-bold px-6 py-3 rounded-xl text-sm transition shadow-sm">
                Commit Entry Registry
            </button>
            <a href="list.php" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-6 py-3 rounded-xl text-sm transition text-center">
                Cancel
            </a>
        </div>
    </form>
</div>

<?php include("../includes/footer.php"); ?>