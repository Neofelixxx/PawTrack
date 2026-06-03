<?php include("../config/db.php");
include("../includes/header.php");
$role = $_SESSION['role'] ?? null;
$query = "
SELECT
c.*,
s.Name AS ShelterName
FROM Cat c
JOIN Shelter s
ON c.ShelterID = s.ShelterID
WHERE c.Status = 'Available'
ORDER BY c.CatID DESC
";
$result = pg_query($conn, $query);
if (!$result) {
die(pg_last_error($conn));
}
?>
<div class="flex">
<div class="flex-1 p-6">
    
    <!-- HEADER BAR WITH INTENSIFIED HIGH-CONTRAST TYPOGRAPHY -->
    <div class="flex justify-between items-center mb-8 border-b border-sky-200 pb-4">
        <div>
            <!-- FIXED CONTRAST: Changed text-slate-800 to text-slate-900 / font-black -->
            <h2 class="text-4xl font-black text-slate-900 tracking-tight">Available Cats</h2>
            <!-- FIXED CONTRAST: Changed text-slate-500 to text-slate-800 / font-semibold -->
            <p class="text-slate-800 font-semibold text-sm mt-1">Find lovely cats waiting for a home in Johor Bahru.</p>
        </div>
        <?php if ($role == "Admin" || $role == "Staff") { ?>
            <a href="add.php" class="bg-sky-600 hover:bg-sky-700 text-white font-bold px-5 py-2.5 rounded-xl shadow-md transition-all duration-200 text-sm flex items-center gap-2">
                <span class="text-base">＋</span> Add New Cat
            </a>
        <?php } ?>
    </div>

    <!-- MODERN PASTEL CATALOG CARD GRID -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php while ($row = pg_fetch_assoc($result)) { ?>
            <div class="bg-white rounded-3xl border border-sky-100 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col justify-between group">
                <div>
                    <?php if ($row['image']) { ?> 
                        <div class="relative overflow-hidden aspect-[4/3] bg-sky-50">
                            <img src="<?php echo $base_path; ?>assets/images/cats/<?php echo $row['image']; ?>" 
                                 class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500"
                                 alt="<?php echo $row['name']; ?>">
                            <span class="absolute top-4 right-4 bg-sky-600 text-white text-xs font-extrabold px-3 py-1.5 rounded-full shadow-sm tracking-wide">
                                <?php echo $row['status']; ?>
                            </span>
                        </div>
                    <?php } else { ?>
                        <div class="aspect-[4/3] bg-sky-50 flex flex-col items-center justify-center text-sky-400 gap-2">
                            <span class="text-4xl">🐈</span>
                            <span class="text-xs font-bold uppercase tracking-wider">No Image Uploaded</span>
                        </div>
                    <?php } ?>

                    <!-- Content Details -->
                    <div class="p-6">
                        <div class="flex justify-between items-start gap-2 mb-3">
                            <!-- FIXED CONTRAST: Changed text-slate-800 to text-slate-900 / font-black -->
                            <h3 class="text-2xl font-black text-slate-900 group-hover:text-sky-600 transition duration-200 truncate">
                                <a href="/PawTrack/cats/view.php?id=<?php echo $row['catid']; ?>">
                                    <?php echo $row['name']; ?>
                                </a>
                            </h3>
                            <span class="bg-sky-50 text-sky-700 text-xs font-bold px-2.5 py-1 rounded-lg border border-sky-200 uppercase tracking-wider shrink-0 mt-1">
                                <?php echo $row['breed']; ?>
                            </span>
                        </div>

                        <!-- Scannable Meta Grid (Text darkened to avoid drowning out) -->
                        <div class="space-y-2.5 mt-4 pt-4 border-t border-slate-100 text-sm text-slate-700">
                            <div class="flex items-center gap-3">
                                <span class="text-base text-sky-600 w-5 text-center">📊</span>
                                <!-- FIXED CONTRAST: Darkened sub-elements to text-slate-800 -->
                                <p class="text-slate-800"><b class="text-slate-900 font-bold">Age Group:</b> <?php echo $row['agecategory']; ?></p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-base text-sky-600 w-5 text-center">📍</span>
                                <!-- FIXED CONTRAST: Darkened sub-elements to text-slate-800 -->
                                <p class="text-slate-800"><b class="text-slate-900 font-bold">Shelter:</b> <?php echo $row['sheltername']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Operations / Interactive Buttons -->
                <div class="p-6 pt-0 mt-2 flex items-center justify-between border-t border-slate-100 pt-4">
                    <!-- FIXED CONTRAST: Changed text-slate-500 to text-slate-700 / font-bold -->
                    <a href="/PawTrack/cats/view.php?id=<?php echo $row['catid']; ?>" class="text-sm font-bold text-slate-700 hover:text-sky-600 transition duration-200 flex items-center gap-1 group/link">
                        View Profile <span class="transform group-hover/link:translate-x-0.5 transition duration-200">→</span>
                    </a>
                    
                    <div class="flex gap-2">
                        <!-- ADOPTER VIEW -->
                        <?php if (!$role || $role == "Adopter") { ?>
                            <a href="/PawTrack/auth/login.php?redirect=/PawTrack/adoption/add.php?catid=<?php echo $row['catid']; ?>" 
                               class="bg-sky-500 hover:bg-sky-600 text-white font-bold px-4 py-2 rounded-xl text-xs shadow-sm transition-all duration-200">
                                Adopt Me
                            </a>
                        <?php } ?>
                        
                        <!-- MANAGEMENT PRIVILEGES -->
                        <?php if ($role == "Admin" || $role == "Staff") { ?>
                            <a href="edit.php?id=<?php echo $row['catid']; ?>" 
                               class="border border-sky-300 hover:bg-sky-50 text-sky-600 font-bold px-4 py-2 rounded-xl text-xs transition duration-200">
                                Edit
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
</div>
<?php include("../includes/footer.php"); ?>