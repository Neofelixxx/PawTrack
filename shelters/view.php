<?php
include("../config/db.php");
include("../includes/header.php");

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    die("<div class='max-w-3xl mx-auto mt-10 p-6 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl font-bold text-center'>Invalid Shelter Request.</div>");
}

$result = pg_query_params($conn, "SELECT * FROM Shelter WHERE ShelterID = $1", [$id]);
$shelter = pg_fetch_assoc($result);

if (!$shelter) {
    die("<div class='max-w-3xl mx-auto mt-10 p-6 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl font-bold text-center'>Shelter profile could not be located.</div>");
}

/* Fetch active felines allocated to this facility */
$cats = pg_query_params($conn, "SELECT * FROM Cat WHERE ShelterID = $1 AND Status = 'Available' ORDER BY CatID DESC", [$id]);
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 mb-12 space-y-8">

    <div class="bg-white rounded-3xl shadow-sm border border-sky-100 overflow-hidden">
        <?php if ($shelter['image']) { ?>
            <div class="h-64 sm:h-80 w-full relative">
                <img src="../assets/images/shelters/<?php echo htmlspecialchars($shelter['image']); ?>" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 to-transparent"></div>
                <div class="absolute bottom-6 left-8 text-white">
                    <span class="bg-sky-500 text-white text-[10px] font-bold uppercase tracking-widest px-3 py-1 rounded-md mb-2 inline-block">Registered Hub</span>
                    <h2 class="text-4xl font-black tracking-tight"><?php echo htmlspecialchars($shelter['name']); ?></h2>
                </div>
            </div>
        <?php } else { ?>
            <div class="bg-slate-900 p-8 text-white">
                <span class="bg-sky-500 text-white text-[10px] font-bold uppercase tracking-widest px-3 py-1 rounded-md mb-2 inline-block">Registered Hub</span>
                <h2 class="text-4xl font-black tracking-tight"><?php echo htmlspecialchars($shelter['name']); ?></h2>
            </div>
        <?php } ?>

        <div class="p-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-3">Facility Overview</h3>
                <p class="text-slate-700 leading-relaxed text-sm"><?php echo nl2br(htmlspecialchars($shelter['description'])); ?></p>
            </div>
            
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-3">Logistical Data</h3>
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Operational District</span>
                    <span class="font-bold text-slate-800 text-sm">📍 <?php echo htmlspecialchars($shelter['district']); ?></span>
                </div>
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Housing Capacity</span>
                    <span class="font-bold text-slate-800 text-sm">📦 <?php echo htmlspecialchars($shelter['capacity']); ?> Units Maximum</span>
                </div>
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Physical Address</span>
                    <span class="font-medium text-slate-700 text-xs"><?php echo htmlspecialchars($shelter['address']); ?></span>
                </div>
            </div>
        </div>
    </div>

    <div>
        <div class="mb-6 flex justify-between items-end border-b border-sky-100 pb-3">
            <h3 class="text-2xl font-black text-slate-900 tracking-tight">Active Population Roster</h3>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider"><?php echo pg_num_rows($cats); ?> Available</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php while ($cat = pg_fetch_assoc($cats)) { ?>
                <div class="bg-white rounded-2xl shadow-sm border border-sky-50 overflow-hidden hover:shadow-md transition">
                    <?php if ($cat['image']) { ?>
                        <img src="../assets/images/cats/<?php echo htmlspecialchars($cat['image']); ?>" class="w-full h-48 object-cover">
                    <?php } else { ?>
                        <div class="w-full h-48 bg-slate-100 flex items-center justify-center text-slate-300 text-3xl">🐱</div>
                    <?php } ?>
                    
                    <div class="p-5">
                        <h4 class="text-lg font-bold text-slate-800 tracking-tight truncate"><?php echo htmlspecialchars($cat['name']); ?></h4>
                        <p class="text-xs text-slate-500 font-medium mt-1 truncate">Breed: <?php echo htmlspecialchars($cat['breed']); ?></p>
                        <p class="text-xs text-slate-500 font-medium mb-4">Age Classification: <?php echo htmlspecialchars($cat['agecategory']); ?></p>
                        
                        <a href="/PawTrack/cats/view.php?id=<?php echo $cat['catid']; ?>" class="block w-full bg-sky-50 hover:bg-sky-100 text-sky-700 text-center font-bold py-2 rounded-xl text-xs transition">
                            View Profile
                        </a>
                    </div>
                </div>
            <?php } ?>
        </div>
        
        <?php if (pg_num_rows($cats) == 0) { ?>
            <div class="bg-slate-50 border border-slate-100 p-8 rounded-2xl text-center text-slate-500 text-sm font-medium">
                No available profiles are currently allocated to this facility.
            </div>
        <?php } ?>
    </div>

</div>

<?php include("../includes/footer.php"); ?>