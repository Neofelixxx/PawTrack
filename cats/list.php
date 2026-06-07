<?php 
include("../config/db.php");
include("../includes/header.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['role'] ?? null;

// Determine access authorization level for query splitting
$is_management = ($role == "Admin" || $role == "Manager" || $role == "Staff");

if ($is_management) {
    // Management roles bypass availability constraints to audit all profiles
    $query = "
        SELECT c.*, s.Name AS ShelterName
        FROM Cat c
        JOIN Shelter s ON c.ShelterID = s.ShelterID
        ORDER BY c.CatID DESC
    ";
    $result = pg_query($conn, $query);
} else {
    // Public guests and adopters are restricted strictly to available profiles
    $query = "
        SELECT c.*, s.Name AS ShelterName
        FROM Cat c
        JOIN Shelter s ON c.ShelterID = s.ShelterID
        WHERE c.Status = 'Available'
        ORDER BY c.CatID DESC
    ";
    $result = pg_query($conn, $query);
}

if (!$result) {
    die(pg_last_error($conn));
}
?>
<div class="flex">
<div class="flex-1 p-6">
    
    <!-- HEADER BAR -->
    <div class="flex justify-between items-center mb-8 border-b border-sky-200 pb-4">
        <div>
            <h2 class="text-4xl font-black text-slate-900 tracking-tight">
                <?php echo $is_management ? "Cat Database Registry" : "Available Cats"; ?>
            </h2>
            <p class="text-slate-600 text-sm mt-1">
                <?php echo $is_management ? "Comprehensive facility archive records detailing all system profiles." : "Profiles of cats currently seeking adoption placement."; ?>
            </p>
        </div>
        <?php if ($is_management) { ?>
            <a href="add.php" class="bg-sky-600 hover:bg-sky-700 text-white font-bold px-5 py-2.5 rounded-xl shadow-md transition-all duration-200 text-sm flex items-center gap-2">
                <span>＋</span> Register New Cat
            </a>
        <?php } ?>
    </div>

    <!-- CATALOG CARD GRID -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php while ($row = pg_fetch_assoc($result)) { ?>
            <div class="bg-white rounded-3xl border border-sky-100 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col justify-between group">
                <div>
                    <!-- CAT PHOTO PREVIEW -->
                    <?php if ($row['image']) { ?> 
                        <div class="relative overflow-hidden aspect-[4/3] bg-sky-50">
                            <img src="<?php echo $base_path; ?>assets/images/cats/<?php echo htmlspecialchars($row['image']); ?>" 
                                 class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500"
                                 alt="<?php echo htmlspecialchars($row['name']); ?>">
                            
                            <!-- Dynamic status color allocation tag for clearer data monitoring -->
                            <?php 
                            $status = $row['status'];
                            $badge_color = "bg-sky-600"; // Default Available
                            if ($status == "Under Treatment") $badge_color = "bg-amber-600";
                            elseif ($status == "Quarantined") $badge_color = "bg-purple-600";
                            elseif ($status == "Adopted") $badge_color = "bg-emerald-600";
                            elseif ($status == "Deceased") $badge_color = "bg-slate-700";
                            ?>
                            <span class="absolute top-4 right-4 <?php echo $badge_color; ?> text-white text-xs font-extrabold px-3 py-1.5 rounded-full shadow-sm tracking-wide uppercase">
                                <?php echo htmlspecialchars($status); ?>
                            </span>
                        </div>
                    <?php } else { ?>
                        <div class="aspect-[4/3] bg-sky-50 flex flex-col items-center justify-center text-sky-400 gap-2 relative">
                            <span class="text-xs font-bold uppercase tracking-wider">No Image Available</span>
                            <span class="absolute top-4 right-4 bg-sky-600 text-white text-xs font-extrabold px-3 py-1.5 rounded-full shadow-sm tracking-wide uppercase">
                                <?php echo htmlspecialchars($row['status']); ?>
                            </span>
                        </div>
                    <?php } ?>

                    <!-- CARD DETAILS -->
                    <div class="p-6">
                        <div class="flex justify-between items-start gap-2 mb-2">
                            <h3 class="text-2xl font-black text-slate-900 group-hover:text-sky-600 transition duration-200 truncate">
                                <a href="/PawTrack/cats/view.php?id=<?php echo $row['catid']; ?>">
                                    <?php echo htmlspecialchars($row['name']); ?>
                                </a>
                            </h3>
                            <span class="bg-sky-50 text-sky-700 text-xs font-bold px-2.5 py-1 rounded-lg border border-sky-200 uppercase tracking-wider shrink-0 mt-1">
                                <?php echo !empty($row['breed']) ? htmlspecialchars($row['breed']) : 'Domestic'; ?>
                            </span>
                        </div>

                        <!-- CHARACTERISTICS TAGS -->
                        <div class="flex flex-wrap gap-1.5 mb-4">
                            <span class="bg-slate-100 text-slate-700 text-[10px] font-bold px-2 py-0.5 rounded">
                                Color: <?php echo !empty($row['color']) ? htmlspecialchars($row['color']) : 'Mixed'; ?>
                            </span>
                            <span class="bg-slate-100 text-slate-700 text-[10px] font-bold px-2 py-0.5 rounded">
                                Pattern: <?php echo !empty($row['pattern']) ? htmlspecialchars($row['pattern']) : 'Solid'; ?>
                            </span>
                            <span class="bg-slate-100 text-slate-700 text-[10px] font-bold px-2 py-0.5 rounded">
                                Eyes: <?php echo !empty($row['eye_color']) ? htmlspecialchars($row['eye_color']) : 'Unknown'; ?>
                            </span>
                        </div>

                        <!-- SPECIAL REMARKS -->
                        <?php if (!empty($row['special_remarks'])) { ?>
                            <p class="text-xs text-slate-500 italic line-clamp-2 bg-slate-50 p-2.5 rounded-xl border border-slate-100 mb-2">
                                "<?php echo htmlspecialchars($row['special_remarks']); ?>"
                            </p>
                        <?php } ?>

                        <!-- FACILITY MANAGEMENT DATA -->
                        <div class="space-y-2 mt-2 pt-3 border-t border-slate-100 text-xs text-slate-700">
                            <div class="flex items-center">
                                <p class="text-slate-600"><b class="text-slate-900 font-bold">Age Group:</b> <?php echo htmlspecialchars($row['agecategory']); ?></p>
                            </div>
                            <div class="flex items-center">
                                <p class="text-slate-600"><b class="text-slate-900 font-bold">Shelter:</b> <?php echo htmlspecialchars($row['sheltername']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD FOOTER ACTIONS -->
                <div class="p-6 pt-0 mt-2 flex items-center justify-between border-t border-slate-100 pt-4">
                    <a href="/PawTrack/cats/view.php?id=<?php echo $row['catid']; ?>" class="text-sm font-bold text-slate-700 hover:text-sky-600 transition duration-200 flex items-center gap-1 group/link">
                        View Profile <span class="transform group-hover/link:translate-x-0.5 transition duration-200">→</span>
                    </a>
                    
                    <div class="flex gap-2">
                        <?php if (($row['status'] == 'Available') && (!$role || $role == "Adopter")) { ?>
                            <a href="/PawTrack/auth/login.php?redirect=/PawTrack/adoption/add.php?catid=<?php echo $row['catid']; ?>" 
                               class="bg-sky-500 hover:bg-sky-600 text-white font-bold px-4 py-2 rounded-xl text-xs shadow-sm transition-all duration-200">
                                Adopt
                            </a>
                        <?php } ?>
                        
                        <!-- Authorized edit access block for Admin, Manager, and Staff roles -->
                        <?php if ($is_management) { ?>
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