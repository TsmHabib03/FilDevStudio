<?php
/**
 * Customization Request Page
 */
$pageTitle = "Request Customization - FilDevStudio";
require_once '../includes/header.php';
require_once '../includes/functions.php';
requireLogin();

$userId = $_SESSION['user_id'];
$siteId = isset($_GET['site_id']) ? (int)$_GET['site_id'] : 0;
$error = '';
$success = '';

// Get user's sites for dropdown
try {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT id, site_name FROM client_sites WHERE user_id = ?");
    $stmt->execute([$userId]);
    $sites = $stmt->fetchAll();
} catch (Exception $e) {
    $sites = [];
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestType = sanitize($_POST['request_type'] ?? '');
    $requestDetails = sanitize($_POST['request_details'] ?? '');
    $selectedSiteId = (int)($_POST['site_id'] ?? 0);
    
    if (empty($requestType) || empty($requestDetails)) {
        $error = 'Please fill in all required fields.';
    } else {
        try {
            // Handle file upload if present
            $attachmentPath = null;
            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = handleFileUpload(
                    $_FILES['attachment'],
                    '../uploads/requests',
                    ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'],
                    10485760 // 10MB
                );
                if ($uploadResult['success']) {
                    $attachmentPath = $uploadResult['path'];
                }
            }
            
            $stmt = $pdo->prepare("INSERT INTO custom_requests (user_id, site_id, request_type, request_details, attachment_path, status) 
                                   VALUES (?, ?, ?, ?, ?, 'pending')");
            $stmt->execute([$userId, $selectedSiteId ?: null, $requestType, $requestDetails, $attachmentPath]);
            
            logActivity($pdo, $userId, 'custom_request', "Submitted $requestType customization request");
            
            $success = 'Your customization request has been submitted! Our team will review it shortly.';
            
        } catch (Exception $e) {
            $error = 'Failed to submit request. Please try again.';
        }
    }
}
?>

<!-- Page Header -->
<section class="gradient-bg py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="dashboard.php" class="text-blue-200 hover:text-white mb-2 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
        </a>
        <h1 class="text-2xl md:text-3xl font-bold text-white">Request Customization</h1>
        <p class="text-blue-100">Submit a request to our Creative Team for custom design changes</p>
    </div>
</section>

<section class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if ($error): echo displayAlert('error', $error); endif; ?>
        <?php if ($success): echo displayAlert('success', $success); endif; ?>
        
        <div class="bg-white rounded-xl shadow-lg p-8">
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="space-y-6">
                    <!-- Site Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Related Website (Optional)</label>
                        <select name="site_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">-- Select a website --</option>
                            <?php foreach ($sites as $site): ?>
                                <option value="<?php echo $site['id']; ?>" <?php echo $siteId == $site['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($site['site_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Request Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Request Type *</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition">
                                <input type="radio" name="request_type" value="color" class="mr-3" required>
                                <span><i class="fas fa-palette text-purple-500 mr-2"></i>Color Change</span>
                            </label>
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition">
                                <input type="radio" name="request_type" value="layout" class="mr-3">
                                <span><i class="fas fa-columns text-blue-500 mr-2"></i>Layout Update</span>
                            </label>
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition">
                                <input type="radio" name="request_type" value="logo" class="mr-3">
                                <span><i class="fas fa-image text-green-500 mr-2"></i>Logo Upload</span>
                            </label>
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition">
                                <input type="radio" name="request_type" value="content" class="mr-3">
                                <span><i class="fas fa-edit text-orange-500 mr-2"></i>Content Revision</span>
                            </label>
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition col-span-2 md:col-span-1">
                                <input type="radio" name="request_type" value="other" class="mr-3">
                                <span><i class="fas fa-ellipsis-h text-gray-500 mr-2"></i>Other</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Request Details -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Request Details *</label>
                        <textarea name="request_details" rows="6" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                  placeholder="Please describe in detail what changes you'd like to make. Be as specific as possible to help our team understand your requirements."></textarea>
                        <p class="text-sm text-gray-500 mt-2">Tip: Include specific colors (hex codes), section names, or reference links if applicable.</p>
                    </div>
                    
                    <!-- File Attachment -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Attachment (Optional)</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary transition">
                            <input type="file" name="attachment" id="attachment" class="hidden" accept="image/*,.pdf">
                            <label for="attachment" class="cursor-pointer">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                                <p class="text-gray-600">Click to upload or drag and drop</p>
                                <p class="text-sm text-gray-500">PNG, JPG, GIF or PDF (max 10MB)</p>
                            </label>
                        </div>
                        <div id="file-name" class="mt-2 text-sm text-gray-600"></div>
                    </div>
                    
                    <!-- Submit -->
                    <div class="flex gap-4">
                        <button type="submit" class="flex-1 gradient-bg text-white py-3 rounded-lg font-semibold hover:opacity-90 transition">
                            <i class="fas fa-paper-plane mr-2"></i>Submit Request
                        </button>
                        <a href="dashboard.php" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition text-center">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Info Box -->
        <div class="mt-8 bg-blue-50 rounded-xl p-6">
            <h3 class="font-semibold text-blue-800 mb-3">
                <i class="fas fa-info-circle mr-2"></i>What Happens Next?
            </h3>
            <ol class="list-decimal list-inside text-blue-700 space-y-2 text-sm">
                <li>Our team will review your request within 24-48 hours</li>
                <li>You'll receive a notification when work begins</li>
                <li>We'll update you on progress and completion</li>
                <li>Check your dashboard for status updates</li>
            </ol>
        </div>
    </div>
</section>

<script>
// Show selected filename
document.getElementById('attachment').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name;
    document.getElementById('file-name').textContent = fileName ? 'Selected: ' + fileName : '';
});
</script>

<?php require_once '../includes/footer.php'; ?>
