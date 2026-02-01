<?php
/**
 * Preview Client Site - Template-Based Rendering
 * Renders different layouts based on the selected template
 * Now uses modular template files from public/templates/
 */
$siteId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

require_once '../config/database.php';
require_once '../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get site data with template info
try {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT cs.*, bp.business_name, bp.contact_phone, bp.contact_email, bp.address, bp.business_type
                           FROM client_sites cs 
                           LEFT JOIN business_profiles bp ON cs.user_id = bp.user_id 
                           WHERE cs.id = ?");
    $stmt->execute([$siteId]);
    $site = $stmt->fetch();
    
    if (!$site) {
        die('Site not found');
    }
    
    // ============================================
    // CHECK FOR CUSTOM FULL HTML (Admin uploaded)
    // If admin has uploaded a custom index.html, show that instead
    // ============================================
    if (!empty($site['custom_full_html'])) {
        echo $site['custom_full_html'];
        exit;
    }
    
    // Get site images
    $logoImage = getSiteImage($pdo, $siteId, 'logo');
    $heroImage = getSiteImage($pdo, $siteId, 'hero');
    $galleryImages = getSiteImages($pdo, $siteId, 'gallery');
    
} catch (Exception $e) {
    die('Error loading site');
}

$templateId = $site['template_id'] ?? 1;
$primaryColor = $site['primary_color'] ?? '#3B82F6';
$secondaryColor = $site['secondary_color'] ?? '#1E40AF';
$accentColor = $site['accent_color'] ?? '#F59E0B';
$fontHeading = $site['font_heading'] ?? 'Inter';
$fontBody = $site['font_body'] ?? 'Inter';
$siteName = $site['site_name'] ?? 'My Business';
$heroTitle = $site['hero_title'] ?? 'Welcome to ' . $siteName;
$heroSubtitle = $site['hero_subtitle'] ?? 'We provide excellent products and services to help your business grow.';
$aboutContent = $site['about_content'] ?? 'We are a dedicated team committed to providing the best products and services to our customers.';
$servicesContent = $site['services_content'] ?? '';
$contactInfo = $site['contact_info'] ?? '';

// Get social media links
$socialLinks = getSocialLinks($site);

// Get font CSS families
$headingFontFamily = getFontFamily($fontHeading);
$bodyFontFamily = getFontFamily($fontBody);
$googleFontsUrl = getGoogleFontsUrl($fontHeading, $fontBody);

