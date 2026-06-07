<?php
include("../config/db.php");
include("../includes/header.php");

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
    die("<div class='p-8 text-center text-rose-600 font-bold'>Missing or invalid record parameter calculation parameters.</div>");
}

$query = "
    SELECT vr.*, 
           c.name AS cat_name, c.breed, c.image AS cat_image,
           v.vaccinename, v.description AS vaccine_desc, v.recagemonth
    FROM Vaccination_Record vr
    JOIN Cat c ON vr.catid = c.catid
    JOIN Vaccination v ON vr.vaccineid = v.vaccineid
    WHERE vr.vacrecordid = $1
";
$result = pg_query_params($conn, $query, [$id]);
$record = pg_fetch_assoc($result);

if (!$record) {
    die("<div class='p-8 text-center text-rose-600 font-bold'>Vaccination record could not be found.</div>");
}
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 mb-12">
    <div class="mb-6 border-b border-sky-100 pb-4">
        <a href="/PawTrack/vaccination/list.php" class="text-sm font-bold text-slate-500 hover:text-sky-700 transition flex items-center gap-2 mb-3">
            ← Back to Vaccination Registry
        </a>
        <h2 class="text-3xl font-black text-slate-900 tracking-tight">Vaccination Log Summary</h2>
        <p class="text-slate-500 text-sm mt-1">Review metrics regarding the administered vaccination entry.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl border border-sky-100 p-6 sm:p-8 shadow-sm space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">
                    <div>
                        <p class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Administered Vaccine Type</p>
                        <p class="font-bold text-slate-800 text-base mt-0.5"><?php echo htmlspecialchars($record['vaccinename']); ?></p>
                    </div>
                    <div>
                        <p class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Recommended Age</p>
                        <p class="font-bold text-slate-800 mt-0.5"><?php echo htmlspecialchars($record['recagemonth']); ?> Months old</p>
                    </div>
                    <div>
                        <p class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Administration Timestamp</p>
                        <p class="font-bold text-slate-700 mt-0.5">📅 <?php echo date("d M Y", strtotime($record['date'])); ?></p>
                    </div>
                    <div>
                        <p class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Dosage Cost Allocation</p>
                        <p class="font-black text-rose-600 text-base mt-0.5">RM <?php echo number_format($record['cost'], 2); ?></p>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <p class="text-slate-400 font-semibold uppercase tracking-wider text-[10px] mb-1">Vaccine Functional Purpose</p>
                    <p class="text-slate-600 text-sm leading-relaxed italic">
                        <?php echo htmlspecialchars($record['vaccine_desc'] ?: 'No primary description contexts recorded for this parameter.'); ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-3xl border border-sky-100 shadow-sm overflow-hidden">
                <div class="h-40 bg-sky-50 relative">
                    <?php if ($record['cat_image']) { ?>
                        <img src="/PawTrack/assets/images/cats/<?php echo htmlspecialchars($record['cat_image']); ?>" class="w-full h-full object-cover" alt="Patient Image">
                    <?php } else { ?>
                        <div class="flex items-center justify-center h-full text-sky-300 font-bold text-xs uppercase tracking-widest">No Image</div>
                    <?php } ?>
                </div>
                <div class="p-5">
                    <h3 class="text-[10px] font-bold text-sky-600 uppercase tracking-wider">Cat Patient</h3>
                    <p class="text-xl font-black text-slate-900 mt-0.5"><?php echo htmlspecialchars($record['cat_name']); ?></p>
                    <p class="text-xs font-semibold text-slate-500 mb-4"><?php echo htmlspecialchars($record['breed']); ?></p>
                    <a href="/PawTrack/cats/view.php?id=<?php echo $record['catid']; ?>" class="text-xs font-bold text-sky-600 hover:underline">View Complete Profile &rarr;</a>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-4 border border-sky-100 shadow-sm flex flex-col gap-2">
                <a href="edit.php?id=<?php echo $record['vacrecordid']; ?>" class="block text-center bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2.5 rounded-xl text-xs shadow-sm transition">
                    Modify Sheet
                </a>
                <a href="list.php" class="block text-center bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 rounded-xl text-xs transition">
                    Close Sheet
                </a>
            </div>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>