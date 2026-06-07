<?php
include("../config/db.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['role'] ?? null;
$userid = $_SESSION['user_id'] ?? null; // The Staff or Admin logged in

if ($role !== 'Admin' && $role !== 'Manager' && $role !== 'Staff') {
    die("Unauthorized access. Only operational staff can process pipelines.");
}

$id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? null;

if (!$id || !$action) {
    die("Invalid system request.");
}

// Fetch the targeted cat ID linked to this specific application
$adoptionResult = pg_query_params($conn, "SELECT catid FROM Adoption WHERE adoptionid = $1", [$id]);
$adoption = pg_fetch_assoc($adoptionResult);
if (!$adoption) {
    die("Adoption record not found.");
}
$catid = $adoption['catid'];

if ($action == "approve") {
    // 1. Mark this specific application as Approved and record the Reviewer ID
    pg_query_params($conn, "
        UPDATE Adoption 
        SET status = 'Approved', approvedby = $1 
        WHERE adoptionid = $2
    ", [$userid, $id]);

    // 2. Reject all OTHER pending applications for this same cat automatically
    pg_query_params($conn, "
        UPDATE Adoption 
        SET status = 'Rejected' 
        WHERE catid = $1 AND adoptionid != $2 AND status = 'Pending'
    ", [$catid, $id]);

    // 3. Mark the global Cat profile as permanently Adopted
    pg_query_params($conn, "
        UPDATE Cat SET status = 'Adopted' WHERE catid = $1
    ", [$catid]);

    $_SESSION['message'] = "Adoption pipeline successfully approved.";

} elseif ($action == "reject") {
    // 1. Mark application as Rejected
    pg_query_params($conn, "
        UPDATE Adoption 
        SET status = 'Rejected', approvedby = $1 
        WHERE adoptionid = $2
    ", [$userid, $id]);

    // 2. Check if there are any other pending applications for this cat
    $pendingCheck = pg_query_params($conn, "SELECT count(*) FROM Adoption WHERE catid = $1 AND status = 'Pending'", [$catid]);
    $pendingCount = pg_fetch_result($pendingCheck, 0, 0);

    // If no other applications are pending, return the cat's status back to Available
    if ($pendingCount == 0) {
        pg_query_params($conn, "UPDATE Cat SET status = 'Available' WHERE catid = $1", [$catid]);
    }

    $_SESSION['message'] = "Adoption application declined.";
}

header("Location: list.php");
exit;
?>