<?php
/**
 * Template Selection Page
 */
require_once '../config/database.php';
require_once '../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check login BEFORE any output
if (!isLoggedIn()) {
    $_SESSION['error'] = 'Please log in to access this page.';
    header('Location: ../auth/login.php');
    exit();
}

$templateId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$userId = $_SESSION['user_id'];
$error = '';
$success = '';

// Get template info BEFORE any output
try {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT * FROM templates WHERE id = ? AND is_active = 1");
    $stmt->execute([$templateId]);
    $template = $stmt->fetch();
    
    if (!$template) {
        header('Location: ../templates.php');
        exit();
    }
    
    // Get business profile
    $stmt = $pdo->prepare("SELECT * FROM business_profiles WHERE user_id = ?");
    $stmt->execute([$userId]);
    $profile = $stmt->fetch();
    
} catch (Exception $e) {
    header('Location: ../templates.php');
    exit();
}

// Process form submission BEFORE any output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $siteName = sanitize($_POST['site_name'] ?? '');
    $heroTitle = sanitize($_POST['hero_title'] ?? '');
    $heroSubtitle = sanitize($_POST['hero_subtitle'] ?? '');
    $primaryColor = sanitize($_POST['primary_color'] ?? '#3B82F6');
    $secondaryColor = sanitize($_POST['secondary_color'] ?? '#1E40AF');
    $accentColor = sanitize($_POST['accent_color'] ?? '#F59E0B');
    
    if (empty($siteName)) {
        $error = 'Please enter a site name.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO client_sites (user_id, template_id, site_name, hero_title, hero_subtitle, primary_color, secondary_color, accent_color, status) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'draft')");
            $stmt->execute([$userId, $templateId, $siteName, $heroTitle, $heroSubtitle, $primaryColor, $secondaryColor, $accentColor]);
            $siteId = $pdo->lastInsertId();
            
            logActivity($pdo, $userId, 'create_site', "Created new site: $siteName");
            
            $_SESSION['success'] = 'Website created successfully!';
            header("Location: edit-site.php?id=$siteId");
            exit();
            
        } catch (Exception $e) {
            $error = 'Failed to create website. Please try again.';
        }
    }
}

// Template configurations with default colors and hints
$templateConfigs = [
    1 => [
        'name' => 'Modern Retail',
        'primary' => '#2C3E50',
        'secondary' => '#1A252F',
        'accent' => '#E67E22',
        'heroHint' => 'e.g., "Discover Your Style"',
        'subtitleHint' => 'Highlight your best products or promotions',
        'style' => 'Elegant serif fonts, clean product grids'
    ],
    2 => [
        'name' => 'Restaurant Pro',
        'primary' => '#8B4513',
        'secondary' => '#5D2E0C',
        'accent' => '#DAA520',
        'heroHint' => 'e.g., "Authentic Filipino Cuisine"',
        'subtitleHint' => 'Your signature dish or dining experience',
        'style' => 'Warm tones, appetizing layout'
    ],
    3 => [
        'name' => 'Freelancer Portfolio',
        'primary' => '#1A1A2E',
        'secondary' => '#4ECCA3',
        'accent' => '#00D9FF',
        'heroHint' => 'e.g., "Creative Developer & Designer"',
        'subtitleHint' => 'Your tagline and what you do',
        'style' => 'Dark theme, minimalist portfolio'
    ],
    4 => [
        'name' => 'Service Business',
        'primary' => '#1E3A5F',
        'secondary' => '#0F2744',
        'accent' => '#3498DB',
        'heroHint' => 'e.g., "Professional Solutions You Can Trust"',
        'subtitleHint' => 'Your value proposition',
        'style' => 'Corporate, trust-building layout'
    ],
    5 => [
        'name' => 'General Business',
        'primary' => '#3B82F6',
        'secondary' => '#1E40AF',
        'accent' => '#F59E0B',
        'heroHint' => 'e.g., "Welcome to Our Business"',
        'subtitleHint' => 'A short tagline about your company',
        'style' => 'Versatile, professional design'
    ],
    6 => [
        'name' => 'E-Commerce Starter',
        'primary' => '#059669',
        'secondary' => '#047857',
        'accent' => '#FBBF24',
        'heroHint' => 'e.g., "Shop Quality Products Online"',
        'subtitleHint' => 'Free shipping, discounts, or bestsellers',
        'style' => 'Shop-focused, conversion optimized'
    ],
    7 => [
        'name' => 'Urban Streetwear',
        'primary' => '#0D0D0D',
        'secondary' => '#FF3131',
        'accent' => '#FFD700',
        'heroHint' => 'e.g., "DROP 001" or "STREET CULTURE"',
        'subtitleHint' => 'Bold statement - keep it short',
        'style' => 'Bold typography, dark theme'
    ],
    8 => [
        'name' => 'Tech Startup',
        'primary' => '#6366F1',
        'secondary' => '#4F46E5',
        'accent' => '#22D3EE',
        'heroHint' => 'e.g., "Build Faster, Scale Smarter"',
        'subtitleHint' => 'Explain your product value in one line',
        'style' => 'Modern gradients, SaaS style'
    ]
];

