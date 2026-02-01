<?php
/**
 * Edit Site Page - Content Management
 * Updated: Replaced DIY image uploads with "Submit for Design" service
 */
require_once '../includes/functions.php';
require_once '../config/database.php';
require_once '../includes/mail.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check login BEFORE any output
if (!isLoggedIn()) {
    $_SESSION['error'] = 'Please log in to access this page.';
    header('Location: ../auth/login.php');
    exit();
}

$siteId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$userId = $_SESSION['user_id'];
$error = '';
$success = '';

// Check for success message from session (used after redirects)
if (isset($_SESSION['success_message'])) {
    $success = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// Get site data
try {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT cs.*, t.name as template_name, t.category as template_category FROM client_sites cs 
                           LEFT JOIN templates t ON cs.template_id = t.id 
                           WHERE cs.id = ? AND cs.user_id = ?");
    $stmt->execute([$siteId, $userId]);
    $site = $stmt->fetch();
    
    if (!$site) {
        redirect('dashboard.php');
    }
    
    // Get user info for email notifications
    $stmtUser = $pdo->prepare("SELECT email, name FROM users WHERE id = ?");
    $stmtUser->execute([$userId]);
    $user = $stmtUser->fetch();
    
} catch (Exception $e) {
    redirect('dashboard.php');
}

// Template-specific hints and labels (5 SME-focused templates)
$templateId = $site['template_id'] ?? 1;
$templateHints = [
    1 => [ // Sari-Sari Store
        'heroTitle' => 'e.g., "Tindahan ng Bayan" or "Mura at Matibay"',
        'heroSubtitle' => 'Your tagline - tingi prices, barangay location, services offered',
        'about' => 'Your sari-sari story - years serving the community, family-owned, location',
        'services' => 'Products: snacks, drinks, e-load, sachet items, cigarettes, candies',
        'servicesLabel' => 'Mga Paninda',
        'icon' => 'fa-store-alt'
    ],
    2 => [ // Carinderia & Food Business
        'heroTitle' => 'e.g., "Masarap at Mura!" or "Lutong Bahay"',
        'heroSubtitle' => 'Home-cooked meals, specialty dishes, affordable food',
        'about' => 'Your carinderia story - family recipes, fresh ingredients, years of service',
        'services' => 'Menu: ulam, rice meals, snacks, beverages, merienda items',
        'servicesLabel' => 'Menu ng Araw',
        'icon' => 'fa-utensils'
    ],
    3 => [ // Local Services
        'heroTitle' => 'e.g., "Serbisyong Tapat" or "Quality Service Guaranteed"',
        'heroSubtitle' => 'Your main service offering and coverage area',
        'about' => 'Your expertise, years of experience, certifications, service guarantee',
        'services' => 'List services: repairs, laundry, salon, installations, rates',
        'servicesLabel' => 'Mga Serbisyo',
        'icon' => 'fa-tools'
    ],
    4 => [ // Small Retail Shop
        'heroTitle' => 'e.g., "Quality Products, Best Prices"',
        'heroSubtitle' => 'Highlight your specialty - gadgets, RTW, ukay-ukay, etc.',
        'about' => 'Your shop story - what you sell, why customers love you',
        'services' => 'Featured products, categories, bestsellers, new arrivals',
        'servicesLabel' => 'Products & Categories',
        'icon' => 'fa-shopping-bag'
    ],
    5 => [ // Freelancer Portfolio
        'heroTitle' => 'e.g., "Creative Designer" or your professional title',
        'heroSubtitle' => 'What you do and who you help - your value proposition',
        'about' => 'Your background, skills, tools you use, achievements',
        'services' => 'Services offered, project types, rates, past work',
        'servicesLabel' => 'Services & Portfolio',
        'icon' => 'fa-laptop-code'
    ]
];

$hints = $templateHints[$templateId] ?? $templateHints[1];

// Get existing design requests for this site
$existingRequests = [];
try {
    $stmtRequests = $pdo->prepare("SELECT * FROM custom_requests WHERE site_id = ? ORDER BY created_at DESC LIMIT 5");
    $stmtRequests->execute([$siteId]);
    $existingRequests = $stmtRequests->fetchAll();
} catch (Exception $e) {
    // Table might not exist yet
}

// Process design request submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_design_request') {
    $requestType = sanitize($_POST['request_type'] ?? 'design');
    $designNotes = sanitize($_POST['design_notes'] ?? '');
    $priority = sanitize($_POST['priority'] ?? 'normal');
    
    // Generate reference number
    $referenceNumber = generateReferenceNumber('FDS');
    
    try {
        // Insert design request
        $stmt = $pdo->prepare("INSERT INTO custom_requests (user_id, site_id, request_type, description, priority, reference_number, status, created_at) 
                               VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())");
        $stmt->execute([$userId, $siteId, $requestType, $designNotes, $priority, $referenceNumber]);
        $requestId = $pdo->lastInsertId();
        
        // Handle file uploads
        $uploadedFiles = [];
        if (!empty($_FILES['design_files']['name'][0])) {
            $uploadDir = '../uploads/requests/' . $requestId . '/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf', 'application/zip'];
            $maxSize = 10 * 1024 * 1024; // 10MB
            
            foreach ($_FILES['design_files']['name'] as $key => $filename) {
                if ($_FILES['design_files']['error'][$key] === UPLOAD_ERR_OK) {
                    $tmpName = $_FILES['design_files']['tmp_name'][$key];
                    $fileType = $_FILES['design_files']['type'][$key];
                    $fileSize = $_FILES['design_files']['size'][$key];
                    
                    if (in_array($fileType, $allowedTypes) && $fileSize <= $maxSize) {
                        $safeName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
                        $destination = $uploadDir . $safeName;
                        
                        if (move_uploaded_file($tmpName, $destination)) {
                            // Store file reference in database if table exists
                            try {
                                $stmtFile = $pdo->prepare("INSERT INTO request_files (request_id, file_name, file_path, file_type, file_size, created_at) 
                                                           VALUES (?, ?, ?, ?, ?, NOW())");
                                $stmtFile->execute([$requestId, $filename, 'uploads/requests/' . $requestId . '/' . $safeName, $fileType, $fileSize]);
                            } catch (Exception $e) {
                                // Table might not exist
                            }
                            $uploadedFiles[] = $filename;
                        }
                    }
                }
            }
        }
        
        // Send notifications
        $requestData = [
            'id' => $requestId,
            'reference_number' => $referenceNumber,
            'site_name' => $site['site_name'],
            'request_type' => $requestType,
            'description' => $designNotes,
            'priority' => $priority,
            'client_name' => $user['name'],
            'client_email' => $user['email']
        ];
        notifyAdminNewRequest($pdo, $requestData);
        
        // Log activity
        logActivity($pdo, $userId, 'design_request', "Submitted design request #$referenceNumber for site: {$site['site_name']}");
        
        $_SESSION['success_message'] = "Design request submitted successfully! Reference: <strong>$referenceNumber</strong>. Our team will contact you within 24-48 hours.";
        header("Location: edit-site.php?id=$siteId&step=2");
        exit;
        
    } catch (Exception $e) {
        $error = 'Failed to submit request. Please try again. ' . $e->getMessage();
    }
}

