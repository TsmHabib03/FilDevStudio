<?php
/**
 * Client Dashboard
 */
$pageTitle = "Dashboard - FilDevStudio";
require_once '../includes/header.php';
require_once '../includes/functions.php';
requireLogin();

$userId = $_SESSION['user_id'];

// Get user data
try {
    $pdo = getConnection();
    
    // Get business profile
    $stmt = $pdo->prepare("SELECT * FROM business_profiles WHERE user_id = ?");
    $stmt->execute([$userId]);
    $profile = $stmt->fetch();
    
    // Get client sites
    $stmt = $pdo->prepare("SELECT cs.*, t.name as template_name FROM client_sites cs 
                           LEFT JOIN templates t ON cs.template_id = t.id 
                           WHERE cs.user_id = ? ORDER BY cs.created_at DESC");
    $stmt->execute([$userId]);
    $sites = $stmt->fetchAll();
    
    // Get recent requests
    $stmt = $pdo->prepare("SELECT * FROM custom_requests WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    $stmt->execute([$userId]);
    $requests = $stmt->fetchAll();
    
} catch (Exception $e) {
    $profile = null;
    $sites = [];
    $requests = [];
}
?>

<!-- Dashboard Header -->
<section class="gradient-bg py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
                <p class="text-blue-100">Manage your websites and customization requests</p>
            </div>
            <div class="flex gap-3">
                <a href="../templates.php" class="bg-white text-primary px-4 py-2 rounded-lg font-medium hover:bg-gray-100 transition">
                    <i class="fas fa-plus mr-2"></i>New Website
                </a>
            </div>
        </div>
    </div>
</section>

<section class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Stats Cards -->
        <div class="grid md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Active Sites</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo count($sites); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-globe text-primary text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Pending Requests</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo count(array_filter($requests, fn($r) => $r['status'] === 'pending')); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-500 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">In Progress</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo count(array_filter($requests, fn($r) => $r['status'] === 'in_progress')); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-spinner text-blue-500 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Completed</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo count(array_filter($requests, fn($r) => $r['status'] === 'completed')); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-green-500 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- My Websites -->
                <div class="bg-white rounded-xl shadow">
                    <div class="p-6 border-b flex justify-between items-center">
                        <h2 class="text-xl font-bold text-gray-800">My Websites</h2>
                        <a href="../templates.php" class="text-primary hover:underline text-sm">
                            <i class="fas fa-plus mr-1"></i>Add New
                        </a>
                    </div>
                    <div class="p-6">
                        <?php if (empty($sites)): ?>
                            <div class="text-center py-8">
                                <i class="fas fa-globe text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500 mb-4">You haven't created any websites yet.</p>
                                <a href="../templates.php" class="inline-flex items-center gradient-bg text-white px-4 py-2 rounded-lg">
                                    <i class="fas fa-plus mr-2"></i>Choose a Template
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($sites as $site): ?>
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center">
                                                <i class="fas fa-globe text-white"></i>
                                            </div>
                                            <div>
                                                <h3 class="font-semibold text-gray-800"><?php echo htmlspecialchars($site['site_name'] ?? 'Untitled Site'); ?></h3>
                                                <p class="text-sm text-gray-500">Template: <?php echo htmlspecialchars($site['template_name']); ?></p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            <?php echo getStatusBadge($site['status']); ?>
                                            <a href="edit-site.php?id=<?php echo $site['id']; ?>" class="text-primary hover:underline">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Requests -->
                <div class="bg-white rounded-xl shadow">
                    <div class="p-6 border-b flex justify-between items-center">
                        <h2 class="text-xl font-bold text-gray-800">Customization Requests</h2>
                        <a href="custom-request.php" class="text-primary hover:underline text-sm">
                            <i class="fas fa-plus mr-1"></i>New Request
                        </a>
                    </div>
                    <div class="p-6">
                        <?php if (empty($requests)): ?>
                            <div class="text-center py-8">
                                <i class="fas fa-palette text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500 mb-4">No customization requests yet.</p>
                                <a href="custom-request.php" class="text-primary hover:underline">Submit a request</a>
                            </div>
                        <?php else: ?>
                            <div class="space-y-3">
                                <?php foreach ($requests as $request): ?>
                                    <div class="flex items-center justify-between p-3 border rounded-lg">
                                        <div>
                                            <p class="font-medium text-gray-800 capitalize"><?php echo $request['request_type']; ?> Change</p>
                                            <p class="text-sm text-gray-500"><?php echo timeAgo($request['created_at']); ?></p>
                                        </div>
                                        <?php echo getStatusBadge($request['status']); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Business Profile Card -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Business Profile</h3>
                    <?php if ($profile): ?>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-500">Business Name</p>
                                <p class="font-medium text-gray-800"><?php echo htmlspecialchars($profile['business_name']); ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Business Type</p>
                                <p class="font-medium text-gray-800 capitalize"><?php echo $profile['business_type']; ?></p>
                            </div>
                        </div>
                        <a href="profile.php" class="mt-4 block text-center text-primary hover:underline text-sm">
                            <i class="fas fa-edit mr-1"></i>Edit Profile
                        </a>
                    <?php else: ?>
                        <p class="text-gray-500">Profile not set up.</p>
                        <a href="profile.php" class="text-primary hover:underline text-sm">Complete your profile</a>
                    <?php endif; ?>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="../templates.php" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <i class="fas fa-plus-circle text-primary mr-3"></i>
                            <span>Create New Website</span>
                        </a>
                        <a href="custom-request.php" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <i class="fas fa-palette text-purple-500 mr-3"></i>
                            <span>Request Customization</span>
                        </a>
                        <a href="profile.php" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <i class="fas fa-user-edit text-green-500 mr-3"></i>
                            <span>Update Profile</span>
                        </a>
                    </div>
                </div>

                <!-- Help Card -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl shadow p-6 text-white">
                    <h3 class="text-lg font-bold mb-2">Need Help?</h3>
                    <p class="text-blue-100 text-sm mb-4">Our team is ready to assist you with any questions.</p>
                    <a href="mailto:support@fildevstudio.com" class="inline-block bg-white text-primary px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-100 transition">
                        <i class="fas fa-envelope mr-2"></i>Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
