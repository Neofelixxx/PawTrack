<?php
include("../config/db.php");
include("../includes/header.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['role'] ?? null;
$query = "SELECT * FROM Shelter ORDER BY ShelterID DESC";
$result = pg_query($conn, $query);
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 mb-12">
    
    <div class="flex justify-between items-center mb-8 border-b border-sky-100 pb-4">
        <div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">Partner Cat Shelters</h2>
            <p class="text-slate-500 text-sm mt-1">Management of active rescue hubs and real-time housing capacity allocations.</p>
        </div>
        <?php if ($role == "Admin" || $role == "Manager") { ?>
            <a href="add.php" class="bg-sky-500 hover:bg-sky-600 text-white font-semibold px-5 py-2.5 rounded-xl shadow-md hover:-translate-y-0.5 transition-all duration-200 text-sm flex items-center gap-2">
                <span class="text-base">＋</span> Register New Hub
            </a>
        <?php } ?>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php while ($row = pg_fetch_assoc($result)) { ?>
            <div class="bg-white rounded-3xl border border-sky-100/60 shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden flex flex-col justify-between group">
                
                <div>
                    <?php if ($row['image']) { ?>
                        <div class="relative overflow-hidden aspect-[16/10] bg-sky-50">
                            <img src="../assets/images/shelters/<?php echo htmlspecialchars($row['image']); ?>" 
                                 class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500"
                                 alt="<?php echo htmlspecialchars($row['name']); ?>">
                        </div>
                    <?php } else { ?>
                        <div class="aspect-[16/10] bg-slate-50 flex flex-col items-center justify-center text-slate-300 gap-2 border-b border-slate-100">
                            <span class="text-4xl">🏢</span>
                            <span class="text-xs font-medium uppercase tracking-wider">No Photo Available</span>
                        </div>
                    <?php } ?>

                    <div class="p-6">
                        <h3 class="text-2xl font-bold text-slate-800 tracking-tight group-hover:text-sky-600 transition duration-200 truncate">
                            <?php echo htmlspecialchars($row['name']); ?>
                        </h3>
                        
                        <div class="grid grid-cols-2 gap-4 mt-5 pt-4 border-t border-slate-50 text-sm">
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Regional District</p>
                                <p class="font-bold text-slate-700 mt-0.5 truncate">📍 <?php echo htmlspecialchars($row['district']); ?></p>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Capacity</p>
                                <p class="font-bold text-slate-700 mt-0.5">📦 <?php echo htmlspecialchars($row['capacity']); ?> Units</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 pt-0 mt-2 flex gap-3">
                    <a href="view.php?id=<?php echo $row['shelterid']; ?>" 
                       class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-center font-bold py-2.5 rounded-xl text-xs transition duration-200">
                        View Details
                    </a>
                    <?php if ($role == "Admin" || $role == "Manager") { ?>
                        <a href="edit.php?id=<?php echo $row['shelterid']; ?>" 
                           class="border border-sky-200 hover:bg-sky-50 text-sky-600 font-bold px-4 py-2.5 rounded-xl text-xs transition duration-200 flex items-center justify-center">
                            Edit
                        </a>
                    <?php } ?>
                </div>

            </div>
        <?php } ?>
    </div>
</div>

<?php include("../includes/footer.php"); ?>