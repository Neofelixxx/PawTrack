<?php

include("../config/db.php");
include("../includes/header.php");

/*
|--------------------------------------------------------------------------
| BASIC ANALYTICS
|--------------------------------------------------------------------------
*/

$cats = pg_fetch_assoc(pg_query($conn, "SELECT COUNT(*) AS total FROM Cat"));
$shelters = pg_fetch_assoc(pg_query($conn, "SELECT COUNT(*) AS total FROM Shelter"));
$adoptions = pg_fetch_assoc(pg_query($conn, "SELECT COUNT(*) AS total FROM Adoption"));
$donations = pg_fetch_assoc(pg_query($conn, "SELECT COUNT(*) AS total FROM Donations"));

?>

<h2 class="text-3xl font-bold text-[#0b1f3b] mb-6">
    System Reports
</h2>

<!-- STATS -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">

    <div class="bg-white p-6 rounded-2xl shadow-lg">
        <p class="text-gray-500">Total Cats</p>
        <h3 class="text-3xl font-bold text-[#3679f7]">
            <?php echo $cats['total']; ?>
        </h3>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-lg">
        <p class="text-gray-500">Shelters</p>
        <h3 class="text-3xl font-bold text-[#3679f7]">
            <?php echo $shelters['total']; ?>
        </h3>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-lg">
        <p class="text-gray-500">Adoptions</p>
        <h3 class="text-3xl font-bold text-[#4ec5c1]">
            <?php echo $adoptions['total']; ?>
        </h3>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-lg">
        <p class="text-gray-500">Donations</p>
        <h3 class="text-3xl font-bold text-[#4ec5c1]">
            <?php echo $donations['total']; ?>
        </h3>
    </div>

</div>

<!-- SIMPLE INSIGHT SECTION -->
<div class="bg-white p-6 rounded-2xl shadow-lg">

    <h3 class="text-xl font-bold mb-4 text-[#0b1f3b]">
        Key Insights
    </h3>

    <ul class="list-disc ml-6 text-gray-700">

        <li>
            System tracks cat intake, medical care, and adoption lifecycle.
        </li>

        <li>
            Donation tracking supports shelter funding analysis.
        </li>

        <li>
            Data can be extended to GIS hotspot analysis (Johor Bahru district).
        </li>

        <li>
            Suitable for decision-making in animal welfare management.
        </li>

    </ul>

</div>

<?php include("../includes/footer.php"); ?>