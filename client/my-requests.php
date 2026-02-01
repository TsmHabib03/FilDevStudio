<?php
/**
 * My Custom Requests Page
 * Shows list of all custom website requests submitted by the user
 */
require_once '../includes/functions.php';
require_once '../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check login
if (!isLoggedIn()) {
    $_SESSION['error'] = 'Please log in to access this page.';
    header('Location: ../auth/login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$pageTitle = "My Requests - FilDevStudio";

// Get database connection
try {
    $pdo = getConnection();
} catch (Exception $e) {
    die('Database connection failed.');
}

// Get all requests for this user
$requests = [];
try {
    $stmt = $pdo->prepare("
        SELECT cr.*, cs.site_name,
               (SELECT COUNT(*) FROM request_files WHERE request_id = cr.id) as file_count
        FROM custom_requests cr
        LEFT JOIN client_sites cs ON cr.site_id = cs.id
        WHERE cr.user_id = ?
        ORDER BY cr.created_at DESC
    ");
    $stmt->execute([$userId]);
    $requests = $stmt->fetchAll();
} catch (Exception $e) {
    // If request_files table doesn't exist, try without it
    try {
        $stmt = $pdo->prepare("
            SELECT cr.*, cs.site_name
            FROM custom_requests cr
            LEFT JOIN client_sites cs ON cr.site_id = cs.id
            WHERE cr.user_id = ?
            ORDER BY cr.created_at DESC
        ");
        $stmt->execute([$userId]);
        $requests = $stmt->fetchAll();
    } catch (Exception $e) {
        $requests = [];
    }
}

// Status badge helper
function getRequestStatusBadge($status) {
    $badges = [
        'pending' => '<span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium"><i class="fas fa-clock mr-1"></i>Pending Review</span>',
        'in_progress' => '<span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium"><i class="fas fa-cog fa-spin mr-1"></i>In Progress</span>',
        'preview_ready' => '<span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-medium"><i class="fas fa-eye mr-1"></i>Preview Ready</span>',
        'revision_requested' => '<span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-medium"><i class="fas fa-redo mr-1"></i>Revision Requested</span>',
        'completed' => '<span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium"><i class="fas fa-check mr-1"></i>Completed</span>',
        'approved' => '<span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium"><i class="fas fa-check-double mr-1"></i>Approved</span>',
        'rejected' => '<span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium"><i class="fas fa-times mr-1"></i>Rejected</span>',
    ];
    return $badges[$status] ?? $badges['pending'];
}

require_once '../includes/header.php';
?>

<!-- Page Header -->
<section class="gradient-bg py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <a href="dashboard.php" class="text-blue-200 hover:text-white mb-2 inline-block">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
                <h1 class="text-3xl md:text-4xl font-bold text-white">My Custom Requests</h1>
                <p class="text-blue-100 mt-2">Track the status of your custom website requests</p>
            </div>
            <a href="custom-request.php" class="px-6 py-3 bg-white text-primary rounded-lg font-semibold hover:bg-gray-100 transition shadow-lg">
                <i class="fas fa-plus mr-2"></i>New Request
            </a>
        </div>
    </div>
</section>

<section class="py-10 bg-gray-50 min-h-[60vh]">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <?php if (empty($requests)): ?>
            <!-- Empty State -->
            <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-clipboard-list text-4xl text-gray-300"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">No Requests Yet</h2>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                    You haven't submitted any custom website requests. Want a unique design? Our team can build something special for your business.
                </p>
                <a href="custom-request.php" class="inline-flex items-center px-6 py-3 gradient-bg text-white rounded-lg font-semibold hover:opacity-90 transition">
                    <i class="fas fa-palette mr-2"></i>Request Custom Website
                </a>
            </div>
        <?php else: ?>
            
            <!-- Stats Row -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <?php
                $statusCounts = ['pending' => 0, 'in_progress' => 0, 'preview_ready' => 0, 'completed' => 0];
                foreach ($requests as $r) {
                    $status = $r['status'];
                    if (isset($statusCounts[$status])) {
                        $statusCounts[$status]++;
                    }
                }
                ?>
                <div class="bg-white rounded-xl p-4 shadow-sm">
                    <p class="text-3xl font-bold text-gray-800"><?php echo count($requests); ?></p>
                    <p class="text-sm text-gray-500">Total Requests</p>
                </div>
                <div class="bg-yellow-50 rounded-xl p-4 shadow-sm border border-yellow-100">
                    <p class="text-3xl font-bold text-yellow-600"><?php echo $statusCounts['pending']; ?></p>
                    <p class="text-sm text-yellow-700">Pending</p>
                </div>
                <div class="bg-blue-50 rounded-xl p-4 shadow-sm border border-blue-100">
                    <p class="text-3xl font-bold text-blue-600"><?php echo $statusCounts['in_progress']; ?></p>
                    <p class="text-sm text-blue-700">In Progress</p>
                </div>
                <div class="bg-green-50 rounded-xl p-4 shadow-sm border border-green-100">
                    <p class="text-3xl font-bold text-green-600"><?php echo $statusCounts['completed'] + ($statusCounts['preview_ready'] ?? 0); ?></p>
                    <p class="text-sm text-green-700">Completed</p>
                </div>
            </div>
            
            <!-- Requests List -->
            <div class="space-y-4">
                <?php foreach ($requests as $request): ?>
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition overflow-hidden">
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <?php echo getRequestStatusBadge($request['status']); ?>
                                        <?php if (!empty($request['reference_number'])): ?>
                                            <span class="text-xs text-gray-500 font-mono"><?php echo htmlspecialchars($request['reference_number']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <h3 class="text-lg font-bold text-gray-800">
                                        <?php 
                                        // Try to get project title, fall back to request type
                                        $title = $request['project_title'] ?? ucfirst($request['request_type']) . ' Request';
                                        echo htmlspecialchars($title);
                                        ?>
                                    </h3>
                                    
                                    <p class="text-gray-600 text-sm mt-1 line-clamp-2">
                                        <?php echo htmlspecialchars(substr($request['request_details'], 0, 150)) . (strlen($request['request_details']) > 150 ? '...' : ''); ?>
                                    </p>
                                    
                                    <div class="flex flex-wrap items-center gap-4 mt-3 text-xs text-gray-500">
                                        <span><i class="fas fa-calendar mr-1"></i><?php echo date('M j, Y', strtotime($request['created_at'])); ?></span>
                                        <?php if (!empty($request['site_name'])): ?>
                                            <span><i class="fas fa-globe mr-1"></i><?php echo htmlspecialchars($request['site_name']); ?></span>
                                        <?php endif; ?>
                                        <?php if (isset($request['file_count']) && $request['file_count'] > 0): ?>
                                            <span><i class="fas fa-paperclip mr-1"></i><?php echo $request['file_count']; ?> files</span>
                                        <?php endif; ?>
                                        <?php if (!empty($request['revision_count']) && $request['revision_count'] > 0): ?>
                                            <span><i class="fas fa-redo mr-1"></i><?php echo $request['revision_count']; ?> revisions</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-2">
                                    <?php if ($request['status'] === 'preview_ready' && !empty($request['preview_url'])): ?>
                                        <a href="<?php echo htmlspecialchars($request['preview_url']); ?>" target="_blank"
                                           class="px-4 py-2 bg-purple-100 text-purple-700 rounded-lg text-sm font-medium hover:bg-purple-200 transition">
                                            <i class="fas fa-eye mr-1"></i>View Preview
                                        </a>
                                    <?php endif; ?>
                                    <a href="view-request.php?id=<?php echo $request['id']; ?>" 
                                       class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                                        <i class="fas fa-arrow-right mr-1"></i>Details
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($request['status'] === 'preview_ready'): ?>
                            <div class="bg-purple-50 border-t border-purple-100 px-6 py-3">
                                <p class="text-sm text-purple-700">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Preview is ready! Review it and submit your feedback or approve.
                                </p>
                            </div>
                        <?php elseif (!empty($request['admin_notes'])): ?>
                            <div class="bg-blue-50 border-t border-blue-100 px-6 py-3">
                                <p class="text-sm text-blue-700">
                                    <i class="fas fa-comment mr-2"></i>
                                    <strong>Admin Note:</strong> <?php echo htmlspecialchars(substr($request['admin_notes'], 0, 100)); ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
        <?php endif; ?>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