$config = $templateConfigs[$templateId] ?? $templateConfigs[5];

// NOW include header (after all redirects are done)
$pageTitle = "Select Template - FilDevStudio";
require_once '../includes/header.php';
?>

<!-- Page Header -->
<section class="gradient-bg py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="../templates.php" class="text-blue-200 hover:text-white mb-4 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Back to Templates
        </a>
        <h1 class="text-2xl md:text-3xl font-bold text-white">Set Up Your Website</h1>
        <p class="text-blue-100">Template: <?php echo htmlspecialchars($template['name']); ?></p>
    </div>
</section>

<section class="py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if ($error): ?>
            <?php echo displayAlert('error', $error); ?>
        <?php endif; ?>
        
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="grid lg:grid-cols-2">
                <!-- Template Preview -->
                <div class="bg-gray-900 p-6 lg:p-8">
                    <h3 class="font-semibold text-white mb-4 flex items-center">
                        <i class="fas fa-eye mr-2"></i>Live Preview
                    </h3>
                    
                    <!-- Dynamic Preview Based on Template -->
                    <div class="rounded-lg overflow-hidden shadow-2xl" id="previewContainer">
                        <?php if ($templateId == 1): // Modern Retail ?>
                        <div class="bg-gray-50">
                            <div class="py-3 px-4 border-b bg-white">
                                <span class="text-sm font-serif italic" id="previewName" style="color: <?php echo $config['primary']; ?>;">Your Store</span>
                            </div>
                            <div class="h-32 flex items-center justify-center px-4" style="background: linear-gradient(to right, #f5f5f0, <?php echo $config['accent']; ?>20);">
                                <div>
                                    <p class="text-xs uppercase tracking-widest mb-1" style="color: <?php echo $config['accent']; ?>;">Welcome</p>
                                    <h4 class="text-lg font-serif" id="previewHero" style="color: <?php echo $config['primary']; ?>;">Discover Your Style</h4>
                                    <p class="text-xs text-gray-500 mt-1" id="previewSubtitle">Premium products for you</p>
                                </div>
                            </div>
                            <div class="p-4 bg-white grid grid-cols-3 gap-2">
                                <div class="aspect-square bg-gray-100 rounded"></div>
                                <div class="aspect-square bg-gray-100 rounded"></div>
                                <div class="aspect-square bg-gray-100 rounded"></div>
                            </div>
                        </div>
                        
                        <?php elseif ($templateId == 2): // Restaurant ?>
                        <div style="background: #FFFBF5;">
                            <div class="py-3 px-4 bg-white/90">
                                <span class="text-sm font-serif" id="previewName" style="color: <?php echo $config['primary']; ?>;">Restaurant Name</span>
                            </div>
                            <div class="h-32 flex items-center justify-center text-white text-center" style="background: linear-gradient(135deg, <?php echo $config['primary']; ?>, <?php echo $config['accent']; ?>);">
                                <div>
                                    <p class="text-xs tracking-widest mb-1" style="color: <?php echo $config['accent']; ?>;">Welcome to</p>
                                    <h4 class="text-lg font-serif" id="previewHero">Your Restaurant</h4>
                                    <p class="text-xs opacity-80 mt-1" id="previewSubtitle">Delicious food awaits</p>
                                </div>
                            </div>
                            <div class="py-2 text-center text-xs text-white" style="background: <?php echo $config['primary']; ?>;">
                                <i class="fas fa-clock mr-1"></i>Open Daily
                            </div>
                        </div>
                        
                        <?php elseif ($templateId == 3): // Freelancer ?>
                        <div style="background: <?php echo $config['primary']; ?>;">
                            <div class="py-3 px-4 flex justify-between items-center border-b border-white/10">
                                <span class="text-white text-sm font-bold" id="previewName">JD.</span>
                                <span class="text-xs px-2 py-1 rounded-full border border-white/30 text-white/80">Let's Talk</span>
                            </div>
                            <div class="p-6">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="w-2 h-2 rounded-full animate-pulse" style="background: <?php echo $config['accent']; ?>;"></span>
                                    <span class="text-xs" style="color: <?php echo $config['accent']; ?>;">Available</span>
                                </div>
                                <h4 class="text-xl font-bold text-white mb-2" id="previewHero">Creative Developer</h4>
                                <p class="text-sm text-gray-400" id="previewSubtitle">Building digital experiences</p>
                            </div>
                        </div>
                        
                        <?php elseif ($templateId == 4): // Service Business ?>
                        <div class="bg-white">
                            <div class="py-3 px-4 flex items-center gap-2 border-b">
                                <div class="w-6 h-6 rounded" style="background: <?php echo $config['primary']; ?>;"></div>
                                <span class="text-sm font-bold" id="previewName">Company</span>
                            </div>
                            <div class="h-32 flex items-center justify-center text-white text-center" style="background: linear-gradient(135deg, <?php echo $config['primary']; ?>, <?php echo $config['secondary']; ?>);">
                                <div>
                                    <p class="text-xs flex items-center justify-center gap-1 mb-2"><i class="fas fa-star text-yellow-400"></i> Trusted</p>
                                    <h4 class="text-lg font-bold" id="previewHero">Professional Solutions</h4>
                                    <p class="text-xs opacity-80 mt-1" id="previewSubtitle">Serving you since 2020</p>
                                </div>
                            </div>
                            <div class="p-3 grid grid-cols-4 gap-2 text-center bg-gray-50">
                                <div><p class="font-bold text-sm" style="color: <?php echo $config['primary']; ?>;">10+</p><p class="text-xs text-gray-500">Years</p></div>
                                <div><p class="font-bold text-sm" style="color: <?php echo $config['primary']; ?>;">500+</p><p class="text-xs text-gray-500">Clients</p></div>
                                <div><p class="font-bold text-sm" style="color: <?php echo $config['primary']; ?>;">98%</p><p class="text-xs text-gray-500">Rating</p></div>
                                <div><p class="font-bold text-sm" style="color: <?php echo $config['primary']; ?>;">24/7</p><p class="text-xs text-gray-500">Support</p></div>
                            </div>
                        </div>
                        
                        <?php elseif ($templateId == 7): // Urban Streetwear ?>
                        <div style="background: <?php echo $config['primary']; ?>;">
                            <div class="py-3 px-4 flex justify-between items-center border-b border-white/10">
                                <span class="text-white text-sm font-black tracking-tighter" id="previewName">BRAND.</span>
                                <div class="flex gap-3 text-gray-500">
                                    <i class="fas fa-search text-xs"></i>
                                    <i class="fas fa-shopping-bag text-xs"></i>
                                </div>
                            </div>
                            <div class="h-32 flex items-center justify-center text-center" style="background: linear-gradient(135deg, <?php echo $config['secondary']; ?>50, <?php echo $config['primary']; ?>);">
                                <div>
                                    <p class="text-xs tracking-widest mb-2" style="color: <?php echo $config['secondary']; ?>;">NEW DROP</p>
                                    <h4 class="text-2xl font-black text-white tracking-tighter" id="previewHero">STREET CULTURE</h4>
                                </div>
                            </div>
                            <div class="py-2 text-center text-xs text-white" style="background: <?php echo $config['secondary']; ?>;">
                                FREE SHIPPING OVER ₱2,000 🔥
                            </div>
                        </div>
                        
                        <?php elseif ($templateId == 8): // Tech Startup ?>
                        <div class="bg-white">
                            <div class="py-3 px-4 flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <div class="w-5 h-5 rounded" style="background: linear-gradient(135deg, <?php echo $config['primary']; ?>, <?php echo $config['accent']; ?>);"></div>
                                    <span class="text-sm font-bold" id="previewName">StartupName</span>
                                </div>
                                <span class="text-xs px-2 py-1 rounded-full text-white" style="background: <?php echo $config['primary']; ?>;">Get Started</span>
                            </div>
                            <div class="py-8 px-4 text-center" style="background: linear-gradient(180deg, <?php echo $config['primary']; ?>15, white);">
                                <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full mb-3" style="background: <?php echo $config['primary']; ?>15; color: <?php echo $config['primary']; ?>;">
                                    <span class="w-1.5 h-1.5 rounded-full" style="background: <?php echo $config['primary']; ?>;"></span> Welcome
                                </span>
                                <h4 class="text-lg font-bold text-gray-900 mb-1" id="previewHero">Build Faster</h4>
                                <p class="text-xs text-gray-500" id="previewSubtitle">Scale your business</p>
                            </div>
                            <div class="mx-4 mb-4 rounded-lg p-3 bg-gray-800">
                                <div class="h-16 bg-gray-700 rounded flex items-center justify-center">
                                    <i class="fas fa-chart-line" style="color: <?php echo $config['primary']; ?>;"></i>
                                </div>
                            </div>
                        </div>
                        
                        <?php else: // Default / General ?>
                        <div class="bg-white">
                            <div class="py-3 px-4 flex items-center gap-2 border-b">
                                <div class="w-6 h-6 rounded-full" style="background: linear-gradient(135deg, <?php echo $config['primary']; ?>, <?php echo $config['accent']; ?>);"></div>
                                <span class="text-sm font-bold" id="previewName">Business Name</span>
                            </div>
                            <div class="h-32 flex items-center justify-center text-center px-4" style="background: linear-gradient(135deg, <?php echo $config['primary']; ?>15, <?php echo $config['accent']; ?>10);">
                                <div>
                                    <span class="inline-block px-2 py-0.5 rounded-full text-xs mb-2" style="background: <?php echo $config['primary']; ?>15; color: <?php echo $config['primary']; ?>;">✨ Welcome</span>
                                    <h4 class="text-lg font-bold text-gray-900" id="previewHero">Your Business</h4>
                                    <p class="text-xs text-gray-500 mt-1" id="previewSubtitle">We're here to help</p>
                                </div>
                            </div>
                            <div class="p-3 grid grid-cols-3 gap-2">
                                <div class="aspect-square bg-gray-100 rounded flex items-center justify-center"><i class="fas fa-star text-gray-300"></i></div>
                                <div class="aspect-square bg-gray-100 rounded flex items-center justify-center"><i class="fas fa-heart text-gray-300"></i></div>
                                <div class="aspect-square bg-gray-100 rounded flex items-center justify-center"><i class="fas fa-bolt text-gray-300"></i></div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mt-4 p-4 bg-white/10 rounded-lg backdrop-blur">
                        <p class="text-sm text-white/80">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong><?php echo $config['style']; ?></strong> - You can fully customize colors and content after setup.
                        </p>
                    </div>
                    
                    <a href="../template-preview.php?id=<?php echo $templateId; ?>" target="_blank" class="mt-4 block text-center text-sm text-blue-300 hover:text-white">
                        <i class="fas fa-external-link-alt mr-1"></i>View Full Template Preview
                    </a>
                </div>
                
                <!-- Setup Form -->
                <div class="p-6 lg:p-8">
                    <h3 class="font-semibold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-magic mr-2 text-primary"></i>Customize Your Site
                    </h3>
                    <form method="POST" action="">
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Site Name *</label>
                                <input type="text" name="site_name" id="siteName"
                                       value="<?php echo htmlspecialchars($profile['business_name'] ?? ''); ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                       placeholder="My Business Website" required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Hero Title</label>
                                <input type="text" name="hero_title" id="heroTitle"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                       placeholder="<?php echo $config['heroHint']; ?>">
                                <p class="text-xs text-gray-500 mt-1"><?php echo $config['heroHint']; ?></p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Hero Subtitle</label>
                                <textarea name="hero_subtitle" id="heroSubtitle" rows="2"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                          placeholder="<?php echo $config['subtitleHint']; ?>"></textarea>
                                <p class="text-xs text-gray-500 mt-1"><?php echo $config['subtitleHint']; ?></p>
                            </div>
                            
                            <div class="border-t pt-5">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Brand Colors</label>
                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Primary</label>
                                        <div class="flex items-center gap-2">
                                            <input type="color" name="primary_color" id="primaryColor" value="<?php echo $config['primary']; ?>"
                                                   class="w-10 h-10 rounded cursor-pointer border-0">
                                            <span class="text-xs text-gray-400" id="primaryHex"><?php echo $config['primary']; ?></span>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Secondary</label>
                                        <div class="flex items-center gap-2">
                                            <input type="color" name="secondary_color" id="secondaryColor" value="<?php echo $config['secondary']; ?>"
                                                   class="w-10 h-10 rounded cursor-pointer border-0">
                                            <span class="text-xs text-gray-400" id="secondaryHex"><?php echo $config['secondary']; ?></span>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Accent</label>
                                        <div class="flex items-center gap-2">
                                            <input type="color" name="accent_color" id="accentColor" value="<?php echo $config['accent']; ?>"
                                                   class="w-10 h-10 rounded cursor-pointer border-0">
                                            <span class="text-xs text-gray-400" id="accentHex"><?php echo $config['accent']; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-8 flex gap-3">
                            <button type="submit" class="flex-1 gradient-bg text-white py-3 rounded-lg font-semibold hover:opacity-90 transition">
                                <i class="fas fa-rocket mr-2"></i>Create Website
                            </button>
                            <a href="../templates.php" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition text-center">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Live preview updates
document.getElementById('siteName').addEventListener('input', function() {
    const previewName = document.getElementById('previewName');
    if (previewName) {
        previewName.textContent = this.value || 'Your Business';
    }
});

document.getElementById('heroTitle').addEventListener('input', function() {
    const previewHero = document.getElementById('previewHero');
    if (previewHero) {
        previewHero.textContent = this.value || 'Your Headline';
    }
});

document.getElementById('heroSubtitle').addEventListener('input', function() {
    const previewSubtitle = document.getElementById('previewSubtitle');
    if (previewSubtitle) {
        previewSubtitle.textContent = this.value || 'Your tagline here';
    }
});

// Color hex display
document.getElementById('primaryColor').addEventListener('input', function() {
    document.getElementById('primaryHex').textContent = this.value;
});
document.getElementById('secondaryColor').addEventListener('input', function() {
    document.getElementById('secondaryHex').textContent = this.value;
});
document.getElementById('accentColor').addEventListener('input', function() {
    document.getElementById('accentHex').textContent = this.value;
});
</script>

<?php require_once '../includes/footer.php'; ?>
