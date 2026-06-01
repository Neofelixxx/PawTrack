<?php

include("../config/db.php");
include("../includes/header.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "
        SELECT * FROM \"User\"
        WHERE username = $1
        LIMIT 1
    ";

    $result = pg_query_params($conn, $query, [$username]);

    $user = pg_fetch_assoc($result);

    if ($user) {

        if ($password == $user['password']) {

            $_SESSION['user'] = $user['userid'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];

            $redirect =
                $_GET['redirect']
                ?? "/PawTrack/index.php";

            header("Location: $redirect");
            exit;

        } else {
            $error = "Invalid password";
        }

    } else {
        $error = "User not found";
    }
}

?>

<div class="max-w-md mx-auto bg-white p-6 rounded shadow">

    <h2 class="text-2xl font-bold mb-6 text-center">
        Login
    </h2>

    <?php if (isset($error)) { ?>

        <p class="text-red-500 mb-4">
            <?php echo $error; ?>
        </p>

    <?php } ?>

    <form method="POST">

        <input
            type="text"
            name="username"
            placeholder="Username"
            required
            class="w-full border p-2 mb-3 rounded"
        >

        <input
            type="password"
            name="password"
            placeholder="Password"
            required
            class="w-full border p-2 mb-4 rounded"
        >

        <button
            type="submit"
            class="bg-blue-500 text-white px-4 py-2 rounded w-full"
        >
            Login
        </button>

    </form>

    <p class="mt-4 text-center">

        Don't have an account?

        <a
            href="/PawTrack/auth/register.php"
            class="text-blue-500 underline"
        >
            Register
        </a>

    </p>

</div>

<?php include("../includes/footer.php"); ?>