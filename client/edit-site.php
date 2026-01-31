<?php
/**
 * Edit Site Page - Content Management
 */
$pageTitle = "Edit Website - FilDevStudio";
require_once '../includes/header.php';
require_once '../includes/functions.php';
requireLogin();

$siteId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$userId = $_SESSION['user_id'];
$error = '';
$success = '';

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
    
} catch (Exception $e) {
    redirect('dashboard.php');
}

// Template-specific hints and labels
$templateId = $site['template_id'] ?? 1;
$templateHints = [
    1 => [ // Modern Retail
        'heroTitle' => 'e.g., "Discover Your Style" or "New Collection Out Now"',
        'heroSubtitle' => 'Highlight your best products or current promotions',
        'about' => 'Share your brand story and what makes your products special',
        'services' => 'List featured products, categories, or bestsellers',
        'servicesLabel' => 'Products & Categories',
        'icon' => 'fa-shopping-bag'
    ],
    2 => [ // Restaurant Pro
        'heroTitle' => 'e.g., "Authentic Filipino Cuisine" or "A Taste of Home"',
        'heroSubtitle' => 'Describe your dining experience or signature dishes',
        'about' => 'Tell your restaurant\'s story - family recipes, chef background, etc.',
        'services' => 'List your menu categories or specialties',
        'servicesLabel' => 'Menu Highlights',
        'icon' => 'fa-utensils'
    ],
    3 => [ // Freelancer Portfolio
        'heroTitle' => 'e.g., "Creative Developer & Designer" or your name',
        'heroSubtitle' => 'Your tagline - what you do and who you help',
        'about' => 'Your professional background, skills, and what drives you',
        'services' => 'List your services or describe your notable projects',
        'servicesLabel' => 'Services & Projects',
        'icon' => 'fa-laptop-code'
    ],
    4 => [ // Service Business
        'heroTitle' => 'e.g., "Professional Solutions You Can Trust"',
        'heroSubtitle' => 'Your value proposition - why choose your services',
        'about' => 'Company history, expertise, certifications, and team',
        'services' => 'List all your services with brief descriptions',
        'servicesLabel' => 'Our Services',
        'icon' => 'fa-briefcase'
    ],
    5 => [ // General Business
        'heroTitle' => 'Your main headline - grab attention!',
        'heroSubtitle' => 'A supporting tagline or call to action',
        'about' => 'Your company story, mission, and values',
        'services' => 'What you offer to your customers',
        'servicesLabel' => 'What We Offer',
        'icon' => 'fa-building'
    ],
    6 => [ // E-Commerce Starter
        'heroTitle' => 'e.g., "Shop Quality Products Online"',
        'heroSubtitle' => 'Highlight free shipping, discounts, or best sellers',
        'about' => 'Why customers should buy from you',
        'services' => 'Featured products or product categories',
        'servicesLabel' => 'Featured Products',
        'icon' => 'fa-store'
    ],
    7 => [ // Urban Streetwear
        'heroTitle' => 'e.g., "DROP 001" or "STREET CULTURE"',
        'heroSubtitle' => 'Bold statement - keep it short and impactful',
        'about' => 'Your brand\'s philosophy and street culture inspiration',
        'services' => 'New drops, featured items, or collections',
        'servicesLabel' => 'Collections & Drops',
        'icon' => 'fa-tshirt'
    ],
    8 => [ // Tech Startup
        'heroTitle' => 'e.g., "Build Faster, Scale Smarter"',
        'heroSubtitle' => 'Explain your product/service value in one line',
        'about' => 'Your company vision, technology, and team background',
        'services' => 'Key features, pricing tiers, or solutions',
        'servicesLabel' => 'Features & Solutions',
        'icon' => 'fa-rocket'
    ],
    9 => [ // Boutique Shop
        'heroTitle' => 'e.g., "Spring Collection 2024" or "Discover Elegance"',
        'heroSubtitle' => 'A soft, elegant tagline for your boutique brand',
        'about' => 'Your brand story - craftsmanship, inspiration, and style philosophy',
        'services' => 'Featured products, new arrivals, or seasonal collections',
        'servicesLabel' => 'Collections & Products',
        'icon' => 'fa-gem'
    ],
    10 => [ // Electronics Store
        'heroTitle' => 'e.g., "Latest Tech, Best Prices" or "Gadget Heaven"',
        'heroSubtitle' => 'Highlight deals, warranties, or exclusive offers',
        'about' => 'Why buy from you - authenticity, warranty, after-sales support',
        'services' => 'Featured gadgets, categories, or bestselling products',
        'servicesLabel' => 'Featured Products',
        'icon' => 'fa-microchip'
    ],
    11 => [ // Grocery & Supermarket
        'heroTitle' => 'e.g., "Fresh Daily!" or "Quality Groceries"',
        'heroSubtitle' => 'Highlight freshness, prices, or delivery options',
        'about' => 'Your store story - family-owned, fresh sourcing, community focus',
        'services' => 'Product categories, weekly deals, or delivery info',
        'servicesLabel' => 'Products & Deals',
        'icon' => 'fa-shopping-basket'
    ],
    12 => [ // Sari-Sari Store
        'heroTitle' => 'e.g., "Tindahan ng Bayan" or "Mura at Matibay"',
        'heroSubtitle' => 'Your tagline - tingi prices, services offered',
        'about' => 'Your sari-sari story - barangay location, years serving, etc.',
        'services' => 'Products available: snacks, drinks, e-load, sachet items',
        'servicesLabel' => 'Mga Paninda',
        'icon' => 'fa-store-alt'
    ],
    13 => [ // Sari-Sari Plus
        'heroTitle' => 'e.g., "Modern Convenience Store" or "Order Online!"',
        'heroSubtitle' => 'Highlight delivery, GCash, or 24/7 service',
        'about' => 'Your modern sari-sari concept - delivery, digital payments',
        'services' => 'Services: groceries, e-load, bills payment, delivery',
        'servicesLabel' => 'Products & Services',
        'icon' => 'fa-motorcycle'
    ]
];

