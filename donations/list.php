<?php
include("../config/db.php");
include("../includes/header.php");
$role = $_SESSION['role'] ?? null; 

$query = "
SELECT
d.*,
s.Name AS ShelterName
FROM Donations d
JOIN Shelter s
ON d.ShelterID = s.ShelterID
ORDER BY d.DonationDate DESC
";
$result = pg_query($conn, $query);
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- LEDGER HEADER BAR -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 border-b border-sky-100 pb-5">
        <div>
            <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Funding & Supplies Ledger</h2>
            <p class="text-slate-500 text-sm mt-1">Audit trail tracking community contributions and supply allocations across district hubs[cite: 1, 2].</p>
        </div>
        <a href="/PawTrack/donation/add.php" 
           class="bg-sky-500 hover:bg-sky-600 text-white font-semibold px-5 py-2.5 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 text-sm flex items-center gap-2 self-start sm:self-center">
            <span class="text-base">＋</span> Register Contribution
        </a>
    </div>

    <!-- MODERN BALANCED TABLE WRAPPER -->
    <div class="bg-white rounded-3xl border border-sky-100/60 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="bg-sky-50/70 border-b border-sky-100/60 text-xs font-bold uppercase tracking-wider text-sky-800">
                        <th class="p-4 pl-6">Donor Identity</th>
                        <th class="p-4">Contribution Type</th>
                        <th class="p-4">Valuation / Description</th>
                        <th class="p-4">Allocated Hub</th>
                        <th class="p-4 pr-6 text-right">Filing Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                    <?php while ($row = pg_fetch_assoc($result)) { ?>
                        <tr class="hover:bg-sky-50/30 transition duration-150">
                            <!-- Donor Name -->
                            <td class="p-4 pl-6 font-semibold text-slate-800">
                                👤 <?php echo $row['donorname']; ?>
                            </td>
                            <!-- Type Badge -->
                            <td class="p-4">
                                <?php if ($row['type'] == 'Money') { ?>
                                    <span class="bg-emerald-50 text-emerald-700 text-xs font-bold px-2.5 py-1 rounded-lg border border-emerald-100/50">
                                        Financial
                                    </span>
                                <?php } else { ?>
                                    <span class="bg-amber-50 text-amber-700 text-xs font-bold px-2.5 py-1 rounded-lg border border-amber-100/50">
                                        Material Item
                                    </span>
                                <?php } ?>
                            </td>
                            <!-- Valuation Summary -->
                            <td class="p-4 font-mono text-xs font-semibold">
                                <?php
                                if ($row['type'] == 'Money') {
                                    echo "<span class='text-emerald-600 text-sm font-bold font-sans'>RM " . number_format($row['amount'], 2) . "</span>";[cite: 2]
                                } else {
                                    echo "<span class='text-slate-700 font-sans text-sm'>" . $row['itemdescription'] . "</span> <span class='text-slate-400 font-normal ml-1'>(x" . $row['quantity'] . ")</span>";[cite: 2]
                                }
                                ?>
                            </td>
                            <!-- Shelter Hub -->
                            <td class="p-4 font-medium text-slate-700">
                                🏢 <?php echo $row['sheltername'];[cite: 2] ?>
                            </td>
                            <!-- Date Filed -->
                            <td class="p-4 pr-6 text-right font-medium text-slate-400 text-xs">
                                📅 <?php echo date("d M Y", strtotime($row['donationdate'])); ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        
        <!-- EMPTY STATE FALLBACK (If ledger rows are blank) -->
        <?php if (pg_num_rows($result) == 0) { ?>
            <div class="p-12 flex flex-col items-center justify-center text-slate-400 bg-slate-50/20 gap-2">
                <span class="text-4xl">📋</span>
                <p class="text-sm font-medium">No donation records registered in the system yet[cite: 2].</p>
            </div>
        <?php } ?>
    </div>
</div>

<?php include("../includes/footer.php"); ?>