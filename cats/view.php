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
    
    <!-- BACK TO CATALOG LINK -->
    <div class="mb-6">
        <a href="/PawTrack/cats/list.php" class="text-sm font-bold text-slate-900 hover:text-sky-700 transition flex items-center gap-2 group">
            ← Back to Available Cats
        </a>
    </div>

    <!-- UPPER ROW: HERO PANEL -->
    <div class="bg-white rounded-3xl border border-sky-100 shadow-sm overflow-hidden grid grid-cols-1 md:grid-cols-12 items-stretch mb-8">
        
        <!-- LEFT PANELS: DATA BRIEF -->
        <div class="p-8 md:p-12 md:col-span-7 flex flex-col justify-between space-y-6">
            <div class="space-y-4">
                <div class="flex items-center gap-3 flex-wrap">
                    <h2 class="text-4xl font-black text-slate-900 tracking-tight">Meet <?php echo htmlspecialchars($cat['name']); ?></h2>
                    <span class="bg-sky-600 text-white text-xs font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">
                        <?php echo htmlspecialchars($cat['status']); ?>
                    </span>
                </div>
                
                <div class="text-slate-700 text-sm md:text-base leading-relaxed space-y-4">
                    <?php echo !empty($cat['description']) ? nl2br(htmlspecialchars($cat['description'])) : 'No background information provided for this cat record.'; ?>
                </div>
            </div>

            <!-- ADOPTION ACTION HUB -->
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
                        Adoption locked. This profile is currently flagged as: <?php echo htmlspecialchars($cat['status']); ?>.
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- RIGHT PANEL: PHOTO -->
        <div class="md:col-span-5 h-72 md:h-full min-h-[380px] bg-sky-50 relative overflow-hidden flex items-center justify-center border-t md:border-t-0 md:border-l border-sky-100">
            <?php if ($cat['image']) { ?> 
                <img src="<?php echo $base_path; ?>assets/images/cats/<?php echo $cat['image']; ?>" 
                     class="w-full h-full object-cover" 
                     alt="<?php echo htmlspecialchars($cat['name']); ?>">
            <?php } else { ?>
                <div class="aspect-[4/3] bg-sky-50 flex flex-col items-center justify-center text-sky-400 gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider">No Image Uploaded</span>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- LOWER ROW: METADATA INFORMATION & PERSONALITY REMARKS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        
        <!-- SPECIFICATION GRID PANELS -->
        <div class="bg-white rounded-3xl border border-sky-100 p-8 shadow-sm lg:col-span-2">
            <h3 class="text-lg font-black text-slate-900 mb-6 pb-2 border-b border-slate-100 tracking-tight uppercase tracking-wider">Physical Characteristics</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-12 gap-y-4 text-sm text-slate-700">
                <div class="space-y-3.5">
                    <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                        <span class="font-bold text-slate-400 uppercase text-[11px] tracking-wider">Gender</span>
                        <span class="font-semibold text-slate-800"><?php echo ($cat['gender'] == 'Male') ? 'Male' : 'Female'; ?></span>
                    </div>
                    <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                        <span class="font-bold text-slate-400 uppercase text-[11px] tracking-wider">Breed Lineage</span>
                        <span class="font-semibold text-slate-800"><?php echo !empty($cat['breed']) ? htmlspecialchars($cat['breed']) : 'Domestic Shorthair'; ?></span>
                    </div>
                    <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                        <span class="font-bold text-slate-400 uppercase text-[11px] tracking-wider">Fur Color</span>
                        <span class="font-semibold text-slate-800"><?php echo !empty($cat['color']) ? htmlspecialchars($cat['color']) : 'Not Logged'; ?></span>
                    </div>
                    <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                        <span class="font-bold text-slate-400 uppercase text-[11px] tracking-wider">Coat Pattern</span>
                        <span class="font-semibold text-slate-800"><?php echo !empty($cat['pattern']) ? htmlspecialchars($cat['pattern']) : 'Not Logged'; ?></span>
                    </div>
                </div>

                <div class="space-y-3.5">
                    <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                        <span class="font-bold text-slate-400 uppercase text-[11px] tracking-wider">Current Home Base</span>
                        <span class="font-semibold text-slate-800"><?php echo htmlspecialchars($cat['shelter_name']); ?></span>
                    </div>
                    <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                        <span class="font-bold text-slate-400 uppercase text-[11px] tracking-wider">Cat Reference Code</span>
                        <span class="font-mono font-bold text-slate-800">#PT-00<?php echo (int)$cat['catid']; ?></span>
                    </div>
                    <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                        <span class="font-bold text-slate-400 uppercase text-[11px] tracking-wider">Age Group</span>
                        <span class="font-semibold text-slate-800"><?php echo htmlspecialchars($cat['agecategory']); ?></span>
                    </div>
                    <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                        <span class="font-bold text-slate-400 uppercase text-[11px] tracking-wider">Estimated Birthdate</span>
                        <span class="font-semibold text-slate-800"><?php echo !empty($cat['birthdate']) ? date("d M Y", strtotime($cat['birthdate'])) : 'Unknown'; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- SPECIAL TALENTS & REMARKS CARD -->
        <div class="bg-slate-900 text-white rounded-3xl p-8 shadow-md flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2 mb-4 text-sky-400">
                    <h3 class="text-sm font-black uppercase tracking-widest">Special Talents & Tricks</h3>
                </div>
                <div class="text-xs text-slate-300 leading-relaxed space-y-3">
                    <?php if (!empty($cat['special_remarks'])) { ?>
                        <p class="italic bg-white/5 p-4 rounded-xl border border-white/5">
                            "<?php echo nl2br(htmlspecialchars($cat['special_remarks'])); ?>"
                        </p>
                    <?php } else { ?>
                        <p class="text-slate-400 italic">No specific learned habits or unique personality talents logged for this cat yet.</p>
                    <?php } ?>
                </div>
            </div>
            <div class="text-[10px] text-slate-500 font-mono mt-4 pt-4 border-t border-white/5">
                Verified by Shelter Caretaker Node
            </div>
        </div>

    </div>
</div>

<?php include("../includes/footer.php"); ?>