<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("../config/db.php");
include("../includes/header.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ERD Rule: If user is logged in, grab their ID. If not, it sets to NULL (Guest)
    $userid = $_SESSION['user_id'] ?? null; 
    $shelterid = $_POST['shelterid'];
    $type = $_POST['type'];
    
    // ERD Rule: DonorName is NOT NULL if UserID is NULL
    $donorname = !empty($_POST['donorname']) ? trim($_POST['donorname']) : 'Anonymous Donor';

    // Separate values based on chosen input type
    $amount = ($type === 'Money' && !empty($_POST['amount'])) ? (float)$_POST['amount'] : null;
    $item = ($type === 'Item' && !empty($_POST['item'])) ? trim($_POST['item']) : null;
    $qty = ($type === 'Item' && !empty($_POST['quantity'])) ? (int)$_POST['quantity'] : null;

    $query = 'INSERT INTO "Donations" (UserID, ShelterID, DonorName, Type, Amount, ItemDescription, Quantity, DonationDate) VALUES ($1, $2, $3, $4, $5, $6, $7, CURRENT_DATE)';
    
    $result = pg_query_params($conn, $query, [$userid, $shelterid, $donorname, $type, $amount, $item, $qty]);

    if ($result) {
        header("Location: /PawTrack/donations/list.php");
        exit;
    }
}

$shelters = pg_query($conn, "SELECT shelterid, name FROM Shelter ORDER BY name");
?>

<div class="max-w-2xl mx-auto px-4 mt-6">
    <div class="mb-6">
        <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">Make a Contribution</h2>
        <p class="text-slate-500 text-sm">Contribute materials or custom monetary funds directly to help support our shelter operations.</p>
    </div>

    <form method="POST" class="bg-white p-8 rounded-3xl border border-sky-100 shadow-sm space-y-5">
        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Donor Reference Name</label>
            <input type="text" name="donorname" placeholder="<?php echo isset($_SESSION['user_id']) ? 'Leave empty to use profile identity' : 'e.g., Anonymous Donor, Anonymous'; ?>" class="w-full bg-slate-50/50 border border-slate-100 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Target Facility Hub</label>
            <select name="shelterid" required class="w-full bg-slate-50/50 border border-slate-100 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm">
                <?php while ($s = pg_fetch_assoc($shelters)) { ?>
                    <option value="<?php echo $s['shelterid']; ?>"><?php echo $s['name']; ?></option>
                <?php } ?>
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Contribution Mode</label>
            <select name="type" id="donationType" onchange="toggleFormInputs()" class="w-full bg-slate-50/50 border border-slate-100 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm font-semibold text-slate-700">
                <option value="Money">Financial Contribution (RM)</option>
                <option value="Item">Material Supply Item</option>
            </select>
        </div>

        <div id="financialSection">
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Funding Amount (RM)</label>
            <input type="number" step="0.01" name="amount" id="amountField" placeholder="0.00" class="w-full bg-slate-50/50 border border-slate-100 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm">
        </div>

        <div id="materialSection" class="hidden grid grid-cols-3 gap-4">
            <div class="col-span-2">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Item Description</label>
                <input type="text" name="item" id="itemField" placeholder="e.g., Cat Kibbles, Canned Food, Litter Box" class="w-full bg-slate-50/50 border border-slate-100 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Quantity</label>
                <input type="number" name="quantity" id="qtyField" placeholder="1" class="w-full bg-slate-50/50 border border-slate-100 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm">
            </div>
        </div>

        <button class="w-full bg-sky-500 hover:bg-sky-600 text-white font-semibold py-3.5 rounded-xl text-sm shadow-md transition duration-150 mt-4">
            Submit Contribution
        </button>
    </form>
</div>

<script>
function toggleFormInputs() {
    const type = document.getElementById('donationType').value;
    const financial = document.getElementById('financialSection');
    const material = document.getElementById('materialSection');
    
    if (type === 'Money') {
        financial.classList.remove('hidden');
        material.classList.add('hidden');
        document.getElementById('amountField').required = true;
        document.getElementById('itemField').required = false;
        document.getElementById('qtyField').required = false;
    } else {
        financial.classList.add('hidden');
        material.classList.remove('hidden');
        document.getElementById('amountField').required = false;
        document.getElementById('itemField').required = true;
        document.getElementById('qtyField').required = true;
    }
}
toggleFormInputs();
</script>

<?php include("../includes/footer.php"); ?>