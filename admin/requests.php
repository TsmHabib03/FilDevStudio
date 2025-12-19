<?php
/**
 * Admin - Manage Customization Requests
 */
$pageTitle = "Manage Requests - FilDevStudio Admin";
require_once '../includes/header.php';
require_once '../includes/functions.php';
requireAdmin();

$error = '';
$success = '';

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $requestId = (int)$_POST['request_id'];
    $newStatus = sanitize($_POST['status']);
    $adminNotes = sanitize($_POST['admin_notes'] ?? '');
    
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("UPDATE custom_requests SET status = ?, admin_notes = ?, assigned_to = ? WHERE id = ?");
        $stmt->execute([$newStatus, $adminNotes, $_SESSION['user_id'], $requestId]);
        $success = 'Request updated successfully!';
    } catch (Exception $e) {
        $error = 'Failed to update request.';
    }
}

// Get filter
$statusFilter = $_GET['status'] ?? 'all';

// Get requests
try {
    $pdo = getConnection();
    $sql = "SELECT cr.*, u.name as user_name, u.email, cs.site_name, a.name as assigned_name
            FROM custom_requests cr 
            LEFT JOIN users u ON cr.user_id = u.id 
            LEFT JOIN client_sites cs ON cr.site_id = cs.id
            LEFT JOIN users a ON cr.assigned_to = a.id";
    
    if ($statusFilter !== 'all') {
        $sql .= " WHERE cr.status = :status";
    }
    $sql .= " ORDER BY cr.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    if ($statusFilter !== 'all') {
        $stmt->bindParam(':status', $statusFilter);
    }
    $stmt->execute();
    $requests = $stmt->fetchAll();
    
} catch (Exception $e) {
    $requests = [];
}
?>

<!-- Page Header -->
<section class="gradient-bg py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="dashboard.php" class="text-blue-200 hover:text-white mb-2 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
        </a>
        <h1 class="text-2xl md:text-3xl font-bold text-white">Customization Requests</h1>
        <p class="text-blue-100">Review and manage client customization requests</p>
    </div>
</section>

<section class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if ($error): echo displayAlert('error', $error); endif; ?>
        <?php if ($success): echo displayAlert('success', $success); endif; ?>
        
        <!-- Filters -->
        <div class="bg-white rounded-xl shadow p-4 mb-6">
            <div class="flex flex-wrap gap-3">
                <a href="?status=all" class="px-4 py-2 rounded-lg <?php echo $statusFilter === 'all' ? 'gradient-bg text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'; ?>">
                    All
                </a>
                <a href="?status=pending" class="px-4 py-2 rounded-lg <?php echo $statusFilter === 'pending' ? 'gradient-bg text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'; ?>">
                    <i class="fas fa-clock mr-2"></i>Pending
                </a>
                <a href="?status=in_progress" class="px-4 py-2 rounded-lg <?php echo $statusFilter === 'in_progress' ? 'gradient-bg text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'; ?>">
                    <i class="fas fa-spinner mr-2"></i>In Progress
                </a>
                <a href="?status=completed" class="px-4 py-2 rounded-lg <?php echo $statusFilter === 'completed' ? 'gradient-bg text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'; ?>">
                    <i class="fas fa-check mr-2"></i>Completed
                </a>
            </div>
        </div>
        
        <!-- Requests List -->
        <div class="space-y-4">
            <?php if (empty($requests)): ?>
                <div class="bg-white rounded-xl shadow p-8 text-center">
                    <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">No requests found</p>
                </div>
            <?php else: ?>
                <?php foreach ($requests as $request): ?>
                    <div class="bg-white rounded-xl shadow overflow-hidden">
                        <div class="p-6">
                            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <?php echo getStatusBadge($request['status']); ?>
                                        <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-sm capitalize">
                                            <?php echo $request['request_type']; ?>
                                        </span>
                                        <span class="text-gray-400 text-sm">#<?php echo $request['id']; ?></span>
                                    </div>
                                    <div class="mb-3">
                                        <p class="font-medium text-gray-800"><?php echo htmlspecialchars($request['user_name']); ?></p>
                                        <p class="text-sm text-gray-500"><?php echo htmlspecialchars($request['email']); ?></p>
                                        <?php if ($request['site_name']): ?>
                                            <p class="text-sm text-blue-600">Site: <?php echo htmlspecialchars($request['site_name']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4 mb-3">
                                        <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($request['request_details'])); ?></p>
                                    </div>
                                    <?php if ($request['attachment_path']): ?>
                                        <p class="text-sm"><i class="fas fa-paperclip text-gray-400 mr-2"></i>
                                            <a href="../<?php echo htmlspecialchars($request['attachment_path']); ?>" target="_blank" class="text-primary hover:underline">View Attachment</a>
                                        </p>
                                    <?php endif; ?>
                                    <?php if ($request['admin_notes']): ?>
                                        <div class="mt-3 bg-blue-50 rounded-lg p-3">
                                            <p class="text-sm text-blue-800"><strong>Admin Notes:</strong> <?php echo htmlspecialchars($request['admin_notes']); ?></p>
                                        </div>
                                    <?php endif; ?>
                                    <div class="mt-3 text-sm text-gray-500">
                                        <span><i class="fas fa-calendar mr-1"></i><?php echo formatDate($request['created_at'], 'M d, Y h:i A'); ?></span>
                                        <?php if ($request['assigned_name']): ?>
                                            <span class="ml-4"><i class="fas fa-user mr-1"></i>Assigned to: <?php echo htmlspecialchars($request['assigned_name']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Update Form -->
                                <div class="lg:w-64 bg-gray-50 rounded-lg p-4">
                                    <form method="POST" action="">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                        <div class="mb-3">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                            <select name="status" class="w-full px-3 py-2 border rounded-lg text-sm">
                                                <option value="pending" <?php echo $request['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="in_progress" <?php echo $request['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                                <option value="completed" <?php echo $request['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                <option value="rejected" <?php echo $request['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                            <textarea name="admin_notes" rows="2" class="w-full px-3 py-2 border rounded-lg text-sm"
                                                      placeholder="Add notes..."><?php echo htmlspecialchars($request['admin_notes'] ?? ''); ?></textarea>
                                        </div>
                                        <button type="submit" class="w-full gradient-bg text-white py-2 rounded-lg text-sm font-medium hover:opacity-90 transition">
                                            Update
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
