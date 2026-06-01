<?php

include("../config/db.php");
include("../includes/header.php");

$role = $_SESSION['role'] ?? null;

if (!$role || ($role != "Admin" && $role != "Staff")) {

    header("Location: /PawTrack/auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $catid = $_POST['catid'];
    $treatid = $_POST['treatid'];
    $category = $_POST['category'];
    $cost = $_POST['cost'];
    $date = $_POST['date'];
    $notes = $_POST['notes'];

    $query = "
    INSERT INTO Medical_Record
    (
        CatID,
        TreatID,
        Category,
        Cost,
        TreatmentDate,
        Notes
    )
    VALUES
    (
        $1, $2, $3, $4, $5, $6
    )
    ";

    $result = pg_query_params(
        $conn,
        $query,
        [
            $catid,
            $treatid,
            $category,
            $cost,
            $date,
            $notes
        ]
    );

    if ($result) {

        $_SESSION['message'] =
            "Medical record added successfully.";

        header("Location: /PawTrack/medical/list.php");
        exit;
    }
}

$cats = pg_query(
    $conn,
    "SELECT * FROM Cat ORDER BY Name"
);

$treatments = pg_query(
    $conn,
    "SELECT * FROM Treatment ORDER BY TreatName"
);

?>

<h2 class="text-3xl font-bold text-[#0b1f3b] mb-6">
    Add Medical Record
</h2>

<form
    method="POST"
    class="bg-white p-6 rounded-2xl shadow-lg"
>

<div class="max-w-2xl mx-auto px-4">
    
    <!-- CONSOLE HEADER BAR -->
    <div class="mb-8 border-b border-sky-100 pb-4 text-center sm:text-left">
        <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Add Medical Record</h2>
        <p class="text-slate-500 text-sm mt-1">File vital diagnosis data parameters and cost metrics straight into database arrays[cite: 1, 2].</p>
    </div>

    <!-- ENTRY MATRIX PANEL -->
    <form method="POST" class="bg-white p-6 sm:p-8 rounded-3xl border border-sky-100/60 shadow-xl space-y-5">
        
        <!-- ROW 1: SPLIT PATIENT AND TREATMENT FIELDS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Feline Patient</label>
                <select name="catid" required class="w-full border border-sky-100 bg-slate-50/50 px-4 py-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-sm transition">
                    <?php while ($cat = pg_fetch_assoc($cats)) { ?>
                        <option value="<?php echo $cat['catid']; ?>">🐱 <?php echo $cat['name']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Assigned Treatment</label>
                <select name="treatid" required class="w-full border border-sky-100 bg-slate-50/50 px-4 py-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-sm transition">
                    <?php while ($t = pg_fetch_assoc($treatments)) { ?>
                        <option value="<?php echo $t['treatid']; ?>">🩺 <?php echo $t['treatname']; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <!-- ROW 2: CATEGORY TYPE AND OPERATIONAL COST -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Clinical Category</label>
                <input type="text" name="category" placeholder="e.g. Surgery / Vaccination"
                       class="w-full border border-sky-100 bg-slate-50/50 px-4 py-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-sm transition">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Operational Expense (RM)</label>
                <input type="number" step="0.01" name="cost" placeholder="0.00" required
                       class="w-full border border-sky-100 bg-slate-50/50 px-4 py-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-sm transition font-mono">
            </div>
        </div>

        <!-- ROW 3: TREATMENT DATE -->
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Filing Date</label>
            <input type="date" name="date" required class="w-full border border-sky-100 bg-slate-50/50 px-4 py-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-sm transition">
        </div>

        <!-- ROW 4: DIAGNOSTIC CLINICAL NOTES -->
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Clinical Assessment Notes</label>
            <textarea name="notes" rows="4" placeholder="Detail any post-op recovery details, medication protocols, or observations..."
                      class="w-full border border-sky-100 bg-slate-50/50 px-4 py-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-sm transition"></textarea>
        </div>

        <!-- TRANSACTION CONTROLS -->
        <div class="flex gap-3 pt-4 border-t border-slate-50">
            <button type="submit" class="flex-1 bg-sky-500 hover:bg-sky-600 text-white font-semibold py-3 rounded-xl shadow-md hover:shadow-lg transition duration-200 text-sm">
                Save Health Record
            </button>
            <a href="/PawTrack/medical/list.php" class="border border-slate-200 hover:bg-slate-50 text-slate-600 font-semibold px-5 py-3 rounded-xl text-sm transition duration-200 text-center">
                Cancel
            </a>
        </div>
    </form>
</div>

<?php include("../includes/footer.php"); ?>