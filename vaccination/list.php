<?php
include("../config/db.php");
include("../includes/header.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['role'] ?? null;

// Only operational management can view ledgers
if (!$role || ($role != "Admin" && $role != "Staff" && $role != "Manager")) {
    header("Location: /PawTrack/auth/login.php");
    exit;
}

$query = "
SELECT
    vr.*,
    c.Name AS CatName,
    v.VaccineName,
    v.Description
FROM Vaccination_Record vr
JOIN Cat c ON vr.CatID = c.CatID
JOIN Vaccination v ON vr.VaccineID = v.VaccineID
ORDER BY vr.Date DESC
";
$result = pg_query($conn, $query);
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 mb-12">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 border-b border-sky-100 pb-5">
        <div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">Vaccination Registry</h2>
            <p class="text-slate-500 text-sm mt-2">Track preventative health records and core vaccination costs.</p>
        </div>
        <a href="/PawTrack/vaccination/add.php" 
           class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-5 py-3 rounded-xl shadow-md hover:-translate-y-0.5 transition-all duration-200 text-sm flex items-center gap-2 self-start sm:self-center">
            <span class="text-base">＋</span> Log Vaccination
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-sky-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-sky-100 text-xs font-bold uppercase tracking-wider text-slate-600">
                        <th class="p-4 pl-6">Patient Cat</th>
                        <th class="p-4">Vaccine Type</th>
                        <th class="p-4 hidden sm:table-cell">Details</th>
                        <th class="p-4 font-mono">Cost</th>
                        <th class="p-4">Date Administered</th>
                        <th class="p-4 pr-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                    <?php while ($row = pg_fetch_assoc($result)) { ?>
                        <tr class="hover:bg-slate-50/50 transition duration-150">
                            <td class="p-4 pl-6 font-bold text-slate-800 whitespace-nowrap">
                                🐱 <?php echo htmlspecialchars($row['catname']); ?>
                            </td>
                            <td class="p-4 font-medium text-slate-700 whitespace-nowrap">
                                <span class="bg-emerald-50 text-emerald-700 text-xs font-bold px-2.5 py-1 rounded-lg border border-emerald-100/40">
                                    <?php echo htmlspecialchars($row['vaccinename']); ?>
                                </span>
                            </td>
                            <td class="p-4 text-slate-500 italic hidden sm:table-cell">
                                <?php echo htmlspecialchars($row['description']); ?>
                            </td>
                            <td class="p-4 font-mono font-bold text-rose-600 whitespace-nowrap">
                                RM <?php echo number_format($row['cost'], 2); ?>
                            </td>
                            <td class="p-4 font-medium text-slate-500 text-xs whitespace-nowrap">
                                📅 <?php echo date("d M Y", strtotime($row['date'])); ?>
                            </td>
                            <td class="p-4 pr-6 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="view.php?id=<?php echo $row['vacrecordid']; ?>" 
                                       class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-3 py-1.5 rounded-xl text-xs transition">
                                        View
                                    </a>
                                    <a href="edit.php?id=<?php echo $row['vacrecordid']; ?>" 
                                       class="border border-sky-200 hover:bg-sky-50 text-sky-600 font-bold px-3 py-1.5 rounded-xl text-xs transition">
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <?php if (pg_num_rows($result) == 0) { ?>
            <div class="p-12 flex flex-col items-center justify-center text-slate-400 bg-slate-50/20 gap-2">
                <span class="text-4xl">💉</span>
                <p class="text-sm font-medium">No vaccination records have been filed yet.</p>
            </div>
        <?php } ?>
    </div>
</div>

<?php include("../includes/footer.php"); ?>