// Check if modular template file exists
$templateFile = __DIR__ . '/../public/templates/template-' . $templateId . '.php';
$useModularTemplate = file_exists($templateFile) && $templateId >= 1 && $templateId <= 5;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($siteName); ?> - Preview</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '<?php echo $primaryColor; ?>',
                        secondary: '<?php echo $secondaryColor; ?>',
                        accent: '<?php echo $accentColor; ?>'
                    },
                    fontFamily: {
                        heading: [<?php echo $headingFontFamily; ?>],
                        body: [<?php echo $bodyFontFamily; ?>]
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="<?php echo htmlspecialchars($googleFontsUrl); ?>" rel="stylesheet">
    <style>
        .gradient-hero { background: linear-gradient(135deg, <?php echo $primaryColor; ?> 0%, <?php echo $secondaryColor; ?> 100%); }
        .font-heading { font-family: <?php echo $headingFontFamily; ?>; }
        .font-body { font-family: <?php echo $bodyFontFamily; ?>; }
        h1, h2, h3, h4, h5, h6, nav a { font-family: <?php echo $headingFontFamily; ?>; }
        body, p, span, li, div { font-family: <?php echo $bodyFontFamily; ?>; }
        .social-icon { transition: all 0.3s ease; }
        .social-icon:hover { transform: scale(1.15); }
    </style>
</head>
<body class="font-body">
    <!-- Preview Banner -->
    <div class="bg-yellow-500 text-yellow-900 text-center py-2 text-sm fixed top-0 left-0 right-0 z-[100]">
        <i class="fas fa-eye mr-2"></i>Preview Mode - 
        <a href="edit-site.php?id=<?php echo $siteId; ?>" class="underline font-medium">Edit this site</a> |
        <a href="dashboard.php" class="underline font-medium ml-2">Back to Dashboard</a>
    </div>
    <div class="h-8"></div>

    <?php 
    // Use modular template if available (templates 1-5)
    if ($useModularTemplate):
        include $templateFile;
    else:
        // Fallback to inline templates for any other template IDs
    ?>
    
    <?php if ($templateId == 4): // ========== SMALL RETAIL SHOP (was Modern Retail template 1) ========== ?>
    <div class="min-h-screen" style="background: #FAFAFA;">
        <!-- Header -->
        <nav class="bg-white border-b sticky top-8 z-50">
            <div class="max-w-6xl mx-auto px-4 md:px-8 py-4 flex justify-between items-center">
                <?php if ($logoImage): ?>
                <img src="../<?php echo htmlspecialchars($logoImage['image_path']); ?>" 
                     alt="<?php echo htmlspecialchars($logoImage['alt_text'] ?? $siteName); ?>"
                     class="h-10 w-auto object-contain">
                <?php else: ?>
                <div class="text-xl md:text-2xl font-serif-display tracking-wide" style="color: <?php echo $primaryColor; ?>;">
                    <em><?php echo htmlspecialchars($siteName); ?></em>
                </div>
                <?php endif; ?>
                <div class="hidden md:flex space-x-8 text-sm font-medium text-gray-600">
                    <a href="#home" class="hover:text-gray-900">Home</a>
                    <a href="#about" class="hover:text-gray-900">About</a>
                    <a href="#services" class="hover:text-gray-900">Services</a>
                    <a href="#contact" class="hover:text-gray-900">Contact</a>
                </div>
                <div class="flex items-center space-x-4 text-gray-600">
                    <?php if (!empty($socialLinks)): ?>
                    <div class="hidden md:flex items-center space-x-3 social-icons-header">
                        <?php foreach (array_slice($socialLinks, 0, 4) as $link): ?>
                        <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo htmlspecialchars($link['label']); ?>" class="social-icon">
                            <i class="<?php echo htmlspecialchars($link['icon']); ?>"></i>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <i class="fas fa-search cursor-pointer hover:text-gray-900"></i>
                    <i class="fas fa-shopping-bag cursor-pointer hover:text-gray-900"></i>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section id="home" class="py-12 md:py-20 px-4 md:px-8" style="background: linear-gradient(to right, #f5f5f0, <?php echo $accentColor; ?>15);">
            <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-8 md:gap-12 items-center">
                <div>
                    <span class="text-sm font-medium tracking-widest uppercase" style="color: <?php echo $accentColor; ?>;">Welcome</span>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-serif-display mt-3 mb-6 leading-tight" style="color: <?php echo $primaryColor; ?>;">
                        <?php echo htmlspecialchars($heroTitle); ?>
                    </h1>
                    <p class="text-gray-600 mb-8 text-lg leading-relaxed">
                        <?php echo htmlspecialchars($heroSubtitle); ?>
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="#services" class="px-8 py-3 text-white font-medium hover:opacity-90 transition rounded" style="background: <?php echo $primaryColor; ?>;">
                            Explore
                        </a>
                        <a href="#contact" class="px-8 py-3 border-2 font-medium hover:bg-gray-100 transition rounded" style="border-color: <?php echo $primaryColor; ?>; color: <?php echo $primaryColor; ?>;">
                            Contact Us
                        </a>
                    </div>
                </div>
                <?php if ($heroImage): ?>
                <div class="h-64 md:h-80 rounded-2xl overflow-hidden shadow-lg">
                    <img src="../<?php echo htmlspecialchars($heroImage['image_path']); ?>" 
                         alt="<?php echo htmlspecialchars($heroImage['alt_text'] ?? 'Hero Image'); ?>"
                         class="w-full h-full object-cover">
                </div>
                <?php else: ?>
                <div class="h-64 md:h-80 rounded-2xl flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, <?php echo $accentColor; ?>30, <?php echo $primaryColor; ?>20);">
                    <div class="text-center text-gray-400">
                        <i class="fas fa-image text-5xl mb-3"></i>
                        <p class="text-sm">Hero Image</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Features Bar -->
        <div class="border-y py-6 px-4 bg-white">
            <div class="max-w-6xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div class="flex flex-col items-center gap-2">
                    <i class="fas fa-truck" style="color: <?php echo $accentColor; ?>;"></i>
                    <span class="text-sm text-gray-600">Free Shipping</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                    <i class="fas fa-undo" style="color: <?php echo $accentColor; ?>;"></i>
                    <span class="text-sm text-gray-600">Easy Returns</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                    <i class="fas fa-shield-alt" style="color: <?php echo $accentColor; ?>;"></i>
                    <span class="text-sm text-gray-600">Secure Payment</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                    <i class="fas fa-headset" style="color: <?php echo $accentColor; ?>;"></i>
                    <span class="text-sm text-gray-600">24/7 Support</span>
                </div>
            </div>
        </div>

        <!-- About Section -->
        <section id="about" class="py-16 px-4 md:px-8 bg-white">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-serif-display mb-4" style="color: <?php echo $primaryColor; ?>;">About Us</h2>
                <div class="w-16 h-1 mx-auto rounded mb-8" style="background: <?php echo $accentColor; ?>;"></div>
                <p class="text-gray-600 text-lg leading-relaxed">
                    <?php echo nl2br(htmlspecialchars($aboutContent)); ?>
                </p>
            </div>
        </section>

        <!-- Services/Products Section -->
        <section id="services" class="py-16 px-4 md:px-8" style="background: #f9f9f7;">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-3xl font-serif-display text-center mb-4" style="color: <?php echo $primaryColor; ?>;">Our Products</h2>
                <div class="w-16 h-1 mx-auto rounded mb-12" style="background: <?php echo $accentColor; ?>;"></div>
                
                <?php if (!empty($servicesContent)): ?>
                    <div class="max-w-3xl mx-auto text-center mb-12">
                        <p class="text-gray-600 text-lg"><?php echo nl2br(htmlspecialchars($servicesContent)); ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($galleryImages)): ?>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                        <?php foreach ($galleryImages as $img): ?>
                        <div class="group cursor-pointer">
                            <div class="aspect-[3/4] rounded-lg overflow-hidden mb-3">
                                <img src="../<?php echo htmlspecialchars($img['image_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($img['alt_text'] ?? 'Product'); ?>"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php elseif (empty($servicesContent)): ?>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                        <?php for($i = 1; $i <= 4; $i++): ?>
                        <div class="group cursor-pointer">
                            <div class="aspect-[3/4] rounded-lg mb-3 flex items-center justify-center" style="background: <?php echo $primaryColor; ?>10;">
                                <i class="fas fa-box text-3xl" style="color: <?php echo $primaryColor; ?>30;"></i>
                            </div>
                            <h3 class="font-medium text-gray-800 text-sm">Product <?php echo $i; ?></h3>
                            <p style="color: <?php echo $accentColor; ?>;" class="font-semibold">₱999</p>
                        </div>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="py-16 px-4 md:px-8 bg-white">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-serif-display mb-4" style="color: <?php echo $primaryColor; ?>;">Contact Us</h2>
                <div class="w-16 h-1 mx-auto rounded mb-8" style="background: <?php echo $accentColor; ?>;"></div>
                
                <div class="rounded-xl p-8" style="background: <?php echo $primaryColor; ?>05;">
                    <?php if (!empty($contactInfo)): ?>
                        <p class="text-gray-600 whitespace-pre-line"><?php echo htmlspecialchars($contactInfo); ?></p>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php if (!empty($site['contact_phone'])): ?>
                                <p><i class="fas fa-phone mr-2" style="color: <?php echo $accentColor; ?>;"></i><?php echo htmlspecialchars($site['contact_phone']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($site['contact_email'])): ?>
                                <p><i class="fas fa-envelope mr-2" style="color: <?php echo $accentColor; ?>;"></i><?php echo htmlspecialchars($site['contact_email']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($site['address'])): ?>
                                <p><i class="fas fa-map-marker-alt mr-2" style="color: <?php echo $accentColor; ?>;"></i><?php echo htmlspecialchars($site['address']); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-10 px-4 text-white" style="background: <?php echo $primaryColor; ?>;">
            <div class="max-w-6xl mx-auto text-center">
                <p class="font-serif-display text-xl mb-4"><em><?php echo htmlspecialchars($siteName); ?></em></p>
                <?php if (!empty($socialLinks)): ?>
                <div class="flex justify-center items-center space-x-4 mb-4 social-icons-footer">
                    <?php foreach ($socialLinks as $link): ?>
                    <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" rel="noopener noreferrer" 
                       title="<?php echo htmlspecialchars($link['label']); ?>"
                       class="w-10 h-10 flex items-center justify-center bg-white/20 hover:bg-white/30 rounded-full transition social-icon">
                        <i class="<?php echo htmlspecialchars($link['icon']); ?> text-lg"></i>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <p class="text-white/60 text-sm">&copy; <?php echo date('Y'); ?> All rights reserved. Powered by FilDevStudio</p>
            </div>
        </footer>
    </div>

    <?php elseif ($templateId == 2): // ========== RESTAURANT PRO ========== ?>
    <div class="min-h-screen" style="background: #FFFBF5;">
        <!-- Header -->
        <nav class="flex justify-between items-center py-4 px-4 md:px-8 bg-white/90 backdrop-blur-sm sticky top-8 z-50 shadow-sm">
            <?php if ($logoImage): ?>
            <img src="../<?php echo htmlspecialchars($logoImage['image_path']); ?>" 
                 alt="<?php echo htmlspecialchars($logoImage['alt_text'] ?? $siteName); ?>"
                 class="h-10 w-auto object-contain">
            <?php else: ?>
            <div class="text-xl md:text-2xl font-serif-display" style="color: <?php echo $primaryColor; ?>;">
                <?php echo htmlspecialchars($siteName); ?>
            </div>
            <?php endif; ?>
            <div class="hidden md:flex space-x-8 text-sm font-medium text-gray-700">
                <a href="#home" class="hover:opacity-70">Home</a>
                <a href="#about" class="hover:opacity-70">About</a>
                <a href="#menu" class="hover:opacity-70">Menu</a>
                <a href="#contact" class="hover:opacity-70">Contact</a>
            </div>
            <div class="flex items-center gap-4">
                <?php if (!empty($socialLinks)): ?>
                <div class="hidden md:flex items-center space-x-3 social-icons-header">
                    <?php foreach (array_slice($socialLinks, 0, 3) as $link): ?>
                    <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo htmlspecialchars($link['label']); ?>" class="social-icon text-gray-600 hover:text-gray-900">
                        <i class="<?php echo htmlspecialchars($link['icon']); ?>"></i>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <button class="px-4 md:px-6 py-2 rounded-full text-sm font-medium text-white transition hover:opacity-90" style="background: <?php echo $accentColor; ?>;">
                    Reserve Table
                </button>
            </div>
        </nav>

        <!-- Hero -->
        <section id="home" class="relative h-80 md:h-[450px] flex items-center justify-center overflow-hidden">
            <?php if ($heroImage): ?>
            <img src="../<?php echo htmlspecialchars($heroImage['image_path']); ?>" 
                 alt="<?php echo htmlspecialchars($heroImage['alt_text'] ?? 'Hero Image'); ?>"
                 class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/50"></div>
            <?php else: ?>
            <div class="absolute inset-0" style="background: linear-gradient(135deg, <?php echo $primaryColor; ?>, <?php echo $accentColor; ?>);"></div>
            <div class="absolute inset-0 bg-black/40"></div>
            <?php endif; ?>
            <div class="relative z-10 text-center text-white px-4">
                <p class="text-sm tracking-widest uppercase mb-3" style="color: <?php echo $accentColor; ?>;">Welcome to</p>
                <h1 class="text-4xl md:text-6xl font-serif-display mb-4"><?php echo htmlspecialchars($heroTitle); ?></h1>
                <p class="text-lg opacity-80 mb-6 max-w-lg mx-auto"><?php echo htmlspecialchars($heroSubtitle); ?></p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="#menu" class="px-6 py-3 rounded-full font-semibold transition text-white" style="background: <?php echo $accentColor; ?>;">View Menu</a>
                    <a href="#contact" class="px-6 py-3 border-2 border-white rounded-full font-semibold hover:bg-white hover:text-gray-900 transition">Book Now</a>
                </div>
            </div>
        </section>

        <!-- Info Bar -->
        <div class="py-4 text-center text-sm" style="background: <?php echo $primaryColor; ?>; color: rgba(255,255,255,0.9);">
            <div class="max-w-4xl mx-auto px-4 flex flex-wrap justify-center gap-6 md:gap-12">
                <span><i class="fas fa-clock mr-2" style="color: <?php echo $accentColor; ?>;"></i>Mon-Sun: 11AM - 11PM</span>
                <?php if (!empty($site['contact_phone'])): ?>
                <span><i class="fas fa-phone mr-2" style="color: <?php echo $accentColor; ?>;"></i><?php echo htmlspecialchars($site['contact_phone']); ?></span>
                <?php endif; ?>
            </div>
        </div>

        <!-- About -->
        <section id="about" class="py-16 px-4 md:px-8">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-serif-display mb-4" style="color: <?php echo $primaryColor; ?>;">Our Story</h2>
                <div class="w-16 h-1 mx-auto rounded mb-8" style="background: <?php echo $accentColor; ?>;"></div>
                <p class="text-gray-600 text-lg leading-relaxed"><?php echo nl2br(htmlspecialchars($aboutContent)); ?></p>
            </div>
        </section>

        <!-- Menu Section -->
        <section id="menu" class="py-16 px-4 md:px-8 bg-white">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-3xl font-serif-display text-center mb-2" style="color: <?php echo $primaryColor; ?>;">Our Menu</h2>
                <p class="text-center text-gray-500 mb-8">Handcrafted dishes made with love</p>
                
                <?php if (!empty($servicesContent)): ?>
                    <div class="text-center text-gray-600"><?php echo nl2br(htmlspecialchars($servicesContent)); ?></div>
                <?php else: ?>
                    <div class="grid md:grid-cols-3 gap-6">
                        <?php 
                        $dishes = [
                            ['name' => 'Appetizers', 'icon' => 'fa-cheese'],
                            ['name' => 'Main Course', 'icon' => 'fa-utensils'],
                            ['name' => 'Desserts', 'icon' => 'fa-ice-cream']
                        ];
                        foreach($dishes as $dish): ?>
                        <div class="rounded-lg p-6 text-center shadow-md hover:shadow-lg transition cursor-pointer" style="background: <?php echo $accentColor; ?>10;">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background: <?php echo $accentColor; ?>20;">
                                <i class="fas <?php echo $dish['icon']; ?> text-2xl" style="color: <?php echo $accentColor; ?>;"></i>
                            </div>
                            <h3 class="font-serif-display text-xl mb-2" style="color: <?php echo $primaryColor; ?>;"><?php echo $dish['name']; ?></h3>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Contact -->
        <section id="contact" class="py-16 px-4 text-white text-center" style="background: <?php echo $primaryColor; ?>;">
            <h2 class="text-3xl font-serif-display mb-4">Make a Reservation</h2>
            <p class="opacity-80 mb-6">Book your table for an unforgettable dining experience</p>
            
            <?php if (!empty($contactInfo)): ?>
                <div class="max-w-lg mx-auto p-6 rounded-xl bg-white/10 backdrop-blur">
                    <p class="whitespace-pre-line"><?php echo htmlspecialchars($contactInfo); ?></p>
                </div>
            <?php else: ?>
                <button class="px-8 py-3 bg-white rounded-full font-semibold hover:bg-gray-100 transition" style="color: <?php echo $primaryColor; ?>;">
                    <i class="fas fa-calendar-alt mr-2"></i>Book Now
                </button>
            <?php endif; ?>
        </section>

        <!-- Footer -->
        <footer class="py-8 px-4 text-center text-white text-sm" style="background: <?php echo $secondaryColor; ?>;">
            <p class="font-serif-display text-lg mb-2"><?php echo htmlspecialchars($siteName); ?></p>
            <?php if (!empty($socialLinks)): ?>
            <div class="flex justify-center items-center space-x-4 mb-3 social-icons-footer">
                <?php foreach ($socialLinks as $link): ?>
                <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" rel="noopener noreferrer" 
                   title="<?php echo htmlspecialchars($link['label']); ?>"
                   class="w-9 h-9 flex items-center justify-center bg-white/20 hover:bg-white/30 rounded-full transition social-icon">
                    <i class="<?php echo htmlspecialchars($link['icon']); ?>"></i>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <p class="opacity-60">&copy; <?php echo date('Y'); ?> All rights reserved. Powered by FilDevStudio</p>
        </footer>
    </div>

    <?php elseif ($templateId == 5): // ========== FREELANCER PORTFOLIO ========== ?>
    <div class="min-h-screen text-white" style="background: <?php echo $primaryColor; ?>;">
        <!-- Header -->
        <nav class="flex justify-between items-center py-6 px-4 md:px-8 sticky top-8 z-50">
            <?php if ($logoImage): ?>
            <img src="../<?php echo htmlspecialchars($logoImage['image_path']); ?>" 
                 alt="<?php echo htmlspecialchars($logoImage['alt_text'] ?? $siteName); ?>"
                 class="h-10 w-auto object-contain">
            <?php else: ?>
            <div class="text-xl font-bold"><?php echo htmlspecialchars(substr($siteName, 0, 2)); ?>.</div>
            <?php endif; ?>
            <div class="hidden md:flex space-x-8 text-sm text-gray-400">
                <a href="#home" class="hover:text-white">Home</a>
                <a href="#about" class="hover:text-white">About</a>
                <a href="#work" class="hover:text-white">Work</a>
                <a href="#contact" class="hover:text-white">Contact</a>
            </div>
            <div class="flex items-center gap-4">
                <?php if (!empty($socialLinks)): ?>
                <div class="hidden md:flex items-center space-x-3 social-icons-header text-gray-400">
                    <?php foreach (array_slice($socialLinks, 0, 4) as $link): ?>
                    <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo htmlspecialchars($link['label']); ?>" class="social-icon hover:text-white">
                        <i class="<?php echo htmlspecialchars($link['icon']); ?>"></i>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <a href="#contact" class="border px-4 py-2 rounded-full text-sm hover:bg-white hover:text-gray-900 transition" style="border-color: <?php echo $secondaryColor; ?>;">
                    Let's Talk
                </a>
            </div>
        </nav>

        <!-- Hero -->
        <section id="home" class="py-16 md:py-24 px-4 md:px-8">
            <div class="max-w-4xl mx-auto">
                <div class="flex items-center gap-3 mb-6">
                    <span class="w-3 h-3 rounded-full animate-pulse" style="background: <?php echo $accentColor; ?>;"></span>
                    <span class="text-sm" style="color: <?php echo $accentColor; ?>;">Available for freelance</span>
                </div>
                <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold leading-tight mb-6">
                    <?php echo htmlspecialchars($heroTitle); ?>
                </h1>
                <p class="text-gray-400 text-lg md:text-xl max-w-2xl mb-8">
                    <?php echo htmlspecialchars($heroSubtitle); ?>
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="#work" class="bg-white text-gray-900 px-6 py-3 rounded-full font-medium hover:bg-gray-200 transition">View Projects</a>
                    <a href="#contact" class="border border-gray-600 px-6 py-3 rounded-full font-medium hover:border-white transition">Contact Me</a>
                </div>
            </div>
        </section>

        <!-- About -->
        <section id="about" class="py-16 px-4 md:px-8" style="background: rgba(255,255,255,0.05);">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-2xl font-bold mb-8">About Me</h2>
                <p class="text-gray-400 text-lg leading-relaxed"><?php echo nl2br(htmlspecialchars($aboutContent)); ?></p>
            </div>
        </section>

        <!-- Work/Services -->
        <section id="work" class="py-16 px-4 md:px-8">
            <div class="max-w-5xl mx-auto">
                <h2 class="text-2xl font-bold mb-8">My Work</h2>
                
                <?php if (!empty($galleryImages)): ?>
                    <div class="grid md:grid-cols-2 gap-6 mb-8">
                        <?php foreach ($galleryImages as $img): ?>
                        <div class="group cursor-pointer rounded-xl overflow-hidden">
                            <img src="../<?php echo htmlspecialchars($img['image_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($img['alt_text'] ?? 'Project'); ?>"
                                 class="w-full h-64 object-cover group-hover:scale-105 transition duration-300">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (!empty($servicesContent)): ?>
                        <p class="text-gray-400"><?php echo nl2br(htmlspecialchars($servicesContent)); ?></p>
                    <?php endif; ?>
                <?php elseif (!empty($servicesContent)): ?>
                    <p class="text-gray-400"><?php echo nl2br(htmlspecialchars($servicesContent)); ?></p>
                <?php else: ?>
                    <div class="grid md:grid-cols-2 gap-6">
                        <?php for($i = 1; $i <= 4; $i++): ?>
                        <div class="group cursor-pointer">
                            <div class="aspect-video rounded-xl mb-4 flex items-center justify-center" style="background: linear-gradient(135deg, <?php echo $secondaryColor; ?>30, <?php echo $accentColor; ?>30);">
                                <i class="fas fa-folder text-4xl" style="color: <?php echo $secondaryColor; ?>;"></i>
                            </div>
                            <h3 class="font-semibold mb-1">Project <?php echo $i; ?></h3>
                            <p class="text-gray-500 text-sm">Design • Development</p>
                        </div>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Contact -->
        <section id="contact" class="py-16 px-4 text-center border-t" style="border-color: rgba(255,255,255,0.1);">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Let's work together</h2>
            <p class="text-gray-400 mb-8">Have a project in mind? Let's create something amazing.</p>
            
            <?php if (!empty($contactInfo)): ?>
                <div class="max-w-lg mx-auto p-6 rounded-xl" style="background: rgba(255,255,255,0.05);">
                    <p class="whitespace-pre-line"><?php echo htmlspecialchars($contactInfo); ?></p>
                </div>
            <?php elseif (!empty($site['contact_email'])): ?>
                <a href="mailto:<?php echo htmlspecialchars($site['contact_email']); ?>" class="inline-flex items-center gap-2 text-lg hover:opacity-70 transition" style="color: <?php echo $secondaryColor; ?>;">
                    <span><?php echo htmlspecialchars($site['contact_email']); ?></span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            <?php endif; ?>
        </section>

        <!-- Footer -->
        <footer class="py-8 px-4 text-center text-gray-500 text-sm border-t" style="border-color: rgba(255,255,255,0.1);">
            <?php if (!empty($socialLinks)): ?>
            <div class="flex justify-center items-center space-x-4 mb-4 social-icons-footer">
                <?php foreach ($socialLinks as $link): ?>
                <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" rel="noopener noreferrer" 
                   title="<?php echo htmlspecialchars($link['label']); ?>"
                   class="w-10 h-10 flex items-center justify-center bg-white/10 hover:bg-white/20 rounded-full transition social-icon text-gray-400 hover:text-white">
                    <i class="<?php echo htmlspecialchars($link['icon']); ?> text-lg"></i>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($siteName); ?>. Powered by FilDevStudio</p>
        </footer>
    </div>

    <?php elseif ($templateId == 3): // ========== LOCAL SERVICES ========== ?>
    <div class="min-h-screen bg-white">
        <!-- Header -->
        <nav class="bg-white shadow-sm sticky top-8 z-50">
            <div class="max-w-6xl mx-auto px-4 md:px-8 py-4 flex justify-between items-center">
                <?php if ($logoImage): ?>
                <img src="../<?php echo htmlspecialchars($logoImage['image_path']); ?>" 
                     alt="<?php echo htmlspecialchars($logoImage['alt_text'] ?? $siteName); ?>"
                     class="h-10 w-auto object-contain">
                <?php else: ?>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: <?php echo $primaryColor; ?>;">
                        <span class="text-white font-bold text-sm"><?php echo strtoupper(substr($siteName, 0, 1)); ?></span>
                    </div>
                    <span class="font-bold text-gray-900"><?php echo htmlspecialchars($siteName); ?></span>
                </div>
                <?php endif; ?>
                <div class="hidden md:flex space-x-8 text-sm font-medium text-gray-600">
                    <a href="#home" class="hover:opacity-70">Home</a>
                    <a href="#services" class="hover:opacity-70">Services</a>
                    <a href="#about" class="hover:opacity-70">About</a>
                    <a href="#contact" class="hover:opacity-70">Contact</a>
                </div>
                <div class="flex items-center gap-4">
                    <?php if (!empty($socialLinks)): ?>
                    <div class="hidden lg:flex items-center space-x-2 social-icons-header text-gray-500">
                        <?php foreach (array_slice($socialLinks, 0, 3) as $link): ?>
                        <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo htmlspecialchars($link['label']); ?>" class="social-icon hover:text-gray-900">
                            <i class="<?php echo htmlspecialchars($link['icon']); ?>"></i>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($site['contact_phone'])): ?>
                    <span class="hidden md:block text-sm text-gray-600"><i class="fas fa-phone mr-2" style="color: <?php echo $primaryColor; ?>;"></i><?php echo htmlspecialchars($site['contact_phone']); ?></span>
                    <?php endif; ?>
                    <a href="#contact" class="text-white px-4 py-2 rounded text-sm font-medium hover:opacity-90 transition" style="background: <?php echo $primaryColor; ?>;">
                        Get Quote
                    </a>
                </div>
            </div>
        </nav>

        <!-- Hero -->
        <section id="home" class="relative py-16 md:py-24 px-4 md:px-8 text-white overflow-hidden">
            <?php if ($heroImage): ?>
            <img src="../<?php echo htmlspecialchars($heroImage['image_path']); ?>" 
                 alt="<?php echo htmlspecialchars($heroImage['alt_text'] ?? 'Hero Image'); ?>"
                 class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/60"></div>
            <?php else: ?>
            <div class="absolute inset-0" style="background: linear-gradient(135deg, <?php echo $primaryColor; ?>, <?php echo $secondaryColor; ?>);"></div>
            <?php endif; ?>
            <div class="relative max-w-4xl mx-auto text-center">
                <div class="flex items-center justify-center gap-2 mb-4">
                    <i class="fas fa-star" style="color: <?php echo $accentColor; ?>;"></i>
                    <span class="text-sm opacity-80">Trusted by businesses</span>
                </div>
                <h1 class="text-3xl md:text-5xl font-bold mb-6 leading-tight"><?php echo htmlspecialchars($heroTitle); ?></h1>
                <p class="text-lg opacity-80 mb-8 max-w-2xl mx-auto"><?php echo htmlspecialchars($heroSubtitle); ?></p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="#services" class="bg-white px-6 py-3 rounded font-semibold hover:bg-gray-100 transition" style="color: <?php echo $primaryColor; ?>;">Our Services</a>
                    <a href="#contact" class="border-2 border-white px-6 py-3 rounded font-semibold hover:bg-white/10 transition">Contact Us</a>
                </div>
            </div>
        </section>

        <!-- Trust Badges -->
        <div class="py-8 px-4 bg-gray-50 border-b">
            <div class="max-w-4xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div>
                    <p class="text-3xl font-bold" style="color: <?php echo $primaryColor; ?>;">10+</p>
                    <p class="text-sm text-gray-500">Years Experience</p>
                </div>
                <div>
                    <p class="text-3xl font-bold" style="color: <?php echo $primaryColor; ?>;">500+</p>
                    <p class="text-sm text-gray-500">Clients Served</p>
                </div>
                <div>
                    <p class="text-3xl font-bold" style="color: <?php echo $primaryColor; ?>;">98%</p>
                    <p class="text-sm text-gray-500">Satisfaction</p>
                </div>
                <div>
                    <p class="text-3xl font-bold" style="color: <?php echo $primaryColor; ?>;">24/7</p>
                    <p class="text-sm text-gray-500">Support</p>
                </div>
            </div>
        </div>

        <!-- About -->
        <section id="about" class="py-16 px-4 md:px-8">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-bold mb-4" style="color: <?php echo $primaryColor; ?>;">About Us</h2>
                <div class="w-16 h-1 mx-auto rounded mb-8" style="background: <?php echo $accentColor; ?>;"></div>
                <p class="text-gray-600 text-lg leading-relaxed"><?php echo nl2br(htmlspecialchars($aboutContent)); ?></p>
            </div>
        </section>

        <!-- Services -->
        <section id="services" class="py-16 px-4 md:px-8 bg-gray-50">
            <div class="max-w-5xl mx-auto">
                <h2 class="text-3xl font-bold text-center mb-2" style="color: <?php echo $primaryColor; ?>;">Our Services</h2>
                <p class="text-center text-gray-500 mb-10">Comprehensive solutions tailored to your needs</p>
                
                <?php if (!empty($servicesContent)): ?>
                    <div class="max-w-3xl mx-auto text-center text-gray-600"><?php echo nl2br(htmlspecialchars($servicesContent)); ?></div>
                <?php else: ?>
                    <div class="grid md:grid-cols-3 gap-6">
                        <?php 
                        $services = [
                            ['icon' => 'fa-chart-line', 'title' => 'Consulting'],
                            ['icon' => 'fa-cogs', 'title' => 'Solutions'],
                            ['icon' => 'fa-headset', 'title' => 'Support'],
                        ];
                        foreach($services as $service): ?>
                        <div class="p-6 bg-white rounded-xl shadow-sm border hover:shadow-lg transition">
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-4" style="background: <?php echo $primaryColor; ?>20;">
                                <i class="fas <?php echo $service['icon']; ?> text-xl" style="color: <?php echo $primaryColor; ?>;"></i>
                            </div>
                            <h3 class="font-bold text-gray-900 mb-2"><?php echo $service['title']; ?></h3>
                            <p class="text-gray-500 text-sm">Professional services tailored to your needs.</p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Contact -->
        <section id="contact" class="py-16 px-4 text-white text-center" style="background: <?php echo $primaryColor; ?>;">
            <h2 class="text-2xl font-bold mb-4">Ready to Get Started?</h2>
            
            <?php if (!empty($contactInfo)): ?>
                <div class="max-w-lg mx-auto p-6 rounded-xl bg-white/10 backdrop-blur">
                    <p class="whitespace-pre-line"><?php echo htmlspecialchars($contactInfo); ?></p>
                </div>
            <?php else: ?>
                <p class="opacity-80 mb-6">Contact us today for a free consultation</p>
                <button class="bg-white px-8 py-3 rounded font-semibold hover:bg-gray-100 transition" style="color: <?php echo $primaryColor; ?>;">
                    Contact Us Now
                </button>
            <?php endif; ?>
        </section>

        <!-- Footer -->
        <footer class="py-10 px-4 bg-gray-900 text-white">
            <div class="max-w-5xl mx-auto text-center">
                <p class="font-bold text-lg mb-2"><?php echo htmlspecialchars($siteName); ?></p>
                <?php if (!empty($socialLinks)): ?>
                <div class="flex justify-center items-center space-x-4 mb-3 social-icons-footer">
                    <?php foreach ($socialLinks as $link): ?>
                    <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" rel="noopener noreferrer" 
                       title="<?php echo htmlspecialchars($link['label']); ?>"
                       class="w-10 h-10 flex items-center justify-center bg-white/10 hover:bg-white/20 rounded-full transition social-icon">
                        <i class="<?php echo htmlspecialchars($link['icon']); ?> text-lg"></i>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <p class="text-gray-400 text-sm">&copy; <?php echo date('Y'); ?> All rights reserved. Powered by FilDevStudio</p>
            </div>
        </footer>
    </div>

    <?php elseif ($templateId == 7): // ========== URBAN STREETWEAR ========== ?>
    <div class="min-h-screen text-white" style="background: <?php echo $primaryColor; ?>;">
        <!-- Header -->
        <nav class="flex justify-between items-center p-4 md:p-6 border-b sticky top-8 z-50" style="border-color: rgba(255,255,255,0.1); background: <?php echo $primaryColor; ?>;">
            <?php if ($logoImage): ?>
            <img src="../<?php echo htmlspecialchars($logoImage['image_path']); ?>" 
                 alt="<?php echo htmlspecialchars($logoImage['alt_text'] ?? $siteName); ?>"
                 class="h-10 w-auto object-contain">
            <?php else: ?>
            <div class="text-2xl md:text-3xl font-black tracking-tighter"><?php echo strtoupper(htmlspecialchars($siteName)); ?>.</div>
            <?php endif; ?>
            <div class="hidden md:flex space-x-8 text-xs uppercase tracking-widest text-gray-400">
                <a href="#home" class="hover:text-white">Home</a>
                <a href="#shop" class="hover:text-white">Shop</a>
                <a href="#about" class="hover:text-white">About</a>
                <a href="#contact" class="hover:text-white">Contact</a>
            </div>
            <div class="flex items-center space-x-4">
                <?php if (!empty($socialLinks)): ?>
                <div class="hidden md:flex items-center space-x-3 social-icons-header text-gray-400">
                    <?php foreach (array_slice($socialLinks, 0, 3) as $link): ?>
                    <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo htmlspecialchars($link['label']); ?>" class="social-icon hover:text-white">
                        <i class="<?php echo htmlspecialchars($link['icon']); ?>"></i>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <i class="fas fa-search text-gray-400 hover:text-white cursor-pointer"></i>
                <i class="fas fa-shopping-bag text-gray-400 hover:text-white cursor-pointer"></i>
            </div>
        </nav>

        <!-- Hero -->
        <section id="home" class="relative min-h-[400px] md:min-h-[500px] flex items-center justify-center overflow-hidden">
            <?php if ($heroImage): ?>
            <img src="../<?php echo htmlspecialchars($heroImage['image_path']); ?>" 
                 alt="<?php echo htmlspecialchars($heroImage['alt_text'] ?? 'Hero Image'); ?>"
                 class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/50"></div>
            <?php else: ?>
            <div class="absolute inset-0" style="background: linear-gradient(135deg, <?php echo $secondaryColor; ?>50, <?php echo $primaryColor; ?>, <?php echo $accentColor; ?>30);"></div>
            <?php endif; ?>
            <div class="relative z-10 text-center px-4">
                <p class="text-sm tracking-widest uppercase mb-4" style="color: <?php echo $secondaryColor; ?>;">New Collection</p>
                <h1 class="text-5xl md:text-8xl font-black tracking-tighter leading-none mb-6">
                    <?php echo strtoupper(htmlspecialchars($heroTitle)); ?>
                </h1>
                <a href="#shop" class="inline-block bg-white text-black px-8 py-3 text-sm uppercase tracking-widest font-bold hover:opacity-80 transition">
                    Shop Now
                </a>
            </div>
        </section>

        <!-- Announcement -->
        <div class="py-2 text-center text-sm" style="background: <?php echo $secondaryColor; ?>;">
            <p>FREE SHIPPING ON ORDERS OVER ₱2,000 🔥</p>
        </div>

        <!-- About -->
        <section id="about" class="py-16 px-4 md:px-8">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-2xl font-black uppercase tracking-tight mb-6">Our Story</h2>
                <p class="text-gray-400 leading-relaxed"><?php echo nl2br(htmlspecialchars($aboutContent)); ?></p>
            </div>
        </section>

        <!-- Shop/Products with Gallery -->
        <section id="shop" class="py-12 px-4 md:px-8">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-black uppercase tracking-tight">New Drops</h2>
                <a href="#" class="text-gray-400 text-sm hover:text-white">View All →</a>
            </div>
            
            <?php if (!empty($galleryImages)): ?>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <?php foreach ($galleryImages as $index => $img): ?>
                    <div class="group cursor-pointer">
                        <div class="aspect-[3/4] rounded mb-3 relative overflow-hidden bg-white/5">
                            <img src="../<?php echo htmlspecialchars($img['image_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($img['alt_text'] ?? 'Product ' . ($index + 1)); ?>"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            <div class="absolute top-2 left-2 text-xs px-2 py-1 rounded" style="background: <?php echo $secondaryColor; ?>;">NEW</div>
                        </div>
                        <h3 class="text-sm font-medium"><?php echo htmlspecialchars($img['alt_text'] ?? 'Product ' . ($index + 1)); ?></h3>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php elseif (!empty($servicesContent)): ?>
                <div class="text-gray-400 text-center"><?php echo nl2br(htmlspecialchars($servicesContent)); ?></div>
            <?php else: ?>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <?php for($i = 1; $i <= 4; $i++): ?>
                    <div class="group cursor-pointer">
                        <div class="aspect-[3/4] rounded mb-3 flex items-center justify-center relative overflow-hidden" style="background: rgba(255,255,255,0.05);">
                            <i class="fas fa-tshirt text-2xl" style="color: rgba(255,255,255,0.2);"></i>
                            <div class="absolute top-2 left-2 text-xs px-2 py-1 rounded" style="background: <?php echo $secondaryColor; ?>;">NEW</div>
                        </div>
                        <h3 class="text-sm font-medium">Product <?php echo $i; ?></h3>
                        <p class="text-gray-500 text-sm">₱2,499</p>
                    </div>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- Contact -->
        <section id="contact" class="py-12 px-4 text-center border-t" style="border-color: rgba(255,255,255,0.1);">
            <h2 class="text-xl font-black uppercase tracking-tight mb-4">Get in Touch</h2>
            <?php if (!empty($contactInfo)): ?>
                <p class="text-gray-400 whitespace-pre-line"><?php echo htmlspecialchars($contactInfo); ?></p>
            <?php elseif (!empty($site['contact_email'])): ?>
                <p class="text-gray-400"><?php echo htmlspecialchars($site['contact_email']); ?></p>
            <?php endif; ?>
            
            <?php if (!empty($socialLinks)): ?>
            <div class="flex justify-center space-x-4 mt-6 social-icons-footer">
                <?php foreach ($socialLinks as $link): ?>
                <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" rel="noopener noreferrer" 
                   title="<?php echo htmlspecialchars($link['label']); ?>"
                   class="text-gray-500 hover:text-white cursor-pointer text-xl social-icon">
                    <i class="<?php echo htmlspecialchars($link['icon']); ?>"></i>
                </a>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="flex justify-center space-x-6 mt-6">
                <i class="fab fa-instagram text-gray-500 hover:text-white cursor-pointer text-xl"></i>
                <i class="fab fa-tiktok text-gray-500 hover:text-white cursor-pointer text-xl"></i>
                <i class="fab fa-twitter text-gray-500 hover:text-white cursor-pointer text-xl"></i>
            </div>
            <?php endif; ?>
        </section>

        <!-- Footer -->
        <footer class="py-8 px-4 border-t text-center" style="border-color: rgba(255,255,255,0.1);">
            <p class="text-gray-600 text-sm">&copy; <?php echo date('Y'); ?> <?php echo strtoupper(htmlspecialchars($siteName)); ?>. Powered by FilDevStudio</p>
        </footer>
    </div>

    <?php elseif ($templateId == 8): // ========== TECH STARTUP ========== ?>
    <div class="min-h-screen bg-white">
        <!-- Header -->
        <nav class="flex justify-between items-center py-4 px-4 md:px-8 max-w-6xl mx-auto sticky top-8 z-50 bg-white">
            <?php if ($logoImage): ?>
            <img src="../<?php echo htmlspecialchars($logoImage['image_path']); ?>" 
                 alt="<?php echo htmlspecialchars($logoImage['alt_text'] ?? $siteName); ?>"
                 class="h-10 w-auto object-contain">
            <?php else: ?>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg" style="background: linear-gradient(135deg, <?php echo $primaryColor; ?>, <?php echo $accentColor; ?>);"></div>
                <span class="font-bold text-gray-900"><?php echo htmlspecialchars($siteName); ?></span>
            </div>
            <?php endif; ?>
            <div class="hidden md:flex space-x-8 text-sm font-medium text-gray-600">
                <a href="#home" class="hover:opacity-70">Home</a>
                <a href="#features" class="hover:opacity-70">Features</a>
                <a href="#about" class="hover:opacity-70">About</a>
                <a href="#contact" class="hover:opacity-70">Contact</a>
            </div>
            <a href="#contact" class="text-white px-4 py-2 rounded-full text-sm font-medium hover:opacity-90 transition" style="background: <?php echo $primaryColor; ?>;">
                Get Started
            </a>
        </nav>

        <!-- Hero -->
        <section id="home" class="py-16 md:py-24 px-4 text-center relative overflow-hidden" style="background: linear-gradient(180deg, <?php echo $primaryColor; ?>10, white);">
            <?php if ($heroImage): ?>
            <div class="absolute inset-0">
                <img src="../<?php echo htmlspecialchars($heroImage['image_path']); ?>" 
                     alt="<?php echo htmlspecialchars($heroImage['alt_text'] ?? 'Hero Image'); ?>"
                     class="w-full h-full object-cover opacity-20">
            </div>
            <?php endif; ?>
            <div class="max-w-4xl mx-auto relative z-10">
                <div class="inline-flex items-center gap-2 px-4 py-1 rounded-full text-sm mb-6" style="background: <?php echo $primaryColor; ?>15; color: <?php echo $primaryColor; ?>;">
                    <span class="w-2 h-2 rounded-full animate-pulse" style="background: <?php echo $primaryColor; ?>;"></span>
                    Welcome
                </div>
                <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6 leading-tight">
                    <?php echo htmlspecialchars($heroTitle); ?>
                </h1>
                <p class="text-xl text-gray-500 mb-10 max-w-2xl mx-auto"><?php echo htmlspecialchars($heroSubtitle); ?></p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="#contact" class="text-white px-8 py-3 rounded-lg font-semibold hover:opacity-90 transition shadow-lg" style="background: <?php echo $primaryColor; ?>; box-shadow: 0 10px 40px <?php echo $primaryColor; ?>40;">
                        Get Started
                    </a>
                    <a href="#features" class="border px-8 py-3 rounded-lg font-semibold hover:bg-gray-50 transition flex items-center justify-center gap-2" style="border-color: #e5e7eb;">
                        <i class="fas fa-play-circle" style="color: <?php echo $primaryColor; ?>;"></i>
                        Learn More
                    </a>
                </div>
            </div>
        </section>

        <!-- Dashboard Preview / Gallery -->
        <div class="px-4 md:px-8 pb-16">
            <div class="max-w-4xl mx-auto rounded-2xl shadow-2xl p-4 md:p-8" style="background: linear-gradient(135deg, #1f2937, #111827);">
                <?php if (!empty($galleryImages)): ?>
                <div class="rounded-xl aspect-video overflow-hidden">
                    <img src="../<?php echo htmlspecialchars($galleryImages[0]['image_path']); ?>" 
                         alt="<?php echo htmlspecialchars($galleryImages[0]['alt_text'] ?? 'Product Preview'); ?>"
                         class="w-full h-full object-cover">
                </div>
                <?php else: ?>
                <div class="rounded-xl aspect-video flex items-center justify-center" style="background: #374151;">
                    <div class="text-center">
                        <i class="fas fa-chart-line text-5xl mb-3" style="color: <?php echo $primaryColor; ?>;"></i>
                        <p class="text-gray-500">Product Preview</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- About -->
        <section id="about" class="py-16 px-4 md:px-8 bg-gray-50">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-bold mb-4" style="color: <?php echo $primaryColor; ?>;">About Us</h2>
                <p class="text-gray-600 text-lg leading-relaxed"><?php echo nl2br(htmlspecialchars($aboutContent)); ?></p>
            </div>
        </section>

        <!-- Features -->
        <section id="features" class="py-16 px-4 md:px-8">
            <div class="max-w-5xl mx-auto">
                <h2 class="text-3xl font-bold text-center mb-10" style="color: <?php echo $primaryColor; ?>;">Features</h2>
                
                <?php if (!empty($servicesContent)): ?>
                    <div class="max-w-3xl mx-auto text-center text-gray-600"><?php echo nl2br(htmlspecialchars($servicesContent)); ?></div>
                <?php else: ?>
                    <div class="grid md:grid-cols-3 gap-8">
                        <?php 
                        $features = [
                            ['icon' => 'fa-rocket', 'title' => 'Fast'],
                            ['icon' => 'fa-shield-alt', 'title' => 'Secure'],
                            ['icon' => 'fa-sync', 'title' => 'Reliable'],
                        ];
                        foreach($features as $feature): ?>
                        <div class="text-center p-6">
                            <div class="w-14 h-14 rounded-xl flex items-center justify-center mx-auto mb-4" style="background: <?php echo $primaryColor; ?>15;">
                                <i class="fas <?php echo $feature['icon']; ?> text-2xl" style="color: <?php echo $primaryColor; ?>;"></i>
                            </div>
                            <h3 class="font-bold text-gray-900 mb-2"><?php echo $feature['title']; ?></h3>
                            <p class="text-gray-500 text-sm">Built for performance and reliability.</p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Contact -->
        <section id="contact" class="py-16 px-4 text-center" style="background: linear-gradient(135deg, <?php echo $primaryColor; ?>, <?php echo $accentColor; ?>);">
            <h2 class="text-2xl font-bold text-white mb-4">Ready to get started?</h2>
            
            <?php if (!empty($contactInfo)): ?>
                <div class="max-w-lg mx-auto p-6 rounded-xl bg-white/10 backdrop-blur text-white">
                    <p class="whitespace-pre-line"><?php echo htmlspecialchars($contactInfo); ?></p>
                </div>
            <?php else: ?>
                <p class="text-white/80 mb-6">Join thousands of satisfied customers</p>
                <button class="bg-white px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition" style="color: <?php echo $primaryColor; ?>;">
                    Start Free Trial
                </button>
            <?php endif; ?>
        </section>

        <!-- Footer -->
        <footer class="py-10 px-4 bg-gray-900 text-center">
            <p class="text-white font-bold mb-2"><?php echo htmlspecialchars($siteName); ?></p>
            <?php if (!empty($socialLinks)): ?>
            <div class="flex justify-center items-center space-x-4 mb-3 social-icons-footer">
                <?php foreach ($socialLinks as $link): ?>
                <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" rel="noopener noreferrer" 
                   title="<?php echo htmlspecialchars($link['label']); ?>"
                   class="text-gray-400 hover:text-white social-icon">
                    <i class="<?php echo htmlspecialchars($link['icon']); ?> text-lg"></i>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <p class="text-gray-500 text-sm">&copy; <?php echo date('Y'); ?> All rights reserved. Powered by FilDevStudio</p>
        </footer>
    </div>

    <?php elseif ($templateId == 9): // ========== BOUTIQUE SHOP ========== ?>
    <div class="min-h-screen" style="background: #FDF8F8;">
        <!-- Header -->
        <nav class="bg-white border-b sticky top-8 z-50">
            <div class="max-w-6xl mx-auto px-4 md:px-8 py-4 flex justify-between items-center">
                <?php if ($logoImage): ?>
                <img src="../<?php echo htmlspecialchars($logoImage['image_path']); ?>" 
                     alt="<?php echo htmlspecialchars($logoImage['alt_text'] ?? $siteName); ?>"
                     class="h-10 w-auto object-contain">
                <?php else: ?>
                <div class="text-xl md:text-2xl font-light tracking-widest" style="color: <?php echo $primaryColor; ?>;">
                    <?php echo htmlspecialchars(strtoupper($siteName)); ?>
                </div>
                <?php endif; ?>
                <div class="hidden md:flex space-x-8 text-sm text-gray-500 tracking-wide">
                    <a href="#home" class="hover:text-gray-900">Home</a>
                    <a href="#about" class="hover:text-gray-900">Collection</a>
                    <a href="#services" class="hover:text-gray-900">New In</a>
                    <a href="#contact" class="hover:text-gray-900">Contact</a>
                </div>
                <div class="flex items-center space-x-5 text-gray-400">
                    <?php if (!empty($socialLinks)): ?>
                    <div class="hidden md:flex items-center space-x-3 social-icons-header">
                        <?php foreach (array_slice($socialLinks, 0, 3) as $link): ?>
                        <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo htmlspecialchars($link['label']); ?>" class="social-icon hover:text-gray-600">
                            <i class="<?php echo htmlspecialchars($link['icon']); ?>"></i>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <i class="fas fa-search cursor-pointer hover:text-gray-600"></i>
                    <i class="fas fa-heart cursor-pointer hover:text-gray-600"></i>
                    <i class="fas fa-shopping-bag cursor-pointer hover:text-gray-600"></i>
                </div>
            </div>
        </nav>

        <!-- Hero -->
        <section id="home" class="py-16 md:py-24 px-4 text-center relative overflow-hidden" style="background: linear-gradient(135deg, <?php echo $primaryColor; ?>10, <?php echo $accentColor; ?>10);">
            <?php if ($heroImage): ?>
            <div class="absolute inset-0">
                <img src="../<?php echo htmlspecialchars($heroImage['image_path']); ?>" 
                     alt="<?php echo htmlspecialchars($heroImage['alt_text'] ?? 'Hero Image'); ?>"
                     class="w-full h-full object-cover opacity-30">
            </div>
            <?php endif; ?>
            <div class="max-w-4xl mx-auto relative z-10">
                <p class="text-sm tracking-widest mb-6" style="color: <?php echo $primaryColor; ?>;">✦ NEW COLLECTION ✦</p>
                <h1 class="text-4xl md:text-6xl font-light tracking-wide mb-6" style="font-family: 'Playfair Display', serif; color: <?php echo $secondaryColor; ?>;">
                    <?php echo htmlspecialchars($heroTitle); ?>
                </h1>
                <p class="text-gray-500 text-lg mb-10 max-w-xl mx-auto"><?php echo htmlspecialchars($heroSubtitle); ?></p>
                <a href="#services" class="inline-block px-10 py-4 text-white text-sm tracking-widest hover:opacity-90 transition" style="background: <?php echo $primaryColor; ?>;">
                    SHOP NOW
                </a>
            </div>
        </section>

        <!-- About -->
        <section id="about" class="py-16 px-4 md:px-8 bg-white">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-2xl font-light tracking-wide mb-6" style="font-family: 'Playfair Display', serif; color: <?php echo $primaryColor; ?>;">Our Story</h2>
                <div class="w-16 h-px mx-auto mb-8" style="background: <?php echo $primaryColor; ?>;"></div>
                <p class="text-gray-600 leading-relaxed max-w-2xl mx-auto"><?php echo nl2br(htmlspecialchars($aboutContent)); ?></p>
            </div>
        </section>

        <!-- Services/Products -->
        <section id="services" class="py-16 px-4 md:px-8" style="background: <?php echo $primaryColor; ?>05;">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-2xl font-light tracking-wide text-center mb-12" style="font-family: 'Playfair Display', serif; color: <?php echo $primaryColor; ?>;">Featured Collection</h2>
                
                <?php if (!empty($galleryImages)): ?>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <?php foreach ($galleryImages as $index => $img): ?>
                        <div class="bg-white p-6 text-center group cursor-pointer hover:shadow-lg transition">
                            <div class="aspect-square mb-4 overflow-hidden">
                                <img src="../<?php echo htmlspecialchars($img['image_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($img['alt_text'] ?? 'Product ' . ($index + 1)); ?>"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            </div>
                            <p class="text-sm tracking-wide mb-1"><?php echo htmlspecialchars($img['alt_text'] ?? 'Product ' . ($index + 1)); ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php elseif (!empty($servicesContent)): ?>
                    <div class="max-w-3xl mx-auto text-center text-gray-600 leading-relaxed"><?php echo nl2br(htmlspecialchars($servicesContent)); ?></div>
                <?php else: ?>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <?php 
                        $products = [
                            ['icon' => 'fa-tshirt', 'title' => 'Tops', 'price' => '₱1,299'],
                            ['icon' => 'fa-gem', 'title' => 'Accessories', 'price' => '₱899'],
                            ['icon' => 'fa-shopping-bag', 'title' => 'Bags', 'price' => '₱2,499'],
                            ['icon' => 'fa-shoe-prints', 'title' => 'Footwear', 'price' => '₱1,899'],
                        ];
                        foreach($products as $product): ?>
                        <div class="bg-white p-6 text-center group cursor-pointer">
                            <div class="aspect-square flex items-center justify-center mb-4" style="background: <?php echo $primaryColor; ?>08;">
                                <i class="fas <?php echo $product['icon']; ?> text-4xl transition group-hover:scale-110" style="color: <?php echo $primaryColor; ?>40;"></i>
                            </div>
                            <p class="text-sm tracking-wide mb-1"><?php echo $product['title']; ?></p>
                            <p class="font-medium" style="color: <?php echo $primaryColor; ?>;"><?php echo $product['price']; ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Contact -->
        <section id="contact" class="py-16 px-4 md:px-8 bg-white">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-2xl font-light tracking-wide mb-6" style="font-family: 'Playfair Display', serif; color: <?php echo $primaryColor; ?>;">Get In Touch</h2>
                <div class="w-16 h-px mx-auto mb-8" style="background: <?php echo $primaryColor; ?>;"></div>
                
                <?php if (!empty($contactInfo)): ?>
                    <p class="text-gray-600 whitespace-pre-line"><?php echo htmlspecialchars($contactInfo); ?></p>
                <?php else: ?>
                    <div class="flex flex-wrap justify-center gap-8 text-gray-500">
                        <?php if (!empty($site['contact_email'])): ?>
                            <p><i class="fas fa-envelope mr-2" style="color: <?php echo $primaryColor; ?>;"></i><?php echo htmlspecialchars($site['contact_email']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($site['contact_phone'])): ?>
                            <p><i class="fas fa-phone mr-2" style="color: <?php echo $primaryColor; ?>;"></i><?php echo htmlspecialchars($site['contact_phone']); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-10 px-4 text-center" style="background: <?php echo $secondaryColor; ?>;">
            <p class="text-white/90 text-sm tracking-widest mb-2"><?php echo htmlspecialchars(strtoupper($siteName)); ?></p>
            <?php if (!empty($socialLinks)): ?>
            <div class="flex justify-center items-center space-x-4 mb-3 social-icons-footer">
                <?php foreach ($socialLinks as $link): ?>
                <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" rel="noopener noreferrer" 
                   title="<?php echo htmlspecialchars($link['label']); ?>"
                   class="text-white/60 hover:text-white social-icon">
                    <i class="<?php echo htmlspecialchars($link['icon']); ?>"></i>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <p class="text-white/50 text-xs">&copy; <?php echo date('Y'); ?> All rights reserved. Powered by FilDevStudio</p>
        </footer>
    </div>

    <?php elseif ($templateId == 10): // ========== ELECTRONICS STORE ========== ?>
    <div class="min-h-screen" style="background: <?php echo $primaryColor; ?>;">
        <!-- Header -->
        <nav class="border-b border-white/10 sticky top-8 z-50" style="background: <?php echo $primaryColor; ?>;">
            <div class="max-w-6xl mx-auto px-4 md:px-8 py-4 flex justify-between items-center">
                <?php if ($logoImage): ?>
                <img src="../<?php echo htmlspecialchars($logoImage['image_path']); ?>" 
                     alt="<?php echo htmlspecialchars($logoImage['alt_text'] ?? $siteName); ?>"
                     class="h-10 w-auto object-contain">
                <?php else: ?>
                <div class="text-xl md:text-2xl font-bold tracking-tight text-white">
                    <?php echo htmlspecialchars($siteName); ?>
                </div>
                <?php endif; ?>
                <div class="hidden md:flex space-x-8 text-sm text-gray-400">
                    <a href="#home" class="hover:text-white">Home</a>
                    <a href="#about" class="hover:text-white">About</a>
                    <a href="#services" class="hover:text-white">Products</a>
                    <a href="#contact" class="hover:text-white">Contact</a>
                </div>
                <div class="flex items-center space-x-4 text-gray-400">
                    <i class="fas fa-search cursor-pointer hover:text-white"></i>
                    <i class="fas fa-shopping-cart cursor-pointer hover:text-white"></i>
                </div>
            </div>
            <div class="border-b border-white/5">
                <div class="max-w-6xl mx-auto px-4 py-2 flex justify-center space-x-8 text-xs text-gray-500">
                    <span>📱 Phones</span><span>💻 Laptops</span><span>🎧 Audio</span><span>🎮 Gaming</span><span>📷 Cameras</span>
                </div>
            </div>
        </nav>

        <!-- Hero -->
        <section id="home" class="py-16 md:py-20 px-4 text-center relative overflow-hidden" style="background: linear-gradient(135deg, <?php echo $secondaryColor; ?>, <?php echo $primaryColor; ?>);">
            <?php if ($heroImage): ?>
            <div class="absolute inset-0">
                <img src="../<?php echo htmlspecialchars($heroImage['image_path']); ?>" 
                     alt="<?php echo htmlspecialchars($heroImage['alt_text'] ?? 'Hero Image'); ?>"
                     class="w-full h-full object-cover opacity-30">
            </div>
            <?php endif; ?>
            <div class="max-w-4xl mx-auto relative z-10">
                <p class="text-sm mb-4" style="color: <?php echo $accentColor; ?>;">🔥 HOT DEALS THIS WEEK</p>
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-6"><?php echo htmlspecialchars($heroTitle); ?></h1>
                <p class="text-gray-400 text-lg mb-8 max-w-xl mx-auto"><?php echo htmlspecialchars($heroSubtitle); ?></p>
                <a href="#services" class="inline-block px-8 py-3 text-sm font-bold rounded hover:opacity-90 transition" style="background: <?php echo $accentColor; ?>; color: <?php echo $primaryColor; ?>;">
                    SHOP NOW
                </a>
            </div>
        </section>

        <!-- About -->
        <section id="about" class="py-16 px-4 md:px-8" style="background: rgba(0,0,0,0.3);">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-2xl font-bold text-white mb-6">About Us</h2>
                <div class="w-16 h-1 mx-auto rounded mb-8" style="background: <?php echo $accentColor; ?>;"></div>
                <p class="text-gray-400 leading-relaxed"><?php echo nl2br(htmlspecialchars($aboutContent)); ?></p>
            </div>
        </section>

        <!-- Services/Products -->
        <section id="services" class="py-16 px-4 md:px-8" style="background: <?php echo $secondaryColor; ?>;">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-2xl font-bold text-white text-center mb-12">Featured Products</h2>
                
                <?php if (!empty($galleryImages)): ?>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <?php foreach ($galleryImages as $index => $img): ?>
                        <div class="p-4 rounded-lg" style="background: <?php echo $primaryColor; ?>;">
                            <div class="aspect-square mb-3 rounded overflow-hidden">
                                <img src="../<?php echo htmlspecialchars($img['image_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($img['alt_text'] ?? 'Product ' . ($index + 1)); ?>"
                                     class="w-full h-full object-cover hover:scale-105 transition duration-300">
                            </div>
                            <p class="text-sm text-white mb-1"><?php echo htmlspecialchars($img['alt_text'] ?? 'Product ' . ($index + 1)); ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php elseif (!empty($servicesContent)): ?>
                    <div class="max-w-3xl mx-auto text-center text-gray-400 leading-relaxed"><?php echo nl2br(htmlspecialchars($servicesContent)); ?></div>
                <?php else: ?>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <?php 
                        $products = [
                            ['icon' => 'fa-mobile-alt', 'title' => 'Smartphone Pro', 'price' => '₱24,999'],
                            ['icon' => 'fa-laptop', 'title' => 'Gaming Laptop', 'price' => '₱54,999'],
                            ['icon' => 'fa-headphones-alt', 'title' => 'Wireless Earbuds', 'price' => '₱3,999'],
                            ['icon' => 'fa-camera', 'title' => 'Action Camera', 'price' => '₱12,499'],
                        ];
                        foreach($products as $product): ?>
                        <div class="p-4 rounded-lg" style="background: <?php echo $primaryColor; ?>;">
                            <div class="aspect-square flex items-center justify-center mb-3 rounded" style="background: rgba(255,255,255,0.05);">
                                <i class="fas <?php echo $product['icon']; ?> text-3xl" style="color: <?php echo $accentColor; ?>;"></i>
                            </div>
                            <p class="text-sm text-white mb-1"><?php echo $product['title']; ?></p>
                            <p class="font-bold" style="color: <?php echo $accentColor; ?>;"><?php echo $product['price']; ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Contact -->
        <section id="contact" class="py-16 px-4 md:px-8" style="background: <?php echo $primaryColor; ?>;">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-2xl font-bold text-white mb-6">Contact Us</h2>
                <div class="w-16 h-1 mx-auto rounded mb-8" style="background: <?php echo $accentColor; ?>;"></div>
                
                <?php if (!empty($contactInfo)): ?>
                    <p class="text-gray-400 whitespace-pre-line"><?php echo htmlspecialchars($contactInfo); ?></p>
                <?php else: ?>
                    <div class="flex flex-wrap justify-center gap-8 text-gray-400">
                        <?php if (!empty($site['contact_email'])): ?>
                            <p><i class="fas fa-envelope mr-2" style="color: <?php echo $accentColor; ?>;"></i><?php echo htmlspecialchars($site['contact_email']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($site['contact_phone'])): ?>
                            <p><i class="fas fa-phone mr-2" style="color: <?php echo $accentColor; ?>;"></i><?php echo htmlspecialchars($site['contact_phone']); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-8 px-4 text-center border-t border-white/10" style="background: <?php echo $secondaryColor; ?>;">
            <p class="text-white font-bold mb-2"><?php echo htmlspecialchars($siteName); ?></p>
            <?php if (!empty($socialLinks)): ?>
            <div class="flex justify-center items-center space-x-4 mb-3 social-icons-footer">
                <?php foreach ($socialLinks as $link): ?>
                <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" rel="noopener noreferrer" 
                   title="<?php echo htmlspecialchars($link['label']); ?>"
                   class="text-gray-500 hover:text-white social-icon">
                    <i class="<?php echo htmlspecialchars($link['icon']); ?>"></i>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <p class="text-gray-500 text-xs">&copy; <?php echo date('Y'); ?> All rights reserved. Powered by FilDevStudio</p>
        </footer>
    </div>

    <?php elseif ($templateId == 11): // ========== GROCERY & SUPERMARKET ========== ?>
    <div class="min-h-screen bg-white">
        <!-- Header -->
        <nav class="sticky top-8 z-50" style="background: <?php echo $primaryColor; ?>;">
            <div class="max-w-6xl mx-auto px-4 md:px-8 py-4 flex justify-between items-center">
                <?php if ($logoImage): ?>
                <img src="../<?php echo htmlspecialchars($logoImage['image_path']); ?>" 
                     alt="<?php echo htmlspecialchars($logoImage['alt_text'] ?? $siteName); ?>"
                     class="h-10 w-auto object-contain bg-white/10 rounded px-2">
                <?php else: ?>
                <div class="text-xl md:text-2xl font-bold text-white flex items-center gap-2">
                    🛒 <?php echo htmlspecialchars($siteName); ?>
                </div>
                <?php endif; ?>
                <div class="hidden md:flex space-x-8 text-sm text-white/80">
                    <a href="#home" class="hover:text-white">Home</a>
                    <a href="#about" class="hover:text-white">About</a>
                    <a href="#services" class="hover:text-white">Products</a>
                    <a href="#contact" class="hover:text-white">Contact</a>
                </div>
                <div class="flex items-center space-x-4 text-white/80">
                    <i class="fas fa-search cursor-pointer hover:text-white"></i>
                    <div class="relative">
                        <i class="fas fa-shopping-basket cursor-pointer hover:text-white"></i>
                        <span class="absolute -top-2 -right-2 w-5 h-5 rounded-full text-xs flex items-center justify-center" style="background: <?php echo $accentColor; ?>; color: <?php echo $primaryColor; ?>;">3</span>
                    </div>
                </div>
            </div>
            <div class="border-t border-white/10">
                <div class="max-w-6xl mx-auto px-4 py-2 flex justify-center space-x-6 text-xs text-white/70">
                    <span>🥬 Fresh Produce</span><span>🥩 Meat & Seafood</span><span>🥛 Dairy</span><span>🍞 Bakery</span><span>🧴 Household</span>
                </div>
            </div>
        </nav>

        <!-- Hero -->
        <section id="home" class="py-12 md:py-16 px-4 text-center relative overflow-hidden" style="background: linear-gradient(135deg, <?php echo $primaryColor; ?>15, <?php echo $accentColor; ?>15);">
            <?php if ($heroImage): ?>
            <div class="absolute inset-0">
                <img src="../<?php echo htmlspecialchars($heroImage['image_path']); ?>" 
                     alt="<?php echo htmlspecialchars($heroImage['alt_text'] ?? 'Hero Image'); ?>"
                     class="w-full h-full object-cover opacity-20">
            </div>
            <?php endif; ?>
            <div class="max-w-4xl mx-auto relative z-10">
                <p class="inline-block px-4 py-1 rounded-full text-sm mb-4" style="background: <?php echo $accentColor; ?>; color: white;">🏷️ DAILY DEALS - UP TO 50% OFF!</p>
                <h1 class="text-3xl md:text-5xl font-bold mb-4" style="color: <?php echo $secondaryColor; ?>;"><?php echo htmlspecialchars($heroTitle); ?></h1>
                <p class="text-gray-600 text-lg mb-8 max-w-xl mx-auto"><?php echo htmlspecialchars($heroSubtitle); ?></p>
                <a href="#services" class="inline-block px-8 py-3 text-white font-semibold rounded-lg hover:opacity-90 transition" style="background: <?php echo $primaryColor; ?>;">
                    Shop Now
                </a>
            </div>
        </section>

        <!-- About -->
        <section id="about" class="py-16 px-4 md:px-8 bg-green-50">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-2xl font-bold mb-4" style="color: <?php echo $primaryColor; ?>;">About Our Store</h2>
                <div class="w-16 h-1 mx-auto rounded mb-8" style="background: <?php echo $accentColor; ?>;"></div>
                <p class="text-gray-600 leading-relaxed"><?php echo nl2br(htmlspecialchars($aboutContent)); ?></p>
            </div>
        </section>

        <!-- Services/Products -->
        <section id="services" class="py-16 px-4 md:px-8">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-2xl font-bold text-center mb-12" style="color: <?php echo $primaryColor; ?>;">Fresh Products</h2>
                
                <?php if (!empty($galleryImages)): ?>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <?php foreach ($galleryImages as $index => $img): ?>
                        <div class="p-4 rounded-xl text-center cursor-pointer hover:shadow-md transition bg-white">
                            <div class="aspect-square mb-3 rounded-lg overflow-hidden">
                                <img src="../<?php echo htmlspecialchars($img['image_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($img['alt_text'] ?? 'Product ' . ($index + 1)); ?>"
                                     class="w-full h-full object-cover hover:scale-105 transition duration-300">
                            </div>
                            <p class="font-medium text-gray-800"><?php echo htmlspecialchars($img['alt_text'] ?? 'Product ' . ($index + 1)); ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php elseif (!empty($servicesContent)): ?>
                    <div class="max-w-3xl mx-auto text-center text-gray-600 leading-relaxed"><?php echo nl2br(htmlspecialchars($servicesContent)); ?></div>
                <?php else: ?>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <?php 
                        $products = [
                            ['emoji' => '🍎', 'title' => 'Fresh Apples', 'price' => '₱99/kg', 'bg' => '#FEE2E2'],
                            ['emoji' => '🥕', 'title' => 'Carrots', 'price' => '₱45/kg', 'bg' => '#FED7AA'],
                            ['emoji' => '🥬', 'title' => 'Vegetables', 'price' => '₱65/bundle', 'bg' => '#D1FAE5'],
                            ['emoji' => '🍞', 'title' => 'Fresh Bread', 'price' => '₱55/loaf', 'bg' => '#FEF3C7'],
                        ];
                        foreach($products as $product): ?>
                        <div class="p-4 rounded-xl text-center cursor-pointer hover:shadow-md transition" style="background: <?php echo $product['bg']; ?>;">
                            <p class="text-4xl mb-3"><?php echo $product['emoji']; ?></p>
                            <p class="font-medium text-gray-800"><?php echo $product['title']; ?></p>
                            <p class="font-bold" style="color: <?php echo $primaryColor; ?>;"><?php echo $product['price']; ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Contact -->
        <section id="contact" class="py-16 px-4 md:px-8" style="background: <?php echo $primaryColor; ?>;">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-2xl font-bold text-white mb-6">Visit Us Today!</h2>
                <div class="w-16 h-1 mx-auto rounded mb-8" style="background: <?php echo $accentColor; ?>;"></div>
                
                <?php if (!empty($contactInfo)): ?>
                    <p class="text-white/80 whitespace-pre-line"><?php echo htmlspecialchars($contactInfo); ?></p>
                <?php else: ?>
                    <div class="flex flex-wrap justify-center gap-8 text-white/80">
                        <?php if (!empty($site['address'])): ?>
                            <p><i class="fas fa-map-marker-alt mr-2" style="color: <?php echo $accentColor; ?>;"></i><?php echo htmlspecialchars($site['address']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($site['contact_phone'])): ?>
                            <p><i class="fas fa-phone mr-2" style="color: <?php echo $accentColor; ?>;"></i><?php echo htmlspecialchars($site['contact_phone']); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-8 px-4 text-center" style="background: <?php echo $secondaryColor; ?>;">
            <p class="text-white font-bold mb-2">🛒 <?php echo htmlspecialchars($siteName); ?></p>
            <?php if (!empty($socialLinks)): ?>
            <div class="flex justify-center items-center space-x-4 mb-3 social-icons-footer">
                <?php foreach ($socialLinks as $link): ?>
                <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" rel="noopener noreferrer" 
                   title="<?php echo htmlspecialchars($link['label']); ?>"
                   class="text-white/70 hover:text-white social-icon">
                    <i class="<?php echo htmlspecialchars($link['icon']); ?>"></i>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <p class="text-white/60 text-xs">&copy; <?php echo date('Y'); ?> All rights reserved. Powered by FilDevStudio</p>
        </footer>
    </div>

    <?php elseif ($templateId == 1): // ========== SARI-SARI STORE ========== ?>
    <div class="min-h-screen" style="background: linear-gradient(180deg, <?php echo $primaryColor; ?>, <?php echo $accentColor; ?>);">
        <!-- Header -->
        <nav class="sticky top-8 z-50" style="background: <?php echo $primaryColor; ?>;">
            <div class="max-w-6xl mx-auto px-4 md:px-8 py-4 flex justify-between items-center">
                <?php if ($logoImage): ?>
                <img src="../<?php echo htmlspecialchars($logoImage['image_path']); ?>" 
                     alt="<?php echo htmlspecialchars($logoImage['alt_text'] ?? $siteName); ?>"
                     class="h-10 w-auto object-contain bg-white/10 rounded px-2">
                <?php else: ?>
                <div class="text-xl md:text-2xl font-bold text-white flex items-center gap-2">
                    🏪 <?php echo htmlspecialchars($siteName); ?>
                </div>
                <?php endif; ?>
                <span class="px-3 py-1 rounded-full text-sm font-bold" style="background: white; color: <?php echo $primaryColor; ?>;">
                    BUKAS NA! 🎉
                </span>
            </div>
        </nav>

        <!-- Hero -->
        <section id="home" class="py-10 md:py-16 px-4">
            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-2xl p-6 md:p-10 text-center shadow-xl">
                    <?php if ($heroImage): ?>
                    <div class="mb-6 rounded-xl overflow-hidden">
                        <img src="../<?php echo htmlspecialchars($heroImage['image_path']); ?>" 
                             alt="<?php echo htmlspecialchars($heroImage['alt_text'] ?? 'Store Image'); ?>"
                             class="w-full h-48 object-cover">
                    </div>
                    <?php endif; ?>
                    <p class="inline-block px-4 py-1 rounded-full text-sm mb-4" style="background: <?php echo $primaryColor; ?>15; color: <?php echo $primaryColor; ?>;">📦 TINGI PRICES - MURA NA!</p>
                    <h1 class="text-3xl md:text-4xl font-bold mb-4" style="color: <?php echo $secondaryColor; ?>;"><?php echo htmlspecialchars($heroTitle); ?></h1>
                    <p class="text-gray-600 text-lg mb-6"><?php echo htmlspecialchars($heroSubtitle); ?></p>
                    
                    <!-- Services Icons -->
                    <div class="flex flex-wrap justify-center gap-4 mb-6">
                        <div class="px-4 py-2 rounded-full flex items-center gap-2 text-sm font-medium" style="background: <?php echo $primaryColor; ?>10; color: <?php echo $primaryColor; ?>;">
                            📱 E-Load Available
                        </div>
                        <div class="px-4 py-2 rounded-full flex items-center gap-2 text-sm font-medium bg-blue-50 text-blue-600">
                            💳 GCash Accepted
                        </div>
                        <div class="px-4 py-2 rounded-full flex items-center gap-2 text-sm font-medium bg-green-50 text-green-600">
                            📶 Piso WiFi
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- About -->
        <section id="about" class="py-12 px-4 md:px-8">
            <div class="max-w-4xl mx-auto bg-white/95 backdrop-blur rounded-2xl p-6 md:p-8 text-center">
                <h2 class="text-2xl font-bold mb-4" style="color: <?php echo $primaryColor; ?>;">Tungkol Sa Amin</h2>
                <div class="w-16 h-1 mx-auto rounded mb-6" style="background: <?php echo $accentColor; ?>;"></div>
                <p class="text-gray-600 leading-relaxed"><?php echo nl2br(htmlspecialchars($aboutContent)); ?></p>
            </div>
        </section>

        <!-- Services/Products -->
        <section id="services" class="py-12 px-4 md:px-8">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-2xl font-bold text-white text-center mb-8">Mga Paninda Namin</h2>
                
                <?php if (!empty($servicesContent)): ?>
                    <div class="bg-white/95 rounded-2xl p-6 text-center text-gray-600 leading-relaxed"><?php echo nl2br(htmlspecialchars($servicesContent)); ?></div>
                <?php else: ?>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <?php 
                        $products = [
                            ['emoji' => '🍬', 'title' => 'Candy & Snacks', 'price' => '₱1 - ₱20'],
                            ['emoji' => '🥤', 'title' => 'Softdrinks', 'price' => '₱15 - ₱35'],
                            ['emoji' => '🧴', 'title' => 'Sachet Items', 'price' => '₱5 - ₱15'],
                            ['emoji' => '🍜', 'title' => 'Instant Noodles', 'price' => '₱12 - ₱25'],
                            ['emoji' => '📱', 'title' => 'E-Load All Networks', 'price' => '₱10 - ₱500'],
                            ['emoji' => '🍞', 'title' => 'Bread & Pandesal', 'price' => '₱3 - ₱15'],
                            ['emoji' => '🧊', 'title' => 'Ice & Cold Drinks', 'price' => '₱5 - ₱25'],
                            ['emoji' => '🥚', 'title' => 'Eggs (Tingi)', 'price' => '₱8/piraso'],
                        ];
                        foreach($products as $product): ?>
                        <div class="bg-white/95 backdrop-blur p-4 rounded-xl text-center">
                            <p class="text-3xl mb-2"><?php echo $product['emoji']; ?></p>
                            <p class="font-medium text-gray-800 text-sm"><?php echo $product['title']; ?></p>
                            <p class="font-bold text-sm" style="color: <?php echo $primaryColor; ?>;"><?php echo $product['price']; ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Contact -->
        <section id="contact" class="py-12 px-4 md:px-8">
            <div class="max-w-4xl mx-auto bg-white rounded-2xl p-6 md:p-8 text-center shadow-xl">
                <h2 class="text-2xl font-bold mb-4" style="color: <?php echo $primaryColor; ?>;">Bisitahin Kami!</h2>
                <div class="w-16 h-1 mx-auto rounded mb-6" style="background: <?php echo $accentColor; ?>;"></div>
                
                <?php if (!empty($contactInfo)): ?>
                    <p class="text-gray-600 whitespace-pre-line"><?php echo htmlspecialchars($contactInfo); ?></p>
                <?php else: ?>
                    <div class="space-y-3 text-gray-600">
                        <?php if (!empty($site['address'])): ?>
                            <p><i class="fas fa-map-marker-alt mr-2" style="color: <?php echo $primaryColor; ?>;"></i><?php echo htmlspecialchars($site['address']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($site['contact_phone'])): ?>
                            <p><i class="fas fa-phone mr-2" style="color: <?php echo $primaryColor; ?>;"></i><?php echo htmlspecialchars($site['contact_phone']); ?></p>
                        <?php endif; ?>
                        <p class="text-sm pt-4" style="color: <?php echo $primaryColor; ?>;">⏰ Bukas: 6AM - 10PM Daily</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-8 px-4 text-center" style="background: <?php echo $secondaryColor; ?>;">
            <p class="text-white font-bold mb-2">🏪 <?php echo htmlspecialchars($siteName); ?></p>
            <?php if (!empty($socialLinks)): ?>
            <div class="flex justify-center items-center space-x-4 mb-3 social-icons-footer">
                <?php foreach ($socialLinks as $link): ?>
                <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" rel="noopener noreferrer" 
                   title="<?php echo htmlspecialchars($link['label']); ?>"
                   class="text-white/70 hover:text-white social-icon">
                    <i class="<?php echo htmlspecialchars($link['icon']); ?>"></i>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <p class="text-white/60 text-xs">&copy; <?php echo date('Y'); ?> Lahat ng karapatan ay nakalaan. Powered by FilDevStudio</p>
        </footer>
    </div>

    <?php elseif ($templateId == 13): // ========== SARI-SARI PLUS (Modern) ========== ?>
    <div class="min-h-screen bg-white">
        <!-- Header -->
        <nav class="sticky top-8 z-50 shadow-sm" style="background: <?php echo $primaryColor; ?>;">
            <div class="max-w-6xl mx-auto px-4 md:px-8 py-4 flex justify-between items-center">
                <?php if ($logoImage): ?>
                <img src="../<?php echo htmlspecialchars($logoImage['image_path']); ?>" 
                     alt="<?php echo htmlspecialchars($logoImage['alt_text'] ?? $siteName); ?>"
                     class="h-10 w-auto object-contain bg-white/10 rounded px-2">
                <?php else: ?>
                <div class="text-xl md:text-2xl font-bold text-white flex items-center gap-2">
                    🛒 <?php echo htmlspecialchars($siteName); ?>
                </div>
                <?php endif; ?>
                <div class="hidden md:flex space-x-6 text-sm text-white/80">
                    <a href="#home" class="hover:text-white">Home</a>
                    <a href="#about" class="hover:text-white">About</a>
                    <a href="#services" class="hover:text-white">Products</a>
                    <a href="#contact" class="hover:text-white">Contact</a>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-white text-sm hidden md:block"><i class="fas fa-motorcycle mr-1"></i> We Deliver!</span>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-white" style="color: <?php echo $primaryColor; ?>;">ORDER NOW</span>
                </div>
            </div>
            <div class="border-t border-white/10">
                <div class="max-w-6xl mx-auto px-4 py-2 flex justify-center space-x-6 text-xs text-white/70">
                    <span>🛍️ Groceries</span><span>📱 E-Load</span><span>💳 Bills Payment</span><span>🚚 Delivery</span>
                </div>
            </div>
        </nav>

        <!-- Hero -->
        <section id="home" class="py-12 md:py-16 px-4 text-center relative overflow-hidden" style="background: linear-gradient(135deg, <?php echo $primaryColor; ?>15, <?php echo $accentColor; ?>15);">
            <?php if ($heroImage): ?>
            <div class="absolute inset-0">
                <img src="../<?php echo htmlspecialchars($heroImage['image_path']); ?>" 
                     alt="<?php echo htmlspecialchars($heroImage['alt_text'] ?? 'Hero Image'); ?>"
                     class="w-full h-full object-cover opacity-20">
            </div>
            <?php endif; ?>
            <div class="max-w-4xl mx-auto relative z-10">
                <div class="flex flex-wrap justify-center gap-2 mb-6">
                    <span class="px-3 py-1 rounded-full text-xs font-medium" style="background: <?php echo $primaryColor; ?>15; color: <?php echo $primaryColor; ?>;">💳 GCash</span>
                    <span class="px-3 py-1 rounded-full text-xs font-medium" style="background: <?php echo $accentColor; ?>15; color: <?php echo $accentColor; ?>;">🚚 Delivery</span>
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-600">⏰ 24/7</span>
                </div>
                <h1 class="text-3xl md:text-5xl font-bold mb-4" style="color: <?php echo $secondaryColor; ?>;"><?php echo htmlspecialchars($heroTitle); ?></h1>
                <p class="text-gray-600 text-lg mb-8 max-w-xl mx-auto"><?php echo htmlspecialchars($heroSubtitle); ?></p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="#services" class="px-8 py-3 text-white font-semibold rounded-lg hover:opacity-90 transition" style="background: <?php echo $primaryColor; ?>;">
                        <i class="fas fa-shopping-bag mr-2"></i>Shop Now
                    </a>
                    <a href="#contact" class="px-8 py-3 font-semibold rounded-lg border-2 hover:bg-gray-50 transition" style="border-color: <?php echo $primaryColor; ?>; color: <?php echo $primaryColor; ?>;">
                        <i class="fas fa-motorcycle mr-2"></i>Order Delivery
                    </a>
                </div>
            </div>
        </section>

        <!-- About -->
        <section id="about" class="py-16 px-4 md:px-8 bg-gray-50">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-2xl font-bold mb-4" style="color: <?php echo $primaryColor; ?>;">About Us</h2>
                <div class="w-16 h-1 mx-auto rounded mb-8" style="background: <?php echo $accentColor; ?>;"></div>
                <p class="text-gray-600 leading-relaxed"><?php echo nl2br(htmlspecialchars($aboutContent)); ?></p>
            </div>
        </section>

        <!-- Services/Products -->
        <section id="services" class="py-16 px-4 md:px-8">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-2xl font-bold text-center mb-12" style="color: <?php echo $primaryColor; ?>;">Our Products & Services</h2>
                
                <?php if (!empty($galleryImages)): ?>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <?php foreach ($galleryImages as $index => $img): ?>
                        <div class="p-4 rounded-xl text-center hover:shadow-md transition bg-white">
                            <div class="aspect-square mb-3 rounded-lg overflow-hidden">
                                <img src="../<?php echo htmlspecialchars($img['image_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($img['alt_text'] ?? 'Product ' . ($index + 1)); ?>"
                                     class="w-full h-full object-cover hover:scale-105 transition duration-300">
                            </div>
                            <p class="font-bold text-gray-800"><?php echo htmlspecialchars($img['alt_text'] ?? 'Product ' . ($index + 1)); ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php elseif (!empty($servicesContent)): ?>
                    <div class="max-w-3xl mx-auto text-center text-gray-600 leading-relaxed"><?php echo nl2br(htmlspecialchars($servicesContent)); ?></div>
                <?php else: ?>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <?php 
                        $services = [
                            ['icon' => 'fa-shopping-basket', 'title' => 'Groceries', 'desc' => 'Daily essentials'],
                            ['icon' => 'fa-mobile-alt', 'title' => 'E-Load', 'desc' => 'All networks'],
                            ['icon' => 'fa-file-invoice-dollar', 'title' => 'Bills Payment', 'desc' => 'Bayad Center'],
                            ['icon' => 'fa-motorcycle', 'title' => 'Delivery', 'desc' => 'Within 2km'],
                        ];
                        foreach($services as $service): ?>
                        <div class="p-6 rounded-xl text-center hover:shadow-md transition" style="background: <?php echo $primaryColor; ?>08;">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3" style="background: <?php echo $primaryColor; ?>;">
                                <i class="fas <?php echo $service['icon']; ?> text-white"></i>
                            </div>
                            <p class="font-bold text-gray-800"><?php echo $service['title']; ?></p>
                            <p class="text-sm text-gray-500"><?php echo $service['desc']; ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Contact -->
        <section id="contact" class="py-16 px-4 md:px-8" style="background: <?php echo $primaryColor; ?>;">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-2xl font-bold text-white mb-6">Order or Visit Us</h2>
                <div class="w-16 h-1 mx-auto rounded mb-8" style="background: <?php echo $accentColor; ?>;"></div>
                
                <?php if (!empty($contactInfo)): ?>
                    <p class="text-white/80 whitespace-pre-line"><?php echo htmlspecialchars($contactInfo); ?></p>
                <?php else: ?>
                    <div class="flex flex-wrap justify-center gap-6 text-white/80 mb-6">
                        <?php if (!empty($site['contact_phone'])): ?>
                            <p><i class="fas fa-phone mr-2" style="color: <?php echo $accentColor; ?>;"></i><?php echo htmlspecialchars($site['contact_phone']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($site['contact_email'])): ?>
                            <p><i class="fas fa-envelope mr-2" style="color: <?php echo $accentColor; ?>;"></i><?php echo htmlspecialchars($site['contact_email']); ?></p>
                        <?php endif; ?>
                    </div>
                    <p class="text-white/60 text-sm">💬 Message us on Facebook or Viber for orders!</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-8 px-4 text-center" style="background: <?php echo $secondaryColor; ?>;">
            <p class="text-white font-bold mb-2">🛒 <?php echo htmlspecialchars($siteName); ?></p>
            <?php if (!empty($socialLinks)): ?>
            <div class="flex justify-center items-center space-x-4 mb-3 social-icons-footer">
                <?php foreach ($socialLinks as $link): ?>
                <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" rel="noopener noreferrer" 
                   title="<?php echo htmlspecialchars($link['label']); ?>"
                   class="text-white/70 hover:text-white social-icon">
                    <i class="<?php echo htmlspecialchars($link['icon']); ?>"></i>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <p class="text-white/60 text-xs">&copy; <?php echo date('Y'); ?> All rights reserved. Powered by FilDevStudio</p>
        </footer>
    </div>

    <?php else: // ========== DEFAULT / GENERAL BUSINESS (Template 5, 6, or fallback) ========== ?>
    <div class="min-h-screen bg-white">
        <!-- Header -->
        <nav class="bg-white shadow-sm sticky top-8 z-50">
            <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
                <?php if ($logoImage): ?>
                <img src="../<?php echo htmlspecialchars($logoImage['image_path']); ?>" 
                     alt="<?php echo htmlspecialchars($logoImage['alt_text'] ?? $siteName); ?>"
                     class="h-10 w-auto object-contain">
                <?php else: ?>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full" style="background: linear-gradient(135deg, <?php echo $primaryColor; ?>, <?php echo $accentColor; ?>);"></div>
                    <span class="font-bold text-gray-900"><?php echo htmlspecialchars($siteName); ?></span>
                </div>
                <?php endif; ?>
                <div class="hidden md:flex space-x-8 text-sm font-medium text-gray-600">
                    <a href="#home" class="hover:opacity-70">Home</a>
                    <a href="#about" class="hover:opacity-70">About</a>
                    <a href="#services" class="hover:opacity-70">Services</a>
                    <a href="#contact" class="hover:opacity-70">Contact</a>
                </div>
                <a href="#contact" class="text-white px-4 py-2 rounded-full text-sm font-medium hover:opacity-90 transition" style="background: <?php echo $primaryColor; ?>;">
                    Get Started
                </a>
            </div>
        </nav>

        <!-- Hero -->
        <section id="home" class="py-16 md:py-24 px-4 text-center relative overflow-hidden" style="background: linear-gradient(135deg, <?php echo $primaryColor; ?>15, <?php echo $accentColor; ?>10);">
            <?php if ($heroImage): ?>
            <div class="absolute inset-0">
                <img src="../<?php echo htmlspecialchars($heroImage['image_path']); ?>" 
                     alt="<?php echo htmlspecialchars($heroImage['alt_text'] ?? 'Hero Image'); ?>"
                     class="w-full h-full object-cover opacity-20">
            </div>
            <?php endif; ?>
            <div class="max-w-4xl mx-auto relative z-10">
                <span class="inline-block px-4 py-1 rounded-full text-sm font-medium mb-4" style="background: <?php echo $primaryColor; ?>15; color: <?php echo $primaryColor; ?>;">
                    ✨ Welcome
                </span>
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6"><?php echo htmlspecialchars($heroTitle); ?></h1>
                <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto"><?php echo htmlspecialchars($heroSubtitle); ?></p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="#services" class="text-white px-8 py-3 rounded-lg font-semibold hover:opacity-90 transition shadow-lg" style="background: <?php echo $primaryColor; ?>;">
                        Learn More
                    </a>
                    <a href="#contact" class="bg-white px-8 py-3 rounded-lg font-semibold border hover:bg-gray-50 transition flex items-center gap-2">
                        <i class="fas fa-play-circle" style="color: <?php echo $primaryColor; ?>;"></i>
                        Contact Us
                    </a>
                </div>
            </div>
        </section>

        <!-- About -->
        <section id="about" class="py-16 px-4 md:px-8">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-bold mb-4" style="color: <?php echo $primaryColor; ?>;">About Us</h2>
                <div class="w-16 h-1 mx-auto rounded mb-8" style="background: <?php echo $accentColor; ?>;"></div>
                <p class="text-gray-600 text-lg leading-relaxed"><?php echo nl2br(htmlspecialchars($aboutContent)); ?></p>
            </div>
        </section>

        <!-- Services -->
        <section id="services" class="py-16 px-4 md:px-8 bg-gray-50">
            <div class="max-w-5xl mx-auto">
                <h2 class="text-3xl font-bold text-center mb-8" style="color: <?php echo $primaryColor; ?>;">Our Services</h2>
                
                <?php if (!empty($galleryImages)): ?>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                        <?php foreach ($galleryImages as $index => $img): ?>
                        <div class="text-center p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition">
                            <div class="aspect-square mb-4 rounded-lg overflow-hidden">
                                <img src="../<?php echo htmlspecialchars($img['image_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($img['alt_text'] ?? 'Service ' . ($index + 1)); ?>"
                                     class="w-full h-full object-cover hover:scale-105 transition duration-300">
                            </div>
                            <h3 class="font-semibold mb-2"><?php echo htmlspecialchars($img['alt_text'] ?? 'Service ' . ($index + 1)); ?></h3>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php elseif (!empty($servicesContent)): ?>
                    <div class="max-w-3xl mx-auto text-center text-gray-600"><?php echo nl2br(htmlspecialchars($servicesContent)); ?></div>
                <?php else: ?>
                    <div class="grid md:grid-cols-3 gap-6">
                        <?php 
                        $services = [
                            ['icon' => 'fa-star', 'title' => 'Quality'],
                            ['icon' => 'fa-heart', 'title' => 'Care'],
                            ['icon' => 'fa-bolt', 'title' => 'Speed'],
                        ];
                        foreach($services as $service): ?>
                        <div class="text-center p-6 bg-white rounded-xl shadow-sm">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4" style="background: <?php echo $primaryColor; ?>15;">
                                <i class="fas <?php echo $service['icon']; ?>" style="color: <?php echo $primaryColor; ?>;"></i>
                            </div>
                            <h3 class="font-semibold mb-2"><?php echo $service['title']; ?></h3>
                            <p class="text-gray-600 text-sm">Dedicated to excellence in everything we do.</p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Contact -->
        <section id="contact" class="py-16 px-4 md:px-8">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-bold mb-4" style="color: <?php echo $primaryColor; ?>;">Contact Us</h2>
                <div class="w-16 h-1 mx-auto rounded mb-8" style="background: <?php echo $accentColor; ?>;"></div>
                
                <div class="rounded-xl p-8 bg-gray-50">
                    <?php if (!empty($contactInfo)): ?>
                        <p class="text-gray-600 whitespace-pre-line"><?php echo htmlspecialchars($contactInfo); ?></p>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php if (!empty($site['contact_phone'])): ?>
                                <p><i class="fas fa-phone mr-2" style="color: <?php echo $primaryColor; ?>;"></i><?php echo htmlspecialchars($site['contact_phone']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($site['contact_email'])): ?>
                                <p><i class="fas fa-envelope mr-2" style="color: <?php echo $primaryColor; ?>;"></i><?php echo htmlspecialchars($site['contact_email']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($site['address'])): ?>
                                <p><i class="fas fa-map-marker-alt mr-2" style="color: <?php echo $primaryColor; ?>;"></i><?php echo htmlspecialchars($site['address']); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-10 px-4 text-white" style="background: <?php echo $primaryColor; ?>;">
            <div class="max-w-6xl mx-auto text-center">
                <p class="font-bold text-lg mb-2"><?php echo htmlspecialchars($siteName); ?></p>
                <?php if (!empty($socialLinks)): ?>
                <div class="flex justify-center items-center space-x-4 mb-3 social-icons-footer">
                    <?php foreach ($socialLinks as $link): ?>
                    <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" rel="noopener noreferrer" 
                       title="<?php echo htmlspecialchars($link['label']); ?>"
                       class="w-10 h-10 flex items-center justify-center bg-white/20 hover:bg-white/30 rounded-full transition social-icon">
                        <i class="<?php echo htmlspecialchars($link['icon']); ?> text-lg"></i>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <p class="text-white/60 text-sm">&copy; <?php echo date('Y'); ?> All rights reserved. Powered by FilDevStudio</p>
            </div>
        </footer>
    </div>
    <?php endif; // End of template ID check ?>
    
    <?php endif; // End of modular template check ?>

    <script>
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    </script>
</body>
</html>
