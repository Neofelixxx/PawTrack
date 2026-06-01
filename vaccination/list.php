<?php
include("../config/db.php");
include("../includes/header.php");
$role = $_SESSION['role'] ?? null;

if (!$role || ($role != "Admin" && $role != "Staff")) {
    header("Location: /PawTrack/auth/login.php");
    exit;
}

$query = "
SELECT
    vr.*,
    c.Name AS CatName,
    v.VaccineName
FROM Vaccination_Record vr
JOIN Cat c ON vr.CatID = c.CatID
JOIN Vaccination v ON vr.VaccineID = v.VaccineID
ORDER BY vr.Date DESC
";
$result = pg_query($conn, $query);
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- REGISTRY HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 border-b border-sky-100 pb-5">
        <div>
            <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Vaccination Compliance Registry</h2>
            <p class="text-slate-500 text-sm mt-1">Immunization matrix logs tracking active viral coverage and shelter defense metrics[cite: 1, 2].</p>
        </div>
        <a href="/PawTrack/vaccination/add.php" 
           class="bg-sky-500 hover:bg-sky-600 text-white font-semibold px-5 py-2.5 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 text-sm flex items-center gap-2 self-start sm:self-center">
            <span class="text-base">＋</span> Log Immunization
        </a>
    </div>

    <!-- HIGH-FIDELITY IMMUNIZATION TABLE WRAPPER -->
    <div class="bg-white rounded-3xl border border-sky-100/60 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="bg-sky-50/70 border-b border-sky-100/60 text-xs font-bold uppercase tracking-wider text-sky-800">
                        <th class="p-4 pl-6">Feline Patient</th>
                        <th class="p-4">Administered Vaccine</th>
                        <th class="p-4">Batch Cost</th>
                        <th class="p-4 pr-6 text-right">Immunization Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                    <?php while ($row = pg_fetch_assoc($result)) { ?>
                        <tr class="hover:bg-sky-50/30 transition duration-150">
                            <!-- Cat Name -->
                            <td class="p-4 pl-6 font-bold text-slate-800">
                                🐈 <?php echo $row['catname']; ?>
                            </td>
                            <!-- Vaccine Type Badge -->
                            <td class="p-4">
                                <span class="bg-sky-50 text-sky-700 text-xs font-bold px-2.5 py-1 rounded-lg border border-sky-100/40 font-mono">
                                    🛡️ <?php echo $row['vaccinename']; ?>
                                </span>
                            </td>
                            <!-- Cost -->
                            <td class="p-4 font-mono font-semibold text-slate-600">
                                RM <?php echo number_format($row['cost'], 2); ?>
                            </td>
                            <!-- Date -->
                            <td class="p-4 pr-6 text-right font-medium text-slate-400 text-xs">
                                📅 <?php echo date("d M Y", strtotime($row['date'])); ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- EMPTY STATE RECOVERY CONTROLLER -->
        <?php if (pg_num_rows($result) == 0) { ?>
            <div class="p-12 flex flex-col items-center justify-center text-slate-400 bg-slate-50/20 gap-2">
                <span class="text-4xl">💉</span>
                <p class="text-sm font-medium">No immunization records logged in the active database yet[cite: 2].</p>
            </div>
        <?php } ?>
    </div>
</div>

<?php include("../includes/footer.php"); ?>