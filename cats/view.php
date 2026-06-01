<?php

include("../config/db.php");
include("../includes/header.php");

$id = $_GET['id'] ?? null;

if (!$id) {
    die("Invalid Cat ID");
}

$query = "
    SELECT c.*, s.name AS shelter_name
    FROM Cat c
    JOIN Shelter s ON c.shelterid = s.shelterid
    WHERE c.catid = $1
";

$result = pg_query_params($conn, $query, [$id]);

$cat = pg_fetch_assoc($result);

if (!$cat) {
    die("Cat not found");
}

$role = $_SESSION['role'] ?? null;

?>

<div class="bg-white p-6 rounded shadow max-w-3xl mx-auto">

    <?php if ($cat['image']) { ?>

        <img src="../assets/images/cats/<?php echo $cat['image']; ?>"
             class="w-full h-96 object-cover rounded mb-6">

    <?php } ?>

    <h2 class="text-3xl font-bold mb-4">
        <?php echo $cat['name']; ?>
    </h2>

    <div class="space-y-2">

        <p><strong>Breed:</strong> <?php echo $cat['breed']; ?></p>

        <p><strong>Age Category:</strong>
            <?php echo $cat['agecategory']; ?>
        </p>

        <p><strong>Gender:</strong>
            <?php echo $cat['gender']; ?>
        </p>

        <p><strong>Status:</strong>
            <?php echo $cat['status']; ?>
        </p>

        <p><strong>Shelter:</strong>
            <?php echo $cat['shelter_name']; ?>
        </p>

        <p><strong>Description:</strong></p>

        <p class="text-gray-700">
            <?php echo $cat['description']; ?>
        </p>

    </div>

    <div class="mt-6">

        <?php if ($cat['status'] == 'Available') { ?>

            <?php if ($role == "Adopter") { ?>

                <a href="/PawTrack/adoption/add.php?catid=<?php echo $cat['catid']; ?>"
                   class="bg-green-500 text-white px-4 py-2 rounded">
                    Adopt This Cat
                </a>

            <?php } elseif (!$role) { ?>

                <a href="/PawTrack/auth/login.php"
                   class="bg-blue-500 text-white px-4 py-2 rounded">
                    Login to Adopt
                </a>

            <?php } ?>

        <?php } ?>

    </div>

</div>

<?php include("../includes/footer.php"); ?>