<?php
include("../config/db.php");
include("../includes/header.php");

$role = $_SESSION['role'] ?? null;
$userid = $_SESSION['user'] ?? null;

if (!$role) {
    header("Location: /PawTrack/auth/login.php");
    exit;
}

/* |--------------------------------------------------------------------------
| ADOPTION PIPELINE DATA FETCHING
|--------------------------------------------------------------------------
| Admins/Staff see all applications across the district.
| Adopters can only monitor the requests submitted by that specific account.
*/
if ($role == "Admin" || $role == "Staff") {
    $query = "
        SELECT 
            a.adoptionid, a.adoptionfee, a.adoptiondate, a.status,
            c.name AS cat_name, c.breed,
            u1.name AS adopter_name, u1.email AS adopter_email,
            u2.name AS reviewer_name
        FROM Adoption a
        JOIN Cat c ON a.catid = c.catid
        JOIN \"User\" u1 ON a.adopterid = u1.userid
        LEFT JOIN \"User\" u2 ON a.approvedby = u2.userid
        ORDER BY a.adoptionid DESC
    ";
    $result = pg_query($conn, $query);
} else {
    $query = "
        SELECT 
            a.adoptionid, a.adoptionfee, a.adoptiondate, a.status,
            c.name AS cat_name, c.breed,
            u2.name AS reviewer_name
        FROM Adoption a
        JOIN Cat c ON a.catid = c.catid
        LEFT JOIN \"User\" u2 ON a.approvedby = u2.userid
        WHERE a.adopterid = $1
        ORDER BY a.adoptionid DESC
    ";
    $result = pg_query_params($conn, $query, [$userid]);
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 mb-12">
    
    <div class="mb-8 border-b border-sky-100 pb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">Adoption Requests</h2>
            <p class="text-slate-500 text-sm mt-1">
                <?php echo ($role == "Admin" || $role == "Staff") 
                    ? "Review profiles, evaluate qualifications, and process application approvals." 
                    : "Track the status of submitted adoption applications."; ?>
            </p>
        </div>
        <a href="/PawTrack/cats/list.php" 
           class="bg-sky-500 hover:bg-sky-600 text-white font-semibold px-5 py-2.5 rounded-xl shadow-md hover:-translate-y-0.5 transition-all duration-200 text-sm flex items-center gap-2 self-start sm:self-center">
            Browse Available Cats
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-sky-100/60 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="bg-sky-50/70 border-b border-sky-100/60 text-xs font-bold uppercase tracking-wider text-sky-800">
                        <th class="p-4 pl-6">Cat Profile</th>
                        <?php if ($role == "Admin" || $role == "Staff") { ?>
                            <th class="p-4">Applicant</th>
                        <?php } ?>
                        <th class="p-4">Status</th>
                        <th class="p-4">Reviewed By</th>
                        <th class="p-4 pr-6 <?php echo ($role == "Admin" || $role == "Staff") ? 'text-center' : 'text-right'; ?>">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                    <?php while ($row = pg_fetch_assoc($result)) { ?>
                        <tr class="hover:bg-sky-50/30 transition duration-150">
                            
                            <td class="p-4 pl-6">
                                <div class="font-bold text-slate-800">🐱 <?php echo htmlspecialchars($row['cat_name']); ?></div>
                                <div class="text-xs text-slate-400 mt-0.5"><?php echo htmlspecialchars($row['breed']); ?></div>
                            </td>
                            
                            <?php if ($role == "Admin" || $role == "Staff") { ?>
                                <td class="p-4">
                                    <div class="font-semibold text-slate-700">👤 <?php echo htmlspecialchars($row['adopter_name']); ?></div>
                                    <div class="text-xs text-slate-400 font-mono mt-0.5"><?php echo htmlspecialchars($row['adopter_email']); ?></div>
                                </td>
                            <?php } ?>
                            
                            <td class="p-4">
                                <?php if ($row['status'] == 'Approved') { ?>
                                    <span class="bg-emerald-50 text-emerald-700 text-xs font-bold px-2.5 py-1 rounded-lg border border-emerald-100/50 uppercase tracking-wide">
                                        Approved
                                    </span>
                                <?php } elseif ($row['status'] == 'Rejected') { ?>
                                    <span class="bg-rose-50 text-rose-700 text-xs font-bold px-2.5 py-1 rounded-lg border border-rose-100/50 uppercase tracking-wide">
                                        Declined
                                    </span>
                                <?php } else { ?>
                                    <span class="bg-amber-50 text-amber-700 text-xs font-bold px-2.5 py-1 rounded-lg border border-amber-100/50 uppercase tracking-wide animate-pulse">
                                        Pending
                                    </span>
                                <?php } ?>
                            </td>
                            
                            <td class="p-4 font-medium text-slate-600">
                                🛡️ <?php echo !empty($row['reviewer_name']) ? htmlspecialchars($row['reviewer_name']) : '<span class="text-slate-300 font-normal italic text-xs">Unassigned</span>'; ?>
                            </td>
                            
                        <td class="p-4 pr-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="view.php?id=<?php echo $row['adoptionid']; ?>" 
                                       class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-3 py-1.5 rounded-xl text-xs transition">
                                        View
                                    </a>
                                    
                                    <?php if (($role == "Admin" || $role == "Staff" || $role == "Manager") && $row['status'] == 'Pending') { ?>
                                        <a href="action.php?id=<?php echo $row['adoptionid']; ?>&action=approve" 
                                           class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-3 py-1.5 rounded-xl text-xs shadow-sm transition">
                                            Approve
                                        </a>
                                        <a href="action.php?id=<?php echo $row['adoptionid']; ?>&action=reject" 
                                           class="border border-rose-200 hover:bg-rose-50 text-rose-600 font-bold px-3 py-1.5 rounded-xl text-xs transition">
                                            Reject
                                        </a>
                                    <?php } ?>
                                </div>
                            </td>

                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <?php if (pg_num_rows($result) == 0) { ?>
            <div class="p-12 flex flex-col items-center justify-center text-slate-400 bg-slate-50/20 gap-2">
                <span class="text-4xl">💼</span>
                <p class="text-sm font-medium">No applications currently exist in this phase.</p>
            </div>
        <?php } ?>
    </div>
</div>

<?php include("../includes/footer.php"); ?>