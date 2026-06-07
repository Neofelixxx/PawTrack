<?php
include("../config/db.php");
include("../includes/header.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['role'] ?? null;
if (!$role) {
    header("Location: /PawTrack/auth/login.php?redirect=/PawTrack/adoption/add.php?catid=" . ($_GET['catid'] ?? ''));
    exit;
}

if ($role != 'Adopter') {
    die("<div class='p-8 text-center text-rose-600 font-bold'>System Error: Only registered public Adopter accounts can file application requests. Management staff cannot adopt via this node.</div>");
}

$catid = $_GET['catid'] ?? null;
if (!$catid) {
    die("Invalid Cat Reference ID");
}

$query = "SELECT * FROM Cat WHERE catid = $1";
$result = pg_query_params($conn, $query, [$catid]);
$cat = pg_fetch_assoc($result);

if (!$cat || $cat['status'] != 'Available') {
    die("<div class='p-8 text-center text-rose-600 font-bold'>This feline is no longer available for adoption.</div>");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userid = $_SESSION['user_id']; // Assuming your session variable for ID is user_id based on standard setup
    
    // Compile the questionnaire answers into a structured text block for staff review
    $housing = trim($_POST['housing_type']);
    $other_pets = trim($_POST['other_pets']);
    $experience = trim($_POST['experience']);
    $financial = trim($_POST['financial_readiness']);
    $reason = trim($_POST['adoption_reason']);

    $compiled_details = "HOUSING: $housing\n";
    $compiled_details .= "OTHER PETS: $other_pets\n";
    $compiled_details .= "EXPERIENCE: $experience\n";
    $compiled_details .= "FINANCIAL COMMITMENT: $financial\n";
    $compiled_details .= "REASON: $reason";

    $insert = "
        INSERT INTO Adoption (catid, adopterid, status, application_details, adoptiondate)
        VALUES ($1, $2, 'Pending', $3, CURRENT_DATE)
    ";

    $save = pg_query_params($conn, $insert, [$catid, $userid, $compiled_details]);

    if ($save) {
        // Automatically lock the cat so others can't apply while this is pending
        pg_query_params($conn, "UPDATE Cat SET Status = 'Under Treatment' WHERE catid = $1", [$catid]); 
        
        $_SESSION['message'] = "Adoption application submitted for screening.";
        header("Location: /PawTrack/adoption/list.php");
        exit;
    } else {
        $error = "Database insertion failed.";
    }
}
?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 mt-6 mb-12">
    <div class="mb-8">
        <h2 class="text-3xl font-black text-slate-900 tracking-tight">Adoption Vetting Form</h2>
        <p class="text-slate-600 text-sm mt-2">You are filing an official request to adopt <b class="text-sky-700"><?php echo htmlspecialchars($cat['name']); ?></b>. Please complete the screening questionnaire below truthfully.</p>
    </div>

    <form method="POST" class="bg-white p-8 rounded-3xl border border-sky-100 shadow-sm space-y-8">
        
        <!-- Section 1: Household Environment -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold text-sky-600 uppercase tracking-wider border-b border-sky-50 pb-2">1. Household Environment</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Housing Type</label>
                    <select name="housing_type" required class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm font-medium text-slate-700">
                        <option value="Landed Property (Owned)">Landed Property (Owned)</option>
                        <option value="Landed Property (Rented - Landlord Approved)">Landed Property (Rented - Landlord Approved)</option>
                        <option value="Apartment/Condo (Pet Friendly)">Apartment/Condo (Pet Friendly)</option>
                        <option value="Student Accommodation / Hostel">Student Accommodation / Hostel</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Other Current Pets?</label>
                    <select name="other_pets" required class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm font-medium text-slate-700">
                        <option value="None">None</option>
                        <option value="Other Cats">Other Cats</option>
                        <option value="Dogs">Dogs</option>
                        <option value="Exotic Pets / Reptiles">Exotic Pets / Reptiles</option>
                        <option value="Small Mammals (Hamsters, Rabbits)">Small Mammals</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Section 2: Experience & Financials -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold text-sky-600 uppercase tracking-wider border-b border-sky-50 pb-2">2. Readiness & Capabilities</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Feline Experience</label>
                    <select name="experience" required class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm font-medium text-slate-700">
                        <option value="First Time Owner">First Time Owner</option>
                        <option value="Have owned cats in the past">Have owned cats in the past</option>
                        <option value="Currently own cats">Currently own cats</option>
                        <option value="Experienced with special medical needs">Experienced with special medical needs</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Financial Commitment</label>
                    <select name="financial_readiness" required class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm font-medium text-slate-700">
                        <option value="Prepared for routine costs only">Prepared for routine costs only (Food/Litter)</option>
                        <option value="Prepared for routine + emergency vet costs">Prepared for routine + emergency vet costs</option>
                        <option value="Unsure about vet costs">Unsure about vet costs</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Section 3: Intent -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold text-sky-600 uppercase tracking-wider border-b border-sky-50 pb-2">3. Statement of Intent</h3>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Why do you want to adopt this specific cat?</label>
                <textarea name="adoption_reason" required rows="3" placeholder="Provide a brief explanation of your lifestyle and why this cat is a good fit..." class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm font-medium text-slate-700"></textarea>
            </div>
        </div>

        <div class="pt-4 flex items-center justify-between border-t border-slate-100">
            <a href="/PawTrack/cats/view.php?id=<?php echo $catid; ?>" class="text-sm font-bold text-slate-500 hover:text-slate-800 transition">
                Cancel Application
            </a>
            <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-8 py-3.5 rounded-xl shadow-md transition duration-150 text-sm">
                Submit Vetting Form
            </button>
        </div>
    </form>
</div>

<?php include("../includes/footer.php"); ?>