// Process form submission (content update)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_POST['action']) || $_POST['action'] === 'update_content')) {
    $siteName = sanitize($_POST['site_name'] ?? '');
    $heroTitle = sanitize($_POST['hero_title'] ?? '');
    $heroSubtitle = sanitize($_POST['hero_subtitle'] ?? '');
    $aboutContent = sanitize($_POST['about_content'] ?? '');
    $servicesContent = sanitize($_POST['services_content'] ?? '');
    $contactInfo = sanitize($_POST['contact_info'] ?? '');
    $primaryColor = sanitize($_POST['primary_color'] ?? '#3B82F6');
    $secondaryColor = sanitize($_POST['secondary_color'] ?? '#1E40AF');
    $accentColor = sanitize($_POST['accent_color'] ?? '#F59E0B');
    $fontHeading = sanitize($_POST['font_heading'] ?? 'Inter');
    $fontBody = sanitize($_POST['font_body'] ?? 'Inter');
    
    // Social media links
    $socialFacebook = sanitizeSocialInput($_POST['social_facebook'] ?? '');
    $socialInstagram = sanitizeSocialInput($_POST['social_instagram'] ?? '');
    $socialTiktok = sanitizeSocialInput($_POST['social_tiktok'] ?? '');
    $socialTwitter = sanitizeSocialInput($_POST['social_twitter'] ?? '');
    $socialYoutube = sanitizeSocialInput($_POST['social_youtube'] ?? '');
    $socialLinkedin = sanitizeSocialInput($_POST['social_linkedin'] ?? '');
    $socialWhatsapp = sanitizeSocialInput($_POST['social_whatsapp'] ?? '');
    $socialMessenger = sanitizeSocialInput($_POST['social_messenger'] ?? '');
    
    // Validate fonts
    if (!isValidFont($fontHeading)) $fontHeading = 'Inter';
    if (!isValidFont($fontBody)) $fontBody = 'Inter';
    
    try {
        $stmt = $pdo->prepare("UPDATE client_sites SET 
                               site_name = ?, hero_title = ?, hero_subtitle = ?, 
                               about_content = ?, services_content = ?, contact_info = ?,
                               primary_color = ?, secondary_color = ?, accent_color = ?,
                               font_heading = ?, font_body = ?,
                               social_facebook = ?, social_instagram = ?, social_tiktok = ?,
                               social_twitter = ?, social_youtube = ?, social_linkedin = ?,
                               social_whatsapp = ?, social_messenger = ?,
                               updated_at = NOW()
                               WHERE id = ? AND user_id = ?");
        $stmt->execute([
            $siteName, $heroTitle, $heroSubtitle,
            $aboutContent, $servicesContent, $contactInfo,
            $primaryColor, $secondaryColor, $accentColor,
            $fontHeading, $fontBody,
            $socialFacebook ?: null, $socialInstagram ?: null, $socialTiktok ?: null,
            $socialTwitter ?: null, $socialYoutube ?: null, $socialLinkedin ?: null,
            $socialWhatsapp ?: null, $socialMessenger ?: null,
            $siteId, $userId
        ]);
        
        logActivity($pdo, $userId, 'update_site', "Updated site: $siteName");
        $success = 'Website updated successfully!';
        
        // Refresh site data
        $stmt = $pdo->prepare("SELECT cs.*, t.name as template_name, t.category as template_category FROM client_sites cs 
                               LEFT JOIN templates t ON cs.template_id = t.id 
                               WHERE cs.id = ?");
        $stmt->execute([$siteId]);
        $site = $stmt->fetch();
        
    } catch (Exception $e) {
        $error = 'Failed to update. Please try again.';
    }
}

// NOW include header (after all redirects are done)
$pageTitle = "Edit Website - FilDevStudio";
require_once '../includes/header.php';
?>

<!-- Page Header -->
<section class="gradient-bg py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <a href="dashboard.php" class="text-blue-200 hover:text-white mb-2 inline-block">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
                <h1 class="text-2xl md:text-3xl font-bold text-white"><?php echo htmlspecialchars($site['site_name'] ?? 'Edit Website'); ?></h1>
                <p class="text-blue-100">Template: <?php echo htmlspecialchars($site['template_name']); ?> • <?php echo getStatusBadge($site['status']); ?></p>
            </div>
            <div class="flex gap-3">
                <a href="preview-site.php?id=<?php echo $siteId; ?>" target="_blank"
                   class="bg-white text-primary px-4 py-2 rounded-lg font-medium hover:bg-gray-100 transition">
                    <i class="fas fa-eye mr-2"></i>Preview Site
                </a>
            </div>
        </div>
    </div>
</section>

<section class="py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if ($error): echo displayAlert('error', $error); endif; ?>
        <?php if ($success): echo displayAlert('success', $success); endif; ?>
        
        <!-- Multi-Step Progress Bar -->
        <div class="bg-white rounded-xl shadow p-6 mb-8">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-500">Edit Your Website</h3>
                <span class="text-sm text-gray-500" id="stepIndicator">Step 1 of 2</span>
            </div>
            <div class="relative">
                <div class="overflow-hidden h-3 text-xs flex rounded-full bg-gray-200">
                    <div id="progressBar" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-gradient-to-r from-blue-500 to-blue-600 transition-all duration-500 ease-out rounded-full" style="width: 50%"></div>
                </div>
                <div class="flex justify-between mt-3">
                    <div class="flex items-center cursor-pointer" onclick="goToStep(1)">
                        <div id="step1Dot" class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center text-sm font-semibold shadow-lg">
                            <i class="fas fa-pen"></i>
                        </div>
                        <span class="ml-2 text-sm font-medium text-blue-600">Content & Settings</span>
                    </div>
                    <div class="flex items-center cursor-pointer" onclick="goToStep(2)">
                        <span class="mr-2 text-sm font-medium text-gray-400" id="step2Label">Submit for Design</span>
                        <div id="step2Dot" class="w-8 h-8 rounded-full bg-gray-300 text-gray-500 flex items-center justify-center text-sm font-semibold">
                            <i class="fas fa-palette"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Step 1: Content & Settings -->
        <div id="step1Content" class="step-content">
            <form method="POST" action="" enctype="multipart/form-data" id="contentForm">
                <input type="hidden" name="action" value="update_content">
                
                <div class="space-y-6">
                    <!-- Basic Info -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">
                            <i class="fas fa-info-circle text-primary mr-2"></i>Basic Information
                        </h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Site Name <span class="text-red-500">*</span></label>
                                <input type="text" name="site_name" value="<?php echo htmlspecialchars($site['site_name'] ?? ''); ?>" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hero Section -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">
                            <i class="fas fa-home text-primary mr-2"></i>Hero Section
                        </h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Hero Title</label>
                                <input type="text" name="hero_title" value="<?php echo htmlspecialchars($site['hero_title'] ?? ''); ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                       placeholder="<?php echo $hints['heroTitle']; ?>">
                                <p class="text-xs text-gray-500 mt-1"><?php echo $hints['heroTitle']; ?></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Hero Subtitle</label>
                                <textarea name="hero_subtitle" rows="2"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                          placeholder="<?php echo $hints['heroSubtitle']; ?>"><?php echo htmlspecialchars($site['hero_subtitle'] ?? ''); ?></textarea>
                                <p class="text-xs text-gray-500 mt-1"><?php echo $hints['heroSubtitle']; ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- About Section -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">
                            <i class="fas fa-building text-primary mr-2"></i>About Section
                        </h2>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">About Your Business</label>
                            <textarea name="about_content" rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                      placeholder="<?php echo $hints['about']; ?>"><?php echo htmlspecialchars($site['about_content'] ?? ''); ?></textarea>
                            <p class="text-xs text-gray-500 mt-1"><?php echo $hints['about']; ?></p>
                        </div>
                    </div>
                    
                    <!-- Services Section -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">
                            <i class="fas <?php echo $hints['icon']; ?> text-primary mr-2"></i><?php echo $hints['servicesLabel']; ?>
                        </h2>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo $hints['servicesLabel']; ?></label>
                            <textarea name="services_content" rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                      placeholder="<?php echo $hints['services']; ?>"><?php echo htmlspecialchars($site['services_content'] ?? ''); ?></textarea>
                            <p class="text-xs text-gray-500 mt-1"><?php echo $hints['services']; ?></p>
                        </div>
                    </div>
                    
                    <!-- Contact Section -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">
                            <i class="fas fa-address-card text-primary mr-2"></i>Contact Information
                        </h2>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Contact Details</label>
                            <textarea name="contact_info" rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                      placeholder="Phone, email, address..."><?php echo htmlspecialchars($site['contact_info'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Social Media Links Section -->
                    <?php $socialPlatforms = getSocialPlatforms(); ?>
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">
                            <i class="fas fa-share-alt text-primary mr-2"></i>Social Media Links
                        </h2>
                        <p class="text-sm text-gray-500 mb-4">Add your social media profiles. Only filled platforms will be displayed on your site.</p>
                        
                        <div class="grid md:grid-cols-2 gap-4">
                            <!-- Facebook -->
                            <div>
                                <label class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                    <i class="fab fa-facebook-f w-5 text-[#1877F2] mr-2"></i>Facebook
                                </label>
                                <input type="text" name="social_facebook" 
                                       value="<?php echo htmlspecialchars($site['social_facebook'] ?? ''); ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-sm"
                                       placeholder="<?php echo htmlspecialchars($socialPlatforms['facebook']['placeholder']); ?>">
                            </div>
                            
                            <!-- Instagram -->
                            <div>
                                <label class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                    <i class="fab fa-instagram w-5 text-[#E4405F] mr-2"></i>Instagram
                                </label>
                                <input type="text" name="social_instagram" 
                                       value="<?php echo htmlspecialchars($site['social_instagram'] ?? ''); ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-sm"
                                       placeholder="<?php echo htmlspecialchars($socialPlatforms['instagram']['placeholder']); ?>">
                            </div>
                            
                            <!-- TikTok -->
                            <div>
                                <label class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                    <i class="fab fa-tiktok w-5 mr-2"></i>TikTok
                                </label>
                                <input type="text" name="social_tiktok" 
                                       value="<?php echo htmlspecialchars($site['social_tiktok'] ?? ''); ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-sm"
                                       placeholder="<?php echo htmlspecialchars($socialPlatforms['tiktok']['placeholder']); ?>">
                            </div>
                            
                            <!-- WhatsApp -->
                            <div>
                                <label class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                    <i class="fab fa-whatsapp w-5 text-[#25D366] mr-2"></i>WhatsApp
                                </label>
                                <input type="text" name="social_whatsapp" 
                                       value="<?php echo htmlspecialchars($site['social_whatsapp'] ?? ''); ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-sm"
                                       placeholder="<?php echo htmlspecialchars($socialPlatforms['whatsapp']['placeholder']); ?>">
                            </div>
                            
                            <!-- Messenger -->
                            <div>
                                <label class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                    <i class="fab fa-facebook-messenger w-5 text-[#0084FF] mr-2"></i>Messenger
                                </label>
                                <input type="text" name="social_messenger" 
                                       value="<?php echo htmlspecialchars($site['social_messenger'] ?? ''); ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-sm"
                                       placeholder="<?php echo htmlspecialchars($socialPlatforms['messenger']['placeholder']); ?>">
                            </div>
                            
                            <!-- LinkedIn -->
                            <div>
                                <label class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                    <i class="fab fa-linkedin-in w-5 text-[#0A66C2] mr-2"></i>LinkedIn
                                </label>
                                <input type="text" name="social_linkedin" 
                                       value="<?php echo htmlspecialchars($site['social_linkedin'] ?? ''); ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-sm"
                                       placeholder="<?php echo htmlspecialchars($socialPlatforms['linkedin']['placeholder']); ?>">
                            </div>
                            
                            <!-- Twitter -->
                            <div>
                                <label class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                    <i class="fab fa-x-twitter w-5 mr-2"></i>Twitter / X
                                </label>
                                <input type="text" name="social_twitter" 
                                       value="<?php echo htmlspecialchars($site['social_twitter'] ?? ''); ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-sm"
                                       placeholder="<?php echo htmlspecialchars($socialPlatforms['twitter']['placeholder']); ?>">
                            </div>
                            
                            <!-- YouTube -->
                            <div>
                                <label class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                    <i class="fab fa-youtube w-5 text-[#FF0000] mr-2"></i>YouTube
                                </label>
                                <input type="text" name="social_youtube" 
                                       value="<?php echo htmlspecialchars($site['social_youtube'] ?? ''); ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-sm"
                                       placeholder="<?php echo htmlspecialchars($socialPlatforms['youtube']['placeholder']); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Colors & Typography Row -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Colors -->
                        <div class="bg-white rounded-xl shadow p-6">
                            <h2 class="text-lg font-bold text-gray-800 mb-4">
                                <i class="fas fa-palette text-primary mr-2"></i>Colors
                            </h2>
                            
                            <!-- Color Presets -->
                            <div class="mb-5">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Quick Color Presets</label>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" onclick="applyColorPreset('#EF4444', '#B91C1C', '#F59E0B')" 
                                            class="px-3 py-1.5 text-xs font-medium rounded-lg border-2 border-orange-200 hover:border-orange-400 transition-all" 
                                            style="background: linear-gradient(135deg, #FED7AA 0%, #FDBA74 100%);">
                                        <span class="text-orange-800">🏪 Sari-Sari</span>
                                    </button>
                                    <button type="button" onclick="applyColorPreset('#DC2626', '#991B1B', '#22C55E')" 
                                            class="px-3 py-1.5 text-xs font-medium rounded-lg border-2 border-red-200 hover:border-red-400 transition-all"
                                            style="background: linear-gradient(135deg, #FECACA 0%, #FCA5A5 100%);">
                                        <span class="text-red-800">🍲 Carinderia</span>
                                    </button>
                                    <button type="button" onclick="applyColorPreset('#0D9488', '#0F766E', '#06B6D4')" 
                                            class="px-3 py-1.5 text-xs font-medium rounded-lg border-2 border-teal-200 hover:border-teal-400 transition-all"
                                            style="background: linear-gradient(135deg, #CCFBF1 0%, #99F6E4 100%);">
                                        <span class="text-teal-800">🔧 Services</span>
                                    </button>
                                    <button type="button" onclick="applyColorPreset('#3B82F6', '#1D4ED8', '#8B5CF6')" 
                                            class="px-3 py-1.5 text-xs font-medium rounded-lg border-2 border-blue-200 hover:border-blue-400 transition-all"
                                            style="background: linear-gradient(135deg, #DBEAFE 0%, #BFDBFE 100%);">
                                        <span class="text-blue-800">🛍️ Retail</span>
                                    </button>
                                    <button type="button" onclick="applyColorPreset('#7C3AED', '#5B21B6', '#EC4899')" 
                                            class="px-3 py-1.5 text-xs font-medium rounded-lg border-2 border-purple-200 hover:border-purple-400 transition-all"
                                            style="background: linear-gradient(135deg, #EDE9FE 0%, #DDD6FE 100%);">
                                        <span class="text-purple-800">💼 Freelancer</span>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Primary Color</label>
                                    <div class="flex items-center space-x-3">
                                        <input type="color" name="primary_color" id="primaryColor" value="<?php echo $site['primary_color'] ?? '#3B82F6'; ?>"
                                               class="w-12 h-10 rounded cursor-pointer border" onchange="syncColorText(this)">
                                        <input type="text" id="primaryColorText" value="<?php echo $site['primary_color'] ?? '#3B82F6'; ?>"
                                               class="flex-1 px-3 py-2 border rounded text-sm bg-gray-50" readonly>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Secondary Color</label>
                                    <div class="flex items-center space-x-3">
                                        <input type="color" name="secondary_color" id="secondaryColor" value="<?php echo $site['secondary_color'] ?? '#1E40AF'; ?>"
                                               class="w-12 h-10 rounded cursor-pointer border" onchange="syncColorText(this)">
                                        <input type="text" id="secondaryColorText" value="<?php echo $site['secondary_color'] ?? '#1E40AF'; ?>"
                                               class="flex-1 px-3 py-2 border rounded text-sm bg-gray-50" readonly>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Accent Color</label>
                                    <div class="flex items-center space-x-3">
                                        <input type="color" name="accent_color" id="accentColor" value="<?php echo $site['accent_color'] ?? '#F59E0B'; ?>"
                                               class="w-12 h-10 rounded cursor-pointer border" onchange="syncColorText(this)">
                                        <input type="text" id="accentColorText" value="<?php echo $site['accent_color'] ?? '#F59E0B'; ?>"
                                               class="flex-1 px-3 py-2 border rounded text-sm bg-gray-50" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Typography -->
                        <?php 
                        $availableFonts = getAvailableFonts();
                        $currentHeadingFont = $site['font_heading'] ?? 'Inter';
                        $currentBodyFont = $site['font_body'] ?? 'Inter';
                        ?>
                        <div class="bg-white rounded-xl shadow p-6">
                            <h2 class="text-lg font-bold text-gray-800 mb-4">
                                <i class="fas fa-font text-primary mr-2"></i>Typography
                            </h2>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Heading Font</label>
                                    <select name="font_heading" id="fontHeading" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent"
                                            onchange="updateFontPreview()">
                                        <?php foreach ($availableFonts as $fontName => $fontData): ?>
                                        <option value="<?php echo htmlspecialchars($fontName); ?>" 
                                                data-category="<?php echo $fontData['category']; ?>"
                                                <?php echo $currentHeadingFont === $fontName ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($fontData['label']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Body Font</label>
                                    <select name="font_body" id="fontBody"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent"
                                            onchange="updateFontPreview()">
                                        <?php foreach ($availableFonts as $fontName => $fontData): ?>
                                        <option value="<?php echo htmlspecialchars($fontName); ?>"
                                                data-category="<?php echo $fontData['category']; ?>"
                                                <?php echo $currentBodyFont === $fontName ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($fontData['label']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="border rounded-lg p-4 bg-gray-50">
                                    <p class="text-xs text-gray-500 mb-2">Preview</p>
                                    <h3 id="fontPreviewHeading" class="text-lg font-bold text-gray-800 mb-1" style="font-family: '<?php echo $currentHeadingFont; ?>', sans-serif;">
                                        Your Heading
                                    </h3>
                                    <p id="fontPreviewBody" class="text-sm text-gray-600" style="font-family: '<?php echo $currentBodyFont; ?>', sans-serif;">
                                        Body text preview.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 1 Navigation -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                            <a href="preview-site.php?id=<?php echo $siteId; ?>" target="_blank"
                               class="text-primary hover:text-blue-700 font-medium">
                                <i class="fas fa-eye mr-2"></i>Preview Site
                            </a>
                            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                <button type="submit" 
                                        class="w-full sm:w-auto bg-green-500 text-white px-8 py-3 rounded-lg font-semibold hover:bg-green-600 transition flex items-center justify-center">
                                    <i class="fas fa-save mr-2"></i>Save Changes
                                </button>
                                <button type="button" onclick="goToStep(2)" 
                                        class="w-full sm:w-auto gradient-bg text-white px-8 py-3 rounded-lg font-semibold hover:opacity-90 transition flex items-center justify-center">
                                    Next: Submit for Design
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Step 2: Submit for Design -->
        <div id="step2Content" class="step-content hidden">
            <div class="space-y-6">
                
                <!-- Info Banner -->
                <div class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl p-6 text-white">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-magic text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold mb-2">Let Us Design Your Website!</h2>
                            <p class="text-white/90">
                                Upload your logo, images, and design preferences. Our team will professionally design your website within 24-48 hours.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Design Request Form -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-6">
                        <i class="fas fa-paper-plane text-primary mr-2"></i>Submit Design Request
                    </h2>
                    
                    <form method="POST" action="" enctype="multipart/form-data" id="designRequestForm">
                        <input type="hidden" name="action" value="submit_design_request">
                        
                        <div class="space-y-6">
                            <!-- Request Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">What do you need?</label>
                                <div class="grid sm:grid-cols-3 gap-3">
                                    <label class="relative flex items-center p-4 border-2 border-primary bg-primary/5 rounded-xl cursor-pointer hover:border-primary transition group">
                                        <input type="radio" name="request_type" value="full_design" checked class="sr-only">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition">
                                                <i class="fas fa-paint-brush text-blue-600"></i>
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-800">Full Design</div>
                                                <div class="text-xs text-gray-500">Complete website setup</div>
                                            </div>
                                        </div>
                                    </label>
                                    
                                    <label class="relative flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-primary transition group">
                                        <input type="radio" name="request_type" value="logo_upload" class="sr-only">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition">
                                                <i class="fas fa-image text-green-600"></i>
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-800">Add Images</div>
                                                <div class="text-xs text-gray-500">Logo & photos only</div>
                                            </div>
                                        </div>
                                    </label>
                                    
                                    <label class="relative flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-primary transition group">
                                        <input type="radio" name="request_type" value="revision" class="sr-only">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition">
                                                <i class="fas fa-sync text-orange-600"></i>
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-800">Revision</div>
                                                <div class="text-xs text-gray-500">Update existing design</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            
                            <!-- File Uploads -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-cloud-upload-alt mr-2"></i>Upload Your Assets
                                </label>
                                <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-primary transition bg-gray-50">
                                    <input type="file" name="design_files[]" id="designFiles" multiple 
                                           accept="image/*,.pdf,.zip"
                                           class="hidden" onchange="updateFileList()">
                                    <label for="designFiles" class="cursor-pointer">
                                        <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-upload text-2xl text-primary"></i>
                                        </div>
                                        <p class="text-gray-700 font-medium mb-1">Click to upload or drag and drop</p>
                                        <p class="text-gray-500 text-sm">Logo, product photos, banners, etc.</p>
                                        <p class="text-gray-400 text-xs mt-2">PNG, JPG, PDF, ZIP up to 10MB each</p>
                                    </label>
                                </div>
                                <div id="fileList" class="mt-3 space-y-2"></div>
                            </div>
                            
                            <!-- Design Notes -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-comment-alt mr-2"></i>Design Notes & Instructions
                                </label>
                                <textarea name="design_notes" rows="5"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                          placeholder="Tell us about your design preferences:
• What colors do you prefer?
• Any specific style (modern, classic, minimalist)?
• Reference websites you like?
• Any special instructions for images?
• Preferred arrangement or layout?"></textarea>
                            </div>
                            
                            <!-- Priority -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                                <select name="priority" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                    <option value="normal">Normal (24-48 hours)</option>
                                    <option value="high">High Priority (12-24 hours)</option>
                                    <option value="low">Low (3-5 days)</option>
                                </select>
                            </div>
                            
                            <!-- Submit Button -->
                            <button type="submit" 
                                    class="w-full bg-gradient-to-r from-purple-500 to-pink-500 text-white py-4 rounded-xl font-bold text-lg hover:from-purple-600 hover:to-pink-600 transition shadow-lg">
                                <i class="fas fa-paper-plane mr-2"></i>Submit Design Request
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Previous Requests -->
                <?php if (!empty($existingRequests)): ?>
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-history text-primary mr-2"></i>Your Design Requests
                    </h3>
                    <div class="space-y-3">
                        <?php foreach ($existingRequests as $request): ?>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <div class="font-medium text-gray-800">
                                    <?php echo htmlspecialchars($request['reference_number'] ?? 'Request #' . $request['id']); ?>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <?php echo ucfirst(str_replace('_', ' ', $request['request_type'])); ?> •
                                    <?php echo date('M j, Y', strtotime($request['created_at'])); ?>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                <?php 
                                switch($request['status']) {
                                    case 'pending': echo 'bg-yellow-100 text-yellow-700'; break;
                                    case 'in_progress': echo 'bg-blue-100 text-blue-700'; break;
                                    case 'completed': echo 'bg-green-100 text-green-700'; break;
                                    case 'rejected': echo 'bg-red-100 text-red-700'; break;
                                    default: echo 'bg-gray-100 text-gray-700';
                                }
                                ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $request['status'])); ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- How It Works -->
                <div class="bg-blue-50 rounded-xl p-6">
                    <h3 class="font-semibold text-blue-800 mb-4">
                        <i class="fas fa-info-circle mr-2"></i>How It Works
                    </h3>
                    <div class="grid sm:grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="w-10 h-10 bg-blue-500 text-white rounded-full flex items-center justify-center mx-auto mb-2 font-bold">1</div>
                            <p class="text-sm text-blue-700">Upload your logo & images</p>
                        </div>
                        <div class="text-center">
                            <div class="w-10 h-10 bg-blue-500 text-white rounded-full flex items-center justify-center mx-auto mb-2 font-bold">2</div>
                            <p class="text-sm text-blue-700">Tell us your preferences</p>
                        </div>
                        <div class="text-center">
                            <div class="w-10 h-10 bg-blue-500 text-white rounded-full flex items-center justify-center mx-auto mb-2 font-bold">3</div>
                            <p class="text-sm text-blue-700">We design your site</p>
                        </div>
                        <div class="text-center">
                            <div class="w-10 h-10 bg-blue-500 text-white rounded-full flex items-center justify-center mx-auto mb-2 font-bold">4</div>
                            <p class="text-sm text-blue-700">Review & approve</p>
                        </div>
                    </div>
                </div>
                
                <!-- Step 2 Navigation -->
                <div class="bg-white rounded-xl shadow p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                        <button type="button" onclick="goToStep(1)" 
                                class="text-gray-600 hover:text-gray-800 font-medium">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Content
                        </button>
                        <a href="preview-site.php?id=<?php echo $siteId; ?>" target="_blank"
                           class="text-primary hover:text-blue-700 font-medium">
                            <i class="fas fa-eye mr-2"></i>Preview Current Site
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Multi-step form navigation
let currentStep = 1;

function goToStep(step) {
    currentStep = step;
    
    document.querySelectorAll('.step-content').forEach(el => el.classList.add('hidden'));
    document.getElementById('step' + step + 'Content').classList.remove('hidden');
    
    const progressBar = document.getElementById('progressBar');
    const stepIndicator = document.getElementById('stepIndicator');
    const step1Dot = document.getElementById('step1Dot');
    const step2Dot = document.getElementById('step2Dot');
    const step2Label = document.getElementById('step2Label');
    
    if (step === 1) {
        progressBar.style.width = '50%';
        stepIndicator.textContent = 'Step 1 of 2';
        step1Dot.className = 'w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center text-sm font-semibold shadow-lg';
        step2Dot.className = 'w-8 h-8 rounded-full bg-gray-300 text-gray-500 flex items-center justify-center text-sm font-semibold';
        step2Label.className = 'mr-2 text-sm font-medium text-gray-400';
    } else {
        progressBar.style.width = '100%';
        stepIndicator.textContent = 'Step 2 of 2';
        step1Dot.className = 'w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center text-sm font-semibold shadow-lg';
        step2Dot.className = 'w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center text-sm font-semibold shadow-lg';
        step2Label.className = 'mr-2 text-sm font-medium text-blue-600';
    }
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// File upload handling
function updateFileList() {
    const input = document.getElementById('designFiles');
    const fileList = document.getElementById('fileList');
    fileList.innerHTML = '';
    
    if (input.files.length > 0) {
        for (let i = 0; i < input.files.length; i++) {
            const file = input.files[i];
            const size = (file.size / 1024 / 1024).toFixed(2);
            const div = document.createElement('div');
            div.className = 'flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200';
            div.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-file-image text-green-500 mr-3"></i>
                    <div>
                        <div class="font-medium text-gray-800 text-sm">${file.name}</div>
                        <div class="text-xs text-gray-500">${size} MB</div>
                    </div>
                </div>
                <i class="fas fa-check-circle text-green-500"></i>
            `;
            fileList.appendChild(div);
        }
    }
}

// Font preview functionality
let loadedFonts = new Set();

function loadGoogleFont(fontName) {
    if (loadedFonts.has(fontName)) return;
    const link = document.createElement('link');
    link.href = `https://fonts.googleapis.com/css2?family=${encodeURIComponent(fontName)}:wght@400;500;600;700&display=swap`;
    link.rel = 'stylesheet';
    document.head.appendChild(link);
    loadedFonts.add(fontName);
}

function updateFontPreview() {
    const headingSelect = document.getElementById('fontHeading');
    const bodySelect = document.getElementById('fontBody');
    const headingPreview = document.getElementById('fontPreviewHeading');
    const bodyPreview = document.getElementById('fontPreviewBody');
    
    if (headingSelect && bodySelect && headingPreview && bodyPreview) {
        const headingFont = headingSelect.value;
        const bodyFont = bodySelect.value;
        const headingCategory = headingSelect.options[headingSelect.selectedIndex].dataset.category || 'sans-serif';
        const bodyCategory = bodySelect.options[bodySelect.selectedIndex].dataset.category || 'sans-serif';
        
        loadGoogleFont(headingFont);
        loadGoogleFont(bodyFont);
        
        headingPreview.style.fontFamily = `'${headingFont}', ${headingCategory}`;
        bodyPreview.style.fontFamily = `'${bodyFont}', ${bodyCategory}`;
    }
}

// Color preset functionality
function applyColorPreset(primary, secondary, accent) {
    document.getElementById('primaryColor').value = primary;
    document.getElementById('secondaryColor').value = secondary;
    document.getElementById('accentColor').value = accent;
    
    syncColorText(document.getElementById('primaryColor'));
    syncColorText(document.getElementById('secondaryColor'));
    syncColorText(document.getElementById('accentColor'));
    
    showToast('Color preset applied!', 'success');
}

function syncColorText(colorInput) {
    const textId = colorInput.id + 'Text';
    const textInput = document.getElementById(textId);
    if (textInput) {
        textInput.value = colorInput.value.toUpperCase();
    }
}

// Toast notification
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
    toast.className = `fixed bottom-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-y-full opacity-0`;
    toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'} mr-2"></i>${message}`;
    document.body.appendChild(toast);
    
    setTimeout(() => toast.classList.remove('translate-y-full', 'opacity-0'), 10);
    setTimeout(() => {
        toast.classList.add('translate-y-full', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 2000);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateFontPreview();
    
    // Check if we need to show step 2
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('step') === '2') {
        goToStep(2);
    }
    
    // Radio button styling
    document.querySelectorAll('input[name="request_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('input[name="request_type"]').forEach(r => {
                r.closest('label').classList.remove('border-primary', 'bg-primary/5');
                r.closest('label').classList.add('border-gray-200');
            });
            if (this.checked) {
                this.closest('label').classList.add('border-primary', 'bg-primary/5');
                this.closest('label').classList.remove('border-gray-200');
            }
        });
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>
