<?php
/**
 * View Custom Request Details Page
 * Shows request details, preview, and revision workflow
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
$requestId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';
$success = '';

if (!$requestId) {
    header('Location: my-requests.php');
    exit();
}

// Get database connection
try {
    $pdo = getConnection();
} catch (Exception $e) {
    die('Database connection failed.');
}

// Get request details
try {
    $stmt = $pdo->prepare("
        SELECT cr.*, cs.site_name, u.name as user_name, u.email as user_email
        FROM custom_requests cr
        LEFT JOIN client_sites cs ON cr.site_id = cs.id
        LEFT JOIN users u ON cr.user_id = u.id
        WHERE cr.id = ? AND cr.user_id = ?
    ");
    $stmt->execute([$requestId, $userId]);
    $request = $stmt->fetch();
    
    if (!$request) {
        header('Location: my-requests.php');
        exit();
    }
} catch (Exception $e) {
    header('Location: my-requests.php');
    exit();
}

// Get uploaded files
$files = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM request_files WHERE request_id = ? ORDER BY file_type, uploaded_at");
    $stmt->execute([$requestId]);
    $files = $stmt->fetchAll();
} catch (Exception $e) {
    // Table might not exist
}

// Get revision history
$revisions = [];
try {
    $stmt = $pdo->prepare("
        SELECT rr.*, u.name as submitted_by_name, au.name as responded_by_name
        FROM request_revisions rr
        LEFT JOIN users u ON rr.submitted_by = u.id
        LEFT JOIN users au ON rr.responded_by = au.id
        WHERE rr.request_id = ?
        ORDER BY rr.revision_number DESC
    ");
    $stmt->execute([$requestId]);
    $revisions = $stmt->fetchAll();
} catch (Exception $e) {
    // Table might not exist
}

// Handle revision submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'submit_revision') {
        $feedback = sanitize($_POST['feedback'] ?? '');
        
        if (empty($feedback)) {
            $error = 'Please provide your feedback for the revision.';
        } else {
            try {
                // Get current revision count
                $revisionNumber = ($request['revision_count'] ?? 0) + 1;
                
                // Insert revision
                $stmt = $pdo->prepare("INSERT INTO request_revisions (request_id, revision_number, feedback, submitted_by) VALUES (?, ?, ?, ?)");
                $stmt->execute([$requestId, $revisionNumber, $feedback, $userId]);
                
                // Update request status and revision count
                $stmt = $pdo->prepare("UPDATE custom_requests SET status = 'revision_requested', revision_count = ?, revision_notes = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$revisionNumber, $feedback, $requestId]);
                
                logActivity($pdo, $userId, 'revision_request', "Requested revision #$revisionNumber for request ID: $requestId");
                
                $success = 'Revision request submitted successfully!';
                
                // Refresh data
                header("Location: view-request.php?id=$requestId&success=revision");
                exit();
                
            } catch (Exception $e) {
                $error = 'Failed to submit revision. Please try again.';
            }
        }
    } elseif ($_POST['action'] === 'approve_design') {
        try {
            $stmt = $pdo->prepare("UPDATE custom_requests SET status = 'approved', approved_at = NOW(), updated_at = NOW() WHERE id = ? AND user_id = ?");
            $stmt->execute([$requestId, $userId]);
            
            logActivity($pdo, $userId, 'approve_design', "Approved design for request ID: $requestId");
            
            header("Location: view-request.php?id=$requestId&success=approved");
            exit();
            
        } catch (Exception $e) {
            $error = 'Failed to approve. Please try again.';
        }
    }
}

// Check for success message from redirect
if (isset($_GET['success'])) {
    if ($_GET['success'] === 'revision') {
        $success = 'Revision request submitted! Our team will review your feedback.';
    } elseif ($_GET['success'] === 'approved') {
        $success = 'Design approved! Your website will be finalized shortly.';
    }
}

// Status badge helper
function getDetailedStatusBadge($status) {
    $badges = [
        'pending' => ['bg-yellow-100 text-yellow-700', 'fa-clock', 'Pending Review'],
        'in_progress' => ['bg-blue-100 text-blue-700', 'fa-cog fa-spin', 'In Progress'],
        'preview_ready' => ['bg-purple-100 text-purple-700', 'fa-eye', 'Preview Ready'],
        'revision_requested' => ['bg-orange-100 text-orange-700', 'fa-redo', 'Revision Requested'],
        'completed' => ['bg-green-100 text-green-700', 'fa-check', 'Completed'],
        'approved' => ['bg-green-100 text-green-800', 'fa-check-double', 'Approved'],
        'rejected' => ['bg-red-100 text-red-700', 'fa-times', 'Rejected'],
    ];
    $info = $badges[$status] ?? $badges['pending'];
    return '<span class="px-4 py-2 ' . $info[0] . ' rounded-full text-sm font-semibold inline-flex items-center"><i class="fas ' . $info[1] . ' mr-2"></i>' . $info[2] . '</span>';
}

$pageTitle = "Request Details - FilDevStudio";
require_once '../includes/header.php';
?>

<!-- Page Header -->
<section class="gradient-bg py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="my-requests.php" class="text-blue-200 hover:text-white mb-2 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Back to My Requests
        </a>
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white">
                    <?php echo htmlspecialchars($request['project_title'] ?? ucfirst($request['request_type']) . ' Request'); ?>
                </h1>
                <p class="text-blue-100 mt-1">
                    <?php if (!empty($request['reference_number'])): ?>
                        <span class="font-mono"><?php echo htmlspecialchars($request['reference_number']); ?></span> • 
                    <?php endif; ?>
                    Submitted <?php echo date('M j, Y', strtotime($request['created_at'])); ?>
                </p>
            </div>
            <div>
                <?php echo getDetailedStatusBadge($request['status']); ?>
            </div>
        </div>
    </div>
</section>

<section class="py-8 bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if ($error): echo displayAlert('error', $error); endif; ?>
        <?php if ($success): echo displayAlert('success', $success); endif; ?>
        
        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Preview Section (if available) -->
                <?php if ($request['status'] === 'preview_ready' && !empty($request['preview_url'])): ?>
                    <div class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl shadow-lg overflow-hidden">
                        <div class="p-6 text-white">
                            <h2 class="text-xl font-bold mb-2">
                                <i class="fas fa-eye mr-2"></i>Your Preview is Ready!
                            </h2>
                            <p class="text-purple-100 mb-4">Review your custom website design and let us know what you think.</p>
                            
                            <div class="flex flex-wrap gap-3">
                                <a href="<?php echo htmlspecialchars($request['preview_url']); ?>" target="_blank"
                                   class="px-6 py-3 bg-white text-purple-600 rounded-lg font-semibold hover:bg-gray-100 transition">
                                    <i class="fas fa-external-link-alt mr-2"></i>Open Preview
                                </a>
                                <button type="button" onclick="document.getElementById('revisionModal').classList.remove('hidden')"
                                        class="px-6 py-3 bg-white/20 text-white border border-white/30 rounded-lg font-semibold hover:bg-white/30 transition">
                                    <i class="fas fa-redo mr-2"></i>Request Changes
                                </button>
                                <form method="POST" action="" class="inline">
                                    <input type="hidden" name="action" value="approve_design">
                                    <button type="submit" onclick="return confirm('Are you sure you want to approve this design?')"
                                            class="px-6 py-3 bg-green-500 text-white rounded-lg font-semibold hover:bg-green-600 transition">
                                        <i class="fas fa-check mr-2"></i>Approve Design
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Request Details -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-clipboard-list text-primary mr-2"></i>Request Details
                    </h2>
                    
                    <div class="prose prose-sm max-w-none text-gray-700">
                        <?php echo nl2br(htmlspecialchars($request['request_details'])); ?>
                    </div>
                    
                    <?php if (!empty($request['preferred_colors'])): ?>
                        <div class="mt-4 pt-4 border-t">
                            <p class="text-sm text-gray-500">Preferred Colors</p>
                            <p class="text-gray-700"><?php echo htmlspecialchars($request['preferred_colors']); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Uploaded Files -->
                <?php if (!empty($files)): ?>
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">
                            <i class="fas fa-paperclip text-primary mr-2"></i>Uploaded Files
                        </h2>
                        
                        <?php
                        $filesByType = [];
                        foreach ($files as $file) {
                            $filesByType[$file['file_type']][] = $file;
                        }
                        ?>
                        
                        <div class="space-y-4">
                            <?php foreach ($filesByType as $type => $typeFiles): ?>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 mb-2 uppercase"><?php echo $type; ?></h3>
                                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
                                        <?php foreach ($typeFiles as $file): ?>
                                            <?php
                                            $isImage = strpos($file['mime_type'] ?? '', 'image/') === 0;
                                            ?>
                                            <a href="../<?php echo htmlspecialchars($file['file_path']); ?>" target="_blank"
                                               class="block group">
                                                <?php if ($isImage): ?>
                                                    <img src="../<?php echo htmlspecialchars($file['file_path']); ?>" 
                                                         alt="<?php echo htmlspecialchars($file['original_name']); ?>"
                                                         class="w-full h-20 object-cover rounded-lg border group-hover:border-primary transition">
                                                <?php else: ?>
                                                    <div class="w-full h-20 bg-gray-100 rounded-lg border flex items-center justify-center group-hover:border-primary transition">
                                                        <i class="fas fa-file text-2xl text-gray-400"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <p class="text-xs text-gray-500 mt-1 truncate"><?php echo htmlspecialchars($file['original_name']); ?></p>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Revision History -->
                <?php if (!empty($revisions)): ?>
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">
                            <i class="fas fa-history text-primary mr-2"></i>Revision History
                        </h2>
                        
                        <div class="space-y-4">
                            <?php foreach ($revisions as $revision): ?>
                                <div class="border-l-4 border-orange-400 pl-4 py-2">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-semibold text-orange-600">Revision #<?php echo $revision['revision_number']; ?></span>
                                        <span class="text-xs text-gray-500"><?php echo date('M j, Y g:i A', strtotime($revision['submitted_at'])); ?></span>
                                    </div>
                                    <p class="text-gray-700 text-sm"><?php echo nl2br(htmlspecialchars($revision['feedback'])); ?></p>
                                    
                                    <?php if (!empty($revision['admin_response'])): ?>
                                        <div class="mt-3 bg-blue-50 rounded-lg p-3">
                                            <p class="text-xs text-blue-600 font-medium mb-1">
                                                <i class="fas fa-reply mr-1"></i>Admin Response 
                                                <?php if (!empty($revision['responded_at'])): ?>
                                                    (<?php echo date('M j', strtotime($revision['responded_at'])); ?>)
                                                <?php endif; ?>
                                            </p>
                                            <p class="text-sm text-blue-800"><?php echo nl2br(htmlspecialchars($revision['admin_response'])); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status Timeline -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 mb-4">Status Timeline</h3>
                    
                    <?php
                    $timeline = [
                        'pending' => ['Submitted', 'fa-paper-plane', $request['created_at']],
                        'in_progress' => ['In Progress', 'fa-cog', null],
                        'preview_ready' => ['Preview Ready', 'fa-eye', null],
                        'approved' => ['Approved', 'fa-check-double', $request['approved_at'] ?? null],
                    ];
                    $currentStatus = $request['status'];
                    $statusOrder = ['pending', 'in_progress', 'preview_ready', 'approved'];
                    $currentIndex = array_search($currentStatus, $statusOrder);
                    if ($currentIndex === false) $currentIndex = 0;
                    ?>
                    
                    <div class="space-y-4">
                        <?php foreach ($statusOrder as $index => $status): ?>
                            <?php
                            $isPast = $index <= $currentIndex;
                            $isCurrent = $status === $currentStatus;
                            $info = $timeline[$status];
                            ?>
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center <?php echo $isPast ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400'; ?>">
                                    <i class="fas <?php echo $info[1]; ?> text-sm"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium <?php echo $isCurrent ? 'text-primary' : ($isPast ? 'text-gray-800' : 'text-gray-400'); ?>">
                                        <?php echo $info[0]; ?>
                                    </p>
                                    <?php if ($info[2]): ?>
                                        <p class="text-xs text-gray-500"><?php echo date('M j, Y', strtotime($info[2])); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if ($index < count($statusOrder) - 1): ?>
                                <div class="ml-4 w-0.5 h-4 <?php echo $index < $currentIndex ? 'bg-green-500' : 'bg-gray-200'; ?>"></div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Request Info -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 mb-4">Request Info</h3>
                    
                    <dl class="space-y-3 text-sm">
                        <?php if (!empty($request['business_type'])): ?>
                            <div>
                                <dt class="text-gray-500">Business Type</dt>
                                <dd class="text-gray-800 font-medium"><?php echo ucfirst($request['business_type']); ?></dd>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($request['budget_range'])): ?>
                            <div>
                                <dt class="text-gray-500">Budget</dt>
                                <dd class="text-gray-800 font-medium"><?php echo htmlspecialchars($request['budget_range']); ?></dd>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($request['timeline'])): ?>
                            <div>
                                <dt class="text-gray-500">Timeline</dt>
                                <dd class="text-gray-800 font-medium"><?php echo htmlspecialchars($request['timeline']); ?></dd>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($request['site_name'])): ?>
                            <div>
                                <dt class="text-gray-500">Related Site</dt>
                                <dd class="text-gray-800 font-medium"><?php echo htmlspecialchars($request['site_name']); ?></dd>
                            </div>
                        <?php endif; ?>
                        
                        <div>
                            <dt class="text-gray-500">Last Updated</dt>
                            <dd class="text-gray-800"><?php echo date('M j, Y g:i A', strtotime($request['updated_at'])); ?></dd>
                        </div>
                    </dl>
                </div>
                
                <!-- Admin Notes -->
                <?php if (!empty($request['admin_notes'])): ?>
                    <div class="bg-blue-50 rounded-xl p-6 border border-blue-100">
                        <h3 class="font-bold text-blue-800 mb-2">
                            <i class="fas fa-comment-alt mr-2"></i>Admin Notes
                        </h3>
                        <p class="text-blue-700 text-sm"><?php echo nl2br(htmlspecialchars($request['admin_notes'])); ?></p>
                    </div>
                <?php endif; ?>
                
                <!-- Help Box -->
                <div class="bg-gray-50 rounded-xl p-6 border">
                    <h3 class="font-bold text-gray-800 mb-2">Need Help?</h3>
                    <p class="text-gray-600 text-sm mb-3">Have questions about your request? Contact our team.</p>
                    <a href="mailto:support@fildevstudio.com" class="text-primary text-sm font-medium hover:underline">
                        <i class="fas fa-envelope mr-1"></i>support@fildevstudio.com
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Revision Modal -->
<div id="revisionModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="document.getElementById('revisionModal').classList.add('hidden')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-6 relative">
            <button type="button" onclick="document.getElementById('revisionModal').classList.add('hidden')"
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
            
            <h2 class="text-xl font-bold text-gray-800 mb-4">
                <i class="fas fa-redo text-orange-500 mr-2"></i>Request Revision
            </h2>
            
            <form method="POST" action="">
                <input type="hidden" name="action" value="submit_revision">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">What changes would you like?</label>
                    <textarea name="feedback" rows="5" required
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                              placeholder="Please describe the changes you'd like us to make. Be as specific as possible:
- What sections need changes?
- What colors or fonts should be different?
- Any layout adjustments needed?"></textarea>
                </div>
                
                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('revisionModal').classList.add('hidden')"
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-orange-500 text-white rounded-lg font-semibold hover:bg-orange-600 transition">
                        <i class="fas fa-paper-plane mr-2"></i>Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
