<?php
include("../config/db.php");
include("../includes/header.php");

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    die("<div class='p-8 text-center text-rose-600 font-bold'>Invalid record parameter reference request context.</div>");
}

// Fetch Cage metadata information parameters
$cage_stmt = pg_query_params($conn, "
    SELECT c.*, s.Name AS ShelterName 
    FROM Cage c 
    JOIN Shelter s ON c.ShelterID = s.ShelterID 
    WHERE c.CageID = $1", [$id]);
$cage = pg_fetch_assoc($cage_stmt);

if (!$cage) {
    die("<div class='p-8 text-center text-rose-600 font-bold'>Cage profile could not be found.</div>");
}

// Fetch active animals housed within the unit
$active_cats = pg_query_params($conn, "
    SELECT ca.*, c.Name, c.Breed, c.Status 
    FROM Cage_Assignment ca
    JOIN Cat c ON ca.CatID = c.CatID
    WHERE ca.CageID = $1 AND ca.EndDate IS NULL", [$id]);

// Fetch complete historical transaction timelines related to this room space
$history = pg_query_params($conn, "
    SELECT ca.*, c.Name, c.Breed, 
           (ca.EndDate - ca.StartDate) AS duration_days
    FROM Cage_Assignment ca
    JOIN Cat c ON ca.CatID = c.CatID
    WHERE ca.CageID = $1 AND ca.EndDate IS NOT NULL
    ORDER BY ca.EndDate DESC LIMIT 20", [$id]);
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 mb-12 grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <div class="lg:col-span-2 space-y-6">
        <!-- Structural overview parameters details card display context -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-sky-100 shadow-sm space-y-4">
            <div>
                <span class="text-xs font-bold text-sky-600 bg-sky-50 px-3 py-1 rounded-md uppercase tracking-wide">Unit Core Specs Overview</span>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight mt-2">Cage Account: <?php echo htmlspecialchars($cage['cagenumber']); ?></h2>
                <p class="text-sm font-semibold text-slate-500 mt-0.5">Facility Location: <?php echo htmlspecialchars($cage['sheltername']); ?></p>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs font-bold uppercase tracking-wider text-slate-400 pt-2">
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span>Room Section</span><br><span class="text-slate-800 normal-case text-sm font-black"><?php echo htmlspecialchars($cage['section']); ?></span>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span>Space Limit Bounds</span><br><span class="text-slate-800 font-mono text-sm font-black"><?php echo (int)$cage['capacity']; ?> Units</span>
                </div>
            </div>
        </div>

        <!-- ACTIVE OCCUPANCY ROSTER PANEL LIST -->
        <div class="bg-white rounded-3xl border border-sky-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100"><h3 class="text-md font-bold text-slate-800 tracking-tight">Active Occupants List</h3></div>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left text-xs sm:text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-slate-400 font-bold uppercase tracking-wider text-[10px] border-b">
                            <th class="p-4 pl-6">Patient Name</th>
                            <th class="p-4">Breed Classification</th>
                            <th class="p-4">Clinical Code Status</th>
                            <th class="p-4 pr-6 text-right">Actions Operations</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-slate-600 font-medium">
                        <?php while ($c = pg_fetch_assoc($active_cats)) { ?>
                            <tr class="hover:bg-slate-50/40">
                                <td class="p-4 pl-6 font-bold text-slate-900">🐈 <?php echo htmlspecialchars($c['name']); ?></td>
                                <td class="p-4"><?php echo htmlspecialchars($c['breed']); ?></td>
                                <td class="p-4"><span class="bg-sky-50 text-sky-700 font-bold px-2 py-0.5 rounded border border-sky-100 uppercase text-[10px]"><?php echo $c['status']; ?></span></td>
                                <td class="p-4 pr-6 text-right">
                                    <a href="unassign.php?catid=<?php echo $c['catid']; ?>" class="bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold px-3 py-1.5 rounded-lg text-xs border border-rose-100 shadow-sm transition">Release From Cage</a>
                                </td>
                            </tr>
                        <?php } if (pg_num_rows($active_cats) === 0) { echo "<tr><td colspan='4' class='p-6 text-center italic text-slate-400 font-normal'>No felines currently assigned to this enclosure space unit.</td></tr>"; } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MOVEMENT RECONSTRUCTION HISTORY COLUMN LOG INDEX -->
    <div class="bg-white rounded-3xl border border-sky-100 shadow-sm p-6 flex flex-col justify-between overflow-hidden">
        <div>
            <h3 class="text-md font-bold text-slate-800 tracking-tight mb-4 pb-2 border-b">Movement History Log</h3>
            <div class="space-y-4 max-h-[460px] overflow-y-auto pr-1">
                <?php while ($h = pg_fetch_assoc($history)) { ?>
                    <div class="bg-slate-50 border p-3.5 rounded-xl space-y-1.5 text-xs text-slate-500 font-medium">
                        <div class="flex justify-between items-center"><span class="font-bold text-sm text-slate-800">🐈 <?php echo htmlspecialchars($h['name']); ?></span><span class="font-mono bg-white border px-2 py-0.5 rounded text-[10px] font-bold text-slate-700"><?php echo ($h['duration_days'] ?: '1'); ?> Days</span></div>
                        <div class="flex items-center gap-1">📅 Time: <span class="font-mono text-slate-700 font-bold"><?php echo date("d M y", strtotime($h['startdate'])); ?> &rarr; <?php echo date("d M y", strtotime($h['enddate'])); ?></span></div>
                    </div>
                <?php } if (pg_num_rows($history) === 0) { echo "<p class='text-slate-400 text-center italic text-xs pt-6 font-normal'>No previous historical transfer timelines documented yet.</p>"; } ?>
            </div>
        </div>
        <div class="pt-4 border-t mt-6"><a href="list.php" class="block text-center bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 rounded-xl text-xs transition">Return to Board</a></div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>