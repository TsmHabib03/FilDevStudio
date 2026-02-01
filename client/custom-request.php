<?php
/**
 * Enhanced Custom Website Request Page
 * For clients who want a fully custom website design
 */
require_once '../includes/functions.php';
require_once '../config/database.php';
require_once '../includes/mail.php'; // Use proper email system

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check login BEFORE any output
if (!isLoggedIn()) {
    $_SESSION['error'] = 'Please log in to access this page.';
    header('Location: ../auth/login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? '';
$userEmail = $_SESSION['user_email'] ?? '';
$siteId = isset($_GET['site_id']) ? (int)$_GET['site_id'] : 0;
$error = '';
$success = '';
$referenceNumber = '';

// Get database connection
try {
    $pdo = getConnection();
} catch (Exception $e) {
    $error = 'Database connection failed.';
}

// Get user's existing sites for dropdown
$sites = [];
if ($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT id, site_name FROM client_sites WHERE user_id = ?");
        $stmt->execute([$userId]);
        $sites = $stmt->fetchAll();
    } catch (Exception $e) {
        $sites = [];
    }
}

/**
 * Generate unique reference number
 */
function generateReferenceNumber($pdo) {
    $prefix = 'FDS';
    $date = date('ymd');
    
    // Get count of requests today
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM custom_requests WHERE DATE(created_at) = CURDATE()");
    $stmt->execute();
    $count = $stmt->fetchColumn() + 1;
    
    return $prefix . '-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
}

/**
 * Send admin notification email
 */
function sendAdminNotification($pdo, $requestId, $referenceNumber, $userName, $userEmail, $projectTitle, $businessType) {
    // Get admin email from settings or use default
    $adminEmail = 'admin@fildevstudio.com';
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM email_settings WHERE setting_key = 'admin_email'");
        $stmt->execute();
        $result = $stmt->fetch();
        if ($result) {
            $adminEmail = $result['setting_value'];
        }
    } catch (Exception $e) {
        // Use default from mail.php config
        $config = getEmailConfig();
        $adminEmail = $config['admin_email'];
    }
    
    $subject = "New Custom Request: $referenceNumber - $projectTitle";
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #3B82F6, #1E40AF); color: white; padding: 20px; border-radius: 8px 8px 0 0; }
            .content { background: #f9fafb; padding: 20px; border: 1px solid #e5e7eb; }
            .footer { background: #1f2937; color: #9ca3af; padding: 15px; text-align: center; border-radius: 0 0 8px 8px; font-size: 12px; }
            .btn { display: inline-block; background: #3B82F6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin-top: 15px; }
            .label { font-weight: bold; color: #6b7280; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2 style='margin:0;'>🎨 New Custom Website Request</h2>
                <p style='margin:5px 0 0 0; opacity:0.9;'>Reference: $referenceNumber</p>
            </div>
            <div class='content'>
                <p>A new custom website request has been submitted.</p>
                
                <p><span class='label'>Client:</span> $userName</p>
                <p><span class='label'>Email:</span> $userEmail</p>
                <p><span class='label'>Project:</span> $projectTitle</p>
                <p><span class='label'>Business Type:</span> " . ucfirst($businessType) . "</p>
                
                <a href='http://localhost/fildevstudio/admin/requests.php?id=$requestId' class='btn'>View Request Details</a>
            </div>
            <div class='footer'>
                FilDevStudio Web Services Platform<br>
                This is an automated notification.
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Use the proper email system from mail.php
    $result = sendEmail($adminEmail, $subject, $message);
    
    return $result['success'];
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $projectTitle = sanitize($_POST['project_title'] ?? '');
    $businessType = sanitize($_POST['business_type'] ?? 'other');
    $requestDetails = sanitize($_POST['request_details'] ?? '');
    $selectedSiteId = (int)($_POST['site_id'] ?? 0);
    $budgetRange = sanitize($_POST['budget_range'] ?? '');
    $timeline = sanitize($_POST['timeline'] ?? '');
    $preferredColors = sanitize($_POST['preferred_colors'] ?? '');
    
    if (empty($projectTitle) || empty($requestDetails)) {
        $error = 'Please fill in all required fields.';
    } else {
        try {
            $pdo->beginTransaction();
            
            // Generate reference number
            $referenceNumber = generateReferenceNumber($pdo);
            
            // Insert main request
            $stmt = $pdo->prepare("INSERT INTO custom_requests 
                (reference_number, user_id, site_id, request_type, request_details, status, created_at) 
                VALUES (?, ?, ?, 'other', ?, 'pending', NOW())");
            $stmt->execute([
                $referenceNumber, 
                $userId, 
                $selectedSiteId ?: null, 
                $requestDetails
            ]);
            
            $requestId = $pdo->lastInsertId();
            
            // Create upload directory
            $uploadDir = dirname(__DIR__) . '/uploads/requests/' . $requestId;
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
                mkdir($uploadDir . '/logo', 0755, true);
                mkdir($uploadDir . '/products', 0755, true);
                mkdir($uploadDir . '/references', 0755, true);
                mkdir($uploadDir . '/assets', 0755, true);
            }
            
            // Handle file uploads
            $fileTypes = ['logo', 'products', 'references', 'assets'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
            $maxSize = 10485760; // 10MB
            
            foreach ($fileTypes as $fileType) {
                $inputName = $fileType . '_files';
                if (isset($_FILES[$inputName]) && is_array($_FILES[$inputName]['name'])) {
                    $fileCount = count($_FILES[$inputName]['name']);
                    
                    for ($i = 0; $i < $fileCount; $i++) {
                        if ($_FILES[$inputName]['error'][$i] === UPLOAD_ERR_OK) {
                            $tmpName = $_FILES[$inputName]['tmp_name'][$i];
                            $originalName = $_FILES[$inputName]['name'][$i];
                            $fileSize = $_FILES[$inputName]['size'][$i];
                            
                            // Check file size
                            if ($fileSize > $maxSize) continue;
                            
                            // Check MIME type
                            $finfo = finfo_open(FILEINFO_MIME_TYPE);
                            $mimeType = finfo_file($finfo, $tmpName);
                            finfo_close($finfo);
                            
                            if (!in_array($mimeType, $allowedTypes)) continue;
                            
                            // Generate unique filename
                            $ext = pathinfo($originalName, PATHINFO_EXTENSION);
                            $newName = uniqid() . '_' . time() . '.' . $ext;
                            $destPath = $uploadDir . '/' . ($fileType === 'logo' ? 'logo' : $fileType) . '/' . $newName;
                            $relativePath = 'uploads/requests/' . $requestId . '/' . ($fileType === 'logo' ? 'logo' : $fileType) . '/' . $newName;
                            
                            if (move_uploaded_file($tmpName, $destPath)) {
                                // Try to insert file record if table exists
                                try {
                                    $fileTypeDb = ($fileType === 'products') ? 'product' : 
                                                  (($fileType === 'references') ? 'reference' : 
                                                  (($fileType === 'assets') ? 'asset' : 'logo'));
                                    
                                    $stmt = $pdo->prepare("INSERT INTO request_files 
                                        (request_id, file_type, file_path, original_name, file_size, mime_type) 
                                        VALUES (?, ?, ?, ?, ?, ?)");
                                    $stmt->execute([$requestId, $fileTypeDb, $relativePath, $originalName, $fileSize, $mimeType]);
                                } catch (Exception $e) {
                                    // Table might not exist yet, continue anyway
                                }
                            }
                        }
                    }
                }
            }
            
            $pdo->commit();
            
            // Log activity
            logActivity($pdo, $userId, 'custom_request', "Submitted custom request: $referenceNumber");
            
            // Send admin notification
            sendAdminNotification($pdo, $requestId, $referenceNumber, $userName, $userEmail, $projectTitle, $businessType);
            
            $success = "Your custom website request has been submitted successfully! Reference: <strong>$referenceNumber</strong>";
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Failed to submit request. Please try again.';
        }
    }
}

// NOW include header (after all processing)
$pageTitle = "Request Custom Website - FilDevStudio";
require_once '../includes/header.php';
?>

<!-- Page Header -->
<section class="gradient-bg py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <div>
                <a href="dashboard.php" class="text-blue-200 hover:text-white mb-2 inline-block">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
                <h1 class="text-3xl md:text-4xl font-bold text-white">Request Custom Website</h1>
                <p class="text-blue-100 mt-2">Don't want a template? Let our team build a custom website for your business.</p>
            </div>
            <div class="hidden md:block">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                    <i class="fas fa-palette text-5xl text-white/80"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-10 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if ($error): echo displayAlert('error', $error); endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-50 border-2 border-green-200 rounded-xl p-8 text-center mb-8">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-3xl text-green-500"></i>
                </div>
                <h2 class="text-2xl font-bold text-green-800 mb-2">Request Submitted!</h2>
                <p class="text-green-700 mb-4"><?php echo $success; ?></p>
                <div class="flex justify-center gap-4">
                    <a href="my-requests.php" class="px-6 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition">
                        <i class="fas fa-list mr-2"></i>View My Requests
                    </a>
                    <a href="dashboard.php" class="px-6 py-3 border-2 border-green-600 text-green-600 rounded-lg font-semibold hover:bg-green-50 transition">
                        <i class="fas fa-home mr-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        <?php else: ?>
        
        <form method="POST" action="" enctype="multipart/form-data" id="customRequestForm">
            <!-- Step Indicator -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center font-bold" id="step1Indicator">1</div>
                        <span class="ml-3 font-medium text-gray-800">Project Details</span>
                    </div>
                    <div class="hidden sm:block flex-1 h-1 bg-gray-200 mx-4" id="progressLine">
                        <div class="h-full bg-primary transition-all duration-300" style="width: 0%"></div>
                    </div>
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gray-200 text-gray-500 rounded-full flex items-center justify-center font-bold" id="step2Indicator">2</div>
                        <span class="ml-3 font-medium text-gray-400" id="step2Label">Upload Files</span>
                    </div>
                </div>
            </div>
            
            <!-- Step 1: Project Details -->
            <div id="step1Content" class="space-y-6">
                <!-- Project Title -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-project-diagram text-primary mr-2"></i>Project Information
                    </h2>
                    
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Project Title <span class="text-red-500">*</span></label>
                            <input type="text" name="project_title" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                   placeholder="e.g., Aling Maria's Sari-Sari Store Website">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Business Type <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                <label class="business-type-option flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-orange-300 hover:bg-orange-50 transition">
                                    <input type="radio" name="business_type" value="sarisari" class="hidden" required>
                                    <span class="text-2xl mr-3">🏪</span>
                                    <span class="font-medium">Sari-Sari Store</span>
                                </label>
                                <label class="business-type-option flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-red-300 hover:bg-red-50 transition">
                                    <input type="radio" name="business_type" value="food" class="hidden">
                                    <span class="text-2xl mr-3">🍲</span>
                                    <span class="font-medium">Carinderia/Food</span>
                                </label>
                                <label class="business-type-option flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-teal-300 hover:bg-teal-50 transition">
                                    <input type="radio" name="business_type" value="services" class="hidden">
                                    <span class="text-2xl mr-3">🔧</span>
                                    <span class="font-medium">Services</span>
                                </label>
                                <label class="business-type-option flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-blue-300 hover:bg-blue-50 transition">
                                    <input type="radio" name="business_type" value="retail" class="hidden">
                                    <span class="text-2xl mr-3">🛍️</span>
                                    <span class="font-medium">Retail Shop</span>
                                </label>
                                <label class="business-type-option flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-purple-300 hover:bg-purple-50 transition">
                                    <input type="radio" name="business_type" value="freelance" class="hidden">
                                    <span class="text-2xl mr-3">💼</span>
                                    <span class="font-medium">Freelancer</span>
                                </label>
                                <label class="business-type-option flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-gray-400 hover:bg-gray-100 transition">
                                    <input type="radio" name="business_type" value="other" class="hidden">
                                    <span class="text-2xl mr-3">✨</span>
                                    <span class="font-medium">Other</span>
                                </label>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Existing Website (Optional)</label>
                            <select name="site_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="">-- No existing site / New project --</option>
                                <?php foreach ($sites as $site): ?>
                                    <option value="<?php echo $site['id']; ?>" <?php echo $siteId == $site['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($site['site_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Select if you want to redesign an existing site</p>
                        </div>
                    </div>
                </div>
                
                <!-- Requirements -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-clipboard-list text-primary mr-2"></i>Project Requirements
                    </h2>
                    
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Describe Your Project <span class="text-red-500">*</span></label>
                            <textarea name="request_details" rows="6" required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                      placeholder="Tell us about your business and what you want for your website:
- What does your business do?
- Who are your target customers?
- What pages do you need? (Home, About, Products, Contact, etc.)
- Any specific features? (GCash QR, Menu, Gallery, etc.)
- Reference websites you like?"></textarea>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Budget Range</label>
                                <select name="budget_range" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                    <option value="">-- Select budget range --</option>
                                    <option value="under-5k">Under ₱5,000</option>
                                    <option value="5k-10k">₱5,000 - ₱10,000</option>
                                    <option value="10k-20k">₱10,000 - ₱20,000</option>
                                    <option value="20k-50k">₱20,000 - ₱50,000</option>
                                    <option value="50k+">₱50,000+</option>
                                    <option value="discuss">Let's Discuss</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Timeline</label>
                                <select name="timeline" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                    <option value="">-- Select timeline --</option>
                                    <option value="asap">ASAP (Rush)</option>
                                    <option value="1-week">Within 1 Week</option>
                                    <option value="2-weeks">Within 2 Weeks</option>
                                    <option value="1-month">Within 1 Month</option>
                                    <option value="flexible">Flexible</option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Colors</label>
                            <div class="flex flex-wrap gap-2 mb-3">
                                <button type="button" onclick="selectColorPreset('Red & Orange - Warm, energetic')" class="px-3 py-1.5 bg-gradient-to-r from-red-500 to-orange-500 text-white text-xs rounded-full hover:opacity-80 transition">🔴 Red & Orange</button>
                                <button type="button" onclick="selectColorPreset('Blue & Teal - Professional, trustworthy')" class="px-3 py-1.5 bg-gradient-to-r from-blue-500 to-teal-500 text-white text-xs rounded-full hover:opacity-80 transition">🔵 Blue & Teal</button>
                                <button type="button" onclick="selectColorPreset('Green & Yellow - Fresh, natural')" class="px-3 py-1.5 bg-gradient-to-r from-green-500 to-yellow-500 text-white text-xs rounded-full hover:opacity-80 transition">🟢 Green & Yellow</button>
                                <button type="button" onclick="selectColorPreset('Purple & Pink - Creative, modern')" class="px-3 py-1.5 bg-gradient-to-r from-purple-500 to-pink-500 text-white text-xs rounded-full hover:opacity-80 transition">🟣 Purple & Pink</button>
                                <button type="button" onclick="selectColorPreset('Black & Gold - Elegant, premium')" class="px-3 py-1.5 bg-gradient-to-r from-gray-800 to-yellow-600 text-white text-xs rounded-full hover:opacity-80 transition">⚫ Black & Gold</button>
                            </div>
                            <input type="text" name="preferred_colors" id="preferredColors"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                   placeholder="e.g., Blue and white, similar to my logo colors">
                        </div>
                    </div>
                </div>
                
                <!-- Next Button -->
                <div class="flex justify-end">
                    <button type="button" onclick="goToStep(2)" class="px-8 py-3 gradient-bg text-white rounded-lg font-semibold hover:opacity-90 transition">
                        Next: Upload Files <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>
            
            <!-- Step 2: File Uploads -->
            <div id="step2Content" class="space-y-6 hidden">
                <!-- Logo Upload -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-image text-primary mr-2"></i>Logo
                    </h2>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-primary transition" id="logoDropzone">
                        <input type="file" name="logo_files[]" id="logoFiles" class="hidden" accept="image/*">
                        <label for="logoFiles" class="cursor-pointer">
                            <i class="fas fa-cloud-upload-alt text-5xl text-gray-300 mb-4 block"></i>
                            <p class="text-gray-600 font-medium">Upload your business logo</p>
                            <p class="text-sm text-gray-500 mt-1">PNG, JPG, or SVG (transparent background preferred)</p>
                        </label>
                        <div id="logoPreview" class="mt-4 flex flex-wrap gap-3 justify-center"></div>
                    </div>
                </div>
                
                <!-- Product Images -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-box-open text-primary mr-2"></i>Product / Service Images
                        <span class="text-sm font-normal text-gray-500">(up to 10)</span>
                    </h2>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-primary transition" id="productsDropzone">
                        <input type="file" name="products_files[]" id="productsFiles" class="hidden" accept="image/*" multiple>
                        <label for="productsFiles" class="cursor-pointer">
                            <i class="fas fa-images text-5xl text-gray-300 mb-4 block"></i>
                            <p class="text-gray-600 font-medium">Upload product or service photos</p>
                            <p class="text-sm text-gray-500 mt-1">High-quality images of your products, menu items, or work samples</p>
                        </label>
                        <div id="productsPreview" class="mt-4 grid grid-cols-3 sm:grid-cols-5 gap-3"></div>
                    </div>
                </div>
                
                <!-- Reference Images -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-lightbulb text-primary mr-2"></i>Reference / Inspiration Images
                        <span class="text-sm font-normal text-gray-500">(optional)</span>
                    </h2>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-primary transition" id="referencesDropzone">
                        <input type="file" name="references_files[]" id="referencesFiles" class="hidden" accept="image/*,.pdf" multiple>
                        <label for="referencesFiles" class="cursor-pointer">
                            <i class="fas fa-palette text-5xl text-gray-300 mb-4 block"></i>
                            <p class="text-gray-600 font-medium">Upload design references</p>
                            <p class="text-sm text-gray-500 mt-1">Screenshots of websites you like, design inspirations, or style guides</p>
                        </label>
                        <div id="referencesPreview" class="mt-4 grid grid-cols-3 sm:grid-cols-5 gap-3"></div>
                    </div>
                </div>
                
                <!-- Other Assets -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-folder-open text-primary mr-2"></i>Other Assets
                        <span class="text-sm font-normal text-gray-500">(optional)</span>
                    </h2>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-primary transition" id="assetsDropzone">
                        <input type="file" name="assets_files[]" id="assetsFiles" class="hidden" accept="image/*,.pdf,.doc,.docx" multiple>
                        <label for="assetsFiles" class="cursor-pointer">
                            <i class="fas fa-file-alt text-5xl text-gray-300 mb-4 block"></i>
                            <p class="text-gray-600 font-medium">Upload other files</p>
                            <p class="text-sm text-gray-500 mt-1">Documents, certificates, or any other relevant files</p>
                        </label>
                        <div id="assetsPreview" class="mt-4 grid grid-cols-3 sm:grid-cols-5 gap-3"></div>
                    </div>
                </div>
                
                <!-- Navigation -->
                <div class="flex justify-between">
                    <button type="button" onclick="goToStep(1)" class="px-6 py-3 border border-gray-300 text-gray-600 rounded-lg font-medium hover:bg-gray-50 transition">
                        <i class="fas fa-arrow-left mr-2"></i>Back
                    </button>
                    <button type="submit" class="px-8 py-3 gradient-bg text-white rounded-lg font-semibold hover:opacity-90 transition">
                        <i class="fas fa-paper-plane mr-2"></i>Submit Request
                    </button>
                </div>
            </div>
        </form>
        
        <?php endif; ?>
        
        <!-- Process Info -->
        <div class="mt-8 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
            <h3 class="font-bold text-blue-800 mb-4 text-lg">
                <i class="fas fa-info-circle mr-2"></i>How It Works
            </h3>
            <div class="grid md:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-blue-600 font-bold">1</span>
                    </div>
                    <h4 class="font-semibold text-gray-800 text-sm">Submit Request</h4>
                    <p class="text-xs text-gray-600 mt-1">Fill out the form and upload your files</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-blue-600 font-bold">2</span>
                    </div>
                    <h4 class="font-semibold text-gray-800 text-sm">Review & Quote</h4>
                    <p class="text-xs text-gray-600 mt-1">We'll review and send you a quote (24-48 hrs)</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-blue-600 font-bold">3</span>
                    </div>
                    <h4 class="font-semibold text-gray-800 text-sm">Design & Preview</h4>
                    <p class="text-xs text-gray-600 mt-1">We build your site, you preview and request revisions</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-green-600 font-bold">4</span>
                    </div>
                    <h4 class="font-semibold text-gray-800 text-sm">Launch!</h4>
                    <p class="text-xs text-gray-600 mt-1">Approve the final design and go live</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
let currentStep = 1;

function goToStep(step) {
    currentStep = step;
    
    // Validate step 1 before proceeding
    if (step === 2) {
        const projectTitle = document.querySelector('input[name="project_title"]').value.trim();
        const businessType = document.querySelector('input[name="business_type"]:checked');
        const requestDetails = document.querySelector('textarea[name="request_details"]').value.trim();
        
        if (!projectTitle || !businessType || !requestDetails) {
            showAlert('Please fill in all required fields in Step 1.', 'error');
            return;
        }
    }
    
    // Update UI
    document.getElementById('step1Content').classList.toggle('hidden', step !== 1);
    document.getElementById('step2Content').classList.toggle('hidden', step !== 2);
    
    // Update indicators
    const step1Indicator = document.getElementById('step1Indicator');
    const step2Indicator = document.getElementById('step2Indicator');
    const step2Label = document.getElementById('step2Label');
    const progressLine = document.querySelector('#progressLine > div');
    
    if (step === 1) {
        step1Indicator.className = 'w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center font-bold';
        step1Indicator.innerHTML = '1';
        step2Indicator.className = 'w-10 h-10 bg-gray-200 text-gray-500 rounded-full flex items-center justify-center font-bold';
        step2Label.className = 'ml-3 font-medium text-gray-400';
        progressLine.style.width = '0%';
    } else {
        step1Indicator.className = 'w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center font-bold';
        step1Indicator.innerHTML = '<i class="fas fa-check"></i>';
        step2Indicator.className = 'w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center font-bold';
        step2Label.className = 'ml-3 font-medium text-gray-800';
        progressLine.style.width = '100%';
    }
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    const bgColor = type === 'error' ? 'bg-red-100 border-red-500 text-red-700' : 'bg-blue-100 border-blue-500 text-blue-700';
    alertDiv.className = `fixed top-4 right-4 z-50 border-l-4 p-4 ${bgColor} rounded shadow-lg max-w-md`;
    alertDiv.innerHTML = `<p class="flex items-center"><i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'info-circle'} mr-2"></i>${message}</p>`;
    document.body.appendChild(alertDiv);
    setTimeout(() => alertDiv.remove(), 4000);
}

function selectColorPreset(value) {
    document.getElementById('preferredColors').value = value;
}

// Business type selection styling
document.querySelectorAll('.business-type-option input').forEach(input => {
    input.addEventListener('change', function() {
        document.querySelectorAll('.business-type-option').forEach(opt => {
            opt.classList.remove('border-primary', 'bg-primary/5', 'ring-2', 'ring-primary/20');
        });
        if (this.checked) {
            this.closest('.business-type-option').classList.add('border-primary', 'bg-primary/5', 'ring-2', 'ring-primary/20');
        }
    });
});

// File upload preview
function setupFileInput(inputId, previewId, maxFiles = 10) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    
    input.addEventListener('change', function() {
        preview.innerHTML = '';
        const files = Array.from(this.files).slice(0, maxFiles);
        
        files.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative group';
                
                if (file.type.startsWith('image/')) {
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-20 h-20 object-cover rounded-lg border">
                        <span class="absolute bottom-1 right-1 bg-black/60 text-white text-xs px-1 rounded">${index + 1}</span>
                    `;
                } else {
                    div.innerHTML = `
                        <div class="w-20 h-20 bg-gray-100 rounded-lg border flex items-center justify-center">
                            <i class="fas fa-file text-2xl text-gray-400"></i>
                        </div>
                        <span class="absolute bottom-1 right-1 bg-black/60 text-white text-xs px-1 rounded">${index + 1}</span>
                    `;
                }
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
        
        if (files.length > 0) {
            const countDiv = document.createElement('div');
            countDiv.className = 'col-span-full text-sm text-gray-500 mt-2';
            countDiv.textContent = `${files.length} file(s) selected`;
            preview.appendChild(countDiv);
        }
    });
}

// Initialize file inputs
setupFileInput('logoFiles', 'logoPreview', 1);
setupFileInput('productsFiles', 'productsPreview', 10);
setupFileInput('referencesFiles', 'referencesPreview', 5);
setupFileInput('assetsFiles', 'assetsPreview', 5);
</script>

<?php require_once '../includes/footer.php'; ?>