$hints = $templateHints[$templateId] ?? $templateHints[5]; // Default to general business

// Get existing site images
$logoImage = getSiteImage($pdo, $siteId, 'logo');
$heroImage = getSiteImage($pdo, $siteId, 'hero');
$galleryImages = getSiteImages($pdo, $siteId, 'gallery');

// Process image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload_image') {
    $imageType = sanitize($_POST['image_type'] ?? '');
    $altText = sanitize($_POST['alt_text'] ?? '');
    
    if (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $displayOrder = ($imageType === 'gallery') ? count($galleryImages) : 0;
        $result = uploadSiteImage($pdo, $siteId, $_FILES['image'], $imageType, $altText, $displayOrder);
        
        if ($result['success']) {
            $success = $result['message'];
            logActivity($pdo, $userId, 'upload_image', "Uploaded $imageType image for site ID: $siteId");
            
            // Refresh images
            $logoImage = getSiteImage($pdo, $siteId, 'logo');
            $heroImage = getSiteImage($pdo, $siteId, 'hero');
            $galleryImages = getSiteImages($pdo, $siteId, 'gallery');
        } else {
            $error = $result['message'];
        }
    } else {
        $error = 'Please select an image to upload.';
    }
}

// Process image deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_image') {
    $imageId = (int)($_POST['image_id'] ?? 0);
    
    if ($imageId > 0) {
        $result = deleteSiteImage($pdo, $imageId, $siteId);
        
        if ($result['success']) {
            $success = $result['message'];
            logActivity($pdo, $userId, 'delete_image', "Deleted image ID: $imageId from site ID: $siteId");
            
            // Refresh images
            $logoImage = getSiteImage($pdo, $siteId, 'logo');
            $heroImage = getSiteImage($pdo, $siteId, 'hero');
            $galleryImages = getSiteImages($pdo, $siteId, 'gallery');
        } else {
            $error = $result['message'];
        }
    }
}

