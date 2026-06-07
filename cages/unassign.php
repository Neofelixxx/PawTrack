<?php
include("../config/db.php");
session_start();

$role = $_SESSION['role'] ?? null;
if (!$role || ($role != "Admin" && $role != "Manager" && $role != "Staff")) {
    die("Access denied.");
}

$catid = $_GET['catid'] ?? null;
if (!$catid || !is_numeric($catid)) {
    die("Malformed request tracking sequence context parameters.");
}

// Releasing allocation profile tracks immediately using a safe execution script string
$result = pg_query_params($conn, "
    UPDATE Cage_Assignment 
    SET EndDate = CURRENT_DATE 
    WHERE CatID = $1 AND EndDate IS NULL", 
    [$catid]
);

if ($result) {
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'list.php'));
    exit;
}
?>