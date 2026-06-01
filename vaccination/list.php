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
    vr.*,
    c.Name AS CatName,
    v.VaccineName
FROM Vaccination_Record vr
JOIN Cat c
ON vr.CatID = c.CatID
JOIN Vaccination v
ON vr.VaccineID = v.VaccineID
ORDER BY vr.Date DESC
";

$result = pg_query($conn, $query);

?>

<div class="flex justify-between items-center mb-6">

    <h2 class="text-3xl font-bold text-[#0b1f3b]">
        Vaccination Records
    </h2>

    <a
        href="/PawTrack/vaccination/add.php"
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
        + Add Vaccination
    </a>

</div>

<div class="bg-white rounded-2xl shadow-lg overflow-hidden">

    <table class="w-full">

        <thead class="bg-[#0b1f3b] text-white">

            <tr>

                <th class="p-4 text-left">Cat</th>
                <th class="p-4 text-left">Vaccine</th>
                <th class="p-4 text-left">Date</th>
                <th class="p-4 text-left">Cost</th>

            </tr>

        </thead>

        <tbody>

        <?php while ($row = pg_fetch_assoc($result)) { ?>

            <tr class="border-b hover:bg-gray-50 transition">

                <td class="p-4">
                    <?php echo $row['catname']; ?>
                </td>

                <td class="p-4">
                    <?php echo $row['vaccinename']; ?>
                </td>

                <td class="p-4">
                    <?php echo $row['date']; ?>
                </td>

                <td class="p-4">
                    RM <?php echo $row['cost']; ?>
                </td>

            </tr>

        <?php } ?>

        </tbody>

    </table>

</div>

<?php include("../includes/footer.php"); ?>