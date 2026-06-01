<?php

include("../config/db.php");
include("../includes/header.php");

$query = "SELECT * FROM Shelter ORDER BY ShelterID DESC";
$result = pg_query($conn, $query);

?>

<h2 class="text-2xl font-bold mb-4">Shelters</h2>

<a href="add.php"
   class="bg-blue-500 text-white px-4 py-2 rounded">
   + Add Shelter
</a>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">

<?php while ($row = pg_fetch_assoc($result)) { ?>

    <div class="bg-white p-4 rounded shadow">

        <?php if ($row['image']) { ?>
            <img src="../assets/images/shelters/<?php echo $row['image']; ?>"
                 class="w-full h-40 object-cover rounded mb-3">
        <?php } ?>

        <h3 class="text-xl font-bold">
            <?php echo $row['name']; ?>
        </h3>

        <p><b>District:</b> <?php echo $row['district']; ?></p>

        <p><b>Capacity:</b> <?php echo $row['capacity']; ?></p>

        <a href="edit.php?id=<?php echo $row['shelterid']; ?>"
           class="inline-block mt-3 bg-yellow-500 text-white px-3 py-1 rounded">
           Edit
        </a>

    </div>

<?php } ?>

</div>

<?php include("../includes/footer.php"); ?>