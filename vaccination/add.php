<?php

include("../config/db.php");
include("../includes/header.php");

$role = $_SESSION['role'] ?? null;

if (!$role || ($role != "Admin" && $role != "Staff")) {

    header("Location: /PawTrack/auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $catid = $_POST['catid'];
    $vaccineid = $_POST['vaccineid'];
    $date = $_POST['date'];
    $cost = $_POST['cost'];

    $query = "
    INSERT INTO Vaccination_Record
    (
        CatID,
        VaccineID,
        Date,
        Cost
    )
    VALUES
    (
        $1, $2, $3, $4
    )
    ";

    $result = pg_query_params(
        $conn,
        $query,
        [
            $catid,
            $vaccineid,
            $date,
            $cost
        ]
    );

    if ($result) {

        $_SESSION['message'] =
            "Vaccination record added successfully.";

        header("Location: /PawTrack/vaccination/list.php");
        exit;
    }
}

$cats = pg_query(
    $conn,
    "SELECT * FROM Cat ORDER BY Name"
);

$vaccines = pg_query(
    $conn,
    "SELECT * FROM Vaccination ORDER BY VaccineName"
);

?>

<h2 class="text-3xl font-bold text-[#0b1f3b] mb-6">
    Add Vaccination Record
</h2>

<form
    method="POST"
    class="bg-white p-6 rounded-2xl shadow-lg"
>

    <!-- CAT -->
    <div class="mb-4">

        <label class="block font-semibold mb-1">
            Cat
        </label>

        <select
            name="catid"
            required
            class="w-full border rounded-lg p-2"
        >

            <?php while ($cat = pg_fetch_assoc($cats)) { ?>

                <option value="<?php echo $cat['catid']; ?>">

                    <?php echo $cat['name']; ?>

                </option>

            <?php } ?>

        </select>

    </div>

    <!-- VACCINE -->
    <div class="mb-4">

        <label class="block font-semibold mb-1">
            Vaccine
        </label>

        <select
            name="vaccineid"
            required
            class="w-full border rounded-lg p-2"
        >

            <?php while ($v = pg_fetch_assoc($vaccines)) { ?>

                <option value="<?php echo $v['vaccineid']; ?>">

                    <?php echo $v['vaccinename']; ?>

                </option>

            <?php } ?>

        </select>

    </div>

    <!-- DATE -->
    <div class="mb-4">

        <label class="block font-semibold mb-1">
            Vaccination Date
        </label>

        <input
            type="date"
            name="date"
            required
            class="w-full border rounded-lg p-2"
        >

    </div>

    <!-- COST -->
    <div class="mb-6">

        <label class="block font-semibold mb-1">
            Cost (RM)
        </label>

        <input
            type="number"
            step="0.01"
            name="cost"
            class="w-full border rounded-lg p-2"
        >

    </div>

    <!-- BUTTONS -->
    <div class="flex gap-3">

        <button
            type="submit"
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
            Save Record
        </button>

        <a
            href="/PawTrack/vaccination/list.php"
            class="
            bg-gray-400
            hover:bg-gray-500
            text-white
            px-4
            py-2
            rounded-lg
            transition
            duration-300
            "
        >
            Cancel
        </a>

    </div>

</form>

<?php include("../includes/footer.php"); ?>