<?php

include("../config/db.php");
include("../includes/header.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = $_POST['name'];
    $address = $_POST['address'];
    $district = $_POST['district'];
    $description = $_POST['description'];
    $capacity = $_POST['capacity'];

    $imageName = "";

    if (!empty($_FILES['image']['name'])) {

        $imageName = time() . "_" . $_FILES['image']['name'];

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            "../assets/images/shelters/" . $imageName
        );
    }

    $query = "
    INSERT INTO Shelter
    (Name, Address, District, Description, Capacity, Image)
    VALUES
    ($1, $2, $3, $4, $5, $6)
    ";

    $result = pg_query_params(
        $conn,
        $query,
        [$name, $address, $district, $description, $capacity, $imageName]
    );

    if ($result) {

        $_SESSION['message'] = "Shelter added successfully!";
        header("Location: list.php");
        exit;

    } else {
        echo "Failed to add shelter.";
    }
}

?>

<h2 class="text-2xl font-bold mb-6">Add Shelter</h2>

<form method="POST" enctype="multipart/form-data"
class="bg-white p-6 rounded shadow">

    <input type="text" name="name" placeholder="Shelter Name"
           class="w-full border p-2 mb-3 rounded">

    <input type="text" name="address" placeholder="Address"
           class="w-full border p-2 mb-3 rounded">

    <input type="text" name="district" placeholder="District"
           class="w-full border p-2 mb-3 rounded">

    <input type="number" name="capacity" placeholder="Capacity"
           class="w-full border p-2 mb-3 rounded">

    <textarea name="description" placeholder="Description"
              class="w-full border p-2 mb-3 rounded"></textarea>

    <input type="file" name="image" class="mb-4">

    <div class="flex gap-3">

        <button type="submit"
                class="bg-green-500 text-white px-4 py-2 rounded">
            Save
        </button>

        <a href="list.php"
           class="bg-gray-400 text-white px-4 py-2 rounded">
            Cancel
        </a>

    </div>

</form>

<?php include("../includes/footer.php"); ?>