<?php

include("../config/db.php");
include("../includes/header.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    $role = "Adopter";

    $query = "
        INSERT INTO \"User\"
        (
            username,
            password,
            name,
            email,
            role
        )
        VALUES
        (
            $1, $2, $3, $4, $5
        )
    ";

    $result = pg_query_params(
        $conn,
        $query,
        [
            $username,
            $password,
            $name,
            $email,
            $role
        ]
    );

    if ($result) {

        $_SESSION['message'] =
            "Registration successful. Please login.";

        header("Location: /PawTrack/auth/login.php");
        exit;

    } else {

        $error = "Registration failed.";

    }
}

?>

<div class="max-w-md mx-auto bg-white p-6 rounded shadow">

    <h2 class="text-3xl font-bold mb-6 text-center text-orange-500">
        Create Account
    </h2>

    <?php if (isset($error)) { ?>

        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <?php echo $error; ?>
        </div>

    <?php } ?>

    <form method="POST">

        <!-- NAME -->
        <div class="mb-4">

            <label class="block font-semibold mb-1">
                Full Name
            </label>

            <input
                type="text"
                name="name"
                required
                class="w-full border rounded p-2"
                placeholder="Enter your full name"
            >

        </div>

        <!-- USERNAME -->
        <div class="mb-4">

            <label class="block font-semibold mb-1">
                Username
            </label>

            <input
                type="text"
                name="username"
                required
                class="w-full border rounded p-2"
                placeholder="Choose a username"
            >

        </div>

        <!-- EMAIL -->
        <div class="mb-4">

            <label class="block font-semibold mb-1">
                Email
            </label>

            <input
                type="email"
                name="email"
                required
                class="w-full border rounded p-2"
                placeholder="Enter your email"
            >

        </div>

        <!-- PASSWORD -->
        <div class="mb-6">

            <label class="block font-semibold mb-1">
                Password
            </label>

            <input
                type="password"
                name="password"
                required
                class="w-full border rounded p-2"
                placeholder="Create a password"
            >

        </div>

        <!-- BUTTON -->
        <button
            type="submit"
            class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded w-full"
        >
            Register
        </button>

    </form>

    <p class="mt-4 text-center text-gray-600">

        Already have an account?

        <a
            href="/PawTrack/auth/login.php"
            class="text-blue-500 hover:underline"
        >
            Login
        </a>

    </p>

</div>

<?php include("../includes/footer.php"); ?>