// Process publish site
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'publish_site') {
    $subdomain = strtolower(trim($_POST['subdomain'] ?? ''));
    
    if (empty($subdomain)) {
        // Auto-generate subdomain from site name
        $subdomain = generateSubdomain($site['site_name'] ?? 'my-site');
        $subdomain = generateUniqueSubdomain($pdo, $subdomain, $siteId);
    }
    
    $result = publishSite($pdo, $siteId, $userId, $subdomain);
    
    if ($result['success']) {
        $success = $result['message'];
        logActivity($pdo, $userId, 'publish_site', "Published site: {$site['site_name']} with subdomain: $subdomain");
        
        // Refresh site data
        $stmt = $pdo->prepare("SELECT cs.*, t.name as template_name, t.category as template_category FROM client_sites cs 
                               LEFT JOIN templates t ON cs.template_id = t.id 
                               WHERE cs.id = ?");
        $stmt->execute([$siteId]);
        $site = $stmt->fetch();
    } else {
        $error = $result['message'];
    }
}

// Process unpublish site
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'unpublish_site') {
    $result = unpublishSite($pdo, $siteId, $userId);
    
    if ($result['success']) {
        $success = $result['message'];
        logActivity($pdo, $userId, 'unpublish_site', "Unpublished site: {$site['site_name']}");
        
        // Refresh site data
        $stmt = $pdo->prepare("SELECT cs.*, t.name as template_name, t.category as template_category FROM client_sites cs 
                               LEFT JOIN templates t ON cs.template_id = t.id 
                               WHERE cs.id = ?");
        $stmt->execute([$siteId]);
        $site = $stmt->fetch();
    } else {
        $error = $result['message'];
    }
}

// Process subdomain update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_subdomain') {
    $subdomain = strtolower(trim($_POST['subdomain'] ?? ''));
    
    $result = updateSubdomain($pdo, $siteId, $userId, $subdomain);
    
    if ($result['success']) {
        $success = $result['message'];
        logActivity($pdo, $userId, 'update_subdomain', "Updated subdomain for site: {$site['site_name']} to: $subdomain");
        
        // Refresh site data
        $stmt = $pdo->prepare("SELECT cs.*, t.name as template_name, t.category as template_category FROM client_sites cs 
                               LEFT JOIN templates t ON cs.template_id = t.id 
                               WHERE cs.id = ?");
        $stmt->execute([$siteId]);
        $site = $stmt->fetch();
    } else {
        $error = $result['message'];
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
    
    // Validate fonts
    if (!isValidFont($fontHeading)) $fontHeading = 'Inter';
    if (!isValidFont($fontBody)) $fontBody = 'Inter';
    
    try {
        $stmt = $pdo->prepare("UPDATE client_sites SET 
                               site_name = ?, hero_title = ?, hero_subtitle = ?, 
                               about_content = ?, services_content = ?, contact_info = ?,
                               primary_color = ?, secondary_color = ?, accent_color = ?,
                               font_heading = ?, font_body = ?,
                               updated_at = NOW()
                               WHERE id = ? AND user_id = ?");
        $stmt->execute([
            $siteName, $heroTitle, $heroSubtitle,
            $aboutContent, $servicesContent, $contactInfo,
            $primaryColor, $secondaryColor, $accentColor,
            $fontHeading, $fontBody,
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
                <a href="custom-request.php?site_id=<?php echo $siteId; ?>" class="bg-white text-primary px-4 py-2 rounded-lg font-medium hover:bg-gray-100 transition">
                    <i class="fas fa-palette mr-2"></i>Request Customization
                </a>
            </div>
        </div>
    </div>
</section>

<section class="py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if ($error): echo displayAlert('error', $error); endif; ?>
        <?php if ($success): echo displayAlert('success', $success); endif; ?>
        
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update_content">
            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Main Content Editor -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Info -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">
                            <i class="fas fa-info-circle text-primary mr-2"></i>Basic Information
                        </h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Site Name</label>
                                <input type="text" name="site_name" value="<?php echo htmlspecialchars($site['site_name'] ?? ''); ?>"
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
                    
                    <!-- Save Content Button -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <button type="submit" class="w-full gradient-bg text-white py-3 rounded-lg font-semibold hover:opacity-90 transition">
                            <i class="fas fa-save mr-2"></i>Save Content Changes
                        </button>
                    </div>
                </div>
                
                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Colors -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">
                            <i class="fas fa-palette text-primary mr-2"></i>Colors
                        </h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Primary Color</label>
                                <div class="flex items-center space-x-3">
                                    <input type="color" name="primary_color" value="<?php echo $site['primary_color'] ?? '#3B82F6'; ?>"
                                           class="w-12 h-10 rounded cursor-pointer border">
                                    <input type="text" value="<?php echo $site['primary_color'] ?? '#3B82F6'; ?>"
                                           class="flex-1 px-3 py-2 border rounded text-sm" readonly>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Secondary Color</label>
                                <div class="flex items-center space-x-3">
                                    <input type="color" name="secondary_color" value="<?php echo $site['secondary_color'] ?? '#1E40AF'; ?>"
                                           class="w-12 h-10 rounded cursor-pointer border">
                                    <input type="text" value="<?php echo $site['secondary_color'] ?? '#1E40AF'; ?>"
                                           class="flex-1 px-3 py-2 border rounded text-sm" readonly>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Accent Color</label>
                                <div class="flex items-center space-x-3">
                                    <input type="color" name="accent_color" value="<?php echo $site['accent_color'] ?? '#F59E0B'; ?>"
                                           class="w-12 h-10 rounded cursor-pointer border">
                                    <input type="text" value="<?php echo $site['accent_color'] ?? '#F59E0B'; ?>"
                                           class="flex-1 px-3 py-2 border rounded text-sm" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Fonts Section -->
                    <?php 
                    $availableFonts = getAvailableFonts();
                    $fontPairings = getFontPairings();
                    $currentHeadingFont = $site['font_heading'] ?? 'Inter';
                    $currentBodyFont = $site['font_body'] ?? 'Inter';
                    ?>
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">
                            <i class="fas fa-font text-primary mr-2"></i>Typography
                        </h2>
                        <div class="space-y-4">
                            <!-- Heading Font -->
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
                            
                            <!-- Body Font -->
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
                            
                            <!-- Font Preview -->
                            <div class="border rounded-lg p-4 bg-gray-50">
                                <p class="text-xs text-gray-500 mb-2">Preview</p>
                                <h3 id="fontPreviewHeading" class="text-xl font-bold text-gray-800 mb-2" style="font-family: '<?php echo $currentHeadingFont; ?>', sans-serif;">
                                    Your Heading Text
                                </h3>
                                <p id="fontPreviewBody" class="text-sm text-gray-600" style="font-family: '<?php echo $currentBodyFont; ?>', sans-serif;">
                                    This is how your body text will look. Choose fonts that match your brand personality.
                                </p>
                            </div>
                            
                            <!-- Font Pairing Suggestions -->
                            <div class="border-t pt-4">
                                <p class="text-xs font-medium text-gray-500 mb-2">
                                    <i class="fas fa-lightbulb mr-1 text-yellow-500"></i>Suggested Pairings
                                </p>
                                <div class="space-y-2 max-h-32 overflow-y-auto">
                                    <?php foreach (array_slice($fontPairings, 0, 4) as $pairing): ?>
                                    <button type="button" 
                                            onclick="applyFontPairing('<?php echo $pairing['heading']; ?>', '<?php echo $pairing['body']; ?>')"
                                            class="w-full text-left px-3 py-2 text-xs bg-gray-100 hover:bg-blue-50 rounded-lg transition group">
                                        <span class="font-medium text-gray-700 group-hover:text-blue-600">
                                            <?php echo $pairing['heading']; ?> + <?php echo $pairing['body']; ?>
                                        </span>
                                        <span class="block text-gray-500"><?php echo $pairing['style']; ?></span>
                                    </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actions (Preview) -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">Quick Actions</h2>
                        <div class="space-y-3">
                            <a href="preview-site.php?id=<?php echo $siteId; ?>" target="_blank"
                               class="block w-full text-center border-2 border-primary text-primary py-3 rounded-lg font-semibold hover:bg-primary hover:text-white transition">
                                <i class="fas fa-eye mr-2"></i>Preview Site
                            </a>
                        </div>
                    </div>
                    
                    <!-- Help -->
                    <div class="bg-blue-50 rounded-xl p-6">
                        <h3 class="font-semibold text-blue-800 mb-2">
                            <i class="fas <?php echo $hints['icon']; ?> mr-2"></i>Tips for <?php echo htmlspecialchars($site['template_name']); ?>
                        </h3>
                        <?php if ($templateId == 1): // Modern Retail ?>
                        <ul class="text-sm text-blue-700 space-y-2">
                            <li>• Keep headlines elegant and minimal</li>
                            <li>• Feature your best-selling products</li>
                            <li>• Use high-quality product images</li>
                            <li>• Highlight promotions and discounts</li>
                        </ul>
                        <?php elseif ($templateId == 2): // Restaurant ?>
                        <ul class="text-sm text-blue-700 space-y-2">
                            <li>• Use mouth-watering food descriptions</li>
                            <li>• Include your operating hours</li>
                            <li>• Mention signature dishes prominently</li>
                            <li>• Add reservation/delivery info in contact</li>
                        </ul>
                        <?php elseif ($templateId == 3): // Freelancer ?>
                        <ul class="text-sm text-blue-700 space-y-2">
                            <li>• Lead with your strongest skill</li>
                            <li>• Showcase 3-4 best projects</li>
                            <li>• Include your availability status</li>
                            <li>• Make contacting you easy</li>
                        </ul>
                        <?php elseif ($templateId == 4): // Service Business ?>
                        <ul class="text-sm text-blue-700 space-y-2">
                            <li>• Build trust with your experience stats</li>
                            <li>• List all services clearly</li>
                            <li>• Include testimonials if possible</li>
                            <li>• Add multiple contact options</li>
                        </ul>
                        <?php elseif ($templateId == 7): // Urban Streetwear ?>
                        <ul class="text-sm text-blue-700 space-y-2">
                            <li>• Keep text bold and minimal</li>
                            <li>• Use ALL CAPS for impact</li>
                            <li>• Create urgency with "limited drops"</li>
                            <li>• Include your social media handles</li>
                        </ul>
                        <?php elseif ($templateId == 8): // Tech Startup ?>
                        <ul class="text-sm text-blue-700 space-y-2">
                            <li>• Focus on the problem you solve</li>
                            <li>• Use clear, jargon-free language</li>
                            <li>• Highlight key features with benefits</li>
                            <li>• Include a clear call-to-action</li>
                        </ul>
                        <?php else: // Default ?>
                        <ul class="text-sm text-blue-700 space-y-2">
                            <li>• Keep your hero title short and impactful</li>
                            <li>• Use bullet points in your services section</li>
                            <li>• Include all contact methods customers might use</li>
                        </ul>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Template Info -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="font-semibold text-gray-800 mb-2">
                            <i class="fas fa-info-circle mr-2"></i>Current Template
                        </h3>
                        <p class="text-sm text-gray-600 mb-3"><?php echo htmlspecialchars($site['template_name']); ?></p>
                        <a href="select-template.php" class="text-primary text-sm hover:underline">
                            <i class="fas fa-exchange-alt mr-1"></i>Change Template
                        </a>
                    </div>
                </div>
            </div>
        </form>
        
        <!-- Images Section (Separate Forms for File Uploads) -->
        <div class="grid lg:grid-cols-3 gap-8 mt-8">
            <div class="lg:col-span-2 space-y-6">
                <!-- Logo Upload -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-image text-primary mr-2"></i>Site Logo
                    </h2>
                    
                    <?php if ($logoImage): ?>
                    <div class="mb-4">
                        <div class="relative inline-block">
                            <img src="../<?php echo htmlspecialchars($logoImage['image_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($logoImage['alt_text'] ?? 'Logo'); ?>"
                                 class="h-24 w-auto object-contain rounded-lg border bg-gray-50 p-2">
                            <form method="POST" action="" class="absolute -top-2 -right-2">
                                <input type="hidden" name="action" value="delete_image">
                                <input type="hidden" name="image_id" value="<?php echo $logoImage['id']; ?>">
                                <button type="submit" onclick="return confirm('Delete this logo?')"
                                        class="w-6 h-6 bg-red-500 text-white rounded-full text-xs hover:bg-red-600 transition shadow">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Current logo uploaded</p>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" enctype="multipart/form-data" class="space-y-3">
                        <input type="hidden" name="action" value="upload_image">
                        <input type="hidden" name="image_type" value="logo">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Logo</label>
                            <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent
                                          file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium
                                          file:bg-primary file:text-white hover:file:bg-blue-600 file:cursor-pointer">
                            <p class="text-xs text-gray-500 mt-1">Recommended: PNG with transparent background, max 5MB</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alt Text</label>
                            <input type="text" name="alt_text" placeholder="e.g., Company Logo"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-sm">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-blue-600 transition">
                            <i class="fas fa-upload mr-2"></i>Upload Logo
                        </button>
                    </form>
                </div>
                
                <!-- Hero Image Upload -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-panorama text-primary mr-2"></i>Hero/Banner Image
                    </h2>
                    
                    <?php if ($heroImage): ?>
                    <div class="mb-4">
                        <div class="relative">
                            <img src="../<?php echo htmlspecialchars($heroImage['image_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($heroImage['alt_text'] ?? 'Hero Image'); ?>"
                                 class="w-full max-h-48 object-cover rounded-lg border">
                            <form method="POST" action="" class="absolute top-2 right-2">
                                <input type="hidden" name="action" value="delete_image">
                                <input type="hidden" name="image_id" value="<?php echo $heroImage['id']; ?>">
                                <button type="submit" onclick="return confirm('Delete this hero image?')"
                                        class="w-8 h-8 bg-red-500 text-white rounded-full text-sm hover:bg-red-600 transition shadow-lg">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Current hero image</p>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" enctype="multipart/form-data" class="space-y-3">
                        <input type="hidden" name="action" value="upload_image">
                        <input type="hidden" name="image_type" value="hero">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Hero Image</label>
                            <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent
                                          file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium
                                          file:bg-primary file:text-white hover:file:bg-blue-600 file:cursor-pointer">
                            <p class="text-xs text-gray-500 mt-1">Recommended: 1920x800px or larger, max 5MB</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alt Text</label>
                            <input type="text" name="alt_text" placeholder="e.g., Welcome to our store"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-sm">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-blue-600 transition">
                            <i class="fas fa-upload mr-2"></i>Upload Hero Image
                        </button>
                    </form>
                </div>
                
                <!-- Gallery Images -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-images text-primary mr-2"></i>Gallery Images
                        <span class="text-sm font-normal text-gray-500">(<?php echo count($galleryImages); ?> images)</span>
                    </h2>
                    
                    <?php if (!empty($galleryImages)): ?>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mb-6">
                        <?php foreach ($galleryImages as $index => $galleryImg): ?>
                        <div class="relative group">
                            <img src="../<?php echo htmlspecialchars($galleryImg['image_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($galleryImg['alt_text'] ?? 'Gallery Image'); ?>"
                                 class="w-full h-32 object-cover rounded-lg border">
                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition rounded-lg flex items-center justify-center gap-2">
                                <form method="POST" action="">
                                    <input type="hidden" name="action" value="delete_image">
                                    <input type="hidden" name="image_id" value="<?php echo $galleryImg['id']; ?>">
                                    <button type="submit" onclick="return confirm('Delete this image?')"
                                            class="w-8 h-8 bg-red-500 text-white rounded-full text-sm hover:bg-red-600 transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                            <span class="absolute bottom-1 right-1 bg-black/60 text-white text-xs px-2 py-0.5 rounded">#<?php echo $index + 1; ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-8 bg-gray-50 rounded-lg mb-4">
                        <i class="fas fa-images text-4xl text-gray-300 mb-2"></i>
                        <p class="text-gray-500 text-sm">No gallery images yet</p>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" enctype="multipart/form-data" class="space-y-3">
                        <input type="hidden" name="action" value="upload_image">
                        <input type="hidden" name="image_type" value="gallery">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Add Gallery Image</label>
                            <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent
                                          file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium
                                          file:bg-primary file:text-white hover:file:bg-blue-600 file:cursor-pointer">
                            <p class="text-xs text-gray-500 mt-1">Upload images for your product gallery or portfolio, max 5MB each</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alt Text</label>
                            <input type="text" name="alt_text" placeholder="e.g., Product showcase"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-sm">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-blue-600 transition">
                            <i class="fas fa-plus mr-2"></i>Add to Gallery
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Sidebar for Images Section -->
            <div class="space-y-6">
                <!-- Publishing Section -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-globe text-primary mr-2"></i>Publishing
                    </h2>
                    
                    <?php if ($site['status'] === 'active' && !empty($site['subdomain'])): ?>
                    <!-- Published State -->
                    <div class="mb-4">
                        <div class="flex items-center mb-3">
                            <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">
                                <i class="fas fa-check-circle mr-2"></i>Published
                            </span>
                        </div>
                        <?php if (!empty($site['published_at'])): ?>
                        <p class="text-xs text-gray-500 mb-3">
                            <i class="fas fa-clock mr-1"></i>Published: <?php echo date('M j, Y g:i A', strtotime($site['published_at'])); ?>
                        </p>
                        <?php endif; ?>
                        
                        <!-- Public URL -->
                        <div class="bg-gray-50 rounded-lg p-3 mb-4">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Public URL</label>
                            <div class="flex items-center gap-2">
                                <input type="text" id="publicUrl" readonly 
                                       value="<?php echo htmlspecialchars(getPublicSiteUrl($site['subdomain'])); ?>"
                                       class="flex-1 text-xs bg-white px-2 py-1.5 border rounded truncate">
                                <button type="button" onclick="copyPublicUrl()" 
                                        class="px-2 py-1.5 bg-primary text-white rounded text-xs hover:bg-blue-600 transition"
                                        title="Copy URL">
                                    <i class="fas fa-copy"></i>
                                </button>
                                <a href="<?php echo htmlspecialchars(getPublicSiteUrl($site['subdomain'])); ?>" target="_blank"
                                   class="px-2 py-1.5 bg-gray-200 text-gray-700 rounded text-xs hover:bg-gray-300 transition"
                                   title="Open in new tab">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Update Subdomain -->
                        <form method="POST" action="" class="mb-4">
                            <input type="hidden" name="action" value="update_subdomain">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subdomain</label>
                            <div class="flex gap-2">
                                <input type="text" name="subdomain" 
                                       value="<?php echo htmlspecialchars($site['subdomain']); ?>"
                                       pattern="[a-z0-9][a-z0-9-]*[a-z0-9]|[a-z0-9]"
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent"
                                       placeholder="my-business">
                                <button type="submit" class="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300 transition">
                                    <i class="fas fa-save"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Lowercase letters, numbers, and hyphens only</p>
                        </form>
                        
                        <!-- Unpublish Button -->
                        <form method="POST" action="" onsubmit="return confirm('Are you sure you want to unpublish this site? It will no longer be accessible via the public URL.');">
                            <input type="hidden" name="action" value="unpublish_site">
                            <button type="submit" class="w-full border-2 border-red-500 text-red-500 py-2 rounded-lg font-medium hover:bg-red-500 hover:text-white transition text-sm">
                                <i class="fas fa-eye-slash mr-2"></i>Unpublish Site
                            </button>
                        </form>
                    </div>
                    
                    <?php else: ?>
                    <!-- Draft State -->
                    <div class="mb-4">
                        <div class="flex items-center mb-3">
                            <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-sm font-medium">
                                <i class="fas fa-file-alt mr-2"></i>Draft
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">
                            Publish your site to make it accessible via a public URL that you can share with customers.
                        </p>
                        
                        <form method="POST" action="" id="publishForm">
                            <input type="hidden" name="action" value="publish_site">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Choose your URL</label>
                            <div class="mb-3">
                                <div class="flex items-center bg-gray-100 rounded-lg overflow-hidden border">
                                    <span class="px-3 py-2 text-gray-500 text-sm bg-gray-200">site/</span>
                                    <input type="text" name="subdomain" id="subdomainInput"
                                           value="<?php echo htmlspecialchars($site['subdomain'] ?? generateSubdomain($site['site_name'] ?? 'my-site')); ?>"
                                           pattern="[a-z0-9][a-z0-9-]*[a-z0-9]|[a-z0-9]"
                                           class="flex-1 px-3 py-2 text-sm border-0 focus:ring-0 focus:outline-none"
                                           placeholder="my-business">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">3-50 characters: lowercase letters, numbers, hyphens</p>
                            </div>
                            <button type="submit" class="w-full bg-green-500 text-white py-3 rounded-lg font-semibold hover:bg-green-600 transition">
                                <i class="fas fa-rocket mr-2"></i>Publish Site
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Actions -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Actions</h2>
                    <div class="space-y-3">
                        <a href="preview-site.php?id=<?php echo $siteId; ?>" target="_blank"
                           class="block w-full text-center border-2 border-primary text-primary py-3 rounded-lg font-semibold hover:bg-primary hover:text-white transition">
                            <i class="fas fa-eye mr-2"></i>Preview Site
                        </a>
                        <?php if ($site['status'] === 'active' && !empty($site['subdomain'])): ?>
                        <a href="<?php echo htmlspecialchars(getPublicSiteUrl($site['subdomain'])); ?>" target="_blank"
                           class="block w-full text-center bg-green-500 text-white py-3 rounded-lg font-semibold hover:bg-green-600 transition">
                            <i class="fas fa-globe mr-2"></i>View Live Site
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Image Upload Tips -->
                <div class="bg-green-50 rounded-xl p-6">
                    <h3 class="font-semibold text-green-800 mb-2">
                        <i class="fas fa-camera mr-2"></i>Image Tips
                    </h3>
                    <ul class="text-sm text-green-700 space-y-2">
                        <li>• Logo: Use PNG with transparent background</li>
                        <li>• Hero: 1920x800px landscape for best results</li>
                        <li>• Gallery: Square or portrait images work best</li>
                        <li>• Keep file sizes under 5MB</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Copy public URL to clipboard
function copyPublicUrl() {
    const urlInput = document.getElementById('publicUrl');
    if (urlInput) {
        navigator.clipboard.writeText(urlInput.value).then(function() {
            // Show feedback
            const btn = event.target.closest('button');
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i>';
            btn.classList.remove('bg-primary');
            btn.classList.add('bg-green-500');
            setTimeout(function() {
                btn.innerHTML = originalHtml;
                btn.classList.remove('bg-green-500');
                btn.classList.add('bg-primary');
            }, 2000);
        }).catch(function() {
            // Fallback for older browsers
            urlInput.select();
            document.execCommand('copy');
        });
    }
}

// Auto-format subdomain input
document.addEventListener('DOMContentLoaded', function() {
    const subdomainInput = document.getElementById('subdomainInput');
    if (subdomainInput) {
        subdomainInput.addEventListener('input', function(e) {
            // Convert to lowercase and replace invalid characters
            let value = e.target.value.toLowerCase();
            value = value.replace(/[^a-z0-9-]/g, '');
            value = value.replace(/--+/g, '-');
            e.target.value = value;
        });
    }
    
    // Also handle the update subdomain input
    const updateSubdomainInputs = document.querySelectorAll('input[name="subdomain"]');
    updateSubdomainInputs.forEach(function(input) {
        input.addEventListener('input', function(e) {
            let value = e.target.value.toLowerCase();
            value = value.replace(/[^a-z0-9-]/g, '');
            value = value.replace(/--+/g, '-');
            e.target.value = value;
        });
    });
    
    // Initialize font preview
    updateFontPreview();
});

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
        
        // Load fonts dynamically
        loadGoogleFont(headingFont);
        loadGoogleFont(bodyFont);
        
        // Apply to preview
        headingPreview.style.fontFamily = `'${headingFont}', ${headingCategory}`;
        bodyPreview.style.fontFamily = `'${bodyFont}', ${bodyCategory}`;
    }
}

function applyFontPairing(headingFont, bodyFont) {
    const headingSelect = document.getElementById('fontHeading');
    const bodySelect = document.getElementById('fontBody');
    
    if (headingSelect && bodySelect) {
        headingSelect.value = headingFont;
        bodySelect.value = bodyFont;
        updateFontPreview();
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>
