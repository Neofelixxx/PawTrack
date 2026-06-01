<?php

include("../config/db.php");
include("../includes/header.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $userid = $_SESSION['user'] ?? null;
    $shelterid = $_POST['shelterid'];
    $type = $_POST['type'];
    $donorname = $_POST['donorname'];
    $amount = $_POST['amount'] ?? null;
    $item = $_POST['item'] ?? null;
    $qty = $_POST['quantity'] ?? null;

    $query = "
    INSERT INTO Donations
    (
        UserID,
        ShelterID,
        DonorName,
        Type,
        Amount,
        ItemDescription,
        Quantity,
        DonationDate
    )
    VALUES
    (
        $1,$2,$3,$4,$5,$6,$7,CURRENT_DATE
    )
    ";

    $result = pg_query_params(
        $conn,
        $query,
        [
            $userid,
            $shelterid,
            $donorname,
            $type,
            $amount,
            $item,
            $qty
        ]
    );

    if ($result) {

        $_SESSION['message'] = "Donation submitted successfully!";
        header("Location: /PawTrack/donation/list.php");
        exit;
    }
}

$shelters = pg_query($conn, "SELECT * FROM Shelter");

?>

<h2 class="text-3xl font-bold mb-6 text-[#0b1f3b]">
    Make a Donation
</h2>

<form method="POST" class="bg-white p-6 rounded-2xl shadow-lg">

    <!-- NAME -->
    <input
        type="text"
        name="donorname"
        placeholder="Your Name"
        required
        class="w-full border p-2 rounded mb-4"
    >

    <!-- SHELTER -->
    <select name="shelterid" class="w-full border p-2 rounded mb-4">

        <?php while ($s = pg_fetch_assoc($shelters)) { ?>

            <option value="<?php echo $s['shelterid']; ?>">
                <?php echo $s['name']; ?>
            </option>

        <?php } ?>

    </select>

    <!-- TYPE -->
    <select name="type" class="w-full border p-2 rounded mb-4">

        <option value="Money">Money</option>
        <option value="Item">Item</option>

    </select>

    <!-- AMOUNT -->
    <input
        type="number"
        name="amount"
        placeholder="Amount (RM)"
        class="w-full border p-2 rounded mb-4"
    >

    <!-- ITEM -->
    <input
        type="text"
        name="item"
        placeholder="Item Description"
        class="w-full border p-2 rounded mb-4"
    >

    <!-- QTY -->
    <input
        type="number"
        name="quantity"
        placeholder="Quantity"
        class="w-full border p-2 rounded mb-6"
    >

    <button
        class="
        bg-[#3679f7]
        hover:bg-[#4ec5c1]
        text-white
        px-4
        py-2
        rounded-lg
        transition
        duration-300
        w-full
        "
    >
        Submit Donation
    </button>

</form>

<?php include("../includes/footer.php"); ?>