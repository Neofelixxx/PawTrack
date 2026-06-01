<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("../config/db.php");
include("../includes/header.php");

/* 1. PROCESS INTENT PLEDGE INSIDE THE UNIFIED CONSOLE */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userid = $_SESSION['user_id'] ?? null; 
    $shelterid = $_POST['shelterid'];
    $type = $_POST['type'];
    
    $donorname = !empty($_POST['donorname']) ? trim($_POST['donorname']) : 'Anonymous Donor';
    if ($userid && empty($_POST['donorname'])) {
        $donorname = $_SESSION['username'] ?? 'Registered User';
    }

    $amount = ($type === 'Money' && !empty($_POST['amount'])) ? (float)$_POST['amount'] : null;
    $item = ($type === 'Item' && !empty($_POST['item'])) ? trim($_POST['item']) : null;
    $qty = ($type === 'Item' && !empty($_POST['quantity'])) ? (int)$_POST['quantity'] : null;

    $query = 'INSERT INTO "Donations" (UserID, ShelterID, DonorName, Type, Amount, ItemDescription, Quantity, DonationDate) VALUES ($1, $2, $3, $4, $5, $6, $7, CURRENT_DATE)';
    
    $result = pg_query_params($conn, $query, [$userid, $shelterid, $donorname, $type, $amount, $item, $qty]);

    if ($result) {
        $_SESSION['message'] = "Donation intent successfully recorded.";
        header("Location: /PawTrack/donations/add.php");
        exit;
    }
}

/* 2. FETCH ACTIVE DATA SEEDS FOR HIGHER ACCORDANCE SUMMARY */
$shelters = pg_query($conn, "SELECT shelterid, name FROM Shelter ORDER BY name");

// Aggregate data counts
$summary_funds = pg_fetch_assoc(pg_query($conn, 'SELECT CEIL(COALESCE(SUM(Amount), 0)) AS overall FROM "Donations" WHERE Type = \'Money\''));

// Isolate Top 5 Contributors (Registered users name tracking only)
$top_query = "
    SELECT u.Name, SUM(d.Amount) as total_funded 
    FROM \"Donations\" d
    JOIN \"User\" u ON d.UserID = u.UserID
    WHERE d.Type = 'Money'
    GROUP BY u.Name
    ORDER BY total_funded DESC
    LIMIT 5
