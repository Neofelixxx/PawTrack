<?php
include("../config/db.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$role = $_SESSION['role'] ?? null;
if (!$role || ($role != "Admin" && $role != "Manager" && $role != "Staff")) {
    header("Location: /PawTrack/auth/login.php");
    exit;
}

$selected_cage = $_GET['cageid'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $catid = $_POST['catid'];
    $cageid = $_POST['cageid'];

    // 1. Capacity Safeguard Enforcement Check Rule
    $cap_stmt = pg_query_params($conn, "
        SELECT c.Capacity, COUNT(ca.AssignmentID) AS occupied 
        FROM Cage c 
        LEFT JOIN Cage_Assignment ca ON c.CageID = ca.CageID AND ca.EndDate IS NULL 
        WHERE c.CageID = $1 GROUP BY c.Capacity", [$cageid]);
    $cap = pg_fetch_assoc($cap_stmt);

    if ($cap && (int)$cap['occupied'] >= (int)$cap['capacity']) {
        die("Operational error boundary triggered: Target cage allocation threshold reached maximum cap indices.");
    }

    // 2. Unassign and terminate any currently active tracking row lines
    pg_query_params($conn, "UPDATE Cage_Assignment SET EndDate = CURRENT_DATE WHERE CatID = $1 AND EndDate IS NULL", [$catid]);

    // 3. Insert fresh allocation entry parameters
    $result = pg_query_params($conn, "INSERT INTO Cage_Assignment (CatID, CageID, StartDate, EndDate) VALUES ($1, $2, CURRENT_DATE, NULL)", [$catid, $cageid]);

    if ($result) {
        header("Location: list.php");
        exit;
    }
}

// Fetch only valid cats (Exclude adopted or deceased profiles from housing grids)
$cats = pg_query($conn, "SELECT catid, name, status FROM Cat WHERE status != 'Adopted' AND status != 'Deceased' ORDER BY name");

// Only show cages that contain free space inside them
$cages = pg_query($conn, "
    SELECT c.*, s.Name AS ShelterName, COUNT(ca.AssignmentID) AS occupied
    FROM Cage c
    JOIN Shelter s ON c.ShelterID = s.ShelterID
    LEFT JOIN Cage_Assignment ca ON c.CageID = ca.CageID AND ca.EndDate IS NULL
    GROUP BY c.CageID, s.Name
    HAVING COUNT(ca.AssignmentID) < c.Capacity
    ORDER BY s.Name, c.CageNumber
");

include("../includes/header.php");
?>

<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 mb-12">
    <div class="mb-8 border-b pb-4">
        <h2 class="text-3xl font-black text-slate-900 tracking-tight">Deploy Feline Assignment</h2>
        <p class="text-slate-500 text-sm mt-1">Move a cat profile directly into an open, capacity-validated enclosure cage.</p>
    </div>

    <form method="POST" class="bg-white p-6 sm:p-8 rounded-3xl border border-sky-100 shadow-sm space-y-6 text-xs font-bold text-slate-500 uppercase tracking-wide">
        <div>
            <label class="block mb-1.5">Select Feline Patient</label>
            <select name="catid" required class="w-full border border-slate-200 bg-slate-50 px-4 py-3 rounded-xl text-slate-700 text-sm font-medium focus:outline-none focus:border-sky-500 normal-case">
                <?php while ($c = pg_fetch_assoc($cats)) { ?>
                    <option value="<?php echo $c['catid']; ?>">🐈 <?php echo htmlspecialchars($c['name']); ?> (Status: <?php echo $c['status']; ?>)</option>
                <?php } ?>
            </select>
        </div>

        <div>
            <label class="block mb-1.5">Select Target Enclosure Enclosure Space</label>
            <select name="cageid" required class="w-full border border-slate-200 bg-slate-50 px-4 py-3 rounded-xl text-slate-700 text-sm font-medium focus:outline-none focus:border-sky-500 normal-case">
                <?php while ($cg = pg_fetch_assoc($cages)) { ?>
                    <option value="<?php echo $cg['cageid']; ?>" <?php echo ($selected_cage == $cg['cageid']) ? 'selected' : ''; ?>>
                        🏢 <?php echo htmlspecialchars($cg['sheltername']); ?> — Unit: <?php echo htmlspecialchars($cg['cagenumber']); ?> (Section: <?php echo htmlspecialchars($cg['section']); ?>)
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="flex gap-3 pt-4 border-t">
            <button type="submit" class="flex-1 bg-sky-500 hover:bg-sky-600 text-white font-bold py-3.5 rounded-xl text-sm normal-case">
                Confirm Allocation Placement
            </button>
            <a href="list.php" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-6 py-3.5 rounded-xl text-sm normal-case flex items-center justify-center">Cancel</a>
        </div>
    </form>
</div>

<?php include("../includes/footer.php"); ?>