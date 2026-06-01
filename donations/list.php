<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("../config/db.php");
include("../includes/header.php");

/* 1. CALCULATE ROUNDED SUMMARY TOTALS */
$summary_funds = pg_fetch_assoc(pg_query($conn, 'SELECT CEIL(COALESCE(SUM(Amount), 0)) AS overall FROM "Donations" WHERE Type = \'Money\''));
$summary_items = pg_fetch_assoc(pg_query($conn, 'SELECT COALESCE(SUM(Quantity), 0) AS units FROM "Donations" WHERE Type = \'Item\''));

/* 2. TOP 5 CONTRIBUTORS (REGISTERED SYSTEM USERS ONLY) */
$top_query = "
    SELECT u.Name, SUM(d.Amount) as total_funded 
    FROM \"Donations\" d
    JOIN \"User\" u ON d.UserID = u.UserID
    WHERE d.Type = 'Money'
    GROUP BY u.Name
    ORDER BY total_funded DESC
    LIMIT 5
";
$top_result = pg_query($conn, $top_query);

/* 3. FETCH TRANSACTION LOG LISTING */
$ledger_query = "
    SELECT d.*, s.Name AS ShelterName
    FROM \"Donations\" d
    JOIN Shelter s ON d.ShelterID = s.ShelterID
    ORDER BY d.DonationDate DESC
";
$ledger_result = pg_query($conn, $ledger_query);
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-2">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 border-b border-sky-100 pb-5">
        <div>
            <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">Funding & Supplies Ledger</h2>
            <p class="text-slate-500 text-sm mt-1">Audit log tracking registered users and public anonymous support streams.</p>
        </div>
        <a href="/PawTrack/donations/add.php" class="bg-sky-500 hover:bg-sky-600 text-white font-semibold px-5 py-2.5 rounded-xl shadow-md transition text-sm flex items-center gap-2 self-start sm:self-center">
            <span>＋</span> Add Donation
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
        <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-3xl border border-sky-100/70 shadow-sm flex items-center gap-4">
                <div class="w-14 h-14 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center text-2xl font-bold">💰</div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Funding (Rounded Up)</p>
                    <h3 class="text-2xl font-black text-slate-800 mt-1">RM <?php echo number_format($summary_funds['overall']); ?></h3>
                </div>
            </div>
            <div class="bg-white p-6 rounded-3xl border border-sky-100/70 shadow-sm flex items-center gap-4">
                <div class="w-14 h-14 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center text-2xl font-bold">📦</div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Supplies Contributed</p>
                    <h3 class="text-2xl font-black text-slate-800 mt-1"><?php echo $summary_items['units']; ?> Supply Units</h3>
                </div>
            </div>
        </div>

        <div class="bg-slate-900 text-white p-5 rounded-3xl shadow-sm flex flex-col justify-between">
            <div>
                <h4 class="text-xs font-bold text-sky-400 uppercase tracking-widest mb-3">⭐ Top 5 Registered Contributors</h4>
                <ol class="space-y-2 text-xs font-medium">
                    <?php 
                    $rank = 1;
                    while($top = pg_fetch_assoc($top_result)) { ?>
                        <li class="flex justify-between items-center bg-white/5 px-3 py-1.5 rounded-lg border border-white/5">
                            <span><?php echo $rank++ . ". " . htmlspecialchars($top['name']); ?></span>
                            <span class="font-mono text-emerald-400 font-bold">RM <?php echo number_format($top['total_funded'], 2); ?></span>
                        </li>
                    <?php } 
                    if($rank === 1) { echo "<p class='text-slate-400 italic text-[11px]'>No verified registered backers yet.</p>"; }
                    ?>
                </ol>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-sky-100/60 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="bg-sky-50/70 border-b border-sky-100/60 text-xs font-bold uppercase tracking-wider text-sky-800">
                        <th class="p-4 pl-6">Donor Identity</th>
                        <th class="p-4">Type</th>
                        <th class="p-4">Description / Value</th>
                        <th class="p-4">Allocated Shelter Hub</th>
                        <th class="p-4 pr-6 text-right">Filing Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                    <?php while ($row = pg_fetch_assoc($ledger_result)) { ?>
                        <tr class="hover:bg-sky-50/30 transition duration-150">
                            <td class="p-4 pl-6 font-semibold text-slate-800">
                                👤 <?php echo htmlspecialchars($row['donorname']); ?>
                            </td>
                            <td class="p-4">
                                <span class="<?php echo $row['type'] == 'Money' ? 'bg-emerald-50 text-emerald-700 border-emerald-100/50' : 'bg-amber-50 text-amber-700 border-amber-100/50'; ?> text-xs font-bold px-2.5 py-1 rounded-lg border">
                                    <?php echo $row['type'] == 'Money' ? 'Financial' : 'Material Item'; ?>
                                </span>
                            </td>
                            <td class="p-4 font-mono text-xs font-semibold">
                                <?php
                                if ($row['type'] == 'Money') {
                                    echo "<span class='text-emerald-600 text-sm font-bold font-sans'>RM " . number_format($row['amount'], 2) . "</span>";
                                } else {
                                    echo "<span class='text-slate-700 font-sans text-sm'>" . htmlspecialchars($row['itemdescription']) . "</span> <span class='text-slate-400 font-normal ml-1'>(x" . $row['quantity'] . ")</span>";
                                }
                                ?>
                            </td>
                            <td class="p-4 font-medium text-slate-700">
                                🏢 <?php echo htmlspecialchars($row['sheltername']); ?>
                            </td>
                            <td class="p-4 pr-6 text-right font-medium text-slate-400 text-xs">
                                📅 <?php echo date("d M Y", strtotime($row['donationdate'])); ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        
        <?php if (pg_num_rows($ledger_result) == 0) { ?>
            <div class="p-12 flex flex-col items-center justify-center text-slate-400 bg-slate-50/20 gap-2">
                <span class="text-4xl">📋</span>
                <p class="text-sm font-medium">No records registered inside the database instance yet.</p>
            </div>
        <?php } ?>
    </div>
</div>

<?php include("../includes/footer.php"); ?>