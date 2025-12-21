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
    ]
];

$hints = $templateHints[$templateId] ?? $templateHints[5]; // Default to general business

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $siteName = sanitize($_POST['site_name'] ?? '');
    $heroTitle = sanitize($_POST['hero_title'] ?? '');
    $heroSubtitle = sanitize($_POST['hero_subtitle'] ?? '');
    $aboutContent = sanitize($_POST['about_content'] ?? '');
    $servicesContent = sanitize($_POST['services_content'] ?? '');
    $contactInfo = sanitize($_POST['contact_info'] ?? '');
    $primaryColor = sanitize($_POST['primary_color'] ?? '#3B82F6');
    $secondaryColor = sanitize($_POST['secondary_color'] ?? '#1E40AF');
    $accentColor = sanitize($_POST['accent_color'] ?? '#F59E0B');
    
    try {
        $stmt = $pdo->prepare("UPDATE client_sites SET 
                               site_name = ?, hero_title = ?, hero_subtitle = ?, 
                               about_content = ?, services_content = ?, contact_info = ?,
                               primary_color = ?, secondary_color = ?, accent_color = ?,
                               updated_at = NOW()
                               WHERE id = ? AND user_id = ?");
        $stmt->execute([
            $siteName, $heroTitle, $heroSubtitle,
            $aboutContent, $servicesContent, $contactInfo,
            $primaryColor, $secondaryColor, $accentColor,
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
        
        <form method="POST" action="">
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
                    
                    <!-- Actions -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">Actions</h2>
                        <div class="space-y-3">
                            <button type="submit" class="w-full gradient-bg text-white py-3 rounded-lg font-semibold hover:opacity-90 transition">
                                <i class="fas fa-save mr-2"></i>Save Changes
                            </button>
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
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
