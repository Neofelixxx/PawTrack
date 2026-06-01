<?php

include("../config/db.php");
include("../includes/header.php");

$role = $_SESSION['role'] ?? null;

$query = "
SELECT
    d.*,
    s.Name AS ShelterName
FROM Donations d
JOIN Shelter s
ON d.ShelterID = s.ShelterID
ORDER BY d.DonationDate DESC
";

$result = pg_query($conn, $query);

?>

<div class="flex justify-between items-center mb-6">

    <h2 class="text-3xl font-bold text-[#0b1f3b]">
        Donations
    </h2>

    <a
        href="/PawTrack/donation/add.php"
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
        + New Donation
    </a>

</div>

<div class="bg-white rounded-2xl shadow-lg overflow-hidden">

    <table class="w-full">

        <thead class="bg-[#0b1f3b] text-white">

            <tr>

                <th class="p-4 text-left">Donor</th>
                <th class="p-4 text-left">Type</th>
                <th class="p-4 text-left">Amount / Item</th>
                <th class="p-4 text-left">Shelter</th>
                <th class="p-4 text-left">Date</th>

            </tr>

        </thead>

        <tbody>

        <?php while ($row = pg_fetch_assoc($result)) { ?>

            <tr class="border-b hover:bg-gray-50 transition">

                <td class="p-4">
                    <?php echo $row['donorname']; ?>
                </td>

                <td class="p-4">
                    <?php echo $row['type']; ?>
                </td>

                <td class="p-4">

                    <?php
                    if ($row['type'] == 'Money') {
                        echo "RM " . $row['amount'];
                    } else {
                        echo $row['itemdescription'] . " (x" . $row['quantity'] . ")";
                    }
                    ?>

                </td>

                <td class="p-4">
                    <?php echo $row['sheltername']; ?>
                </td>

                <td class="p-4">
                    <?php echo $row['donationdate']; ?>
                </td>

            </tr>

        <?php } ?>

        </tbody>

    </table>

</div>

<?php include("../includes/footer.php"); ?>