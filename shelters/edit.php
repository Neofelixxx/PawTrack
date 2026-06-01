<?php

include("../config/db.php");
include("../includes/header.php");

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = $_POST['name'];
    $address = $_POST['address'];
    $district = $_POST['district'];
    $description = $_POST['description'];
    $capacity = $_POST['capacity'];

    $imgQuery = pg_query($conn, "SELECT image FROM Shelter WHERE shelterid = $id");
    $old = pg_fetch_assoc($imgQuery);
    $imageName = $old['image'];

    if (!empty($_FILES['image']['name'])) {

        $imageName = time() . "_" . $_FILES['image']['name'];

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            "../assets/images/shelters/" . $imageName
        );
    }

    $query = "
    UPDATE Shelter SET
        name = $1,
        address = $2,
        district = $3,
        description = $4,
        capacity = $5,
        image = $6
    WHERE shelterid = $7
    ";

    $result = pg_query_params(
        $conn,
        $query,
        [$name, $address, $district, $description, $capacity, $imageName, $id]
    );

    if ($result) {

        $_SESSION['message'] = "Shelter updated successfully!";
        header("Location: list.php");
        exit;

    } else {
        echo "Update failed.";
    }
}

$data = pg_fetch_assoc(
    pg_query($conn, "SELECT * FROM Shelter WHERE shelterid = $id")
);

?>

<h2 class="text-2xl font-bold mb-6">Edit Shelter</h2>

<form method="POST" enctype="multipart/form-data"
class="bg-white p-6 rounded shadow">

    <input type="text" name="name"
           value="<?php echo $data['name']; ?>"
           class="w-full border p-2 mb-3 rounded">

    <input type="text" name="address"
           value="<?php echo $data['address']; ?>"
           class="w-full border p-2 mb-3 rounded">

    <input type="text" name="district"
           value="<?php echo $data['district']; ?>"
           class="w-full border p-2 mb-3 rounded">

    <input type="number" name="capacity"
           value="<?php echo $data['capacity']; ?>"
           class="w-full border p-2 mb-3 rounded">

    <textarea name="description"
              class="w-full border p-2 mb-3 rounded"><?php echo $data['description']; ?></textarea>

    <div class="mb-3">
        <?php if ($data['image']) { ?>
            <img src="../assets/images/shelters/<?php echo $data['image']; ?>"
                 class="w-32 h-32 object-cover rounded">
        <?php } ?>
    </div>

    <input type="file" name="image" class="mb-4">

    <div class="flex gap-3">

        <button type="submit"
                class="bg-blue-500 text-white px-4 py-2 rounded">
            Update
        </button>

        <a href="list.php"
           class="bg-gray-400 text-white px-4 py-2 rounded">
            Cancel
        </a>

    </div>

</form>

<?php include("../includes/footer.php"); ?>