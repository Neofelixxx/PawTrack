<?php

include("../config/db.php");
include("../includes/header.php");

session_start();

$role = $_SESSION['role'] ?? null;

?>

<h2 class="text-2xl font-bold mb-4">🐾 Cats</h2>

<?php if ($role == "Admin" || $role == "Staff") { ?>

    <a href="/PawTrack/cats/add.php"
       class="bg-green-500 text-white px-4 py-2 rounded mb-4 inline-block">
        + Add Cat
    </a>

<?php } ?>

<?php
$query = "SELECT * FROM Cat WHERE status = 'Available'";
$result = pg_query($conn, $query);

while ($row = pg_fetch_assoc($result)) {
?>

<div class="bg-white p-4 mb-3 rounded shadow">

    <h3 class="text-lg font-bold">
        <?php echo $row['name']; ?>
    </h3>

    <p>Breed: <?php echo $row['breed']; ?></p>

    <p>Status: <?php echo $row['status']; ?></p>

    <div class="mt-3">

        <!-- ADOPTER / PUBLIC BUTTON -->
        <?php if (!$role || $role == "Adopter") { ?>

            <a href="/PawTrack/auth/login.php?redirect=/PawTrack/adoption/add.php?catid=<?php echo $row['catid']; ?>"
               class="bg-blue-500 text-white px-3 py-1 rounded">
                Adopt
            </a>

        <?php } ?>

        <!-- STAFF / ADMIN BUTTONS -->
        <?php if ($role == "Admin" || $role == "Staff") { ?>

            <a href="/PawTrack/cats/edit.php?id=<?php echo $row['catid']; ?>"
               class="bg-yellow-500 text-white px-3 py-1 rounded">
                Edit
            </a>

        <?php } ?>

    </div>

</div>

<?php } ?>

<?php include("../includes/footer.php"); ?>