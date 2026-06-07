<?php
include("../config/db.php");
include("../includes/header.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['role'] ?? null;
$userid = $_SESSION['user_id'] ?? null;

if (!$role) {
    header("Location: /PawTrack/auth/login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    die("<div class='p-8 text-center text-rose-600 font-bold'>Invalid Application ID.</div>");
}

// Fetch the complete application dossier including Cat, Adopter, and Reviewer details
$query = "
    SELECT a.*, 
           c.name AS cat_name, c.breed, c.image AS cat_image, c.status AS cat_status,
           u.name AS adopter_name, u.email, u.phone, u.address,
           r.name AS reviewer_name
    FROM Adoption a
    JOIN Cat c ON a.catid = c.catid
    JOIN \"User\" u ON a.adopterid = u.userid
    LEFT JOIN \"User\" r ON a.approvedby = r.userid
    WHERE a.adoptionid = $1
";
$result = pg_query_params($conn, $query, [$id]);
$application = pg_fetch_assoc($result);

if (!$application) {
    die("<div class='p-8 text-center text-rose-600 font-bold'>Application record not found.</div>");
}

// Security Check: Adopters can only view their own applications
if ($role == 'Adopter' && $application['adopterid'] != $userid) {
    die("<div class='p-8 text-center text-rose-600 font-bold'>Unauthorized. You do not have permission to view this dossier.</div>");
}
?>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 mb-12">
    
    <!-- BACK NAVIGATION -->
    <div class="mb-6 border-b border-sky-100 pb-4 flex justify-between items-end">
        <div>
            <a href="/PawTrack/adoption/list.php" class="text-sm font-bold text-slate-500 hover:text-sky-700 transition flex items-center gap-2 mb-3">
                ← Back to Pipeline
            </a>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">Application Dossier #<?php echo str_pad($application['adoptionid'], 4, '0', STR_PAD_LEFT); ?></h2>
        </div>
        
        <!-- STATUS BADGE -->
        <div>
            <?php if ($application['status'] == 'Approved') { ?>
                <span class="bg-emerald-100 text-emerald-800 text-sm font-bold px-4 py-2 rounded-xl uppercase tracking-wide">Approved</span>
            <?php } elseif ($application['status'] == 'Rejected') { ?>
                <span class="bg-rose-100 text-rose-800 text-sm font-bold px-4 py-2 rounded-xl uppercase tracking-wide">Declined</span>
            <?php } else { ?>
                <span class="bg-amber-100 text-amber-800 text-sm font-bold px-4 py-2 rounded-xl uppercase tracking-wide animate-pulse">Pending Review</span>
            <?php } ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- LEFT COLUMN: APPLICANT DATA & QUESTIONNAIRE -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Applicant Profile Card -->
            <div class="bg-white rounded-3xl border border-sky-100 p-8 shadow-sm">
                <h3 class="text-xs font-bold text-sky-600 uppercase tracking-wider mb-4 border-b border-sky-50 pb-2">Applicant Information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">
                    <div>
                        <p class="text-slate-400 font-medium mb-1">Full Legal Name</p>
                        <p class="font-bold text-slate-800"><?php echo htmlspecialchars($application['adopter_name']); ?></p>
                    </div>
                    <div>
                        <p class="text-slate-400 font-medium mb-1">Contact Email</p>
                        <p class="font-bold text-slate-800"><?php echo htmlspecialchars($application['email']); ?></p>
                    </div>
                    <div>
                        <p class="text-slate-400 font-medium mb-1">Phone Number</p>
                        <p class="font-bold text-slate-800"><?php echo htmlspecialchars($application['phone']); ?></p>
                    </div>
                    <div>
                        <p class="text-slate-400 font-medium mb-1">Registered Address</p>
                        <p class="font-bold text-slate-800"><?php echo htmlspecialchars($application['address']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Vetting Questionnaire Answers -->
            <div class="bg-white rounded-3xl border border-sky-100 p-8 shadow-sm">
                <h3 class="text-xs font-bold text-sky-600 uppercase tracking-wider mb-4 border-b border-sky-50 pb-2">Vetting Questionnaire Responses</h3>
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-6 text-sm text-slate-700 leading-relaxed font-mono whitespace-pre-wrap"><?php 
                    echo !empty($application['application_details']) 
                        ? htmlspecialchars($application['application_details']) 
                        : 'No detailed vetting questionnaire was recorded for this legacy application.'; 
                ?></div>
            </div>
            
        </div>

        <!-- RIGHT COLUMN: TARGET CAT & ACTION PANEL -->
        <div class="space-y-6">
            
            <!-- Target Feline Card -->
            <div class="bg-white rounded-3xl border border-sky-100 shadow-sm overflow-hidden">
                <div class="h-40 bg-sky-50 relative">
                    <?php if ($application['cat_image']) { ?>
                        <img src="/PawTrack/assets/images/cats/<?php echo $application['cat_image']; ?>" class="w-full h-full object-cover" alt="Cat Image">
                    <?php } else { ?>
                        <div class="flex items-center justify-center h-full text-sky-300 font-bold text-xs uppercase tracking-widest">No Image</div>
                    <?php } ?>
                </div>
                <div class="p-6">
                    <h3 class="text-xs font-bold text-sky-600 uppercase tracking-wider mb-2">Subject Feline</h3>
                    <p class="text-2xl font-black text-slate-900"><?php echo htmlspecialchars($application['cat_name']); ?></p>
                    <p class="text-sm font-semibold text-slate-500 mb-4"><?php echo htmlspecialchars($application['breed']); ?></p>
                    <a href="/PawTrack/cats/view.php?id=<?php echo $application['catid']; ?>" class="text-xs font-bold text-sky-600 hover:text-sky-800 transition">View Full Medical Profile →</a>
                </div>
            </div>

            <!-- Management Action Panel -->
            <div class="bg-slate-900 rounded-3xl p-6 shadow-md text-white">
                <h3 class="text-xs font-bold text-sky-400 uppercase tracking-wider mb-4 border-b border-white/10 pb-2">System Processing</h3>
                
                <div class="space-y-3 text-sm mb-6">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Date Filed:</span>
                        <span class="font-bold"><?php echo !empty($application['adoptiondate']) ? date("d M Y", strtotime($application['adoptiondate'])) : 'Unknown'; ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Reviewer:</span>
                        <span class="font-bold"><?php echo $application['reviewer_name'] ? htmlspecialchars($application['reviewer_name']) : 'Pending Assignment'; ?></span>
                    </div>
                </div>

                <?php if (($role == 'Admin' || $role == 'Staff' || $role == 'Manager') && $application['status'] == 'Pending') { ?>
                    <div class="space-y-3 pt-2">
                        <a href="action.php?id=<?php echo $application['adoptionid']; ?>&action=approve" class="block text-center bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-4 py-3 rounded-xl text-sm transition shadow-sm">
                            Authorize & Approve
                        </a>
                        <a href="action.php?id=<?php echo $application['adoptionid']; ?>&action=reject" class="block text-center border border-rose-400/30 hover:bg-rose-500/10 text-rose-400 font-bold px-4 py-3 rounded-xl text-sm transition">
                            Decline Application
                        </a>
                    </div>
                <?php } elseif ($role != 'Adopter') { ?>
                    <div class="p-3 bg-white/5 rounded-xl border border-white/10 text-xs text-slate-300 text-center font-medium">
                        This dossier is locked as a finalized decision has already been recorded.
                    </div>
                <?php } else { ?>
                    <div class="p-3 bg-sky-500/20 rounded-xl border border-sky-500/30 text-xs text-sky-100 text-center font-medium">
                        Your application is currently under review by our shelter management team.
                    </div>
                <?php } ?>
            </div>

        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>