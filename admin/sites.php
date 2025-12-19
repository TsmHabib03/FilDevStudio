<?php
/**
 * Admin - Manage Client Sites
 */
$pageTitle = "Manage Sites - FilDevStudio Admin";
require_once '../includes/header.php';
require_once '../includes/functions.php';
requireAdmin();

$error = '';
$success = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $siteId = (int)$_POST['site_id'];
    $newStatus = sanitize($_POST['status']);
    
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("UPDATE client_sites SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $siteId]);
        $success = 'Site status updated!';
    } catch (Exception $e) {
        $error = 'Failed to update.';
    }
}

// Get sites
try {
    $pdo = getConnection();
    $stmt = $pdo->query("SELECT cs.*, u.name as user_name, u.email, t.name as template_name, t.category
                         FROM client_sites cs 
                         LEFT JOIN users u ON cs.user_id = u.id 
                         LEFT JOIN templates t ON cs.template_id = t.id 
                         ORDER BY cs.created_at DESC");
    $sites = $stmt->fetchAll();
} catch (Exception $e) {
    $sites = [];
}
?>

<!-- Page Header -->
<section class="gradient-bg py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="dashboard.php" class="text-blue-200 hover:text-white mb-2 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
        </a>
        <h1 class="text-2xl md:text-3xl font-bold text-white">Client Websites</h1>
        <p class="text-blue-100">View and manage all client websites</p>
    </div>
</section>

<section class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if ($error): echo displayAlert('error', $error); endif; ?>
        <?php if ($success): echo displayAlert('success', $success); endif; ?>
        
        <!-- Sites Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (empty($sites)): ?>
                <div class="col-span-full bg-white rounded-xl shadow p-8 text-center">
                    <i class="fas fa-globe text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">No sites created yet</p>
                </div>
            <?php else: ?>
                <?php foreach ($sites as $site): ?>
                    <div class="bg-white rounded-xl shadow overflow-hidden card-hover">
                        <!-- Site Preview Header -->
                        <div class="h-32 relative" style="background: <?php echo $site['primary_color'] ?? '#3B82F6'; ?>;">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center text-white">
                                    <i class="fas fa-globe text-3xl mb-2 opacity-50"></i>
                                    <p class="font-medium"><?php echo htmlspecialchars($site['site_name'] ?? 'Untitled'); ?></p>
                                </div>
                            </div>
                            <div class="absolute top-3 right-3">
                                <?php echo getStatusBadge($site['status']); ?>
                            </div>
                        </div>
                        
                        <!-- Site Info -->
                        <div class="p-4">
                            <div class="mb-3">
                                <p class="font-medium text-gray-800"><?php echo htmlspecialchars($site['user_name']); ?></p>
                                <p class="text-sm text-gray-500"><?php echo htmlspecialchars($site['email']); ?></p>
                            </div>
                            <div class="text-sm text-gray-600 mb-3">
                                <p><i class="fas fa-palette text-primary mr-2"></i>Template: <?php echo htmlspecialchars($site['template_name']); ?></p>
                                <p><i class="fas fa-calendar text-gray-400 mr-2"></i>Created: <?php echo formatDate($site['created_at']); ?></p>
                            </div>
                            
                            <!-- Status Update Form -->
                            <form method="POST" class="flex items-center gap-2">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="site_id" value="<?php echo $site['id']; ?>">
                                <select name="status" class="flex-1 px-3 py-2 border rounded-lg text-sm">
                                    <option value="draft" <?php echo $site['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                    <option value="active" <?php echo $site['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="suspended" <?php echo $site['status'] === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                                </select>
                                <button type="submit" class="gradient-bg text-white px-3 py-2 rounded-lg text-sm">
                                    <i class="fas fa-save"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
