<?php

include("../config/db.php");
include("../includes/header.php");
include("../includes/auth.php");

requireLogin();

if ($_SESSION['role'] != 'Adopter') {
    die("Only adopters can apply.");
}

$catid = $_GET['catid'] ?? null;

if (!$catid) {
    die("Invalid Cat");
}

$query = "SELECT * FROM Cat WHERE catid = $1";
$result = pg_query_params($conn, $query, [$catid]);

$cat = pg_fetch_assoc($result);

if (!$cat) {
    die("Cat not found");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $userid = $_SESSION['user'];

    $insert = "
        INSERT INTO Adoption
        (
            catid,
            adopterid,
            status
        )
        VALUES
        (
            $1,
            $2,
            'Pending'
        )
    ";

    $save = pg_query_params($conn, $insert, [
        $catid,
        $userid
    ]);

    if ($save) {

        $_SESSION['message'] =
            "Adoption application submitted.";

        header("Location: /PawTrack/adoption/list.php");
        exit;
    }
}

?>

<h2 class="text-2xl font-bold mb-6">
    Adopt <?php echo $cat['name']; ?>
</h2>

<div class="bg-white p-6 rounded shadow max-w-xl">

    <p class="mb-4">
        You are applying to adopt:
        <strong><?php echo $cat['name']; ?></strong>
    </p>

    <form method="POST">

        <button type="submit"
            class="bg-green-500 text-white px-4 py-2 rounded">

            Submit Adoption Request

        </button>

        <a href="/PawTrack/cats/view.php?id=<?php echo $catid; ?>"
           class="bg-gray-500 text-white px-4 py-2 rounded ml-2">

           Cancel

        </a>

    </form>

</div>

<?php include("../includes/footer.php"); ?>