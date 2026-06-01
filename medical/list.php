<?php

include("../config/db.php");
include("../includes/header.php");

$role = $_SESSION['role'] ?? null;

if (!$role || ($role != "Admin" && $role != "Staff")) {

    header("Location: /PawTrack/auth/login.php");
    exit;
}

$query = "
SELECT
    m.*,
    c.Name AS CatName,
    t.TreatName
FROM Medical_Record m
JOIN Cat c
ON m.CatID = c.CatID
JOIN Treatment t
ON m.TreatID = t.TreatID
ORDER BY m.TreatmentDate DESC
";

$result = pg_query($conn, $query);

?>

<div class="flex justify-between items-center mb-6">

    <h2 class="text-3xl font-bold text-[#0b1f3b]">
        Medical Records
    </h2>

    <a
        href="/PawTrack/medical/add.php"
        class="
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
        + Add Record
    </a>

</div>

<div class="bg-white rounded-2xl shadow-lg overflow-hidden">

    <table class="w-full">

        <thead class="bg-[#0b1f3b] text-white">

            <tr>

                <th class="p-4 text-left">Cat</th>
                <th class="p-4 text-left">Treatment</th>
                <th class="p-4 text-left">Category</th>
                <th class="p-4 text-left">Cost</th>
                <th class="p-4 text-left">Date</th>

            </tr>

        </thead>

        <tbody>

        <?php while ($row = pg_fetch_assoc($result)) { ?>

            <tr class="border-b hover:bg-gray-50 transition">

                <td class="p-4">
                    <?php echo $row['catname']; ?>
                </td>

                <td class="p-4">
                    <?php echo $row['treatname']; ?>
                </td>

                <td class="p-4">
                    <?php echo $row['category']; ?>
                </td>

                <td class="p-4">
                    RM <?php echo $row['cost']; ?>
                </td>

                <td class="p-4">
                    <?php echo $row['treatmentdate']; ?>
                </td>

            </tr>

        <?php } ?>

        </tbody>

    </table>

</div>

<?php include("../includes/footer.php"); ?>