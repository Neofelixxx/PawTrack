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
    $vaccineid = $_POST['vaccineid'];
    $date = $_POST['date'];
    $cost = $_POST['cost'];

    $query = "
    INSERT INTO Vaccination_Record
    (
        CatID,
        VaccineID,
        Date,
        Cost
    )
    VALUES
    (
        $1, $2, $3, $4
    )
    ";

    $result = pg_query_params(
        $conn,
        $query,
        [
            $catid,
            $vaccineid,
            $date,
            $cost
        ]
    );

    if ($result) {

        $_SESSION['message'] =
            "Vaccination record added successfully.";

        header("Location: /PawTrack/vaccination/list.php");
        exit;
    }
}

$cats = pg_query(
    $conn,
    "SELECT * FROM Cat ORDER BY Name"
);

$vaccines = pg_query(
    $conn,
    "SELECT * FROM Vaccination ORDER BY VaccineName"
);

?>

<h2 class="text-3xl font-bold text-[#0b1f3b] mb-6">
    Add Vaccination Record
</h2>

<form
    method="POST"
    class="bg-white p-6 rounded-2xl shadow-lg"
>

<div class="max-w-2xl mx-auto px-4">
    
    <!-- CONSOLE HEADER BAR -->
    <div class="mb-8 border-b border-sky-100 pb-4 text-center sm:text-left">
        <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Log Immunization Record</h2>
        <p class="text-slate-500 text-sm mt-1">Register feline immunization metrics directly into relational data matrices[cite: 1, 2].</p>
    </div>

    <!-- DATA ENTRY CARD CONTAINER -->
    <form method="POST" class="bg-white p-6 sm:p-8 rounded-3xl border border-sky-100/60 shadow-xl space-y-5">
        
        <!-- SELECTION DUAL ROW -->
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
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Vaccine Variant</label>
                <select name="vaccineid" required class="w-full border border-sky-100 bg-slate-50/50 px-4 py-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-sm transition">
                    <?php while ($v = pg_fetch_assoc($vaccines)) { ?>
                        <option value="<?php echo $v['vaccineid']; ?>">🛡️ <?php echo $v['vaccinename']; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <!-- DATE & MATERIAL COST ROW -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Administration Date</label>
                <input type="date" name="date" required class="w-full border border-sky-100 bg-slate-50/50 px-4 py-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-sm transition">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Batch Cost (RM)</label>
                <input type="number" step="0.01" name="cost" placeholder="0.00" required
                       class="w-full border border-sky-100 bg-slate-50/50 px-4 py-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-sm transition font-mono">
            </div>
        </div>

        <!-- SUBMIT ACTION CONTROLS -->
        <div class="flex gap-3 pt-4 border-t border-slate-50">
            <button type="submit" class="flex-1 bg-sky-500 hover:bg-sky-600 text-white font-semibold py-3 rounded-xl shadow-md hover:shadow-lg transition duration-200 text-sm">
                Save Immunization Log
            </button>
            <a href="/PawTrack/vaccination/list.php" class="border border-slate-200 hover:bg-slate-50 text-slate-600 font-semibold px-5 py-3 rounded-xl text-sm transition duration-200 text-center">
                Cancel
            </a>
        </div>
    </form>
</div>

<?php include("../includes/footer.php"); ?>