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
    
    // Captured the new trait parameters sent from your extended form elements
    $color = trim($_POST['color']);
    $pattern = trim($_POST['pattern']);
    $eye_color = trim($_POST['eye_color']);
    $special_remarks = trim($_POST['special_remarks']);

    $imageName = "";
    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $target = "../assets/images/cats/" . $imageName;
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    }

    // Expanded SQL insert string map to account for parameters $9 through $12
    $query = "INSERT INTO Cat (ShelterID, Name, Breed, BirthDate, AgeCategory, Gender, Description, Image, Status, color, pattern, eye_color, special_remarks) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, 'Available', $9, $10, $11, $12)";
    $result = pg_query_params($conn, $query, [
        $shelterid, $name, $breed, $birthdate ?: null, $agecategory, $gender, $description, $imageName,
        $color ?: null, $pattern ?: null, $eye_color ?: null, $special_remarks ?: null
    ]);

    if ($result) { 
        header("Location: list.php");
        exit;
    }
}
$shelters = pg_query($conn, "SELECT shelterid, name FROM Shelter ORDER BY name");
?>

<div class="max-w-2xl mx-auto px-4 mt-2">
    <div class="mb-6">
        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Register New Cat</h2>
        <p class="text-slate-800 font-semibold text-sm">Add a new rescue case profile to the system management network.</p>
    </div>

    <form method="POST" enctype="multipart/form-data" class="bg-white p-8 rounded-3xl border border-sky-100 shadow-sm space-y-5 text-xs font-bold text-slate-500 uppercase tracking-wider">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block mb-1.5">Cat Given Name</label>
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
                <label class="block mb-1.5">Gender</label>
                <select name="gender" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm font-semibold text-slate-700">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
        </div>

        <!-- NEWLY INJECTED PHYSICAL CHARACTERISTICS INPUT ELEMENT ROW -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-sky-50/40 p-4 rounded-2xl border border-sky-100">
            <div class="sm:col-span-3 flex justify-between items-center pb-1 border-b border-sky-100/60">
                <span class="text-sky-700 tracking-wide">Automated Image Features Selection</span>
                <span id="scan_loading_status" class="text-[10px] tracking-normal text-slate-400 italic font-medium normal-case">Select image below to run auto-fill scanner...</span>
            </div>
            <div>
                <label class="block mb-1.5 text-sky-800">Fur Color</label>
                <input type="text" name="color" id="color_form_field" placeholder="Pending scan..." class="w-full bg-white border border-slate-200 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm font-medium text-slate-800 tracking-normal normal-case">
            </div>
            <div>
                <label class="block mb-1.5 text-sky-800">Coat Pattern</label>
                <input type="text" name="pattern" id="pattern_form_field" placeholder="Pending scan..." class="w-full bg-white border border-slate-200 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm font-medium text-slate-800 tracking-normal normal-case">
            </div>
            <div>
                <label class="block mb-1.5 text-sky-800">Eye Color</label>
                <input type="text" name="eye_color" id="eye_color_form_field" placeholder="Pending scan..." class="w-full bg-white border border-slate-200 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm font-medium text-slate-800 tracking-normal normal-case">
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
            <label class="block mb-1.5">Background Description</label>
            <textarea name="description" rows="3" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm font-medium text-slate-800 tracking-normal normal-case"></textarea>
        </div>

        <!-- NEWLY INJECTED TEXTAREA ELEMENT FOR SPECIAL REMARKS / TRICKS -->
        <div>
            <label class="block mb-1.5">Special Behavior Remarks</label>
            <textarea name="special_remarks" rows="2" placeholder="List any learned tricks, friendly habits, unique house rules behavior..." class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm font-medium text-slate-800 tracking-normal normal-case"></textarea>
        </div>

        <div>
            <label class="block mb-1.5">Profile Photo File Attachment</label>
            <input type="file" name="image" id="cat_image_input" required class="w-full text-slate-600 font-medium text-sm mt-1">
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-sky-500 hover:bg-sky-600 text-white font-bold px-6 py-3 rounded-xl text-sm transition shadow-sm">
                Confirm Registration
            </button>
            <a href="list.php" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-6 py-3 rounded-xl text-sm transition text-center">
                Cancel
            </a>
        </div>
    </form>
</div>

<!-- BACKGROUND AUTOMATED COAT FEATURES ANALYZER ASYNC UTILITY SCRIPT -->
<script>
document.getElementById('cat_image_input').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (!file) return;

    const notificationContainer = document.getElementById('scan_loading_status');
    if (notificationContainer) {
        notificationContainer.classList.remove('text-slate-400');
        notificationContainer.classList.add('text-sky-600');
        notificationContainer.textContent = "Scanning coat traits and features...";
    }

    const formData = new FormData();
    formData.append('cat_photo', file);

    fetch('analyze_cat.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('color_form_field').value = data.color;
            document.getElementById('pattern_form_field').value = data.pattern;
            document.getElementById('eye_color_form_field').value = data.eye_color;
            
            if (notificationContainer) {
                notificationContainer.classList.remove('text-sky-600');
                notificationContainer.classList.add('text-emerald-600');
                notificationContainer.textContent = "Traits matched successfully!";
            }
        } else {
            console.error("Vision API exception:", data.error);
            if (notificationContainer) {
                notificationContainer.classList.remove('text-sky-600');
                notificationContainer.classList.add('text-amber-600');
                notificationContainer.textContent = "Auto-scan failed. Input traits manually.";
            }
        }
    })
    .catch(error => {
        console.error("Network loop handler exception:", error);
        if (notificationContainer) {
            notificationContainer.textContent = "Scanning engine error.";
        }
    });
});
</script>

<?php include("../includes/footer.php"); ?>