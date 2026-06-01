<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("../config/db.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = "Adopter"; // Default public role baseline

    $query = "
        INSERT INTO \"User\" (username, password, name, email, role, shelterid)
        VALUES ($1, $2, $3, $4, $5, NULL)
    ";

    $result = pg_query_params($conn, $query, [$username, $password, $name, $email, $role]);

    if ($result) {
        $_SESSION['message'] = "Registration successful. Please login.";
        header("Location: /PawTrack/auth/login.php");
        exit;
    } else {
        $error = "Registration failed. Username or email might already be active.";
    }
}

include("../includes/header.php");
?>

<div class="max-w-4xl mx-auto bg-white rounded-3xl border border-sky-100/60 shadow-xl overflow-hidden min-h-[550px] flex flex-col md:flex-row mt-4">
    <!-- LEFT SIDE: ARTWORK PANEL -->
    <div class="md:w-1/2 bg-sky-50 relative flex flex-col justify-between p-8 text-center border-b md:border-b-0 md:border-r border-sky-100/40">
        <div class="absolute inset-0 opacity-10 pointer-events-none bg-[radial-gradient(#0ea5e9_1px,transparent_1px)] [background-size:16px_16px]"></div>
        <div class="relative z-10 my-auto flex flex-col items-center">
            <img src="/PawTrack/assets/images/Cat Loading.jpg" alt="PawTrack Registration Art" class="w-72 h-auto object-contain rounded-2xl mix-blend-multiply transition duration-500 hover:scale-102">
            <h3 class="text-2xl font-bold text-sky-800 tracking-tight mt-6">Join the Community</h3>
            <p class="text-sky-600/80 text-sm max-w-xs mt-2 mx-auto">Create a public adopter account to easily search records, manage applications, and contribute donations.</p>
        </div>
    </div>

    <!-- RIGHT SIDE: FORM CONSOLE -->
    <div class="md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
        <div class="mb-6 text-center md:text-left">
            <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">Create Account</h2>
            <p class="text-slate-500 text-sm mt-1">Get started by setting up your public account profile.</p>
        </div>

        <?php if (isset($error)) { ?>
            <div class="bg-rose-50 border border-rose-100 text-rose-600 text-sm p-4 rounded-xl mb-4 flex items-center gap-2">
                <span>⚠️</span> <b>Error:</b> <?php echo $error; ?>
            </div>
        <?php } ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Full Name</label>
                <input type="text" name="name" placeholder="e.g. Nadia Amani" required
                       class="w-full border border-sky-100 bg-slate-50/50 px-4 py-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-sm transition">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Username</label>
                <input type="text" name="username" placeholder="Choose username" required
                       class="w-full border border-sky-100 bg-slate-50/50 px-4 py-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-sm transition">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Email Address</label>
                <input type="email" name="email" placeholder="name@example.com" required
                       class="w-full border border-sky-100 bg-slate-50/50 px-4 py-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-sm transition">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Password</label>
                <input type="password" name="password" placeholder="Create strong password" required
                       class="w-full border border-sky-100 bg-slate-50/50 px-4 py-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-sm transition">
            </div>

            <button type="submit" class="w-full bg-sky-500 hover:bg-sky-600 text-white font-semibold py-3 rounded-xl shadow-md hover:shadow-lg transition duration-200 text-sm mt-2">
                Register Account
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-500">
            Already have an account? <a href="/PawTrack/auth/login.php" class="text-sky-600 font-semibold hover:underline">Login here</a>
        </p>
    </div>
</div>

<?php include("../includes/footer.php"); ?>