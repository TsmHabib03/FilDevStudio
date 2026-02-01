<?php
/**
 * Admin - Manage Customization Requests
 * Enhanced with preview URLs, file viewing, and revision management
 */

// Handle AJAX quick status update (before any HTML output)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action']) && $_POST['ajax_action'] === 'quick_status') {
    // Start session first, before any output
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Suppress errors from being output (they break JSON)
    error_reporting(0);
    ini_set('display_errors', 0);
    
    header('Content-Type: application/json');
    
    try {
        require_once '../config/database.php';
        require_once '../includes/functions.php';
        
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        
        $pdo = getConnection();
        $requestId = (int)($_POST['request_id'] ?? 0);
        $newStatus = sanitize($_POST['status'] ?? '');
        $notifyUser = isset($_POST['notify_user']) && $_POST['notify_user'] === '1';
        
        $validStatuses = ['pending', 'in_progress', 'preview_ready', 'completed', 'rejected'];
        if (!in_array($newStatus, $validStatuses) || $requestId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }
        
        // Get request and user info
        $stmt = $pdo->prepare("SELECT cr.*, u.name as user_name, u.email FROM custom_requests cr JOIN users u ON cr.user_id = u.id WHERE cr.id = ?");
        $stmt->execute([$requestId]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$request) {
            echo json_encode(['success' => false, 'message' => 'Request not found']);
            exit;
        }
        
        // Update status
        $updates = ['status = ?', 'assigned_to = ?', 'updated_at = NOW()'];
        $params = [$newStatus, $_SESSION['user_id']];
        
        if ($newStatus === 'completed') {
            $updates[] = 'completed_at = NOW()';
        }
        
        $params[] = $requestId;
        $stmt = $pdo->prepare("UPDATE custom_requests SET " . implode(', ', $updates) . " WHERE id = ?");
        $stmt->execute($params);
        
        // Log activity
        logActivity($pdo, $_SESSION['user_id'], 'quick_status_update', "Changed request #$requestId to: $newStatus");
        
        // Send email notification to user
        $emailSent = false;
        $emailMessage = 'Email not requested';
        
        if ($notifyUser) {
            // Include mail.php only when needed for email
            require_once '../includes/mail.php';
            
            $statusMessages = [
                'in_progress' => [
                    'subject' => 'Your Request is Now Being Worked On!',
                    'heading' => 'Great News!',
                    'message' => 'Our team has started working on your customization request. We\'ll update you once it\'s ready for preview.'
                ],
                'preview_ready' => [
                    'subject' => 'Your Preview is Ready!',
                    'heading' => 'Preview Available!',
                    'message' => 'Your customization request preview is now ready! Please log in to your dashboard to review it and provide feedback.'
                ],
                'completed' => [
                    'subject' => 'Your Request is Complete!',
                    'heading' => 'All Done!',
                    'message' => 'Your customization request has been completed. Log in to your dashboard to see the final result!'
                ],
                'rejected' => [
                    'subject' => 'Request Update',
                    'heading' => 'Request Status Update',
                    'message' => 'Unfortunately, we were unable to proceed with your request. Please log in to your dashboard for more details.'
                ],
                'pending' => [
                    'subject' => 'Request Received',
                    'heading' => 'Request Updated',
                    'message' => 'Your request status has been updated. Please log in to your dashboard for more details.'
                ]
            ];
            
            $emailData = $statusMessages[$newStatus] ?? $statusMessages['pending'];
            $refNumber = $request['reference_number'] ?? 'REQ-' . $requestId;
            
            // Build email HTML
            $emailHtml = '
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: linear-gradient(135deg, #3B82F6, #1E40AF); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                    .content { background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; }
                    .status-badge { display: inline-block; padding: 8px 16px; border-radius: 20px; font-weight: bold; margin: 15px 0; }
                    .status-in_progress { background: #DBEAFE; color: #1E40AF; }
                    .status-preview_ready { background: #EDE9FE; color: #7C3AED; }
                    .status-completed { background: #D1FAE5; color: #065F46; }
                    .status-rejected { background: #FEE2E2; color: #991B1B; }
                    .status-pending { background: #FEF3C7; color: #92400E; }
                    .btn { display: inline-block; padding: 12px 30px; background: #3B82F6; color: white; text-decoration: none; border-radius: 8px; margin-top: 20px; }
                    .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 12px; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h1 style="margin:0;">FilDevStudio</h1>
                        <p style="margin:5px 0 0 0;opacity:0.9;">Code & Creative Solutions</p>
                    </div>
                    <div class="content">
                        <h2>' . $emailData['heading'] . '</h2>
                        <p>Hi <strong>' . htmlspecialchars($request['user_name']) . '</strong>,</p>
                        <p>' . $emailData['message'] . '</p>
                        
                        <div style="background:white;padding:20px;border-radius:8px;margin:20px 0;">
                            <p style="margin:0;color:#6b7280;font-size:14px;">Reference Number</p>
                            <p style="margin:5px 0 15px 0;font-family:monospace;font-size:18px;font-weight:bold;">' . htmlspecialchars($refNumber) . '</p>
                            
                            <p style="margin:0;color:#6b7280;font-size:14px;">Current Status</p>
                            <span class="status-badge status-' . $newStatus . '">' . ucwords(str_replace('_', ' ', $newStatus)) . '</span>
                        </div>
                        
                        <a href="http://localhost/fildevstudio/client/dashboard.php" class="btn">View in Dashboard</a>
                    </div>
                    <div class="footer">
                        <p>&copy; ' . date('Y') . ' FilDevStudio. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>';
            
            // Send email using proper mail system
            $to = $request['email'];
            $subject = $emailData['subject'] . ' - ' . $refNumber;
            
            try {
                $result = sendEmail($to, $subject, $emailHtml);
                $emailSent = $result['success'];
                $emailMessage = $result['message'];
            } catch (Exception $emailEx) {
                $emailSent = false;
                $emailMessage = 'Email error: ' . $emailEx->getMessage();
            }
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'Status updated to ' . ucwords(str_replace('_', ' ', $newStatus)),
            'email_sent' => $emailSent,
            'email_message' => $emailMessage,
            'new_status' => $newStatus
        ]);
        exit;
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        exit;
    }
}

$pageTitle = "Manage Requests - FilDevStudio Admin";
require_once '../includes/header.php';
require_once '../includes/functions.php';
requireAdmin();

$error = '';
$success = '';
$pdo = getConnection();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $requestId = (int)($_POST['request_id'] ?? 0);
    
    switch ($_POST['action']) {
        case 'update_status':
            $newStatus = sanitize($_POST['status']);
            $adminNotes = sanitize($_POST['admin_notes'] ?? '');
            $previewUrl = sanitize($_POST['preview_url'] ?? '');
            
            try {
                // Build dynamic update query based on provided fields
                $updates = ['status = ?', 'admin_notes = ?', 'assigned_to = ?', 'updated_at = NOW()'];
                $params = [$newStatus, $adminNotes, $_SESSION['user_id']];
                
                // Add preview_url if provided and status is preview_ready
                if (!empty($previewUrl)) {
                    $updates[] = 'preview_url = ?';
                    $params[] = $previewUrl;
                }
                
                // Handle completed/approved timestamps
                if ($newStatus === 'completed') {
                    $updates[] = 'completed_at = NOW()';
                }
                
                $params[] = $requestId;
                $stmt = $pdo->prepare("UPDATE custom_requests SET " . implode(', ', $updates) . " WHERE id = ?");
                $stmt->execute($params);
                
                logActivity($pdo, $_SESSION['user_id'], 'update_request', "Updated request #$requestId to status: $newStatus");
                $success = 'Request updated successfully!';
            } catch (Exception $e) {
                $error = 'Failed to update request: ' . $e->getMessage();
            }
            break;
            
        case 'respond_revision':
            $revisionId = (int)($_POST['revision_id'] ?? 0);
            $response = sanitize($_POST['response'] ?? '');
            
            if (!empty($response) && $revisionId > 0) {
                try {
                    $stmt = $pdo->prepare("UPDATE request_revisions SET admin_response = ?, responded_by = ?, responded_at = NOW(), status = 'addressed' WHERE id = ?");
                    $stmt->execute([$response, $_SESSION['user_id'], $revisionId]);
                    
                    // Update main request status back to in_progress
                    $stmt = $pdo->prepare("UPDATE custom_requests SET status = 'in_progress', updated_at = NOW() WHERE id = ?");
                    $stmt->execute([$requestId]);
                    
                    $success = 'Revision response sent!';
                } catch (Exception $e) {
                    $error = 'Failed to respond to revision.';
                }
            }
            break;
    }
}

// Get filter
$statusFilter = $_GET['status'] ?? 'all';
$viewMode = $_GET['view'] ?? 'list';
$viewRequestId = (int)($_GET['id'] ?? 0);

// Get requests
try {
    $sql = "SELECT cr.*, 
            COALESCE(cr.description, cr.request_details, '') as request_details,
            u.name as user_name, u.email, cs.site_name, a.name as assigned_name,
            (SELECT COUNT(*) FROM request_files WHERE request_id = cr.id) as file_count,
            (SELECT COUNT(*) FROM request_revisions WHERE request_id = cr.id) as revision_count
            FROM custom_requests cr 
            LEFT JOIN users u ON cr.user_id = u.id 
            LEFT JOIN client_sites cs ON cr.site_id = cs.id
            LEFT JOIN users a ON cr.assigned_to = a.id";
    
    if ($statusFilter !== 'all') {
        $sql .= " WHERE cr.status = :status";
    }
    $sql .= " ORDER BY FIELD(cr.status, 'pending', 'revision_requested', 'in_progress', 'preview_ready', 'approved', 'completed', 'rejected'), cr.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    if ($statusFilter !== 'all') {
        $stmt->bindParam(':status', $statusFilter);
    }
    $stmt->execute();
    $requests = $stmt->fetchAll();
    
    // Get stats
    $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM custom_requests GROUP BY status");
    $statusCounts = [];
    while ($row = $stmt->fetch()) {
        $statusCounts[$row['status']] = $row['count'];
    }
    
} catch (Exception $e) {
    $requests = [];
    $statusCounts = [];
}

// If viewing a specific request detail
$viewRequest = null;
$viewFiles = [];
$viewRevisions = [];
if ($viewRequestId > 0) {
    try {
        $stmt = $pdo->prepare("SELECT cr.*, u.name as user_name, u.email, u.phone, cs.site_name, a.name as assigned_name
                               FROM custom_requests cr 
                               LEFT JOIN users u ON cr.user_id = u.id 
                               LEFT JOIN client_sites cs ON cr.site_id = cs.id
                               LEFT JOIN users a ON cr.assigned_to = a.id
                               WHERE cr.id = ?");
        $stmt->execute([$viewRequestId]);
        $viewRequest = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($viewRequest) {
            // Ensure request_files table exists with all columns
            $pdo->exec("CREATE TABLE IF NOT EXISTS request_files (
                id INT AUTO_INCREMENT PRIMARY KEY,
                request_id INT NOT NULL,
                file_type VARCHAR(100),
                file_path VARCHAR(500) NOT NULL,
                original_name VARCHAR(255),
                file_name VARCHAR(255),
                file_size INT,
                mime_type VARCHAR(100),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_request_files (request_id)
            ) ENGINE=InnoDB");
            
            // Add missing columns if table already exists
            try {
                $pdo->exec("ALTER TABLE request_files ADD COLUMN IF NOT EXISTS original_name VARCHAR(255)");
                $pdo->exec("ALTER TABLE request_files ADD COLUMN IF NOT EXISTS mime_type VARCHAR(100)");
            } catch (Exception $e) { /* Columns may already exist */ }
            
            // Get files - simple query without alias issues
            try {
                $stmt = $pdo->prepare("SELECT * FROM request_files WHERE request_id = ?");
                $stmt->execute([$viewRequestId]);
                $viewFiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                $viewFiles = [];
                error_log("Error fetching files: " . $e->getMessage());
            }
            
            // If no files in DB, scan disk and auto-populate
            if (empty($viewFiles)) {
                $uploadDir = __DIR__ . '/../uploads/requests/' . $viewRequestId;
                if (is_dir($uploadDir)) {
                    $diskFiles = [];
                    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($uploadDir));
                    foreach ($iterator as $file) {
                        if ($file->isFile()) {
                            $filePath = $file->getPathname();
                            $relativePath = 'uploads/requests/' . $viewRequestId . '/' . str_replace($uploadDir . DIRECTORY_SEPARATOR, '', $filePath);
                            $relativePath = str_replace('\\', '/', $relativePath);
                            
                            // Insert into database
                            try {
                                $insertStmt = $pdo->prepare("INSERT INTO request_files (request_id, file_path, original_name, file_size, mime_type, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                                $insertStmt->execute([
                                    $viewRequestId,
                                    $relativePath,
                                    $file->getFilename(),
                                    $file->getSize(),
                                    mime_content_type($filePath)
                                ]);
                            } catch (Exception $e) { /* Skip duplicates */ }
                            
                            $diskFiles[] = [
                                'id' => 0,
                                'request_id' => $viewRequestId,
                                'file_path' => $relativePath,
                                'display_name' => $file->getFilename(),
                                'original_name' => $file->getFilename(),
                                'file_name' => $file->getFilename(),
                                'file_size' => $file->getSize(),
                                'file_type' => strpos(mime_content_type($filePath), 'image') !== false ? 'image' : 'document',
                                'mime_type' => mime_content_type($filePath)
                            ];
                        }
                    }
                    $viewFiles = $diskFiles;
                }
            }
            
            // Ensure request_revisions table exists
            $pdo->exec("CREATE TABLE IF NOT EXISTS request_revisions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                request_id INT NOT NULL,
                revision_number INT NOT NULL,
                feedback TEXT NOT NULL,
                submitted_by INT,
                submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                admin_response TEXT,
                responded_at DATETIME,
                responded_by INT,
                INDEX idx_request_revisions (request_id)
            ) ENGINE=InnoDB");
            
            // Get revisions
            $stmt = $pdo->prepare("SELECT rr.*, u.name as client_name, au.name as admin_name
                                   FROM request_revisions rr
                                   LEFT JOIN users u ON rr.submitted_by = u.id
                                   LEFT JOIN users au ON rr.responded_by = au.id
                                   WHERE rr.request_id = ?
                                   ORDER BY rr.revision_number DESC");
            $stmt->execute([$viewRequestId]);
            $viewRevisions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) {
        error_log("Admin requests error: " . $e->getMessage());
        // Tables might not exist yet
    }
}

// Status badge helper with more options
function getAdminStatusBadge($status) {
    $badges = [
        'pending' => ['bg-yellow-100 text-yellow-700', 'fa-clock'],
        'in_progress' => ['bg-blue-100 text-blue-700', 'fa-cog'],
        'preview_ready' => ['bg-purple-100 text-purple-700', 'fa-eye'],
        'revision_requested' => ['bg-orange-100 text-orange-700 font-bold', 'fa-exclamation-triangle'],
        'completed' => ['bg-green-100 text-green-700', 'fa-check'],
        'approved' => ['bg-green-100 text-green-800', 'fa-check-double'],
        'rejected' => ['bg-red-100 text-red-700', 'fa-times'],
    ];
    $info = $badges[$status] ?? $badges['pending'];
    $label = ucwords(str_replace('_', ' ', $status));
    return '<span class="px-3 py-1 ' . $info[0] . ' rounded-full text-sm inline-flex items-center"><i class="fas ' . $info[1] . ' mr-1"></i>' . $label . '</span>';
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
        
        <!-- Stats Row -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-xl p-4 shadow">
                <p class="text-2xl font-bold text-gray-800"><?php echo array_sum($statusCounts); ?></p>
                <p class="text-sm text-gray-500">Total Requests</p>
            </div>
            <div class="bg-yellow-50 rounded-xl p-4 shadow border-l-4 border-yellow-400">
                <p class="text-2xl font-bold text-yellow-700"><?php echo $statusCounts['pending'] ?? 0; ?></p>
                <p class="text-sm text-yellow-600">Pending</p>
            </div>
            <div class="bg-orange-50 rounded-xl p-4 shadow border-l-4 border-orange-400">
                <p class="text-2xl font-bold text-orange-700"><?php echo $statusCounts['revision_requested'] ?? 0; ?></p>
                <p class="text-sm text-orange-600">Revisions</p>
            </div>
            <div class="bg-blue-50 rounded-xl p-4 shadow border-l-4 border-blue-400">
                <p class="text-2xl font-bold text-blue-700"><?php echo $statusCounts['in_progress'] ?? 0; ?></p>
                <p class="text-sm text-blue-600">In Progress</p>
            </div>
            <div class="bg-green-50 rounded-xl p-4 shadow border-l-4 border-green-400">
                <p class="text-2xl font-bold text-green-700"><?php echo ($statusCounts['completed'] ?? 0) + ($statusCounts['approved'] ?? 0); ?></p>
                <p class="text-sm text-green-600">Completed</p>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="bg-white rounded-xl shadow p-4 mb-6">
            <div class="flex flex-wrap gap-2">
                <a href="?status=all" class="px-4 py-2 rounded-lg text-sm <?php echo $statusFilter === 'all' ? 'gradient-bg text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'; ?>">
                    All
                </a>
                <a href="?status=pending" class="px-4 py-2 rounded-lg text-sm <?php echo $statusFilter === 'pending' ? 'gradient-bg text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'; ?>">
                    <i class="fas fa-clock mr-1"></i>Pending
                </a>
                <a href="?status=revision_requested" class="px-4 py-2 rounded-lg text-sm <?php echo $statusFilter === 'revision_requested' ? 'gradient-bg text-white' : 'bg-orange-100 text-orange-600 hover:bg-orange-200'; ?>">
                    <i class="fas fa-exclamation-triangle mr-1"></i>Revisions
                </a>
                <a href="?status=in_progress" class="px-4 py-2 rounded-lg text-sm <?php echo $statusFilter === 'in_progress' ? 'gradient-bg text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'; ?>">
                    <i class="fas fa-spinner mr-1"></i>In Progress
                </a>
                <a href="?status=preview_ready" class="px-4 py-2 rounded-lg text-sm <?php echo $statusFilter === 'preview_ready' ? 'gradient-bg text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'; ?>">
                    <i class="fas fa-eye mr-1"></i>Preview Ready
                </a>
                <a href="?status=completed" class="px-4 py-2 rounded-lg text-sm <?php echo $statusFilter === 'completed' ? 'gradient-bg text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'; ?>">
                    <i class="fas fa-check mr-1"></i>Completed
                </a>
            </div>
        </div>
        
        <?php if ($viewRequest): ?>
        <!-- Detail View -->
        <div class="mb-6">
            <a href="requests.php<?php echo $statusFilter !== 'all' ? '?status=' . $statusFilter : ''; ?>" class="text-primary hover:underline">
                <i class="fas fa-arrow-left mr-1"></i>Back to list
            </a>
        </div>
        
        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Request Info Card -->
                <div class="bg-white rounded-xl shadow p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">
                                <?php echo htmlspecialchars($viewRequest['project_title'] ?? ucfirst($viewRequest['request_type']) . ' Request'); ?>
                            </h2>
                            <p class="text-sm text-gray-500">
                                <?php if (!empty($viewRequest['reference_number'])): ?>
                                    <span class="font-mono"><?php echo htmlspecialchars($viewRequest['reference_number']); ?></span> • 
                                <?php endif; ?>
                                Request #<?php echo $viewRequest['id']; ?>
                            </p>
                        </div>
                        <?php echo getAdminStatusBadge($viewRequest['status']); ?>
                    </div>
                    
                    <!-- Client Info -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <h3 class="font-medium text-gray-700 mb-2">Client</h3>
                        <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($viewRequest['user_name']); ?></p>
                        <p class="text-sm text-gray-600"><?php echo htmlspecialchars($viewRequest['email']); ?></p>
                        <?php if (!empty($viewRequest['phone'])): ?>
                            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($viewRequest['phone']); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Request Details -->
                    <div class="mb-4">
                        <h3 class="font-medium text-gray-700 mb-2">Request Details</h3>
                        <div class="prose prose-sm max-w-none text-gray-700 bg-blue-50 p-4 rounded-lg">
                            <?php 
                            $detailText = $viewRequest['description'] ?? $viewRequest['request_details'] ?? '';
                            if ($detailText): 
                                echo nl2br(htmlspecialchars($detailText)); 
                            else: 
                            ?>
                                <em class="text-gray-400">No description provided</em>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <?php if (!empty($viewRequest['business_type'])): ?>
                            <div>
                                <p class="text-gray-500">Business Type</p>
                                <p class="font-medium"><?php echo ucfirst($viewRequest['business_type']); ?></p>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($viewRequest['budget_range'])): ?>
                            <div>
                                <p class="text-gray-500">Budget</p>
                                <p class="font-medium"><?php echo htmlspecialchars($viewRequest['budget_range']); ?></p>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($viewRequest['timeline'])): ?>
                            <div>
                                <p class="text-gray-500">Timeline</p>
                                <p class="font-medium"><?php echo htmlspecialchars($viewRequest['timeline']); ?></p>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($viewRequest['preferred_colors'])): ?>
                            <div>
                                <p class="text-gray-500">Colors</p>
                                <p class="font-medium"><?php echo htmlspecialchars($viewRequest['preferred_colors']); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Uploaded Files Section -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="font-bold text-gray-800 mb-4">
                        <i class="fas fa-paperclip text-primary mr-2"></i>Uploaded Files (<?php echo count($viewFiles); ?>)
                    </h3>
                    
                    <?php if (empty($viewFiles)): ?>
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-folder-open text-4xl mb-3 text-gray-300"></i>
                            <p>No files uploaded with this request</p>
                            <p class="text-xs mt-2 text-gray-400">Checking folder: uploads/requests/<?php echo $viewRequestId; ?>/</p>
                            
                            <?php 
                            // Auto-scan disk for files
                            $uploadDir = __DIR__ . '/../uploads/requests/' . $viewRequestId;
                            if (is_dir($uploadDir)):
                                $diskFiles = glob($uploadDir . '/*');
                                if (!empty($diskFiles)):
                            ?>
                                <div class="mt-4 p-4 bg-green-50 rounded-lg text-left">
                                    <p class="text-green-700 font-medium mb-2"><i class="fas fa-check-circle mr-1"></i>Found <?php echo count($diskFiles); ?> file(s) on disk:</p>
                                    <div class="grid grid-cols-2 gap-3">
                                        <?php foreach ($diskFiles as $diskFile): 
                                            $fname = basename($diskFile);
                                            $fpath = 'uploads/requests/' . $viewRequestId . '/' . $fname;
                                            $isImg = strpos(mime_content_type($diskFile), 'image') !== false;
                                        ?>
                                            <div class="bg-white p-2 rounded border cursor-pointer hover:shadow" onclick="openFileModal('<?php echo htmlspecialchars($fpath); ?>', '<?php echo htmlspecialchars($fname); ?>', <?php echo $isImg ? 'true' : 'false'; ?>, '<?php echo number_format(filesize($diskFile)/1024, 1); ?>')">
                                                <?php if ($isImg): ?>
                                                    <img src="../<?php echo htmlspecialchars($fpath); ?>" class="w-full h-20 object-cover rounded mb-1">
                                                <?php else: ?>
                                                    <div class="h-20 flex items-center justify-center"><i class="fas fa-file text-3xl text-gray-400"></i></div>
                                                <?php endif; ?>
                                                <p class="text-xs truncate"><?php echo htmlspecialchars($fname); ?></p>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; endif; ?>
                        </div>
                    <?php else: ?>
                        <!-- File Grid - Click to open modal -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                            <?php foreach ($viewFiles as $file): 
                                $displayName = $file['file_name'] ?? basename($file['file_path'] ?? 'unknown');
                                $mimeType = $file['file_type'] ?? '';
                                $isImage = (strpos($mimeType, 'image') !== false);
                                $filePath = $file['file_path'] ?? '';
                                $fileSize = number_format(($file['file_size'] ?? 0) / 1024, 1);
                            ?>
                                <div class="group relative bg-gray-50 rounded-lg p-2 cursor-pointer hover:bg-gray-100 hover:shadow transition"
                                     onclick="openFileModal('<?php echo htmlspecialchars($filePath); ?>', '<?php echo htmlspecialchars(addslashes($displayName)); ?>', <?php echo $isImage ? 'true' : 'false'; ?>, '<?php echo $fileSize; ?>')">
                                    <?php if ($isImage): ?>
                                        <img src="../<?php echo htmlspecialchars($filePath); ?>" 
                                             alt="<?php echo htmlspecialchars($displayName); ?>"
                                             class="w-full h-24 object-cover rounded-lg border-2 border-gray-200 group-hover:border-primary transition"
                                             onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'h-24 flex items-center justify-center bg-red-50 rounded-lg\'><i class=\'fas fa-exclamation-triangle text-red-400\'></i></div>';">
                                    <?php else: ?>
                                        <div class="h-24 flex items-center justify-center">
                                            <i class="fas fa-file text-4xl text-gray-400 group-hover:text-primary transition"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="mt-2 text-center">
                                        <p class="text-xs text-gray-600 truncate font-medium"><?php echo htmlspecialchars($displayName); ?></p>
                                        <p class="text-xs text-gray-400"><?php echo $fileSize; ?> KB</p>
                                    </div>
                                    <div class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition">
                                        <span class="bg-primary text-white text-xs px-2 py-1 rounded-full">
                                            <i class="fas fa-expand"></i>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Revision History -->
                <?php if (!empty($viewRevisions)): ?>
                    <div class="bg-white rounded-xl shadow p-6">
                        <h3 class="font-bold text-gray-800 mb-4">
                            <i class="fas fa-history text-primary mr-2"></i>Revision History
                        </h3>
                        
                        <div class="space-y-4">
                            <?php foreach ($viewRevisions as $revision): ?>
                                <div class="border-l-4 <?php echo empty($revision['admin_response']) ? 'border-orange-400 bg-orange-50' : 'border-gray-300'; ?> pl-4 py-3 rounded-r-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-semibold <?php echo empty($revision['admin_response']) ? 'text-orange-600' : 'text-gray-600'; ?>">
                                            Revision #<?php echo $revision['revision_number']; ?>
                                            <?php if (empty($revision['admin_response'])): ?>
                                                <span class="ml-2 px-2 py-0.5 bg-orange-200 text-orange-800 rounded text-xs">Needs Response</span>
                                            <?php endif; ?>
                                        </span>
                                        <span class="text-xs text-gray-500"><?php echo date('M j, Y g:i A', strtotime($revision['submitted_at'])); ?></span>
                                    </div>
                                    <p class="text-gray-700 text-sm mb-2"><?php echo nl2br(htmlspecialchars($revision['feedback'])); ?></p>
                                    
                                    <?php if (!empty($revision['admin_response'])): ?>
                                        <div class="mt-2 bg-blue-50 rounded-lg p-3">
                                            <p class="text-xs text-blue-600 font-medium mb-1">
                                                <i class="fas fa-reply mr-1"></i><?php echo htmlspecialchars($revision['admin_name'] ?? 'Admin'); ?>
                                                (<?php echo date('M j', strtotime($revision['responded_at'])); ?>)
                                            </p>
                                            <p class="text-sm text-blue-800"><?php echo nl2br(htmlspecialchars($revision['admin_response'])); ?></p>
                                        </div>
                                    <?php else: ?>
                                        <!-- Response Form -->
                                        <form method="POST" action="" class="mt-3">
                                            <input type="hidden" name="action" value="respond_revision">
                                            <input type="hidden" name="request_id" value="<?php echo $viewRequestId; ?>">
                                            <input type="hidden" name="revision_id" value="<?php echo $revision['id']; ?>">
                                            <div class="flex gap-2">
                                                <input type="text" name="response" required placeholder="Type your response..."
                                                       class="flex-1 px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-primary focus:outline-none">
                                                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg text-sm font-medium hover:bg-blue-600 transition">
                                                    <i class="fas fa-paper-plane mr-1"></i>Send
                                                </button>
                                            </div>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar - Update Form -->
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="font-bold text-gray-800 mb-4">Update Request</h3>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="request_id" value="<?php echo $viewRequest['id']; ?>">
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:outline-none">
                                <option value="pending" <?php echo $viewRequest['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="in_progress" <?php echo $viewRequest['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                <option value="preview_ready" <?php echo $viewRequest['status'] === 'preview_ready' ? 'selected' : ''; ?>>Preview Ready</option>
                                <option value="completed" <?php echo $viewRequest['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="rejected" <?php echo $viewRequest['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Preview URL
                                <span class="text-xs text-gray-400 font-normal">(for preview_ready status)</span>
                            </label>
                            <input type="url" name="preview_url" value="<?php echo htmlspecialchars($viewRequest['preview_url'] ?? ''); ?>"
                                   placeholder="https://preview.fildevstudio.com/..."
                                   class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:outline-none">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Admin Notes</label>
                            <textarea name="admin_notes" rows="4" placeholder="Add notes for the client..."
                                      class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:outline-none"><?php echo htmlspecialchars($viewRequest['admin_notes'] ?? ''); ?></textarea>
                        </div>
                        
                        <button type="submit" class="w-full gradient-bg text-white py-3 rounded-lg font-semibold hover:opacity-90 transition">
                            <i class="fas fa-save mr-2"></i>Save Changes
                        </button>
                    </form>
                </div>
                
                <!-- Timeline -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="font-bold text-gray-800 mb-4">Timeline</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-calendar w-6 text-gray-400"></i>
                            <span>Created: <?php echo date('M j, Y', strtotime($viewRequest['created_at'])); ?></span>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-sync w-6 text-gray-400"></i>
                            <span>Updated: <?php echo date('M j, Y g:i A', strtotime($viewRequest['updated_at'])); ?></span>
                        </div>
                        <?php if (!empty($viewRequest['completed_at'])): ?>
                            <div class="flex items-center text-green-600">
                                <i class="fas fa-check w-6"></i>
                                <span>Completed: <?php echo date('M j, Y', strtotime($viewRequest['completed_at'])); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($viewRequest['approved_at'])): ?>
                            <div class="flex items-center text-green-600">
                                <i class="fas fa-check-double w-6"></i>
                                <span>Approved: <?php echo date('M j, Y', strtotime($viewRequest['approved_at'])); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($viewRequest['assigned_name'])): ?>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-user w-6 text-gray-400"></i>
                                <span>Assigned: <?php echo htmlspecialchars($viewRequest['assigned_name']); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <?php else: ?>
<!-- Requests List -->
        <div class="space-y-4">
            <?php if (empty($requests)): ?>
                <div class="bg-white rounded-xl shadow p-8 text-center">
                    <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">No requests found</p>
                </div>
            <?php else: ?>
                <?php foreach ($requests as $request): 
                    // Get files for this request from disk
                    $reqFiles = [];
                    $reqUploadDir = __DIR__ . '/../uploads/requests/' . $request['id'];
                    if (is_dir($reqUploadDir)) {
                        $diskFiles = glob($reqUploadDir . '/*');
                        foreach ($diskFiles as $df) {
                            if (is_file($df)) {
                                $reqFiles[] = [
                                    'name' => basename($df),
                                    'path' => 'uploads/requests/' . $request['id'] . '/' . basename($df),
                                    'size' => round(filesize($df) / 1024, 1),
                                    'isImage' => strpos(mime_content_type($df), 'image') !== false
                                ];
                            }
                        }
                    }
                    $filesJson = htmlspecialchars(json_encode($reqFiles), ENT_QUOTES);
                    $desc = $request['request_details'] ?? $request['description'] ?? '';
                    $descJson = htmlspecialchars($desc, ENT_QUOTES);
                    $siteIdForModal = $request['site_id'] ?? 0;
                ?>
                    <div class="bg-white rounded-xl shadow overflow-hidden hover:shadow-lg transition cursor-pointer" data-request-id="<?php echo $request['id']; ?>"
                         onclick="openRequestModal(<?php echo $request['id']; ?>, '<?php echo htmlspecialchars(addslashes($request['project_title'] ?? $request['user_name']), ENT_QUOTES); ?>', '<?php echo htmlspecialchars(addslashes($request['user_name']), ENT_QUOTES); ?>', '<?php echo htmlspecialchars($request['email'], ENT_QUOTES); ?>', '<?php echo $descJson; ?>', '<?php echo $filesJson; ?>', '<?php echo $request['status']; ?>', '<?php echo htmlspecialchars($request['reference_number'] ?? '', ENT_QUOTES); ?>', '<?php echo date('M j, Y g:i A', strtotime($request['created_at'])); ?>', <?php echo $siteIdForModal; ?>)">>
                        <div class="p-6">
                            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center flex-wrap gap-2 mb-2">
                                        <?php echo getAdminStatusBadge($request['status']); ?>
                                        <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs capitalize">
                                            <?php echo $request['request_type']; ?>
                                        </span>
                                        <?php if (!empty($request['reference_number'])): ?>
                                            <span class="text-gray-400 text-xs font-mono"><?php echo htmlspecialchars($request['reference_number']); ?></span>
                                        <?php endif; ?>
                                        <?php if (count($reqFiles) > 0): ?>
                                            <span class="text-blue-500 text-xs"><i class="fas fa-paperclip mr-1"></i><?php echo count($reqFiles); ?> files</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mb-2">
                                        <p class="font-semibold text-gray-800">
                                            <?php echo htmlspecialchars($request['project_title'] ?? $request['user_name']); ?>
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            <?php echo htmlspecialchars($request['user_name']); ?> • <?php echo htmlspecialchars($request['email']); ?>
                                        </p>
                                    </div>
                                    <p class="text-gray-600 text-sm line-clamp-2"><?php 
                                        echo $desc ? htmlspecialchars(substr($desc, 0, 200)) . (strlen($desc) > 200 ? '...' : '') : '<em class="text-gray-400">No description provided</em>'; 
                                    ?></p>
                                    <div class="mt-2 text-xs text-gray-400">
                                        <span><i class="fas fa-calendar mr-1"></i><?php echo date('M j, Y', strtotime($request['created_at'])); ?></span>
                                    </div>
                                </div>
                                
                                <!-- Quick Actions -->
                                <div class="flex items-center gap-2" onclick="event.stopPropagation()">
                                    <?php if ($request['site_id']): ?>
                                    <a href="workspace.php?request=<?php echo $request['id']; ?>" 
                                       class="px-4 py-2 bg-purple-500 text-white rounded-lg text-sm font-medium hover:bg-purple-600 transition"
                                       title="Open Developer Workspace">
                                        <i class="fas fa-laptop-code mr-1"></i>Work
                                    </a>
                                    <?php endif; ?>
                                    <a href="?id=<?php echo $request['id']; ?>" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                                        <i class="fas fa-edit mr-1"></i>Manage
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<!-- REQUEST DETAILS MODAL -->
<div id="requestModal" class="fixed inset-0 bg-black/70 z-50 hidden items-center justify-center p-4" onclick="closeRequestModal(event)">
    <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden shadow-2xl" onclick="event.stopPropagation()">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-4 border-b gradient-bg">
            <div class="text-white">
                <h3 id="reqModalTitle" class="font-bold text-lg"></h3>
                <p id="reqModalRef" class="text-sm text-blue-100"></p>
            </div>
            <button onclick="closeRequestModal()" class="text-white/80 hover:text-white text-2xl">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- Modal Body -->
        <div class="p-6 overflow-auto max-h-[60vh]">
            <!-- Client Info -->
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center text-white font-bold text-lg">
                        <span id="reqModalAvatar"></span>
                    </div>
                    <div>
                        <p id="reqModalClient" class="font-semibold text-gray-800"></p>
                        <p id="reqModalEmail" class="text-sm text-gray-500"></p>
                    </div>
                </div>
                <p id="reqModalDate" class="text-xs text-gray-400 mt-2"></p>
            </div>
            
            <!-- Description -->
            <div class="mb-6">
                <h4 class="font-semibold text-gray-700 mb-2"><i class="fas fa-align-left mr-2 text-primary"></i>Request Description</h4>
                <div id="reqModalDesc" class="bg-blue-50 p-4 rounded-lg text-gray-700 whitespace-pre-wrap"></div>
            </div>
            
            <!-- Attached Files -->
            <div>
                <h4 class="font-semibold text-gray-700 mb-3"><i class="fas fa-paperclip mr-2 text-primary"></i>Attached Files</h4>
                <div id="reqModalFiles" class="grid grid-cols-2 sm:grid-cols-3 gap-3"></div>
                <div id="reqModalNoFiles" class="hidden text-center py-6 text-gray-400">
                    <i class="fas fa-folder-open text-3xl mb-2"></i>
                    <p>No files attached</p>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="p-4 border-t bg-gradient-to-r from-blue-50 to-purple-50">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-semibold text-gray-700 text-sm"><i class="fas fa-bolt mr-2 text-yellow-500"></i>Quick Status Update</h4>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="notifyUserToggle" checked class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                    <span class="text-sm text-gray-600"><i class="fas fa-envelope mr-1"></i>Notify User</span>
                </label>
            </div>
            <div id="quickStatusBtns" class="flex flex-wrap gap-2">
                <button onclick="quickStatusUpdate('pending')" data-status="pending" class="quick-status-btn px-3 py-2 rounded-lg text-sm font-medium transition-all border-2 bg-white border-yellow-300 text-yellow-700 hover:bg-yellow-100">
                    <i class="fas fa-clock mr-1"></i>Pending
                </button>
                <button onclick="quickStatusUpdate('in_progress')" data-status="in_progress" class="quick-status-btn px-3 py-2 rounded-lg text-sm font-medium transition-all border-2 bg-white border-blue-300 text-blue-700 hover:bg-blue-100">
                    <i class="fas fa-cog mr-1"></i>Working On It
                </button>
                <button onclick="quickStatusUpdate('preview_ready')" data-status="preview_ready" class="quick-status-btn px-3 py-2 rounded-lg text-sm font-medium transition-all border-2 bg-white border-purple-300 text-purple-700 hover:bg-purple-100">
                    <i class="fas fa-eye mr-1"></i>Preview Ready
                </button>
                <button onclick="quickStatusUpdate('completed')" data-status="completed" class="quick-status-btn px-3 py-2 rounded-lg text-sm font-medium transition-all border-2 bg-white border-green-300 text-green-700 hover:bg-green-100">
                    <i class="fas fa-check mr-1"></i>Completed
                </button>
                <button onclick="quickStatusUpdate('rejected')" data-status="rejected" class="quick-status-btn px-3 py-2 rounded-lg text-sm font-medium transition-all border-2 bg-white border-red-300 text-red-700 hover:bg-red-100">
                    <i class="fas fa-times mr-1"></i>Rejected
                </button>
            </div>
            <p id="quickStatusMessage" class="text-xs mt-2 hidden"></p>
        </div>
        
        <!-- Modal Footer -->
        <div class="flex items-center justify-between p-4 border-t bg-gray-50">
            <div id="reqModalStatus"></div>
            <div class="flex gap-2">
                <button onclick="closeRequestModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Close
                </button>
                <a id="reqModalWorkspaceBtn" href="" class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition hidden">
                    <i class="fas fa-laptop-code mr-2"></i>Developer Workspace
                </a>
                <a id="reqModalManageBtn" href="" class="px-4 py-2 gradient-bg text-white rounded-lg hover:opacity-90 transition">
                    <i class="fas fa-cog mr-2"></i>Manage Request
                </a>
            </div>
        </div>
    </div>
</div>

<!-- FILE PREVIEW MODAL -->
<div id="fileModal" class="fixed inset-0 bg-black/70 z-50 hidden items-center justify-center p-4" onclick="closeFileModal(event)">
    <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden shadow-2xl" onclick="event.stopPropagation()">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-4 border-b bg-gray-50">
            <div>
                <h3 id="modalFileName" class="font-bold text-gray-800 text-lg"></h3>
                <p id="modalFileSize" class="text-sm text-gray-500"></p>
            </div>
            <button onclick="closeFileModal()" class="text-gray-400 hover:text-gray-600 text-2xl">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- Modal Body -->
        <div class="p-6 overflow-auto max-h-[70vh]">
            <!-- Image Preview -->
            <div id="modalImageContainer" class="hidden">
                <img id="modalImage" src="" alt="" class="max-w-full mx-auto rounded-lg shadow-lg">
            </div>
            
            <!-- Non-Image File -->
            <div id="modalFileContainer" class="hidden text-center py-12">
                <i class="fas fa-file text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-600">This file cannot be previewed</p>
            </div>
        </div>
        
        <!-- Modal Footer -->
        <div class="flex items-center justify-between p-4 border-t bg-gray-50">
            <div class="text-sm text-gray-500">
                <i class="fas fa-info-circle mr-1"></i>
                Click outside or press ESC to close
            </div>
            <div class="flex gap-2">
                <a id="modalDownloadBtn" href="" download class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-download mr-2"></i>Download
                </a>
                <a id="modalOpenBtn" href="" target="_blank" class="px-4 py-2 gradient-bg text-white rounded-lg hover:opacity-90 transition">
                    <i class="fas fa-external-link-alt mr-2"></i>Open in New Tab
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function openFileModal(filePath, fileName, isImage, fileSize) {
    const modal = document.getElementById('fileModal');
    const imageContainer = document.getElementById('modalImageContainer');
    const fileContainer = document.getElementById('modalFileContainer');
    const modalImage = document.getElementById('modalImage');
    const modalFileName = document.getElementById('modalFileName');
    const modalFileSize = document.getElementById('modalFileSize');
    const downloadBtn = document.getElementById('modalDownloadBtn');
    const openBtn = document.getElementById('modalOpenBtn');
    
    // Set file info
    modalFileName.textContent = fileName;
    modalFileSize.textContent = fileSize + ' KB';
    
    // Set URLs (add ../ for relative path from admin folder)
    const fullPath = '../' + filePath;
    downloadBtn.href = fullPath;
    openBtn.href = fullPath;
    
    // Show appropriate preview
    if (isImage) {
        modalImage.src = fullPath;
        modalImage.alt = fileName;
        imageContainer.classList.remove('hidden');
        fileContainer.classList.add('hidden');
    } else {
        imageContainer.classList.add('hidden');
        fileContainer.classList.remove('hidden');
    }
    
    // Show modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeFileModal(event) {
    if (event && event.target !== document.getElementById('fileModal')) return;
    
    const modal = document.getElementById('fileModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}

// ============ REQUEST DETAILS MODAL ============
let currentRequestId = null;
let currentRequestStatus = null;
let currentSiteId = null;

function openRequestModal(id, title, client, email, description, filesJson, status, refNumber, date, siteId) {
    const modal = document.getElementById('requestModal');
    currentRequestId = id;
    currentRequestStatus = status;
    currentSiteId = siteId || 0;
    
    // Set basic info
    document.getElementById('reqModalTitle').textContent = title || 'Request #' + id;
    document.getElementById('reqModalRef').textContent = refNumber ? refNumber : 'Request #' + id;
    document.getElementById('reqModalClient').textContent = client;
    document.getElementById('reqModalEmail').textContent = email;
    document.getElementById('reqModalAvatar').textContent = client.charAt(0).toUpperCase();
    document.getElementById('reqModalDate').innerHTML = '<i class="fas fa-calendar mr-1"></i>' + date;
    document.getElementById('reqModalManageBtn').href = '?id=' + id;
    
    // Show/hide workspace button based on site_id
    const workspaceBtn = document.getElementById('reqModalWorkspaceBtn');
    if (siteId && siteId > 0) {
        workspaceBtn.href = 'workspace.php?request=' + id;
        workspaceBtn.classList.remove('hidden');
    } else {
        workspaceBtn.classList.add('hidden');
    }
    
    // Highlight current status button
    highlightCurrentStatus(status);
    
    // Set description
    const descEl = document.getElementById('reqModalDesc');
    if (description && description.trim()) {
        descEl.textContent = description;
        descEl.classList.remove('text-gray-400', 'italic');
    } else {
        descEl.textContent = 'No description provided';
        descEl.classList.add('text-gray-400', 'italic');
    }
    
    // Set status badge
    const statusBadges = {
        'pending': '<span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm"><i class="fas fa-clock mr-1"></i>Pending</span>',
        'in_progress': '<span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm"><i class="fas fa-cog mr-1"></i>In Progress</span>',
        'preview_ready': '<span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm"><i class="fas fa-eye mr-1"></i>Preview Ready</span>',
        'completed': '<span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm"><i class="fas fa-check mr-1"></i>Completed</span>',
        'rejected': '<span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm"><i class="fas fa-times mr-1"></i>Rejected</span>'
    };
    document.getElementById('reqModalStatus').innerHTML = statusBadges[status] || statusBadges['pending'];
    
    // Parse and display files
    const filesContainer = document.getElementById('reqModalFiles');
    const noFilesEl = document.getElementById('reqModalNoFiles');
    filesContainer.innerHTML = '';
    
    let files = [];
    try {
        files = JSON.parse(filesJson);
    } catch(e) {
        files = [];
    }
    
    if (files && files.length > 0) {
        noFilesEl.classList.add('hidden');
        filesContainer.classList.remove('hidden');
        
        files.forEach(function(file) {
            const fileEl = document.createElement('div');
            fileEl.className = 'bg-gray-50 rounded-lg p-2 cursor-pointer hover:bg-gray-100 hover:shadow transition';
            fileEl.onclick = function() { openFileModal(file.path, file.name, file.isImage, file.size); };
            
            if (file.isImage) {
                fileEl.innerHTML = `
                    <img src="../${file.path}" alt="${file.name}" class="w-full h-24 object-cover rounded mb-2" onerror="this.src='../assets/images/placeholder.png'">
                    <p class="text-xs text-gray-600 truncate font-medium">${file.name}</p>
                    <p class="text-xs text-gray-400">${file.size} KB</p>
                `;
            } else {
                fileEl.innerHTML = `
                    <div class="h-24 flex items-center justify-center bg-gray-100 rounded mb-2">
                        <i class="fas fa-file text-3xl text-gray-400"></i>
                    </div>
                    <p class="text-xs text-gray-600 truncate font-medium">${file.name}</p>
                    <p class="text-xs text-gray-400">${file.size} KB</p>
                `;
            }
            filesContainer.appendChild(fileEl);
        });
    } else {
        filesContainer.classList.add('hidden');
        noFilesEl.classList.remove('hidden');
    }
    
    // Show modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeRequestModal(event) {
    if (event && event.target !== document.getElementById('requestModal')) return;
    
    const modal = document.getElementById('requestModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}

// Close on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeFileModal();
        closeRequestModal();
    }
});

// Highlight the current status button
function highlightCurrentStatus(status) {
    const buttons = document.querySelectorAll('.quick-status-btn');
    buttons.forEach(btn => {
        const btnStatus = btn.getAttribute('data-status');
        if (btnStatus === status) {
            btn.classList.add('ring-2', 'ring-offset-2', 'scale-105');
            if (btnStatus === 'pending') btn.classList.add('ring-yellow-400', 'bg-yellow-100');
            else if (btnStatus === 'in_progress') btn.classList.add('ring-blue-400', 'bg-blue-100');
            else if (btnStatus === 'preview_ready') btn.classList.add('ring-purple-400', 'bg-purple-100');
            else if (btnStatus === 'completed') btn.classList.add('ring-green-400', 'bg-green-100');
            else if (btnStatus === 'rejected') btn.classList.add('ring-red-400', 'bg-red-100');
        } else {
            btn.classList.remove('ring-2', 'ring-offset-2', 'scale-105', 
                'ring-yellow-400', 'bg-yellow-100',
                'ring-blue-400', 'bg-blue-100',
                'ring-purple-400', 'bg-purple-100',
                'ring-green-400', 'bg-green-100',
                'ring-red-400', 'bg-red-100');
            btn.classList.add('bg-white');
        }
    });
}

// Quick status update via AJAX
function quickStatusUpdate(newStatus) {
    if (!currentRequestId) return;
    if (newStatus === currentRequestStatus) {
        showQuickMessage('Status is already ' + newStatus.replace('_', ' '), 'info');
        return;
    }
    
    const notifyUser = document.getElementById('notifyUserToggle').checked;
    const btn = document.querySelector(`.quick-status-btn[data-status="${newStatus}"]`);
    const originalText = btn.innerHTML;
    
    // Show loading state
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Updating...';
    btn.disabled = true;
    
    // Create form data
    const formData = new FormData();
    formData.append('ajax_action', 'quick_status');
    formData.append('request_id', currentRequestId);
    formData.append('status', newStatus);
    formData.append('notify_user', notifyUser ? '1' : '0');
    
    fetch('requests.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Server returned ' + response.status);
        }
        return response.text();
    })
    .then(text => {
        // Try to parse JSON, handle non-JSON responses
        try {
            return JSON.parse(text);
        } catch (e) {
            console.error('Response was not JSON:', text);
            throw new Error('Invalid response from server');
        }
    })
    .then(data => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        
        if (data.success) {
            currentRequestStatus = data.new_status;
            highlightCurrentStatus(data.new_status);
            
            // Update status badge in modal
            const statusBadges = {
                'pending': '<span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm"><i class="fas fa-clock mr-1"></i>Pending</span>',
                'in_progress': '<span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm"><i class="fas fa-cog mr-1"></i>In Progress</span>',
                'preview_ready': '<span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm"><i class="fas fa-eye mr-1"></i>Preview Ready</span>',
                'completed': '<span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm"><i class="fas fa-check mr-1"></i>Completed</span>',
                'rejected': '<span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm"><i class="fas fa-times mr-1"></i>Rejected</span>'
            };
            document.getElementById('reqModalStatus').innerHTML = statusBadges[data.new_status];
            
            let message = data.message;
            if (notifyUser && data.email_message) {
                message += ' | ' + data.email_message;
            }
            showQuickMessage(message, 'success');
            
            // Update the card in the list (if visible)
            updateCardStatus(currentRequestId, data.new_status);
        } else {
            showQuickMessage(data.message || 'Failed to update status', 'error');
        }
    })
    .catch(error => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        showQuickMessage('Error: ' + error.message, 'error');
        console.error('Error:', error);
    });
}

function showQuickMessage(message, type) {
    const msgEl = document.getElementById('quickStatusMessage');
    msgEl.textContent = message;
    msgEl.classList.remove('hidden', 'text-green-600', 'text-red-600', 'text-blue-600', 'text-yellow-600');
    
    if (type === 'success') msgEl.classList.add('text-green-600');
    else if (type === 'error') msgEl.classList.add('text-red-600');
    else if (type === 'warning') msgEl.classList.add('text-yellow-600');
    else msgEl.classList.add('text-blue-600');
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        msgEl.classList.add('hidden');
    }, 5000);
}

function updateCardStatus(requestId, newStatus) {
    // Find and update the status badge on the card in the list
    const cards = document.querySelectorAll('[data-request-id="' + requestId + '"]');
    if (cards.length === 0) {
        // Refresh the page to show updated status
        setTimeout(() => {
            location.reload();
        }, 1500);
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>
