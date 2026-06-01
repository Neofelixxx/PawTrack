<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("../config/db.php");
include("../includes/header.php");

$id = $_GET['id'] ?? null;
if (!$id) {
    die("Invalid Cat ID");
}

$query = "
    SELECT c.*, s.name AS shelter_name
    FROM Cat c
    JOIN Shelter s ON c.shelterid = s.shelterid
    WHERE c.catid = $1
";
$result = pg_query_params($conn, $query, [$id]);
$cat = pg_fetch_assoc($result);

if (!$cat) {
    die("Cat not found");
}
$role = $_SESSION['role'] ?? null;
?>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-2">
    
    <!-- NAVIGATION OUTBOUND LINK -->
    <div class="mb-6">
        <a href="/PawTrack/cats/list.php" class="text-sm font-bold text-slate-900 hover:text-sky-700 transition flex items-center gap-2 group">
            <span>←</span> Back to Available Cats
        </a>
    </div>

    <!-- UPPER HALF MATRIX: TEXT DESCRIPTION LEFT & GRAPHIC ON THE RIGHT -->
    <div class="bg-white rounded-3xl border border-sky-100 shadow-sm overflow-hidden grid grid-cols-1 md:grid-cols-12 items-stretch mb-8">
        
        <!-- LEFT PANEL: BIO & SUMMARY -->
        <div class="p-8 md:p-12 md:col-span-7 flex flex-col justify-between space-y-6">
            <div class="space-y-4">
                <div class="flex items-center gap-3 flex-wrap">
                    <h2 class="text-4xl font-black text-slate-900 tracking-tight">Meet <?php echo htmlspecialchars($cat['name']); ?></h2>
                    <span class="bg-sky-600 text-white text-xs font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">
                        <?php echo htmlspecialchars($cat['status']); ?>
                    </span>
                </div>
                
                <div class="text-slate-700 text-sm md:text-base leading-relaxed space-y-4">
                    <?php echo !empty($cat['description']) ? nl2br(htmlspecialchars($cat['description'])) : 'No personalized background history descriptions provided for this feline record yet.'; ?>
                </div>
            </div>

            <!-- INTERACTIVE BUTTON CONSOLE -->
            <div class="pt-6 border-t border-slate-100">
                <?php if ($cat['status'] == 'Available') { ?>
                    <?php if ($role == "Adopter") { ?>
                        <a href="/PawTrack/adoption/add.php?catid=<?php echo $cat['catid']; ?>" class="inline-block bg-sky-500 hover:bg-sky-600 text-white font-bold px-8 py-3.5 rounded-xl shadow-md transition duration-150 text-sm">
                            Apply to Adopt <?php echo htmlspecialchars($cat['name']); ?>
                        </a>
                    <?php } elseif (!$role) { ?> 
                        <a href="/PawTrack/auth/login.php?redirect=/PawTrack/cats/view.php?id=<?php echo $cat['catid']; ?>" class="inline-block bg-slate-800 hover:bg-sky-600 text-white font-bold px-8 py-3.5 rounded-xl shadow-md transition duration-150 text-sm">
                            Sign In to File Adoption Request
                        </a>
                    <?php } ?>
                <?php } else { ?>
                    <div class="p-4 bg-amber-50 text-amber-900 rounded-xl border border-amber-200 text-xs font-bold">
                        🔒 Adoption locked. This profile is currently flagged as: <?php echo htmlspecialchars($cat['status']); ?>.
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- RIGHT PANEL: CRISP PHOTO ASSET DISPLAY -->
        <div class="md:col-span-5 h-72 md:h-full min-h-[380px] bg-sky-50 relative overflow-hidden flex items-center justify-center border-t md:border-t-0 md:border-l border-sky-100">
            <?php if ($cat['image']) { ?> 
                <img src="../assets/images/cats/<?php echo htmlspecialchars($cat['image']); ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>" class="w-full h-full object-cover absolute inset-0">
            <?php } else { ?>
                <div class="absolute inset-0 flex flex-col items-center justify-center text-sky-400 gap-2">
                    <span class="text-6xl">🐈</span>
                    <span class="text-xs font-bold uppercase tracking-wider">No Profile Image Provided</span>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- LOWER HALF MATRIX: THE "ABOUT ME" SPECIFICATION GRID (Mirrors image_d3cec1.jpg layout) -->
    <div class="bg-white rounded-3xl border border-sky-100 p-8 md:p-10 shadow-sm">
        <h3 class="text-lg font-black text-slate-900 mb-6 pb-2 border-b border-slate-100 tracking-tight uppercase tracking-wider">About Profile Matrix</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-4 text-sm text-slate-700">
            <!-- Left Grid Metadata Column -->
            <div class="space-y-3.5">
                <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                    <span class="font-bold text-slate-400 uppercase text-[11px] tracking-wider">Gender / Sex</span>
                    <span class="font-semibold text-slate-800"><?php echo ($cat['gender'] == 'Male') ? '♂️ Male' : '♀️ Female'; ?></span>
                </div>
                <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                    <span class="font-bold text-slate-400 uppercase text-[11px] tracking-wider">Breed Variant</span>
                    <span class="font-semibold text-slate-800"><?php echo htmlspecialchars($cat['breed']); ?></span>
                </div>
                <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                    <span class="font-bold text-slate-400 uppercase text-[11px] tracking-wider">Age Group Class</span>
                    <span class="font-semibold text-slate-800">📊 <?php echo htmlspecialchars($cat['agecategory']); ?></span>
                </div>
            </div>

            <!-- Right Grid Metadata Column -->
            <div class="space-y-3.5">
                <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                    <span class="font-bold text-slate-400 uppercase text-[11px] tracking-wider">Housing Operations Base</span>
                    <span class="font-semibold text-slate-800 text-sky-700 font-medium">🏢 <?php echo htmlspecialchars($cat['shelter_name']); ?></span>
                </div>
                <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                    <span class="font-bold text-slate-400 uppercase text-[11px] tracking-wider">Filing Reference Code</span>
                    <span class="font-mono font-bold text-slate-800">#PT-00<?php echo (int)$cat['catid']; ?></span>
                </div>
                <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                    <span class="font-bold text-slate-400 uppercase text-[11px] tracking-wider">Estimated Birthdate</span>
                    <span class="font-semibold text-slate-800">📅 <?php echo !empty($cat['birthdate']) ? date("d M Y", strtotime($cat['birthdate'])) : 'Unknown'; ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>