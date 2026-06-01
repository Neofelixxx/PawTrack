<?php

include("../config/db.php");
include("../includes/header.php");

$id = $_GET['id'] ?? null;

if (!$id) {

    die("Invalid Shelter ID");
}

$query = "
SELECT *
FROM Shelter
WHERE ShelterID = $1
";

$result = pg_query_params(
    $conn,
    $query,
    [$id]
);

$shelter = pg_fetch_assoc($result);

if (!$shelter) {

    die("Shelter not found.");
}

/*
|--------------------------------------------------------------------------
| GET CATS UNDER THIS SHELTER
|--------------------------------------------------------------------------
*/

$catsQuery = "
SELECT *
FROM Cat
WHERE ShelterID = $1
AND Status = 'Available'
ORDER BY CatID DESC
";

$cats = pg_query_params(
    $conn,
    $catsQuery,
    [$id]
);

?>

<!-- SHELTER CARD -->
<div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">

    <!-- IMAGE -->
    <?php if ($shelter['image']) { ?>

        <img
            src="../assets/images/shelters/<?php echo $shelter['image']; ?>"
            class="w-full h-80 object-cover"
        >

    <?php } ?>

    <div class="p-6">

        <!-- TITLE -->
        <h2 class="text-4xl font-bold text-[#0b1f3b] mb-4">

            <?php echo $shelter['name']; ?>

        </h2>

        <!-- INFO -->
        <div class="grid md:grid-cols-2 gap-4 text-gray-700">

            <div>

                <p class="mb-2">
                    <b>District:</b>
                    <?php echo $shelter['district']; ?>
                </p>

                <p class="mb-2">
                    <b>Capacity:</b>
                    <?php echo $shelter['capacity']; ?>
                </p>

            </div>

            <div>

                <p class="mb-2">
                    <b>Address:</b>
                    <?php echo $shelter['address']; ?>
                </p>

            </div>

        </div>

        <!-- DESCRIPTION -->
        <div class="mt-6">

            <h3 class="text-xl font-bold text-[#3679f7] mb-2">
                About Shelter
            </h3>

            <p class="text-gray-700 leading-relaxed">

                <?php echo $shelter['description']; ?>

            </p>

        </div>

    </div>

</div>

<!-- AVAILABLE CATS -->
<div class="mb-6">

    <h3 class="text-3xl font-bold text-[#0b1f3b] mb-4">

        Available Cats

    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <?php while ($cat = pg_fetch_assoc($cats)) { ?>

            <div
                class="
                bg-white
                rounded-2xl
                shadow-lg
                overflow-hidden
                hover:-translate-y-2
                hover:shadow-2xl
                transition-all
                duration-300
                "
            >

                <!-- CAT IMAGE -->
                <?php if ($cat['image']) { ?>

                    <img
                        src="../assets/images/cats/<?php echo $cat['image']; ?>"
                        class="w-full h-56 object-cover"
                    >

                <?php } ?>

                <div class="p-4">

                    <!-- NAME -->
                    <h4 class="text-xl font-bold mb-2">

                        <?php echo $cat['name']; ?>

                    </h4>

                    <p class="mb-1">
                        <b>Breed:</b>
                        <?php echo $cat['breed']; ?>
                    </p>

                    <p class="mb-3">
                        <b>Age:</b>
                        <?php echo $cat['agecategory']; ?>
                    </p>

                    <!-- BUTTON -->
                    <a
                        href="/PawTrack/cats/view.php?id=<?php echo $cat['catid']; ?>"
                        class="
                        inline-block
                        bg-[#3679f7]
                        hover:bg-[#4ec5c1]
                        text-white
                        px-4
                        py-2
                        rounded-lg
                        transition
                        duration-300
                        "
                    >
                        View Details
                    </a>

                </div>

            </div>

        <?php } ?>

    </div>

</div>

<?php include("../includes/footer.php"); ?>