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
    $treatid = $_POST['treatid'];
    $category = $_POST['category'];
    $cost = $_POST['cost'];
    $date = $_POST['date'];
    $notes = $_POST['notes'];

    $query = "
    INSERT INTO Medical_Record
    (
        CatID,
        TreatID,
        Category,
        Cost,
        TreatmentDate,
        Notes
    )
    VALUES
    (
        $1, $2, $3, $4, $5, $6
    )
    ";

    $result = pg_query_params(
        $conn,
        $query,
        [
            $catid,
            $treatid,
            $category,
            $cost,
            $date,
            $notes
        ]
    );

    if ($result) {

        $_SESSION['message'] =
            "Medical record added successfully.";

        header("Location: /PawTrack/medical/list.php");
        exit;
    }
}

$cats = pg_query(
    $conn,
    "SELECT * FROM Cat ORDER BY Name"
);

$treatments = pg_query(
    $conn,
    "SELECT * FROM Treatment ORDER BY TreatName"
);

?>

<h2 class="text-3xl font-bold text-[#0b1f3b] mb-6">
    Add Medical Record
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

    <!-- TREATMENT -->
    <div class="mb-4">

        <label class="block font-semibold mb-1">
            Treatment
        </label>

        <select
            name="treatid"
            required
            class="w-full border rounded-lg p-2"
        >

            <?php while ($t = pg_fetch_assoc($treatments)) { ?>

                <option value="<?php echo $t['treatid']; ?>">

                    <?php echo $t['treatname']; ?>

                </option>

            <?php } ?>

        </select>

    </div>

    <!-- CATEGORY -->
    <div class="mb-4">

        <label class="block font-semibold mb-1">
            Category
        </label>

        <input
            type="text"
            name="category"
            class="w-full border rounded-lg p-2"
            placeholder="Example: Surgery"
        >

    </div>

    <!-- COST -->
    <div class="mb-4">

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

    <!-- DATE -->
    <div class="mb-4">

        <label class="block font-semibold mb-1">
            Treatment Date
        </label>

        <input
            type="date"
            name="date"
            class="w-full border rounded-lg p-2"
        >

    </div>

    <!-- NOTES -->
    <div class="mb-6">

        <label class="block font-semibold mb-1">
            Notes
        </label>

        <textarea
            name="notes"
            rows="4"
            class="w-full border rounded-lg p-2"
        ></textarea>

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
            href="/PawTrack/medical/list.php"
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