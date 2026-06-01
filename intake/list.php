<?php

include("../config/db.php");
include("../includes/header.php");

$query = "
SELECT
    i.*,
    c.name AS cat_name
FROM Intake i
JOIN Cat c ON i.catid = c.catid
ORDER BY i.intakeid DESC
";

$result = pg_query($conn, $query);

?>

<h2 class="text-2xl font-bold mb-4">Intake Records</h2>

<a href="add.php"
   class="bg-blue-500 text-white px-4 py-2 rounded">
   + Add Intake
</a>

<div class="bg-white mt-6 p-4 rounded shadow">

<?php while ($row = pg_fetch_assoc($result)) { ?>

    <div class="border-b py-3">

        <p><b>Cat:</b> <?php echo $row['cat_name']; ?></p>

        <p><b>Date:</b> <?php echo $row['intakedate']; ?></p>

        <p><b>Description:</b> <?php echo $row['locationdesc']; ?></p>

        <p>
            <b>Coordinates:</b>
            <?php echo $row['location']; ?>
        </p>

    </div>

<?php } ?>

</div>

<?php include("../includes/footer.php"); ?>