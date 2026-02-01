<?php
/**
 * Client Dashboard - FilDevStudio Web Services Platform
 * Enhanced UI/UX with Modern Design
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
    
    // Get client sites with template info
    $stmt = $pdo->prepare("SELECT cs.*, cs.subdomain, cs.published_at, t.name as template_name, t.category as template_category FROM client_sites cs 
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

// Count stats
$pendingCount = count(array_filter($requests, fn($r) => $r['status'] === 'pending'));
$inProgressCount = count(array_filter($requests, fn($r) => $r['status'] === 'in_progress'));
$completedCount = count(array_filter($requests, fn($r) => $r['status'] === 'completed'));
?>

<!-- Dashboard Header -->
<section class="relative overflow-hidden bg-gradient-to-br from-primary-900 via-primary-800 to-primary-700 py-10 lg:py-14">
    <!-- Decorative -->
    <div class="absolute inset-0">
        <div class="absolute top-0 right-1/4 w-96 h-96 bg-primary-400/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-1/4 w-80 h-80 bg-accent-500/10 rounded-full blur-3xl"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <div class="flex items-center mb-3">
                    <span class="inline-flex items-center px-3 py-1 bg-white/10 backdrop-blur-sm rounded-full text-sm text-white">
                        <i class="fas fa-circle text-green-400 text-[8px] mr-2 animate-pulse"></i>Online Dashboard
                    </span>
                </div>
                <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
                    Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!
                </h1>
                <p class="text-primary-200 text-lg">Manage your websites and track your customization requests</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="../templates.php" class="inline-flex items-center px-5 py-2.5 bg-white text-primary-600 rounded-xl font-semibold hover:bg-gray-100 transition-all duration-200 shadow-lg">
                    <i class="fas fa-plus mr-2"></i>New Website
                </a>
                <a href="custom-request.php" class="inline-flex items-center px-5 py-2.5 bg-white/10 backdrop-blur-sm text-white border border-white/20 rounded-xl font-semibold hover:bg-white/20 transition-all duration-200">
                    <i class="fas fa-palette mr-2"></i>Request Design
                </a>
            </div>
        </div>
    </div>
</section>

<section class="py-8 lg:py-10 bg-gray-50 min-h-[60vh]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Stats Cards -->
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-10 -mt-14 relative z-10">
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium mb-1">Active Sites</p>
                        <p class="text-4xl font-bold text-dark"><?php echo count($sites); ?></p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl flex items-center justify-center shadow-lg shadow-primary-500/25">
                        <i class="fas fa-globe text-white text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <a href="#my-websites" class="text-primary-600 text-sm font-medium hover:text-primary-700 transition-colors">
                        View all sites <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium mb-1">Pending Requests</p>
                        <p class="text-4xl font-bold text-dark"><?php echo $pendingCount; ?></p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-amber-400 to-amber-500 rounded-2xl flex items-center justify-center shadow-lg shadow-amber-500/25">
                        <i class="fas fa-clock text-white text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <a href="my-requests.php" class="text-amber-600 text-sm font-medium hover:text-amber-700 transition-colors">
                        View requests <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium mb-1">In Progress</p>
                        <p class="text-4xl font-bold text-dark"><?php echo $inProgressCount; ?></p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/25">
                        <i class="fas fa-spinner text-white text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <a href="my-requests.php" class="text-blue-600 text-sm font-medium hover:text-blue-700 transition-colors">
                        View requests <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium mb-1">Completed</p>
                        <p class="text-4xl font-bold text-dark"><?php echo $completedCount; ?></p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-secondary-500 to-secondary-600 rounded-2xl flex items-center justify-center shadow-lg shadow-secondary-500/25">
                        <i class="fas fa-check text-white text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <a href="my-requests.php" class="text-secondary-600 text-sm font-medium hover:text-secondary-700 transition-colors">
                        View requests <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- My Websites -->
                <div id="my-websites" class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gradient-to-r from-gray-50 to-white">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center mr-3">
                                <i class="fas fa-globe text-primary-600"></i>
                            </div>
                            <h2 class="text-xl font-bold text-dark">My Websites</h2>
                        </div>
                        <a href="../templates.php" class="inline-flex items-center px-4 py-2 bg-primary-50 text-primary-600 rounded-lg text-sm font-medium hover:bg-primary-100 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Add New
                        </a>
                    </div>
                    <div class="p-6">
                        <?php if (empty($sites)): ?>
                            <div class="text-center py-12">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-globe text-4xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-dark mb-2">No websites yet</h3>
                                <p class="text-gray-500 mb-6 max-w-sm mx-auto">Start building your online presence by selecting a professional template.</p>
                                <a href="../templates.php" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-xl font-semibold shadow-lg shadow-primary-500/25 hover:from-primary-600 hover:to-primary-700 transition-all duration-200">
                                    <i class="fas fa-rocket mr-2"></i>Choose a Template
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($sites as $site): 
                                    $templatePlaceholder = getTemplatePlaceholder($site['template_id'], $site['template_category'] ?? 'general');
                                    $publicUrl = getPublicSiteUrl($site['subdomain'] ?? null);
                                ?>
                                    <div class="group p-5 bg-gray-50 rounded-xl hover:bg-primary-50 border border-transparent hover:border-primary-100 transition-all duration-200">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-14 h-14 bg-gradient-to-br <?php echo $templatePlaceholder['gradient']; ?> rounded-xl flex items-center justify-center shadow-md group-hover:shadow-lg transition-all duration-200">
                                                    <i class="<?php echo $templatePlaceholder['icon']; ?> text-white text-lg"></i>
                                                </div>
                                                <div>
                                                    <h3 class="font-bold text-dark group-hover:text-primary-600 transition-colors"><?php echo htmlspecialchars($site['site_name'] ?? 'Untitled Site'); ?></h3>
                                                    <p class="text-sm text-gray-500 flex items-center mt-1">
                                                        <i class="fas fa-palette text-xs mr-1.5"></i>
                                                        <?php echo htmlspecialchars($site['template_name']); ?>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-4">
                                                <?php 
                                                $statusClasses = [
                                                    'draft' => 'bg-gray-100 text-gray-600',
                                                    'pending' => 'bg-amber-100 text-amber-700',
                                                    'active' => 'bg-green-100 text-green-700',
                                                    'published' => 'bg-green-100 text-green-700'
                                                ];
                                                $statusClass = $statusClasses[$site['status']] ?? 'bg-gray-100 text-gray-600';
                                                ?>
                                                <span class="px-3 py-1.5 <?php echo $statusClass; ?> text-xs font-semibold rounded-lg capitalize">
                                                    <?php if ($site['status'] === 'active'): ?>
                                                        <i class="fas fa-check-circle mr-1"></i>Published
                                                    <?php else: ?>
                                                        <?php echo $site['status']; ?>
                                                    <?php endif; ?>
                                                </span>
                                                <div class="flex items-center space-x-2">
                                                    <a href="preview-site.php?id=<?php echo $site['id']; ?>" class="w-9 h-9 bg-white rounded-lg flex items-center justify-center text-gray-400 hover:text-primary-600 hover:bg-primary-50 border border-gray-200 hover:border-primary-200 transition-all" title="Preview">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="edit-site.php?id=<?php echo $site['id']; ?>" class="w-9 h-9 bg-white rounded-lg flex items-center justify-center text-gray-400 hover:text-primary-600 hover:bg-primary-50 border border-gray-200 hover:border-primary-200 transition-all" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <?php if ($site['status'] === 'active' && !empty($site['subdomain'])): ?>
                                        <!-- Published URL Section -->
                                        <div class="mt-4 pt-4 border-t border-gray-200">
                                            <div class="flex items-center justify-between gap-3">
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs text-gray-500 mb-1">
                                                        <i class="fas fa-globe mr-1"></i>Public URL
                                                        <?php if (!empty($site['published_at'])): ?>
                                                        <span class="ml-2 text-green-600">
                                                            • Published <?php echo date('M j, Y', strtotime($site['published_at'])); ?>
                                                        </span>
                                                        <?php endif; ?>
                                                    </p>
                                                    <div class="flex items-center gap-2">
                                                        <input type="text" readonly 
                                                               value="<?php echo htmlspecialchars($publicUrl); ?>"
                                                               class="flex-1 text-xs bg-white px-3 py-1.5 border border-gray-200 rounded-lg truncate text-gray-600 cursor-text"
                                                               id="url-<?php echo $site['id']; ?>">
                                                        <button type="button" 
                                                                onclick="copyUrl('url-<?php echo $site['id']; ?>', this)"
                                                                class="px-3 py-1.5 bg-primary-500 text-white rounded-lg text-xs font-medium hover:bg-primary-600 transition flex-shrink-0"
                                                                title="Copy URL">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                        <a href="<?php echo htmlspecialchars($publicUrl); ?>" target="_blank"
                                                           class="px-3 py-1.5 bg-green-500 text-white rounded-lg text-xs font-medium hover:bg-green-600 transition flex-shrink-0"
                                                           title="Open site">
                                                            <i class="fas fa-external-link-alt"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Requests -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gradient-to-r from-gray-50 to-white">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-accent-100 rounded-xl flex items-center justify-center mr-3">
                                <i class="fas fa-palette text-accent-600"></i>
                            </div>
                            <h2 class="text-xl font-bold text-dark">Customization Requests</h2>
                        </div>
                        <a href="custom-request.php" class="inline-flex items-center px-4 py-2 bg-accent-50 text-accent-600 rounded-lg text-sm font-medium hover:bg-accent-100 transition-colors">
                            <i class="fas fa-plus mr-2"></i>New Request
                        </a>
                    </div>
                    <div class="p-6">
                        <?php if (empty($requests)): ?>
                            <div class="text-center py-12">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-palette text-4xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-dark mb-2">No requests yet</h3>
                                <p class="text-gray-500 mb-6 max-w-sm mx-auto">Need custom changes to your website? Our team is ready to help!</p>
                                <a href="custom-request.php" class="inline-flex items-center text-accent-600 font-semibold hover:text-accent-700 transition-colors">
                                    <i class="fas fa-magic mr-2"></i>Submit a request
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="space-y-3">
                                <?php foreach ($requests as $request): 
                                    $statusColors = [
                                        'pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'icon' => 'fa-clock'],
                                        'in_progress' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'fa-spinner fa-spin'],
                                        'completed' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'icon' => 'fa-check-circle'],
                                        'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'fa-times-circle']
                                    ];
                                    $statusStyle = $statusColors[$request['status']] ?? $statusColors['pending'];
                                ?>
                                    <div class="flex items-center justify-between p-4 border border-gray-100 rounded-xl hover:border-primary-100 hover:bg-primary-50/30 transition-all duration-200">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 <?php echo $statusStyle['bg']; ?> rounded-lg flex items-center justify-center">
                                                <i class="fas <?php echo $statusStyle['icon']; ?> <?php echo $statusStyle['text']; ?>"></i>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-dark capitalize"><?php echo $request['request_type']; ?> Change</p>
                                                <p class="text-sm text-gray-500"><?php echo timeAgo($request['created_at']); ?></p>
                                            </div>
                                        </div>
                                        <span class="px-3 py-1.5 <?php echo $statusStyle['bg']; ?> <?php echo $statusStyle['text']; ?> text-xs font-semibold rounded-lg capitalize">
                                            <?php echo str_replace('_', ' ', $request['status']); ?>
                                        </span>
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
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-secondary-100 rounded-xl flex items-center justify-center mr-3">
                                <i class="fas fa-building text-secondary-600"></i>
                            </div>
                            <h3 class="text-lg font-bold text-dark">Business Profile</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <?php if ($profile): ?>
                            <div class="space-y-4">
                                <div class="p-4 bg-gray-50 rounded-xl">
                                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1">Business Name</p>
                                    <p class="font-semibold text-dark"><?php echo htmlspecialchars($profile['business_name']); ?></p>
                                </div>
                                <div class="p-4 bg-gray-50 rounded-xl">
                                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1">Business Type</p>
                                    <p class="font-semibold text-dark capitalize flex items-center">
                                        <?php 
                                        $typeIcons = [
                                            'retail' => '🛒',
                                            'food' => '🍽️',
                                            'freelance' => '💼',
                                            'services' => '🔧',
                                            'other' => '📦'
                                        ];
                                        echo ($typeIcons[$profile['business_type']] ?? '📦') . ' ';
                                        ?>
                                        <?php echo $profile['business_type']; ?>
                                    </p>
                                </div>
                            </div>
                            <a href="profile.php" class="mt-6 flex items-center justify-center p-3 bg-primary-50 text-primary-600 rounded-xl font-medium hover:bg-primary-100 transition-colors">
                                <i class="fas fa-edit mr-2"></i>Edit Profile
                            </a>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-building text-2xl text-gray-400"></i>
                                </div>
                                <p class="text-gray-500 mb-4">Complete your business profile</p>
                                <a href="profile.php" class="inline-flex items-center text-primary-600 font-semibold hover:text-primary-700">
                                    <i class="fas fa-plus-circle mr-2"></i>Set up profile
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center mr-3">
                                <i class="fas fa-bolt text-primary-600"></i>
                            </div>
                            <h3 class="text-lg font-bold text-dark">Quick Actions</h3>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="space-y-2">
                            <a href="../templates.php" class="flex items-center p-4 rounded-xl hover:bg-primary-50 group transition-all duration-200">
                                <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-primary-200 transition-colors">
                                    <i class="fas fa-plus-circle text-primary-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-dark group-hover:text-primary-600 transition-colors">Create New Website</p>
                                    <p class="text-xs text-gray-500">Choose from templates</p>
                                </div>
                                <i class="fas fa-chevron-right ml-auto text-gray-300 group-hover:text-primary-400 transition-colors"></i>
                            </a>
                            <a href="custom-request.php" class="flex items-center p-4 rounded-xl hover:bg-accent-50 group transition-all duration-200">
                                <div class="w-10 h-10 bg-accent-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-accent-200 transition-colors">
                                    <i class="fas fa-palette text-accent-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-dark group-hover:text-accent-600 transition-colors">Request Customization</p>
                                    <p class="text-xs text-gray-500">Get custom changes</p>
                                </div>
                                <i class="fas fa-chevron-right ml-auto text-gray-300 group-hover:text-accent-400 transition-colors"></i>
                            </a>
                            <a href="profile.php" class="flex items-center p-4 rounded-xl hover:bg-secondary-50 group transition-all duration-200">
                                <div class="w-10 h-10 bg-secondary-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-secondary-200 transition-colors">
                                    <i class="fas fa-user-edit text-secondary-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-dark group-hover:text-secondary-600 transition-colors">Update Profile</p>
                                    <p class="text-xs text-gray-500">Manage your info</p>
                                </div>
                                <i class="fas fa-chevron-right ml-auto text-gray-300 group-hover:text-secondary-400 transition-colors"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Help Card -->
                <div class="bg-gradient-to-br from-primary-600 via-primary-700 to-accent-700 rounded-2xl shadow-xl p-6 text-white relative overflow-hidden">
                    <!-- Decorative -->
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>
                    
                    <div class="relative">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                            <i class="fas fa-headset text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold mb-2">Need Help?</h3>
                        <p class="text-primary-100 text-sm mb-5">Our team is ready to assist you with any questions about your website.</p>
                        <a href="mailto:support@fildevstudio.com" class="inline-flex items-center px-5 py-2.5 bg-white text-primary-600 rounded-xl text-sm font-semibold hover:bg-gray-100 transition-colors shadow-lg">
                            <i class="fas fa-envelope mr-2"></i>Contact Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Copy URL to clipboard
function copyUrl(inputId, btn) {
    const urlInput = document.getElementById(inputId);
    if (urlInput) {
        navigator.clipboard.writeText(urlInput.value).then(function() {
            // Show feedback
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i>';
            btn.classList.remove('bg-primary-500', 'hover:bg-primary-600');
            btn.classList.add('bg-green-500');
            setTimeout(function() {
                btn.innerHTML = originalHtml;
                btn.classList.remove('bg-green-500');
                btn.classList.add('bg-primary-500', 'hover:bg-primary-600');
            }, 2000);
        }).catch(function() {
            // Fallback for older browsers
            urlInput.select();
            document.execCommand('copy');
        });
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>
