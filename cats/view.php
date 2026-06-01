<?php

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

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- BACK TO CATALOG LINK -->
    <div class="mb-6">
        <a href="/PawTrack/cats/list.php" class="text-sm font-semibold text-slate-500 hover:text-sky-600 transition flex items-center gap-2 group">
            <span class="transform group-hover:-translate-x-0.5 transition duration-150">←</span> Back to Available Cats
        </a>
    </div>

    <!-- MAIN PROFILE SPLIT MATRIX -->
    <div class="bg-white rounded-3xl border border-sky-100/60 shadow-xl overflow-hidden flex flex-col lg:flex-row items-stretch">
        
        <!-- LEFT COLUMN: LARGE GRAPHIC DISPLAY PANEL -->
        <div class="lg:w-1/2 bg-slate-50 relative min-h-[350px] lg:min-h-[500px]">
            <?php if ($cat['image']) { ?> 
                <img src="../assets/images/cats/<?php echo $cat['image']; ?>" 
                     alt="<?php echo $cat['name']; ?>" 
                     class="w-full h-full object-cover absolute inset-0">
            <?php } else { ?>
                <!-- Graphic Fallback Placeholder -->
                <div class="absolute inset-0 flex flex-col items-center justify-center text-sky-300 gap-3">
                    <span class="text-6xl">🐈</span>
                    <span class="text-sm font-semibold tracking-wide">No Profile Image Provided</span>
                </div>
            <?php } ?>
            
            <!-- Floating Absolute Badge for Status -->
            <span class="absolute top-6 left-6 bg-white/90 backdrop-blur-md text-sky-700 text-xs font-bold px-4 py-2 rounded-full shadow-md tracking-wider uppercase">
                ✨ <?php echo $cat['status']; ?>
            </span>
        </div>

        <!-- RIGHT COLUMN: CHRONICLE SPECIFICATION CONSOLE -->
        <div class="lg:w-1/2 p-8 sm:p-12 flex flex-col justify-between space-y-8">
            
            <!-- Section A: Title & Breed Matrix -->
            <div>
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-sky-50 pb-4">
                    <h2 class="text-4xl font-extrabold text-slate-800 tracking-tight">
                        <?php echo $cat['name']; ?>
                    </h2>
                    <span class="bg-sky-50 text-sky-600 font-bold px-3.5 py-1.5 rounded-xl border border-sky-100/40 text-xs uppercase tracking-widest">
                        <?php echo $cat['breed']; ?>
                    </span>
                </div>

                <!-- Section B: Iconographic Meta Grid -->
                <div class="grid grid-cols-2 gap-4 mt-6">
                    <div class="p-4 bg-slate-50/60 rounded-2xl border border-sky-50/40">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Age Bracket</p>
                        <p class="font-bold text-slate-700 text-sm mt-0.5">📊 <?php echo $cat['agecategory']; ?></p>
                    </div>
                    <div class="p-4 bg-slate-50/60 rounded-2xl border border-sky-50/40">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Gender Variant</p>
                        <p class="font-bold text-slate-700 text-sm mt-0.5">
                            <?php echo ($cat['gender'] == 'Male') ? '♂️ Male' : '♀️ Female'; ?>
                        </p>
                    </div>
                    <div class="p-4 bg-slate-50/60 rounded-2xl border border-sky-50/40 col-span-2">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Current Housing Station</p>
                        <p class="font-bold text-slate-700 text-sm mt-0.5">🏢 Hub: <?php echo $cat['shelter_name']; ?></p>
                    </div>
                </div>

                <!-- Section C: Bio Description Blurb -->
                <div class="mt-6">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">About Profile</h4>
                    <p class="text-slate-600 text-sm leading-relaxed bg-sky-50/20 p-4 rounded-2xl border border-sky-50/30">
                        <?php echo !empty($cat['description']) ? nl2br($cat['description']) : 'No personalized background history descriptions provided for this feline record yet.'; ?>
                    </p>
                </div>
            </div>

            <!-- Section D: Action Execution Module Base -->
            <div class="pt-6 border-t border-slate-100">
                <?php if ($cat['status'] == 'Available') { ?>
                    <?php if ($role == "Adopter") { ?>
                        <a href="/PawTrack/adoption/add.php?catid=<?php echo $cat['catid']; ?>"
                           class="w-full bg-sky-500 hover:bg-sky-600 text-white text-center font-semibold py-4 rounded-xl shadow-md hover:shadow-lg transition duration-200 block text-sm">
                            Apply to Adopt <?php echo $cat['name']; ?>
                        </a>
                    <?php } elseif (!$role) { ?> 
                        <a href="/PawTrack/auth/login.php?redirect=/PawTrack/cats/view.php?id=<?php echo $cat['catid']; ?>"
                           class="w-full bg-slate-800 hover:bg-sky-600 text-white text-center font-semibold py-4 rounded-xl shadow-md hover:shadow-lg transition duration-200 block text-sm">
                            Sign In to File Adoption Request
                        </a>
                    <?php } ?>
                <?php } else { ?>
                    <div class="p-4 bg-amber-50 text-amber-800 rounded-xl border border-amber-100/60 text-center text-xs font-semibold">
                        🔒 Adoption locked. This feline profile status setting is currently flagged as: <span class="underline font-bold"><?php echo $cat['status']; ?></span>.
                    </div>
                <?php } ?>
            </div>

        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>