";
$top_result = pg_query($conn, $top_query);
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
    
    <!-- CONSOLIDATED CONSOLE CONTROL HEADER -->
    <div class="mb-8 border-b border-sky-100/80 pb-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">Support Operations Center</h2>
            <p class="text-slate-500 text-sm mt-1">Unified module managing direct financial transfers, material allocations, and active benefactor logs.</p>
        </div>
        <div class="bg-sky-50 text-sky-700 text-xs font-bold font-mono px-3 py-1.5 rounded-lg border border-sky-100 self-start md:self-center">
            Total Combined Support: RM <?php echo number_format($summary_funds['overall']); ?>
        </div>
    </div>

    <!-- MAIN PLATFORM HUB GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start mb-10">
        
        <!-- LEFT & CENTER DESK: FUNDING SPECIFICS & WISHLISTS -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- PANEL A: DUITNOW QR & ACCOUNT METRICS -->
            <div class="bg-white p-6 rounded-2xl border border-sky-100/60 shadow-sm">
                <div class="pb-3 border-b border-slate-100 mb-5">
                    <h3 class="text-md font-bold text-slate-800 tracking-tight">Direct Capital Funding channels</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                    <!-- Standardized Monochrome DuitNow QR Stub -->
                    <div class="bg-slate-50 border border-slate-100 p-5 rounded-xl flex flex-col items-center justify-center text-center">
                        <div class="w-32 h-32 bg-white border-2 border-sky-500 rounded-xl flex flex-col items-center justify-center p-2 relative">
                            <!-- Clean Vector Data Grid Simulation -->
                            <div class="w-full h-full bg-[radial-gradient(#0284c7_3px,transparent_3px)] [background-size:8px_8px] opacity-75"></div>
                            <div class="absolute bg-sky-600 text-[8px] font-mono font-bold text-white px-2 py-0.5 rounded bottom-1 uppercase tracking-wider">DuitNow QR</div>
                        </div>
                        <p class="text-[10px] font-bold text-slate-400 mt-2 uppercase tracking-widest">Scan Hub Core</p>
                    </div>

                    <!-- Pure Enterprise Data Row Listings -->
                    <div class="md:col-span-2 text-xs text-slate-600 space-y-2.5">
                        <div class="grid grid-cols-3 border-b border-slate-100 pb-1.5">
                            <span class="font-bold text-slate-400 uppercase tracking-wider">Account Number</span>
                            <span class="col-span-2 font-mono font-bold text-sm text-slate-800 tracking-tight">3234-926-830</span>
                        </div>
                        <div class="grid grid-cols-3 border-b border-slate-100 pb-1.5">
                            <span class="font-bold text-slate-400 uppercase tracking-wider">Beneficiary</span>
                            <span class="col-span-2 font-semibold text-slate-700">PawTrack Welfare Society JB</span>
                        </div>
                        <div class="grid grid-cols-3 border-b border-slate-100 pb-1.5">
                            <span class="font-bold text-slate-400 uppercase tracking-wider">Bank Entity</span>
                            <span class="col-span-2 font-medium text-slate-700">Public Bank Berhad</span>
                        </div>
                        <div class="grid grid-cols-3">
                            <span class="font-bold text-slate-400 uppercase tracking-wider">Swift Routing</span>
                            <span class="col-span-2 font-mono text-slate-700">PBBEMYKL</span>
                        </div>
                        <p class="text-[11px] text-slate-400 leading-relaxed pt-2 border-t border-slate-50">
                            Please forward transfer receipts to <span class="text-sky-600 font-semibold">finance@pawtrack.org</span> for credential verification and formal validation logging.
                        </p>
                    </div>
                </div>
            </div>

            <!-- PANEL B: MATERIAL WISHLIST & PORCINE DIETARY NOTICE -->
            <div class="bg-white p-6 rounded-2xl border border-sky-100/60 shadow-sm">
                <div class="pb-3 border-b border-slate-100 mb-4">
                    <h3 class="text-md font-bold text-slate-800 tracking-tight">Standardized Material Wishlist Checklists</h3>
                </div>

                <!-- DIETARY LOCALIZATION ADVISORY -->
                <div class="bg-sky-50/50 border border-sky-100 text-sky-900 rounded-xl p-4 mb-5 text-xs leading-relaxed">
                    <span class="font-bold text-sky-700 block uppercase tracking-wide mb-1">🇲🇾 Regional Dietary Compliance Notice (Porcine-Free Requirement)</span>
                    To respect our majority Muslim stakeholder framework across local Malaysian communities, all dry kibbles, milk replacers, and wet pouches dropped off must be entirely free from porcine (pig) ingredients or cross-contamination derivatives. This ensures all animal feeding and cleaning workflows remain completely accessible to all local area volunteers and staff nodes.
                </div>

                <!-- CLEAN CHECKLIST GRID -->
                <div class="overflow-hidden border border-slate-100 rounded-xl text-xs">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-100 text-[10px]">
                                <th class="p-3 pl-4">Classification</th>
                                <th class="p-3">Required Supply Specifications</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-600">
                            <tr class="hover:bg-slate-50/30">
                                <td class="p-3 pl-4 font-bold text-slate-800">Pet Nourishment</td>
                                <td class="p-3">Kitten milk replacers, junior nutrient kibbles, and premium canned feline food. <span class="text-sky-600 font-semibold font-mono">(Porcine-Free / Bebas Unsur Babi)</span></td>
                            </tr>
                            <tr class="hover:bg-slate-50/30">
                                <td class="p-3 pl-4 font-bold text-slate-800">Hygiene Management</td>
                                <td class="p-3">Laundry detergent powder, chemical liquid bleach, sanitizing hand soaps, and heavy-duty industrial trash bags.</td>
                            </tr>
                            <tr class="hover:bg-slate-50/30">
                                <td class="p-3 pl-4 font-bold text-slate-800">Clinical Protectives</td>
                                <td class="p-3">Flea/tick spot treatments (Advocate, Frontline, Revolution), and nitrile surgical gloves (Sizes 6 and 6.5).</td>
                            </tr>
                            <tr class="hover:bg-slate-50/30">
                                <td class="p-3 pl-4 font-bold text-slate-800">Facility Hardware</td>
                                <td class="p-3">Pinewood/tofu cat litter bags, stainless steel feeding bowls, and modular steel wire cages (3ft × 3ft × 3ft).</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- RIGHT DESK: FORM CONSOLE & LEADERBOARD INTERACTION -->
        <div class="space-y-6">
            
            <!-- FORM COMPONENT: REGISTER INTENT PLEDGE -->
            <div class="bg-white p-6 rounded-2xl border border-sky-100/60 shadow-sm">
                <div class="mb-4">
                    <h4 class="font-bold text-slate-800 text-md tracking-tight">Log Donation Intent</h4>
                    <p class="text-slate-400 text-xs mt-0.5">Record your contribution data directly into our local tracking pipeline.</p>
                </div>

                <form method="POST" action="/PawTrack/donations/add.php" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-400 uppercase tracking-wider mb-1">Donor Profile Name</label>
                        <input type="text" name="donorname" placeholder="<?php echo isset($_SESSION['user_id']) ? 'Using logged account profile' : 'Anonymous Donor'; ?>" class="w-full bg-slate-50/50 border border-slate-100 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-400 uppercase tracking-wider mb-1">Target Distribution Hub</label>
                        <select name="shelterid" required class="w-full bg-slate-50/50 border border-slate-100 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm font-medium text-slate-700">
                            <?php while ($s = pg_fetch_assoc($shelters)) { ?>
                                <option value="<?php echo $s['shelterid']; ?>"><?php echo $s['name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-400 uppercase tracking-wider mb-1">Contribution Category</label>
                        <select name="type" id="donationType" onchange="toggleUnifiedFields()" class="w-full bg-slate-50/50 border border-slate-100 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm font-semibold text-slate-700">
                            <option value="Money">Financial Contribution (RM)</option>
                            <option value="Item">Material Supply Checklist Item</option>
                        </select>
                    </div>

                    <!-- DYNAMIC CONDITIONAL LOGIC FORM SECTIONS -->
                    <div id="financialInputs">
                        <label class="block font-bold text-slate-400 uppercase tracking-wider mb-1">Transferred Capital Amount (RM)</label>
                        <input type="number" step="0.01" name="amount" id="amountField" placeholder="0.00" class="w-full bg-slate-50/50 border border-slate-100 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm font-mono">
                    </div>

                    <div id="materialInputs" class="hidden space-y-3">
                        <div>
                            <label class="block font-bold text-slate-400 uppercase tracking-wider mb-1">Item Category Description</label>
                            <input type="text" name="item" id="itemField" placeholder="e.g. Tofu Litter, Cages" class="w-full bg-slate-50/50 border border-slate-100 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-400 uppercase tracking-wider mb-1">Quantity Units</label>
                            <input type="number" name="quantity" id="qtyField" placeholder="1" class="w-full bg-slate-50/50 border border-slate-100 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm font-mono">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-sky-500 hover:bg-sky-600 text-white font-semibold py-3.5 rounded-xl text-sm shadow-md transition duration-150 mt-2">
                        Commit Log Entry
                    </button>
                </form>
            </div>

            <!-- MONOCHROME BOARD: TOP BENEFACTORS LEDGER -->
            <div class="bg-slate-900 text-slate-100 p-5 rounded-2xl shadow-sm">
                <h4 class="text-[11px] font-bold text-sky-400 uppercase tracking-widest mb-3">Top Contributors Board</h4>
                <ol class="space-y-2 text-xs font-medium">
                    <?php 
                    $rank = 1;
                    while($top = pg_fetch_assoc($top_result)) { ?>
                        <li class="flex justify-between items-center bg-white/5 px-3 py-2 rounded-lg border border-white/5">
                            <span class="text-slate-300"><?php echo $rank++ . ". " . htmlspecialchars($top['name']); ?></span>
                            <span class="font-mono text-sky-400 font-bold">RM <?php echo number_format($top['total_funded'], 2); ?></span>
                        </li>
                    <?php } 
                    if($rank === 1) { echo "<p class='text-slate-400 italic text-[11px] px-1'>No verified logged accounts present.</p>"; }
                    ?>
                </ol>
            </div>
        </div>
    </div>

    <!-- ==================== CORPORATE MONOCHROME SCAM DIRECTION ALERT ==================== -->
    <div class="bg-slate-100 rounded-2xl p-5 border border-slate-200/60 text-xs text-slate-600 leading-relaxed grid grid-cols-1 md:grid-cols-2 gap-6 shadow-sm">
        <div class="border-b md:border-b-0 md:border-r border-slate-200 pb-4 md:pb-0 md:pr-4">
            <strong class="text-slate-800 uppercase tracking-wide block mb-1">🔒 Maklumat Pengesahan Keselamatan</strong>
            Berhati-hati dengan maklumat palsu yang menular di media sosial seperti Telegram, WhatsApp dan sebagainya. Sentiasa berwaspada terhadap sebarang kutipan dana pihak ketiga atau skim yang mencurigakan. Sila lakukan pengesahan rasmi melalui talian rangkaian PawTrack di <span class="font-bold text-slate-800">017-2847500</span> atau emel <span class="font-mono font-semibold text-sky-600">security@pawtrack.org</span>.
        </div>
        <div>
            <strong class="text-slate-800 uppercase tracking-wide block mb-1">🔒 Security Verification Directive</strong>
            Beware of unauthorized collection vectors or fraudulent fundraising requests spreading across messaging networks. Always verify funding accounts directly through official deployment nodes. Address all administrative or data validation audits to our direct network verification helpline at <span class="font-bold text-slate-800">017-2847500</span>.
        </div>
    </div>
</div>

<script>
function toggleUnifiedFields() {
    const type = document.getElementById('donationType').value;
    const financial = document.getElementById('financialInputs');
    const material = document.getElementById('materialInputs');
    
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
toggleUnifiedFields();
</script>

<?php include("../includes/footer.php"); ?>