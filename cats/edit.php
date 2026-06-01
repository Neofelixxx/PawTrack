<?php

include("../config/db.php");
include("../includes/header.php");

// Get Cat ID
$id = $_GET['id'] ?? null;

if (!$id) {
    die("Invalid Cat ID");
}

// UPDATE logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = $_POST['name'];
    $breed = $_POST['breed'];
    $birthdate = $_POST['birthdate'];
    $agecategory = $_POST['agecategory'];
    $gender = $_POST['gender'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $shelterid = $_POST['shelterid'];

    // Get existing image first
    $cat = pg_query($conn, "SELECT image FROM Cat WHERE catid = $id");
    $old = pg_fetch_assoc($cat);
    $imageName = $old['image'];

    // If new image uploaded
    if (!empty($_FILES['image']['name'])) {

        $imageName = time() . "_" . $_FILES['image']['name'];

        $target = "../assets/images/cats/" . $imageName;

        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    }

    $query = "
    UPDATE Cat SET
        name = $1,
        breed = $2,
        birthdate = $3,
        agecategory = $4,
        gender = $5,
        description = $6,
        status = $7,
        shelterid = $8,
        image = $9
    WHERE catid = $10
    ";

    $result = pg_query_params(
        $conn,
        $query,
        [
            $name,
            $breed,
            $birthdate ?: null,
            $agecategory,
            $gender,
            $description,
            $status,
            $shelterid,
            $imageName,
            $id
        ]
    );

    if ($result) {
        $_SESSION['message'] = "Cat updated successfully!";
        header("Location: list.php");
        exit;
    } else {
        echo "Update failed.";
    }
}

// Load cat data
$query = "SELECT * FROM Cat WHERE catid = $id";
$result = pg_query($conn, $query);
$cat = pg_fetch_assoc($result);

// Load shelters
$shelters = pg_query($conn, "SELECT * FROM Shelter ORDER BY name");

?>

<h2 class="text-2xl font-bold mb-6">Edit Cat</h2>

<form method="POST" enctype="multipart/form-data"
class="bg-white p-6 rounded shadow">

    <input type="hidden" name="id" value="<?php echo $id; ?>">

    <div class="mb-4">
        <label>Name</label>
        <input type="text" name="name"
            value="<?php echo $cat['name']; ?>"
            class="w-full border p-2 rounded">
    </div>

    <div class="mb-4">
        <label>Breed</label>
        <input type="text" name="breed"
            value="<?php echo $cat['breed']; ?>"
            class="w-full border p-2 rounded">
    </div>

    <div class="mb-4">
        <label>Birth Date</label>
        <input type="date" name="birthdate"
            value="<?php echo $cat['birthdate']; ?>"
            class="w-full border p-2 rounded">
    </div>

    <div class="mb-4">
        <label>Age Category</label>
        <select name="agecategory" class="w-full border p-2 rounded">
            <option <?php if($cat['agecategory']=='Unknown') echo 'selected'; ?>>Unknown</option>
            <option <?php if($cat['agecategory']=='Kitten') echo 'selected'; ?>>Kitten</option>
            <option <?php if($cat['agecategory']=='Juvenile') echo 'selected'; ?>>Juvenile</option>
            <option <?php if($cat['agecategory']=='Adult') echo 'selected'; ?>>Adult</option>
            <option <?php if($cat['agecategory']=='Senior') echo 'selected'; ?>>Senior</option>
        </select>
    </div>

    <div class="mb-4">
        <label>Gender</label>
        <select name="gender" class="w-full border p-2 rounded">
            <option <?php if($cat['gender']=='Male') echo 'selected'; ?>>Male</option>
            <option <?php if($cat['gender']=='Female') echo 'selected'; ?>>Female</option>
        </select>
    </div>

    <div class="mb-4">
        <label>Status</label>
        <select name="status" class="w-full border p-2 rounded">

            <?php
            $statuses = [
                'Available',
                'Adopted',
                'Under Treatment',
                'Quarantined',
                'Deceased',
                'Transferred'
            ];

            foreach ($statuses as $s) {
                $selected = ($cat['status'] == $s) ? "selected" : "";
                echo "<option $selected>$s</option>";
            }
            ?>

        </select>
    </div>

    <div class="mb-4">
        <label>Shelter</label>
        <select name="shelterid" class="w-full border p-2 rounded">

            <?php while ($s = pg_fetch_assoc($shelters)) { ?>

                <option value="<?php echo $s['shelterid']; ?>"
                    <?php if ($s['shelterid'] == $cat['shelterid']) echo "selected"; ?>>

                    <?php echo $s['name']; ?>

                </option>

            <?php } ?>

        </select>
    </div>

    <div class="mb-4">
        <label>Description</label>
        <textarea name="description"
            class="w-full border p-2 rounded"><?php echo $cat['description']; ?></textarea>
    </div>

    <div class="mb-4">
        <label>Current Image</label><br>

        <?php if ($cat['image']) { ?>
            <img src="../assets/images/cats/<?php echo $cat['image']; ?>"
                class="w-32 h-32 object-cover rounded">
        <?php } ?>
    </div>

    <div class="mb-4">
        <label>Change Image</label>
        <input type="file" name="image">
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