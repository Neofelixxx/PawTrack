<?php

include("../config/db.php");
include("../includes/header.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = $_POST['name'];
    $breed = $_POST['breed'];
    $birthdate = $_POST['birthdate'];
    $agecategory = $_POST['agecategory'];
    $gender = $_POST['gender'];
    $description = $_POST['description'];
    $shelterid = $_POST['shelterid'];

    $imageName = "";

    // Upload image
    if (!empty($_FILES['image']['name'])) {

        $imageName = time() . "_" . $_FILES['image']['name'];

        $target = "../assets/images/cats/" . $imageName;

        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    }

    $query = "
    INSERT INTO Cat
    (
        ShelterID,
        Name,
        Breed,
        BirthDate,
        AgeCategory,
        Gender,
        Description,
        Image
    )
    VALUES
    (
        $1, $2, $3, $4, $5, $6, $7, $8
    )
    ";

    $result = pg_query_params(
        $conn,
        $query,
        [
            $shelterid,
            $name,
            $breed,
            $birthdate ?: null,
            $agecategory,
            $gender,
            $description,
            $imageName
        ]
    );

    if ($result) { 
        
        $_SESSION['message'] = "Cat added successfully!";
        header("Location: list.php");
        exit;

    } else {

        echo "<p class='text-red-500'>Failed to add cat.</p>";
    }
}

$shelters = pg_query(
    $conn,
    "SELECT * FROM Shelter ORDER BY Name"
);

?>

<h2 class="text-2xl font-bold mb-6">Add Cat</h2>

<form method="POST" enctype="multipart/form-data"
class="bg-white p-6 rounded shadow">

    <div class="mb-4">

        <label class="block font-semibold mb-1">
            Name
        </label>

        <input
            type="text"
            name="name"
            class="w-full border rounded p-2"
        >

    </div>

    <div class="mb-4">

        <label class="block font-semibold mb-1">
            Breed
        </label>

        <input
            type="text"
            name="breed"
            class="w-full border rounded p-2"
        >

    </div>

    <div class="mb-4">

        <label class="block font-semibold mb-1">
            Birth Date
        </label>

        <input
            type="date"
            name="birthdate"
            class="w-full border rounded p-2"
        >

    </div>

    <div class="mb-4">

        <label class="block font-semibold mb-1">
            Age Category
        </label>

        <select
            name="agecategory"
            class="w-full border rounded p-2"
        >

            <option value="Unknown">Unknown</option>
            <option value="Kitten">Kitten</option>
            <option value="Juvenile">Juvenile</option>
            <option value="Adult">Adult</option>
            <option value="Senior">Senior</option>

        </select>

    </div>

    <div class="mb-4">

        <label class="block font-semibold mb-1">
            Gender
        </label>

        <select
            name="gender"
            class="w-full border rounded p-2"
        >

            <option value="Male">Male</option>
            <option value="Female">Female</option>

        </select>

    </div>

    <div class="mb-4">

        <label class="block font-semibold mb-1">
            Shelter
        </label>

        <select
            name="shelterid"
            required
            class="w-full border rounded p-2"
        >

            <?php while ($shelter = pg_fetch_assoc($shelters)) { ?>

                <option value="<?php echo $shelter['shelterid']; ?>">

                    <?php echo $shelter['name']; ?>

                </option>

            <?php } ?>

        </select>

    </div>

    <div class="mb-4">

        <label class="block font-semibold mb-1">
            Description
        </label>

        <textarea
            name="description"
            class="w-full border rounded p-2"
        ></textarea>

    </div>

    <div class="mb-6">

        <label class="block font-semibold mb-1">
            Cat Image
        </label>

        <input
            type="file"
            name="image"
            class="w-full"
        >

    </div>

<div class="flex gap-3 mt-6">

    <button
        type="submit"
        class="bg-green-500 text-white px-4 py-2 rounded"
    >
        Save
    </button>

    <a href="list.php"
       class="bg-gray-400 text-white px-4 py-2 rounded">
        Cancel
    </a>

</div>

</form>

<?php include("../includes/footer.php"); ?>