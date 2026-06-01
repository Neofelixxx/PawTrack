<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("../config/db.php");
include("../includes/header.php");

// Fetch active shelters so users can choose which localized hub receives their pledge
$shelters = pg_query($conn, "SELECT shelterid, name FROM Shelter ORDER BY name");
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
    
    <!-- HEADER INTRO -->
    <div class="mb-10 text-center max-w-3xl mx-auto">
        <h2 class="text-4xl font-black text-slate-800 tracking-tight">Support Our Furry Friends 🐈</h2>
        <p class="text-slate-500 font-medium mt-2">Thank you for standing with our rescue network. Every contribution helps us maintain spatial operations, supply medical treatments, and provide nourishment across our Johor Bahru hubs.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start mb-12">
        
        <!-- ================= LEFT & CENTER COLUMNS: PORTAL DETAILS ================= -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- CHANNEL 1: DIRECT BANK-IN & DUITNOW QR -->
            <div class="bg-white p-6 rounded-3xl border border-sky-100/70 shadow-sm">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-4 mb-4">
                    <span class="text-2xl">💳</span>
                    <h3 class="text-lg font-bold text-slate-800">Channel 1: Direct Financial Transfer / DuitNow QR</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                    <!-- Fake DuitNow QR Placeholder Visual -->
                    <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl flex flex-col items-center justify-center text-center">
                        <div class="w-36 h-36 bg-white border-4 border-rose-500 rounded-xl flex flex-col items-center justify-center p-2 relative shadow-inner">
                            <!-- Minimalist Mock QR Pattern -->
                            <div class="w-full h-full bg-[radial-gradient(#000_3px,transparent_3px)] [background-size:8px_8px] opacity-80"></div>
                            <div class="absolute bg-rose-500 text-[9px] font-black text-white px-2 py-0.5 rounded-md bottom-1 uppercase tracking-wider">DuitNow</div>
                        </div>
                        <p class="text-[10px] font-bold text-slate-400 mt-2 uppercase tracking-widest">Scan to Sponsor</p>
                    </div>

                    <!-- Bank Account Table Specifics -->
                    <div class="md:col-span-2 text-xs text-slate-600 space-y-2">
                        <div class="grid grid-cols-3 border-b border-slate-50 pb-1.5">
                            <span class="font-bold text-slate-400 uppercase tracking-wider">Account No.</span>
                            <span class="col-span-2 font-mono font-bold text-sm text-slate-800">3234-926-830</span>
                        </div>
                        <div class="grid grid-cols-3 border-b border-slate-50 pb-1.5">
                            <span class="font-bold text-slate-400 uppercase tracking-wider">Beneficiary</span>
                            <span class="col-span-2 font-semibold text-slate-800">PAWTRACK ANIMAL WELFARE SOCIETY JHB</span>
                        </div>
                        <div class="grid grid-cols-3 border-b border-slate-50 pb-1.5">
                            <span class="font-bold text-slate-400 uppercase tracking-wider">Bank Name</span>
                            <span class="col-span-2 font-medium text-slate-700">PUBLIC BANK BERHAD</span>
                        </div>
                        <div class="grid grid-cols-3">
                            <span class="font-bold text-slate-400 uppercase tracking-wider">Swift Code</span>
                            <span class="col-span-2 font-mono font-medium text-slate-700">PBBEMYKL</span>
                        </div>
                        <p class="text-[11px] text-slate-400 leading-relaxed pt-2 italic">
                            💡 Please email your transaction bank-in slip receipt proof to <span class="text-sky-600 font-semibold font-mono">receipts@pawtrack.org</span> so our management layer can log your profile credit contribution and compile an official receipt layout.
                        </p>
                    </div>
                </div>
            </div>

            <!-- CHANNEL 2: WISHLLIST SUPPLY STANDARDIZATION -->
            <div class="bg-white p-6 rounded-3xl border border-sky-100/70 shadow-sm">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-4 mb-4">
                    <span class="text-2xl">📦</span>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Channel 2: Material Supplies & Wishlist</h3>
                        <p class="text-xs text-slate-400">Standardized item checklist categories used daily in massive volumes across active facility operations.</p>
                    </div>
                </div>

                <!-- ⚠️ CRITICAL MUSLIM MALAYSIA CAT LOVERS LOCALIZATION ALERT -->
                <div class="bg-amber-50/70 border border-amber-100 rounded-2xl p-4 mb-6 flex gap-3 text-xs text-amber-900 leading-relaxed">
                    <span class="text-lg">⚠️</span>
                    <div>
                        <strong class="font-bold">PENTING / CRITICAL FELINE DIETARY NOTE:</strong><br>
                        To respect our majority Muslim community stakeholders in Malaysia, please ensure all pet food packages are entirely free from **porcine/pig ingredients** or associated derivatives. While biological felines can consume them safely, keeping all incoming supplies strictly aligned with porcine-free specifications keeps daily cage cleaning and feeding handling operations completely accessible to all local carers and volunteers.
                    </div>
                </div>

                <!-- WISHLIST DATA ARRAY TABLE -->
                <div class="overflow-hidden border border-slate-100 rounded-xl text-xs">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-100 text-[10px]">
                                <th class="p-3 pl-4">Item Type</th>
                                <th class="p-3">Specification Requirement Baseline</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-600">
                            <tr>
                                <td class="p-3 pl-4 font-bold text-slate-800">Canned / Dry Pet Food</td>
                                <td class="p-3 leading-relaxed">Priority focus on weaning kitten milk replacer, junior growth kibbles, and premium feline wet pouches. <span class="text-amber-600 font-bold font-mono">(Porcine-Free / Tiada unsur babi)</span></td>
                            </tr>
                            <tr>
                                <td class="p-3 pl-4 font-bold text-slate-800">Hygiene & Care Supplies</td>
                                <td class="p-3">Laundry detergent powder, chemical bleach fluids, liquid hand soaps, paper kitchen rolls, and heavy-duty large garbage bags.</td>
                            </tr>
                            <tr>
                                <td class="p-3 pl-4 font-bold text-slate-800">Clinical Pest Preventives</td>
                                <td class="p-3">Flea/tick spot-on treatments (Advocate, Frontline, Revolution), and sterile medical surgical gloves (Sizes 6 and 6.5).</td>
                            </tr>
                            <tr>
                                <td class="p-3 pl-4 font-bold text-slate-800">Facility Accommodations</td>
                                <td class="p-3">Standard pinewood or tofu type cat litter bags, stainless steel feeding bowls, and modular steel assembly wire cages (3ft × 3ft × 3ft).</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p class="text-[11px] text-slate-400 italic text-center mt-3">Note: As we consume these items in bulk, going for wholesale package quantity over high-end brands is highly advisable.</p>
            </div>
        </div>

        <!-- ================= RIGHT COLUMN: VOLUNTEER PLEDGE LOGFORM ================= -->
        <div class="space-y-6">
            <!-- PLEDGE SUBMISSION BLOCK -->
            <div class="bg-white p-6 rounded-3xl border border-sky-100 shadow-sm">
                <div class="mb-4">
                    <h4 class="font-bold text-slate-800 text-md">Register a Intent Pledge</h4>
                    <p class="text-slate-400 text-xs mt-0.5">Let our shelter staff know you have dropped off supplies or bank-in tokens.</p>
                </div>

                <form method="POST" action="/PawTrack/donations/add.php" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-400 uppercase tracking-wider mb-1">Your Identity</label>
                        <input type="text" name="donorname" placeholder="<?php echo isset($_SESSION['user_id']) ? 'Using active account profile' : 'Anonymous Supporter'; ?>" class="w-full bg-slate-50 border border-slate-100 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-400 uppercase tracking-wider mb-1">Target Facility Location</label>
                        <select name="shelterid" required class="w-full bg-slate-50 border border-slate-100 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm font-medium">
                            <?php while ($s = pg_fetch_assoc($shelters)) { ?>
                                <option value="<?php echo $s['shelterid']; ?>"><?php echo $s['name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-400 uppercase tracking-wider mb-1">Contribution Class</label>
                        <select name="type" class="w-full bg-slate-50 border border-slate-100 p-3 rounded-xl focus:outline-none focus:border-sky-400 text-sm font-semibold text-slate-700">
                            <option value="Money">Bank Transfer / QR Code</option>
                            <option value="Item">Material Supply Wishlist Item</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-sky-500 hover:bg-sky-600 text-white font-semibold py-3 rounded-xl text-sm shadow-md transition duration-150 mt-2">
                        Log Contribution Intent
                    </button>
                </form>
            </div>

            <!-- INTEGRATED PAWS PHYSICAL DONATION BOX LOCATION ADVISORY -->
            <div class="bg-gradient-to-br from-slate-800 to-slate-900 text-white p-5 rounded-3xl shadow-sm">
                <span class="text-xl">🏪</span>
                <h4 class="text-xs font-bold text-sky-400 uppercase tracking-widest mt-2 mb-1">Physical Collection Points</h4>
                <p class="text-slate-300 text-[11px] leading-relaxed">
                    Prefer dropping cash currency notes directly? Official PawTrack acrylic collection containers can be accessed across all partner **Pet Lovers Centre** outlets throughout the Johor region! Keep tabs on our Facebook and Instagram streams for localized weekend awareness exhibition calendars.
                </p>
            </div>
        </div>
    </div>

    <!-- ==================== BILINGUAL SCAM WARNING BANNER ALERT ==================== -->
    <div class="bg-rose-50 border border-rose-100 rounded-3xl p-6 flex flex-col md:flex-row gap-6 items-center shadow-sm">
        <div class="w-14 h-14 bg-rose-500 text-white rounded-2xl flex items-center justify-center text-3xl animate-pulse shrink-0">⚠️</div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs leading-relaxed text-rose-950">
            <!-- Malay Warn -->
            <div class="border-b md:border-b-0 md:border-r border-rose-200/60 pb-4 md:pb-0 md:pr-4">
                <h4 class="font-bold uppercase tracking-wide text-rose-700 mb-1">AMARAN PENIPUAN (SCAM ALERT)</h4>
                Berhati-hati dengan maklumat palsu yang menular di Media Sosial seperti Telegram, WhatsApp dan sebagainya. Sentiasa berwaspada terhadap apa-apa peluang pelaburan atau kutipan derma pihak ketiga yang menjanjikan pulangan tinggi dengan risiko rendah. Sekiranya ia indah khabar daripada rupa, ia mungkin satu penipuan. Sila hubungi talian pengesahan rangkaian kami di <span class="font-bold">017-2847500</span> atau emel <span class="font-mono font-semibold text-rose-700">security@pawtrack.org</span>.
            </div>
            <!-- English Warn -->
            <div>
                <h4 class="font-bold uppercase tracking-wide text-rose-700 mb-1">SECURITY ASSURANCE DIRECTIVE</h4>
                Beware of false asset channels spreading across Social Media platforms like Telegram or WhatsApp. Always exercise maximum caution against third-party investment agents or fraudulent fundraiser accounts promising synthetic matching returns. If an optimization asset seems too good to be true, it is a scam. Direct all data validation audits to our verification helpline at <span class="font-bold">017-2847500</span>.
            </div>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>