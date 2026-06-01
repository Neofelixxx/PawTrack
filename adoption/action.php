<?php

include("../config/db.php");
include("../includes/header.php");

$id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? null;

if (!$id || !$action) {
    die("Invalid request");
}

// Get cat ID from adoption
$adoption = pg_fetch_assoc(pg_query($conn, "
    SELECT catid FROM Adoption WHERE adoptionid = $id
"));

$catid = $adoption['catid'];

if ($action == "approve") {

    // approve adoption
    pg_query($conn, "
        UPDATE Adoption
        SET status = 'Approved'
        WHERE adoptionid = $id
    ");

    // update cat status
    pg_query($conn, "
        UPDATE Cat
        SET status = 'Adopted'
        WHERE catid = $catid
    ");

    $_SESSION['message'] = "Adoption approved!";

} elseif ($action == "reject") {

    pg_query($conn, "
        UPDATE Adoption
        SET status = 'Rejected'
        WHERE adoptionid = $id
    ");

    $_SESSION['message'] = "Adoption rejected!";
}

header("Location: list.php");
exit;

?>