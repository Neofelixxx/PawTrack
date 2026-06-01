<?php

include("../config/db.php");
include("../includes/header.php");


$role = $_SESSION['role'] ?? null;

$query = "
SELECT
    c.*,
    s.Name AS ShelterName
FROM Cat c
JOIN Shelter s
ON c.ShelterID = s.ShelterID
WHERE c.Status = 'Available'
ORDER BY c.CatID DESC
";

$result = pg_query($conn, $query);

if (!$result) {
    die(pg_last_error($conn));
}

?>

<div class="flex">

    <?php include("../includes/sidebar.php"); ?>

    <div class="flex-1 p-6">

<h2 class="text-2xl font-bold mb-4">Available Cats</h2>

<?php if ($role == "Admin" || $role == "Staff") { ?>

    <a href="add.php"
    class="bg-blue-500 text-white px-4 py-2 rounded">
        + Add Cat
    </a>

<?php } ?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">

<?php while ($row = pg_fetch_assoc($result)) { ?>

    <div class="bg-white rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 flex flex-col justify-between">
    <div>
        <!-- Image Container with Fixed Aspect Ratio -->
        <?php if ($row['image']) { ?> 
            <div class="relative overflow-hidden group">
                <img src="../assets/images/cats/<?php echo $row['image']; ?>" class="w-full h-64 object-cover transform group-hover:scale-105 transition duration-500">
                <!-- Status Badge -->
                <span class="absolute top-4 right-4 bg-emerald-100 text-emerald-800 text-xs font-bold px-3 py-1 rounded-full shadow-sm">
                    <?php echo $row['status']; ?>
                </span>
            </div>
        <?php } ?>

        <div class="p-6">
            <!-- Name & Breed -->
            <div class="flex justify-between items-start mb-2">
                <h3 class="text-2xl font-bold text-gray-800 hover:text-orange-500 transition">
                    <a href="/PawTrack/cats/view.php?id=<?php echo $row['catid']; ?>">
                        <?php echo $row['name']; ?>
                    </a>
                </h3>
                <span class="bg-orange-50 text-orange-600 text-xs font-semibold px-2.5 py-1 rounded-md">
                    <?php echo $row['breed']; ?>
                </span>
            </div>

            <!-- Meta Details (Icon-friendly text layout) -->
            <div class="space-y-2 mt-4 text-sm text-gray-600">
                <div class="flex items-center gap-2">
                    <span class="text-gray-400">🐾</span>
                    <p><b>Age:</b> <?php echo $row['agecategory']; ?></p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-gray-400">📍</span>
                    <p><b>Shelter:</b> <?php echo $row['sheltername']; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions bound to the bottom -->
    <div class="p-6 pt-0 border-t border-gray-50 mt-4 flex items-center justify-between">
        <a href="/PawTrack/cats/view.php?id=<?php echo $row['catid']; ?>" class="text-sm font-medium text-gray-600 hover:text-orange-500 underline">
            View Details
        </a>
        <?php if (!$role || $role == "Adopter") { ?>
            <a href="/PawTrack/auth/login.php?redirect=/PawTrack/adoption/add.php?catid=<?php echo $row['catid']; ?>" 
               class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-5 py-2.5 rounded-xl shadow-md hover:shadow-lg transition text-sm">
                Adopt Me
            </a>
        <?php } ?>
    </div>
</div>

<?php } ?>

</div>
</div>
</div>

<?php include("../includes/footer.php"); ?>