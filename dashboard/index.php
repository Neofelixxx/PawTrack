<?php

include("../config/db.php");
include("../includes/header.php");

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: /PawTrack/auth/login.php");
    exit;
}

$role = $_SESSION['role'];

?>

<h2 class="text-2xl font-bold mb-6">Dashboard</h2>

<?php if ($role == "Admin") { ?>

    <div class="bg-white p-6 rounded shadow">
        <h3 class="text-xl font-bold mb-3">🛠 Admin Panel</h3>

        <ul class="list-disc ml-6">
            <li><a href="../cats/list.php">Manage Cats</a></li>
            <li><a href="../shelters/list.php">Manage Shelters</a></li>
            <li><a href="../adoption/list.php">Manage Adoptions</a></li>
            <li><a href="../intake/map.php">GIS Intake Map</a></li>
        </ul>
    </div>

<?php } elseif ($role == "Staff") { ?>

    <div class="bg-white p-6 rounded shadow">
        <h3 class="text-xl font-bold mb-3">👩‍⚕️ Staff Panel</h3>

        <ul class="list-disc ml-6">
            <li><a href="../cats/list.php">View Cats</a></li>
            <li><a href="../cats/add.php">Add Cat</a></li>
            <li><a href="../adoption/list.php">Handle Adoption Requests</a></li>
            <li><a href="../intake/map.php">View Intake Map</a></li>
        </ul>
    </div>

<?php } elseif ($role == "Adopter") { ?>

    <div class="bg-white p-6 rounded shadow">
        <h3 class="text-xl font-bold mb-3">🐾 Adopter Panel</h3>

        <ul class="list-disc ml-6">
            <li><a href="../cats/list.php">Browse Available Cats</a></li>
            <li><a href="../adoption/list.php">My Adoption Status</a></li>
            <li><a href="../intake/map.php">View Shelter Map</a></li>
        </ul>
    </div>

<?php } ?>

<?php include("../includes/footer.php"); ?>