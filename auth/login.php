<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Automatically clear out old session states if visiting the page fresh via GET
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    unset($_SESSION['user_id']);
    unset($_SESSION['username']);
    unset($_SESSION['role']);
    unset($_SESSION['shelter_id']);
}

include("../config/db.php");
include("../includes/header.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $query = "
        SELECT * FROM \"User\"
        WHERE username = $1
        LIMIT 1
    ";

    $result = pg_query_params($conn, $query, [$username]);
    $user = pg_fetch_assoc($result);

    if ($user) {
        // Plain text check matching your seed table encryption state
        if ($password == $user['password']) {
            
            // 🔥 CRITICAL ACCORDANCE RE-ALIGNMENT FIX:
            $_SESSION['user_id']    = (int)$user['userid'];
            $_SESSION['username']   = $user['username'];
            $_SESSION['role']       = $user['role'];
            $_SESSION['name']       = $user['name'];
            $_SESSION['shelter_id'] = $user['shelterid'] ? (int)$user['shelterid'] : null;

            // Handle clean direction jumping routing
            $redirect = $_GET['redirect'] ?? "/PawTrack/dashboard/index.php";

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

<div class="max-w-4xl mx-auto bg-white rounded-3xl border border-sky-100/60 shadow-xl overflow-hidden min-h-[500px] flex flex-col md:flex-row mt-4">
    
    <div class="md:w-1/2 bg-sky-50 relative flex flex-col justify-between p-8 text-center border-b md:border-b-0 md:border-r border-sky-100/40">
        <div class="absolute inset-0 opacity-10 pointer-events-none bg-[radial-gradient(#0ea5e9_1px,transparent_1px)] [background-size:16px_16px]"></div>
        
        <div class="relative z-10 my-auto flex flex-col items-center">
            <img src="/PawTrack/assets/images/Cat Loading.jpg" 
                 alt="PawTrack Shelter Art" 
                 class="w-72 h-auto object-contain rounded-2xl mix-blend-multiply transition duration-500 hover:scale-102">
            <h3 class="text-2xl font-bold text-sky-800 tracking-tight mt-6">Welcome Back to PawTrack</h3>
            <p class="text-sky-600/80 text-sm max-w-xs mt-2 mx-auto">Helping Johor Bahru shelters streamline stray cat rescue and adoption tracking operations.</p>
        </div>
    </div>

    <div class="md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
        <div class="mb-6 text-center md:text-left">
            <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">Account Login</h2>
            <p class="text-slate-500 text-sm mt-1">Access your personalized management console dashboard.</p>
        </div>

        <?php if (isset($error)) { ?>
            <div class="bg-rose-50 border border-rose-100 text-rose-600 text-sm p-4 rounded-xl mb-6 flex items-center gap-2">
                <span>⚠️</span> <b>Error:</b> <?php echo $error; ?>
            </div>
        <?php } ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Username</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-3 text-slate-400">👤</span>
                    <input type="text" name="username" placeholder="Enter username" required
                           class="w-full border border-sky-100 bg-slate-50/50 pl-10 pr-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-sm transition">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Password</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-3 text-slate-400">🔒</span>
                    <input type="password" name="password" placeholder="••••••••" required
                           class="w-full border border-sky-100 bg-slate-50/50 pl-10 pr-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-sm transition">
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-sky-500 hover:bg-sky-600 text-white font-semibold py-3.5 rounded-xl shadow-md hover:shadow-lg transition duration-200 text-sm mt-2">
                Sign In
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-slate-500">
            Don't have an account? 
            <a href="/PawTrack/auth/register.php" class="text-sky-600 font-semibold hover:underline">Register here</a>
        </p>
    </div>
</div>

<?php include("../includes/footer.php"); ?>