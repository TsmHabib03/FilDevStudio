<?php
/**
 * Admin Dashboard - FilDevStudio Web Services Platform
 * Enhanced UI/UX with Modern Design
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
    
    // In progress requests
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM custom_requests WHERE status = 'in_progress'");
    $inProgressRequests = $stmt->fetch()['count'];
    
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
    $stmt = $pdo->query("SELECT u.*, bp.business_name, bp.business_type 
                         FROM users u 
                         LEFT JOIN business_profiles bp ON u.id = bp.user_id 
                         WHERE u.role = 'client' 
                         ORDER BY u.created_at DESC LIMIT 5");
    $recentUsers = $stmt->fetchAll();
    
} catch (Exception $e) {
    $totalUsers = $totalSites = $pendingRequests = $inProgressRequests = $totalTemplates = 0;
    $recentRequests = $recentUsers = [];
}

// Calculate percentages for visual indicators (mock data for demo)
$userGrowth = '+12%';
$siteGrowth = '+8%';
?>

<!-- Dashboard Header -->
<section class="relative overflow-hidden bg-gradient-to-br from-dark via-primary-900 to-primary-800 py-10 lg:py-14">
    <!-- Decorative -->
    <div class="absolute inset-0">
        <div class="absolute top-0 right-1/4 w-96 h-96 bg-primary-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-1/4 w-80 h-80 bg-accent-500/10 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] border border-white/5 rounded-full"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <div class="flex items-center mb-3">
                    <span class="inline-flex items-center px-3 py-1 bg-white/10 backdrop-blur-sm rounded-full text-sm text-white">
                        <i class="fas fa-shield-alt text-secondary-400 mr-2"></i>Admin Panel
                    </span>
                </div>
                <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Admin Dashboard</h1>
                <p class="text-primary-200 text-lg">FilDevStudio Web Services Management</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="templates.php" class="inline-flex items-center px-5 py-2.5 bg-white text-primary-600 rounded-xl font-semibold hover:bg-gray-100 transition-all duration-200 shadow-lg">
                    <i class="fas fa-plus mr-2"></i>Add Template
                </a>
                <a href="requests.php" class="inline-flex items-center px-5 py-2.5 bg-white/10 backdrop-blur-sm text-white border border-white/20 rounded-xl font-semibold hover:bg-white/20 transition-all duration-200">
                    <i class="fas fa-tasks mr-2"></i>View Requests
                    <?php if ($pendingRequests > 0): ?>
                        <span class="ml-2 px-2 py-0.5 bg-red-500 text-xs rounded-full"><?php echo $pendingRequests; ?></span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </div>
</section>

<section class="py-8 lg:py-10 bg-gray-50 min-h-[60vh]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Stats Cards -->
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-10 -mt-14 relative z-10">
            <!-- Total Clients -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300 group">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl flex items-center justify-center shadow-lg shadow-primary-500/25 group-hover:scale-105 transition-transform">
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-lg">
                        <i class="fas fa-arrow-up mr-1 text-[10px]"></i><?php echo $userGrowth; ?>
                    </span>
                </div>
                <div>
                    <p class="text-gray-500 text-sm font-medium mb-1">Total Clients</p>
                    <p class="text-4xl font-bold text-dark"><?php echo $totalUsers; ?></p>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <a href="users.php" class="text-primary-600 text-sm font-medium hover:text-primary-700 transition-colors inline-flex items-center group-hover:translate-x-1 duration-200">
                        View all clients <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
            
            <!-- Active Sites -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300 group">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-secondary-500 to-secondary-600 rounded-2xl flex items-center justify-center shadow-lg shadow-secondary-500/25 group-hover:scale-105 transition-transform">
                        <i class="fas fa-globe text-white text-xl"></i>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-lg">
                        <i class="fas fa-arrow-up mr-1 text-[10px]"></i><?php echo $siteGrowth; ?>
                    </span>
                </div>
                <div>
                    <p class="text-gray-500 text-sm font-medium mb-1">Active Sites</p>
                    <p class="text-4xl font-bold text-dark"><?php echo $totalSites; ?></p>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <a href="sites.php" class="text-secondary-600 text-sm font-medium hover:text-secondary-700 transition-colors inline-flex items-center group-hover:translate-x-1 duration-200">
                        View all sites <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
            
            <!-- Pending Requests -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300 group <?php echo $pendingRequests > 0 ? 'ring-2 ring-amber-200' : ''; ?>">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-amber-400 to-amber-500 rounded-2xl flex items-center justify-center shadow-lg shadow-amber-500/25 group-hover:scale-105 transition-transform">
                        <i class="fas fa-clock text-white text-xl"></i>
                    </div>
                    <?php if ($pendingRequests > 0): ?>
                        <span class="inline-flex items-center px-2.5 py-1 bg-amber-100 text-amber-700 text-xs font-semibold rounded-lg animate-pulse">
                            <i class="fas fa-exclamation mr-1"></i>Action needed
                        </span>
                    <?php endif; ?>
                </div>
                <div>
                    <p class="text-gray-500 text-sm font-medium mb-1">Pending Requests</p>
                    <p class="text-4xl font-bold <?php echo $pendingRequests > 0 ? 'text-amber-600' : 'text-dark'; ?>"><?php echo $pendingRequests; ?></p>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <a href="requests.php?status=pending" class="text-amber-600 text-sm font-medium hover:text-amber-700 transition-colors inline-flex items-center group-hover:translate-x-1 duration-200">
                        Review requests <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
            
            <!-- Templates -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300 group">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-accent-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg shadow-accent-500/25 group-hover:scale-105 transition-transform">
                        <i class="fas fa-palette text-white text-xl"></i>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 bg-accent-100 text-accent-700 text-xs font-semibold rounded-lg">
                        Active
                    </span>
                </div>
                <div>
                    <p class="text-gray-500 text-sm font-medium mb-1">Templates</p>
                    <p class="text-4xl font-bold text-dark"><?php echo $totalTemplates; ?></p>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <a href="templates.php" class="text-accent-600 text-sm font-medium hover:text-accent-700 transition-colors inline-flex items-center group-hover:translate-x-1 duration-200">
                        Manage templates <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-8">
            <!-- Recent Requests -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gradient-to-r from-gray-50 to-white">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center mr-3">
                            <i class="fas fa-inbox text-amber-600"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-dark">Recent Requests</h2>
                            <p class="text-sm text-gray-500"><?php echo $pendingRequests + $inProgressRequests; ?> active requests</p>
                        </div>
                    </div>
                    <a href="requests.php" class="inline-flex items-center px-4 py-2 bg-amber-50 text-amber-600 rounded-lg text-sm font-medium hover:bg-amber-100 transition-colors">
                        View All
                    </a>
                </div>
                <div class="p-6">
                    <?php if (empty($recentRequests)): ?>
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-inbox text-3xl text-gray-400"></i>
                            </div>
                            <p class="text-gray-500">No requests yet</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($recentRequests as $request): 
                                $statusColors = [
                                    'pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'dot' => 'bg-amber-500'],
                                    'in_progress' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'dot' => 'bg-blue-500'],
                                    'completed' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'dot' => 'bg-green-500'],
                                    'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'dot' => 'bg-red-500']
                                ];
                                $style = $statusColors[$request['status']] ?? $statusColors['pending'];
                            ?>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-primary-50 border border-transparent hover:border-primary-100 transition-all duration-200 group">
                                    <div class="flex items-center space-x-4">
                                        <div class="relative">
                                            <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-md">
                                                <span class="text-white font-bold"><?php echo strtoupper(substr($request['user_name'] ?? 'U', 0, 1)); ?></span>
                                            </div>
                                            <span class="absolute -bottom-1 -right-1 w-4 h-4 <?php echo $style['dot']; ?> rounded-full border-2 border-white"></span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-dark group-hover:text-primary-600 transition-colors"><?php echo htmlspecialchars($request['user_name'] ?? 'Unknown'); ?></p>
                                            <p class="text-sm text-gray-500 capitalize"><?php echo $request['request_type']; ?> request</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-3 py-1 <?php echo $style['bg']; ?> <?php echo $style['text']; ?> text-xs font-semibold rounded-lg capitalize">
                                            <?php echo str_replace('_', ' ', $request['status']); ?>
                                        </span>
                                        <p class="text-xs text-gray-400 mt-1"><?php echo timeAgo($request['created_at']); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Users -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gradient-to-r from-gray-50 to-white">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center mr-3">
                            <i class="fas fa-user-plus text-primary-600"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-dark">Recent Clients</h2>
                            <p class="text-sm text-gray-500"><?php echo $totalUsers; ?> total clients</p>
                        </div>
                    </div>
                    <a href="users.php" class="inline-flex items-center px-4 py-2 bg-primary-50 text-primary-600 rounded-lg text-sm font-medium hover:bg-primary-100 transition-colors">
                        View All
                    </a>
                </div>
                <div class="p-6">
                    <?php if (empty($recentUsers)): ?>
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-users text-3xl text-gray-400"></i>
                            </div>
                            <p class="text-gray-500">No clients yet</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($recentUsers as $user): 
                                $typeIcons = [
                                    'retail' => ['icon' => 'fa-shopping-bag', 'color' => 'text-primary-500'],
                                    'food' => ['icon' => 'fa-utensils', 'color' => 'text-orange-500'],
                                    'freelance' => ['icon' => 'fa-briefcase', 'color' => 'text-accent-500'],
                                    'services' => ['icon' => 'fa-cogs', 'color' => 'text-secondary-500'],
                                    'other' => ['icon' => 'fa-globe', 'color' => 'text-gray-500']
                                ];
                                $typeStyle = $typeIcons[$user['business_type'] ?? 'other'] ?? $typeIcons['other'];
                            ?>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-primary-50 border border-transparent hover:border-primary-100 transition-all duration-200 group">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-accent-500 rounded-xl flex items-center justify-center shadow-md">
                                            <span class="text-white font-bold"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-dark group-hover:text-primary-600 transition-colors"><?php echo htmlspecialchars($user['name']); ?></p>
                                            <p class="text-sm text-gray-500 flex items-center">
                                                <i class="fas <?php echo $typeStyle['icon']; ?> <?php echo $typeStyle['color']; ?> mr-1.5 text-xs"></i>
                                                <?php echo htmlspecialchars($user['business_name'] ?? $user['email']); ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-lg">
                                            <i class="fas fa-circle text-[6px] mr-1.5 animate-pulse"></i>Active
                                        </span>
                                        <p class="text-xs text-gray-400 mt-1"><?php echo timeAgo($user['created_at']); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-secondary-100 rounded-xl flex items-center justify-center mr-3">
                        <i class="fas fa-bolt text-secondary-600"></i>
                    </div>
                    <h2 class="text-xl font-bold text-dark">Quick Actions</h2>
                </div>
            </div>
            <div class="p-6">
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="templates.php" class="group flex items-center p-5 border-2 border-dashed border-gray-200 rounded-xl hover:border-primary-400 hover:bg-primary-50 transition-all duration-200">
                        <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mr-4 group-hover:bg-primary-200 group-hover:scale-105 transition-all">
                            <i class="fas fa-plus-circle text-xl text-primary-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-dark group-hover:text-primary-600 transition-colors">Add Template</p>
                            <p class="text-sm text-gray-500">Create new design</p>
                        </div>
                    </a>
                    <a href="requests.php" class="group flex items-center p-5 border-2 border-dashed border-gray-200 rounded-xl hover:border-amber-400 hover:bg-amber-50 transition-all duration-200">
                        <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mr-4 group-hover:bg-amber-200 group-hover:scale-105 transition-all">
                            <i class="fas fa-tasks text-xl text-amber-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-dark group-hover:text-amber-600 transition-colors">Manage Requests</p>
                            <p class="text-sm text-gray-500"><?php echo $pendingRequests; ?> pending</p>
                        </div>
                    </a>
                    <a href="users.php" class="group flex items-center p-5 border-2 border-dashed border-gray-200 rounded-xl hover:border-secondary-400 hover:bg-secondary-50 transition-all duration-200">
                        <div class="w-12 h-12 bg-secondary-100 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary-200 group-hover:scale-105 transition-all">
                            <i class="fas fa-user-cog text-xl text-secondary-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-dark group-hover:text-secondary-600 transition-colors">Manage Users</p>
                            <p class="text-sm text-gray-500"><?php echo $totalUsers; ?> clients</p>
                        </div>
                    </a>
                    <a href="sites.php" class="group flex items-center p-5 border-2 border-dashed border-gray-200 rounded-xl hover:border-accent-400 hover:bg-accent-50 transition-all duration-200">
                        <div class="w-12 h-12 bg-accent-100 rounded-xl flex items-center justify-center mr-4 group-hover:bg-accent-200 group-hover:scale-105 transition-all">
                            <i class="fas fa-sitemap text-xl text-accent-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-dark group-hover:text-accent-600 transition-colors">View Sites</p>
                            <p class="text-sm text-gray-500"><?php echo $totalSites; ?> active</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
