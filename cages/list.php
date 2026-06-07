<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("../config/db.php");
include("../includes/header.php");

$role = $_SESSION['role'] ?? null;
if (!$role || ($role != "Admin" && $role != "Manager" && $role != "Staff")) {
    header("Location: /PawTrack/auth/login.php");
    exit;
}

$shelter_filter = $_GET['shelterid'] ?? 'all';
$section_filter = $_GET['section'] ?? 'all';

// 1. FETCH REFERENCE DATA SEEDS FIRST TO PREVENT DROPDOWN LOCKOUT
$shelters_result = pg_query($conn, "SELECT shelterid, name FROM Shelter ORDER BY name ASC");
$sections_result = pg_query($conn, "SELECT DISTINCT section FROM Cage WHERE section IS NOT NULL ORDER BY section ASC");

// 2. CONSTRUCT MAIN CONTROL PANEL BOARD QUERY
$query = "
    SELECT 
        c.*, 
        s.Name AS ShelterName,
        COUNT(ca.AssignmentID) AS occupied_count
    FROM Cage c
    JOIN Shelter s ON c.ShelterID = s.ShelterID
    LEFT JOIN Cage_Assignment ca ON c.CageID = ca.CageID AND ca.EndDate IS NULL
    WHERE 1=1
";

$params = [];
$param_index = 1;

if ($shelter_filter !== 'all' && is_numeric($shelter_filter)) {
    $query .= " AND c.ShelterID = $" . $param_index;
    $params[] = (int)$shelter_filter;
    $param_index++;
}

if ($section_filter !== 'all') {
    $query .= " AND c.Section = $" . $param_index;
    $params[] = $section_filter;
}

// CORRECTED: Replaced invalid 'ASCII' statement with standard 'ASC' tracking
$query .= " GROUP BY c.CageID, s.Name ORDER BY s.Name ASC, c.CageNumber ASC";
$result = pg_query_params($conn, $query, $params);

if (!$result) {
    die("<div class='p-6 bg-red-50 text-red-700 rounded-xl font-bold'>Database operation failed. Please check parameters.</div>");
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 mb-12 space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-sky-100 pb-5">
        <div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">Cage Tracking Board</h2>
            <p class="text-slate-500 text-sm mt-1">Live operational metrics checking cage assignments and open shelter spaces.</p>
        </div>
        <div class="flex gap-3">
            <a href="add.php" class="bg-sky-500 hover:bg-sky-600 text-white font-bold px-5 py-2.5 rounded-xl shadow-md transition text-xs flex items-center gap-2">
                <span>＋</span> Add New Cage Space
            </a>
        </div>
    </div>

    <form method="GET" class="bg-white p-4 rounded-2xl border border-sky-100/60 shadow-sm grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs font-bold text-slate-400 uppercase">
        <div>
            <label class="block mb-1">Filter Shelter Location</label>
            <select name="shelterid" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-200 p-2.5 rounded-xl text-slate-700 text-sm focus:outline-none cursor-pointer normal-case">
                <option value="all">All Locations Combined</option>
                <?php while ($s = pg_fetch_assoc($shelters_result)) { ?>
                    <option value="<?php echo $s['shelterid']; ?>" <?php echo ($shelter_filter == $s['shelterid']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($s['name']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div>
            <label class="block mb-1">Filter Functional Section</label>
            <select name="section" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-200 p-2.5 rounded-xl text-slate-700 text-sm focus:outline-none cursor-pointer normal-case">
                <option value="all">All Room Sections</option>
                <?php while ($sec = pg_fetch_assoc($sections_result)) { ?>
                    <option value="<?php echo htmlspecialchars($sec['section']); ?>" <?php echo ($section_filter == $sec['section']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($sec['section']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="flex items-end justify-end">
            <a href="list.php" class="text-center w-full sm:w-auto bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2.5 rounded-xl text-sm normal-case font-bold transition">Clear Parameters</a>
        </div>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php while ($row = pg_fetch_assoc($result)) { 
            $capacity = (int)$row['capacity'];
            $occupied = (int)$row['occupied_count'];
            $available = $capacity - $occupied;
            
            // Saturation logic checks
            if ($occupied === 0) {
                $status_label = "Available";
                $color_classes = "bg-emerald-50 text-emerald-700 border-emerald-100";
                $dot_color = "bg-emerald-500";
            } elseif ($occupied >= $capacity) {
                $status_label = "Full";
                $color_classes = "bg-red-50 text-red-700 border-red-100";
                $dot_color = "bg-red-500";
            } else {
                $status_label = "Nearly Full";
                $color_classes = "bg-amber-50 text-amber-700 border-amber-100";
                $dot_color = "bg-amber-500";
            }
        ?>
            <div class="bg-white rounded-3xl border border-sky-100 shadow-sm flex flex-col justify-between overflow-hidden hover:shadow-md transition">
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase bg-slate-50 border px-2.5 py-1 rounded-lg">📦 Unit <?php echo htmlspecialchars($row['cagenumber']); ?></span>
                        <span class="px-2.5 py-1 rounded-lg border text-[11px] font-bold uppercase tracking-wide flex items-center gap-1.5 <?php echo $color_classes; ?>">
                            <span class="h-2 w-2 rounded-full <?php echo $dot_color; ?>"></span>
                            <?php echo $status_label; ?>
                        </span>
                    </div>

                    <div>
                        <h4 class="text-lg font-black text-slate-900 tracking-tight truncate"><?php echo htmlspecialchars($row['sheltername']); ?></h4>
                        <p class="text-xs font-semibold text-sky-600 uppercase tracking-wide mt-0.5">Section: <?php echo htmlspecialchars($row['section']); ?></p>
                    </div>

                    <div class="grid grid-cols-2 gap-2 border-t pt-3 text-center text-xs font-bold">
                        <div class="p-2 bg-slate-50 rounded-xl text-slate-500">Limits<br><span class="text-slate-800 font-mono text-sm"><?php echo $capacity; ?></span></div>
                        <div class="p-2 bg-slate-50 rounded-xl text-slate-500">Occupied<br><span class="text-slate-800 font-mono text-sm"><?php echo $occupied; ?></span></div>
                    </div>
                </div>

                <div class="p-4 bg-slate-50/50 border-t border-slate-100 flex gap-2">
                    <a href="view.php?id=<?php echo $row['cageid']; ?>" class="flex-1 text-center bg-white border hover:bg-slate-50 text-slate-700 font-bold py-2 rounded-xl text-xs shadow-sm transition">
                        View Space
                    </a>
                    <?php if ($occupied < $capacity) { ?>
                        <a href="assign.php?cageid=<?php echo $row['cageid']; ?>" class="flex-1 text-center bg-sky-500 hover:bg-sky-600 text-white font-bold py-2 rounded-xl text-xs shadow-sm transition">
                            Assign Cat
                        </a>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </div>

    <?php if (pg_num_rows($result) === 0) { ?>
        <div class="p-12 bg-white rounded-3xl border border-sky-100 text-center text-slate-400">
            <span class="text-4xl block mb-2">📦</span>
            <p class="text-sm font-medium">No cages found matching the selected filtering metrics.</p>
        </div>
    <?php } ?>
</div>

<?php include("../includes/footer.php"); ?>