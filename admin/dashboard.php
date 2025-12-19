<?php
/**
 * Admin Dashboard
 */
$pageTitle = "Admin Dashboard - FilDevStudio";
require_once '../includes/header.php';
require_once '../includes/functions.php';
requireAdmin();

// Get dashboard stats
try {
    $pdo = getConnection();
    
    // Users count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'client'");
    $totalUsers = $stmt->fetch()['count'];
    
    // Active sites
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM client_sites");
    $totalSites = $stmt->fetch()['count'];
    
    // Pending requests
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM custom_requests WHERE status = 'pending'");
    $pendingRequests = $stmt->fetch()['count'];
    
    // Templates
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM templates WHERE is_active = 1");
    $totalTemplates = $stmt->fetch()['count'];
    
    // Recent requests
    $stmt = $pdo->query("SELECT cr.*, u.name as user_name, u.email 
                         FROM custom_requests cr 
                         LEFT JOIN users u ON cr.user_id = u.id 
                         ORDER BY cr.created_at DESC LIMIT 5");
    $recentRequests = $stmt->fetchAll();
    
    // Recent users
    $stmt = $pdo->query("SELECT u.*, bp.business_name 
                         FROM users u 
                         LEFT JOIN business_profiles bp ON u.id = bp.user_id 
                         WHERE u.role = 'client' 
                         ORDER BY u.created_at DESC LIMIT 5");
    $recentUsers = $stmt->fetchAll();
    
} catch (Exception $e) {
    $totalUsers = $totalSites = $pendingRequests = $totalTemplates = 0;
    $recentRequests = $recentUsers = [];
}
?>

<!-- Dashboard Header -->
<section class="gradient-bg py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white">Admin Dashboard</h1>
                <p class="text-blue-100">FilDevStudio Web Services Management</p>
            </div>
            <div class="flex gap-3">
                <a href="templates.php" class="bg-white text-primary px-4 py-2 rounded-lg font-medium hover:bg-gray-100 transition">
                    <i class="fas fa-plus mr-2"></i>Add Template
                </a>
            </div>
        </div>
    </div>
</section>

<section class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Stats Cards -->
        <div class="grid md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Clients</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $totalUsers; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-primary text-xl"></i>
                    </div>
                </div>
                <a href="users.php" class="text-primary text-sm hover:underline mt-2 inline-block">View all →</a>
            </div>
            <div class="bg-white rounded-xl shadow p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Active Sites</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $totalSites; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-globe text-green-500 text-xl"></i>
                    </div>
                </div>
                <a href="sites.php" class="text-primary text-sm hover:underline mt-2 inline-block">View all →</a>
            </div>
            <div class="bg-white rounded-xl shadow p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Pending Requests</p>
                        <p class="text-3xl font-bold text-orange-500"><?php echo $pendingRequests; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-orange-500 text-xl"></i>
                    </div>
                </div>
                <a href="requests.php" class="text-primary text-sm hover:underline mt-2 inline-block">View all →</a>
            </div>
            <div class="bg-white rounded-xl shadow p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Templates</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $totalTemplates; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-palette text-purple-500 text-xl"></i>
                    </div>
                </div>
                <a href="templates.php" class="text-primary text-sm hover:underline mt-2 inline-block">Manage →</a>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-8">
            <!-- Recent Requests -->
            <div class="bg-white rounded-xl shadow">
                <div class="p-6 border-b flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-800">Recent Requests</h2>
                    <a href="requests.php" class="text-primary hover:underline text-sm">View All</a>
                </div>
                <div class="p-6">
                    <?php if (empty($recentRequests)): ?>
                        <p class="text-gray-500 text-center py-4">No requests yet</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($recentRequests as $request): ?>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-800"><?php echo htmlspecialchars($request['user_name']); ?></p>
                                        <p class="text-sm text-gray-500 capitalize"><?php echo $request['request_type']; ?> request</p>
                                    </div>
                                    <div class="text-right">
                                        <?php echo getStatusBadge($request['status']); ?>
                                        <p class="text-xs text-gray-400 mt-1"><?php echo timeAgo($request['created_at']); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Users -->
            <div class="bg-white rounded-xl shadow">
                <div class="p-6 border-b flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-800">Recent Clients</h2>
                    <a href="users.php" class="text-primary hover:underline text-sm">View All</a>
                </div>
                <div class="p-6">
                    <?php if (empty($recentUsers)): ?>
                        <p class="text-gray-500 text-center py-4">No clients yet</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($recentUsers as $user): ?>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 gradient-bg rounded-full flex items-center justify-center">
                                            <span class="text-white font-bold"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></span>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800"><?php echo htmlspecialchars($user['name']); ?></p>
                                            <p class="text-sm text-gray-500"><?php echo htmlspecialchars($user['business_name'] ?? $user['email']); ?></p>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-400"><?php echo timeAgo($user['created_at']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Quick Actions</h2>
            <div class="grid md:grid-cols-4 gap-4">
                <a href="templates.php" class="flex items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-primary hover:bg-blue-50 transition">
                    <i class="fas fa-plus-circle text-2xl text-primary mr-3"></i>
                    <span class="font-medium text-gray-700">Add Template</span>
                </a>
                <a href="requests.php" class="flex items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-primary hover:bg-blue-50 transition">
                    <i class="fas fa-tasks text-2xl text-orange-500 mr-3"></i>
                    <span class="font-medium text-gray-700">Manage Requests</span>
                </a>
                <a href="users.php" class="flex items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-primary hover:bg-blue-50 transition">
                    <i class="fas fa-user-cog text-2xl text-green-500 mr-3"></i>
                    <span class="font-medium text-gray-700">Manage Users</span>
                </a>
                <a href="sites.php" class="flex items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-primary hover:bg-blue-50 transition">
                    <i class="fas fa-sitemap text-2xl text-purple-500 mr-3"></i>
                    <span class="font-medium text-gray-700">View Sites</span>
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
