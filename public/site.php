<?php
/**
 * Public Site Viewer - Template-Based Rendering
 * Renders published client sites accessible via subdomain
 * URL: public/site.php?s={subdomain}
 * Admin Preview: public/site.php?s={subdomain}&preview=1 (requires admin login)
 */

// Get subdomain from URL parameter
$subdomain = isset($_GET['s']) ? trim($_GET['s']) : '';
$isPreviewMode = isset($_GET['preview']) && $_GET['preview'] == '1';

// Validate subdomain format (allow letters, numbers, hyphens, and underscores)
if (empty($subdomain) || !preg_match('/^[a-z0-9_-]+$/i', $subdomain)) {
    http_response_code(404);
    include(__DIR__ . '/404.php');
    exit;
}

require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if admin preview mode
$requireActive = true;
if ($isPreviewMode) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        $requireActive = false; // Admins can preview any site
    }
}

// Get site data by subdomain
try {
    $pdo = getConnection();
    $site = getSiteBySubdomain($pdo, $subdomain, $requireActive);
    
    if (!$site) {
        http_response_code(404);
        include(__DIR__ . '/404.php');
        exit;
    }
    
    $siteId = $site['id'];
    
    // Get site images
    $logoImage = getSiteImage($pdo, $siteId, 'logo');
    $heroImage = getSiteImage($pdo, $siteId, 'hero');
    $galleryImages = getSiteImages($pdo, $siteId, 'gallery');
    
} catch (Exception $e) {
    http_response_code(500);
    include(__DIR__ . '/404.php');
    exit;
}

// Check if site has a custom full HTML override (from developer workspace)
if (!empty($site['custom_full_html'])) {
    // Output the custom full HTML directly
    echo $site['custom_full_html'];
    exit;
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

// Image paths for public view (relative to public folder)
$imageBasePath = '../';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($siteName); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars(substr($heroSubtitle, 0, 160)); ?>">
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
        /* Apply fonts globally */
        h1, h2, h3, h4, h5, h6, nav a { font-family: <?php echo $headingFontFamily; ?>; }
        body, p, span, li, div { font-family: <?php echo $bodyFontFamily; ?>; }
        /* Social icon styles */
        .social-icon { transition: all 0.3s ease; }
        .social-icon:hover { transform: scale(1.15); }
        .social-icons-header a { color: inherit; opacity: 0.7; transition: all 0.2s ease; }
        .social-icons-header a:hover { opacity: 1; }
        .social-icons-footer a { transition: all 0.3s ease; }
        .social-icons-footer a:hover { transform: translateY(-3px); }
    </style>
</head>
<body class="font-body">

    <?php 
    // Check if modular template file exists
    $templateFile = __DIR__ . '/templates/template-' . $templateId . '.php';
    $useModularTemplate = file_exists($templateFile) && $templateId >= 1 && $templateId <= 5;
    
    if ($useModularTemplate):
        include $templateFile;
    else:
    ?>

    <?php if ($templateId == 4): // ========== SMALL RETAIL SHOP ========== ?>
    <div class="min-h-screen" style="background: #FAFAFA;">
        <!-- Header -->
        <nav class="bg-white border-b sticky top-0 z-50">
            <div class="max-w-6xl mx-auto px-4 md:px-8 py-4 flex justify-between items-center">
                <?php if ($logoImage): ?>
                <img src="<?php echo $imageBasePath . htmlspecialchars($logoImage['image_path']); ?>" 
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
                    <a href="#home" class="hover:text-gray-900">Home</a>
                    <a href="#about" class="hover:text-gray-900">About</a>
                    <a href="#services" class="hover:text-gray-900">Services</a>
                    <a href="#contact" class="hover:text-gray-900">Contact</a>
                </div>
                <div class="flex items-center space-x-4 text-gray-600">
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
                    <img src="<?php echo $imageBasePath . htmlspecialchars($heroImage['image_path']); ?>" 
                         alt="<?php echo htmlspecialchars($heroImage['alt_text'] ?? 'Hero Image'); ?>"
                         class="w-full h-full object-cover">
                </div>
                <?php else: ?>
                <div class="h-64 md:h-80 rounded-2xl flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, <?php echo $accentColor; ?>30, <?php echo $primaryColor; ?>20);">
                    <div class="text-center text-gray-400">
                        <i class="fas fa-image text-5xl mb-3"></i>
                        <p class="text-sm">Your Image Here</p>
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
        <section id="services" class="py-16 px-4 md:px-8" style="background: <?php echo $primaryColor; ?>08;">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-3xl font-serif-display text-center mb-4" style="color: <?php echo $primaryColor; ?>;">Our Products</h2>
                <div class="w-16 h-1 mx-auto rounded mb-12" style="background: <?php echo $accentColor; ?>;"></div>
                
                <?php if (!empty($galleryImages)): ?>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <?php foreach ($galleryImages as $image): ?>
                    <div class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-lg transition group">
                        <div class="h-48 overflow-hidden">
                            <img src="<?php echo $imageBasePath . htmlspecialchars($image['image_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($image['alt_text'] ?? 'Product'); ?>"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        </div>
                        <?php if (!empty($image['alt_text'])): ?>
                        <div class="p-4">
                            <h3 class="font-medium text-gray-800"><?php echo htmlspecialchars($image['alt_text']); ?></h3>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-gray-600 leading-relaxed max-w-3xl mx-auto">
                    <?php echo nl2br(htmlspecialchars($servicesContent)); ?>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="py-16 px-4 md:px-8 bg-white">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-serif-display mb-4" style="color: <?php echo $primaryColor; ?>;">Get In Touch</h2>
                <div class="w-16 h-1 mx-auto rounded mb-8" style="background: <?php echo $accentColor; ?>;"></div>
                <div class="text-gray-600 text-lg leading-relaxed">
                    <?php echo nl2br(htmlspecialchars($contactInfo)); ?>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-8 px-4 text-center text-sm text-gray-500 border-t">
            <?php if (!empty($socialLinks)): ?>
            <div class="flex justify-center space-x-4 mb-4 social-icons-footer">
                <?php foreach ($socialLinks as $link): ?>
                <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo htmlspecialchars($link['label']); ?>" class="social-icon" style="--social-color: <?php echo htmlspecialchars($link['color']); ?>">
                    <i class="<?php echo htmlspecialchars($link['icon']); ?>"></i>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($siteName); ?>. All rights reserved.</p>
        </footer>
    </div>

    <?php elseif ($templateId == 2): // ========== RESTAURANT PRO ========== ?>
    <div class="min-h-screen bg-gray-900 text-white">
        <!-- Header -->
        <nav class="absolute top-0 left-0 right-0 z-50 py-6 px-4 md:px-8">
            <div class="max-w-6xl mx-auto flex justify-between items-center">
                <?php if ($logoImage): ?>
                <img src="<?php echo $imageBasePath . htmlspecialchars($logoImage['image_path']); ?>" 
                     alt="<?php echo htmlspecialchars($logoImage['alt_text'] ?? $siteName); ?>"
                     class="h-12 w-auto object-contain">
                <?php else: ?>
                <div class="text-2xl font-serif-display" style="color: <?php echo $accentColor; ?>;">
                    <?php echo htmlspecialchars($siteName); ?>
                </div>
                <?php endif; ?>
                <div class="hidden md:flex space-x-8 text-sm font-medium">
                    <a href="#home" class="hover:text-amber-400">Home</a>
                    <a href="#about" class="hover:text-amber-400">About</a>
                    <a href="#menu" class="hover:text-amber-400">Menu</a>
                    <a href="#contact" class="hover:text-amber-400">Contact</a>
                </div>
                <div class="flex items-center space-x-4">
                    <?php if (!empty($socialLinks)): ?>
                    <div class="hidden md:flex items-center space-x-3 social-icons-header">
                        <?php foreach (array_slice($socialLinks, 0, 3) as $link): ?>
                        <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo htmlspecialchars($link['label']); ?>" class="social-icon text-white hover:text-amber-400">
                            <i class="<?php echo htmlspecialchars($link['icon']); ?>"></i>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <a href="#contact" class="px-4 py-2 text-sm font-medium rounded" style="background: <?php echo $accentColor; ?>; color: #1a1a1a;">
                        Reserve
                    </a>
                </div>
            </div>
        </nav>

        <!-- Hero -->
        <section id="home" class="h-screen relative flex items-center justify-center">
            <?php if ($heroImage): ?>
            <div class="absolute inset-0">
                <img src="<?php echo $imageBasePath . htmlspecialchars($heroImage['image_path']); ?>" 
                     alt="<?php echo htmlspecialchars($heroImage['alt_text'] ?? 'Restaurant'); ?>"
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/60"></div>
            </div>
            <?php else: ?>
            <div class="absolute inset-0 gradient-hero"></div>
            <?php endif; ?>
            <div class="relative text-center px-4 max-w-4xl">
                <p class="text-sm tracking-widest uppercase mb-4" style="color: <?php echo $accentColor; ?>;">Welcome to</p>
                <h1 class="text-5xl md:text-7xl font-serif-display mb-6">
                    <?php echo htmlspecialchars($heroTitle); ?>
                </h1>
                <p class="text-lg md:text-xl text-gray-300 mb-8 max-w-2xl mx-auto">
                    <?php echo htmlspecialchars($heroSubtitle); ?>
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="#menu" class="px-8 py-3 font-medium rounded" style="background: <?php echo $accentColor; ?>; color: #1a1a1a;">
                        View Menu
                    </a>
                    <a href="#contact" class="px-8 py-3 border-2 border-white font-medium rounded hover:bg-white hover:text-gray-900 transition">
                        Reserve Table
                    </a>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="py-20 px-4 md:px-8 bg-gray-800">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-serif-display mb-2" style="color: <?php echo $accentColor; ?>;">Our Story</h2>
                <div class="w-16 h-0.5 mx-auto mb-8" style="background: <?php echo $accentColor; ?>;"></div>
                <p class="text-gray-300 text-lg leading-relaxed">
                    <?php echo nl2br(htmlspecialchars($aboutContent)); ?>
                </p>
            </div>
        </section>

        <!-- Menu Section -->
        <section id="menu" class="py-20 px-4 md:px-8 bg-gray-900">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-3xl font-serif-display text-center mb-2" style="color: <?php echo $accentColor; ?>;">Menu</h2>
                <div class="w-16 h-0.5 mx-auto mb-12" style="background: <?php echo $accentColor; ?>;"></div>
                
                <?php if (!empty($galleryImages)): ?>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($galleryImages as $image): ?>
                    <div class="bg-gray-800 rounded-lg overflow-hidden">
                        <img src="<?php echo $imageBasePath . htmlspecialchars($image['image_path']); ?>" 
                             alt="<?php echo htmlspecialchars($image['alt_text'] ?? 'Dish'); ?>"
                             class="w-full h-48 object-cover">
                        <?php if (!empty($image['alt_text'])): ?>
                        <div class="p-4">
                            <h3 class="font-medium" style="color: <?php echo $accentColor; ?>;"><?php echo htmlspecialchars($image['alt_text']); ?></h3>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-gray-300 leading-relaxed max-w-3xl mx-auto text-center">
                    <?php echo nl2br(htmlspecialchars($servicesContent)); ?>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="py-20 px-4 md:px-8 bg-gray-800">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-serif-display mb-2" style="color: <?php echo $accentColor; ?>;">Contact Us</h2>
                <div class="w-16 h-0.5 mx-auto mb-8" style="background: <?php echo $accentColor; ?>;"></div>
                <div class="text-gray-300 text-lg leading-relaxed">
                    <?php echo nl2br(htmlspecialchars($contactInfo)); ?>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-8 px-4 text-center text-sm text-gray-500 border-t border-gray-800">
            <?php if (!empty($socialLinks)): ?>
            <div class="flex justify-center space-x-4 mb-4 social-icons-footer">
                <?php foreach ($socialLinks as $link): ?>
                <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo htmlspecialchars($link['label']); ?>" class="social-icon text-gray-400" style="--social-color: <?php echo htmlspecialchars($link['color']); ?>">
                    <i class="<?php echo htmlspecialchars($link['icon']); ?>"></i>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($siteName); ?>. All rights reserved.</p>
        </footer>
    </div>

    <?php elseif ($templateId == 5): // ========== FREELANCER PORTFOLIO ========== ?>
    <div class="min-h-screen bg-white">
        <!-- Header -->
        <nav class="bg-white shadow-sm sticky top-0 z-50">
            <div class="max-w-5xl mx-auto px-4 md:px-8 py-4 flex justify-between items-center">
                <?php if ($logoImage): ?>
                <img src="<?php echo $imageBasePath . htmlspecialchars($logoImage['image_path']); ?>" 
                     alt="<?php echo htmlspecialchars($logoImage['alt_text'] ?? $siteName); ?>"
                     class="h-10 w-auto object-contain">
                <?php else: ?>
                <div class="text-xl font-bold" style="color: <?php echo $primaryColor; ?>;">
                    <?php echo htmlspecialchars($siteName); ?>
                </div>
                <?php endif; ?>
                <div class="hidden md:flex space-x-8 text-sm font-medium text-gray-600">
                    <a href="#about" class="hover:text-gray-900">About</a>
                    <a href="#work" class="hover:text-gray-900">Work</a>
                    <a href="#contact" class="hover:text-gray-900">Contact</a>
                </div>
                <div class="flex items-center space-x-4">
                    <?php if (!empty($socialLinks)): ?>
                    <div class="hidden md:flex items-center space-x-3 social-icons-header">
                        <?php foreach (array_slice($socialLinks, 0, 4) as $link): ?>
                        <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo htmlspecialchars($link['label']); ?>" class="social-icon">
                            <i class="<?php echo htmlspecialchars($link['icon']); ?>"></i>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <a href="#contact" class="px-4 py-2 text-sm text-white font-medium rounded" style="background: <?php echo $primaryColor; ?>;">
                        Hire Me
                    </a>
                </div>
            </div>
        </nav>

        <!-- Hero -->
        <section id="home" class="py-20 px-4 md:px-8">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-6" style="color: <?php echo $primaryColor; ?>;">
                    <?php echo htmlspecialchars($heroTitle); ?>
                </h1>
                <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
                    <?php echo htmlspecialchars($heroSubtitle); ?>
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="#work" class="px-8 py-3 text-white font-medium rounded-lg" style="background: <?php echo $primaryColor; ?>;">
                        View My Work
                    </a>
                    <a href="#contact" class="px-8 py-3 border-2 font-medium rounded-lg" style="border-color: <?php echo $primaryColor; ?>; color: <?php echo $primaryColor; ?>;">
                        Get In Touch
                    </a>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="py-16 px-4 md:px-8 bg-gray-50">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-bold mb-8" style="color: <?php echo $primaryColor; ?>;">About Me</h2>
                <p class="text-gray-600 text-lg leading-relaxed">
                    <?php echo nl2br(htmlspecialchars($aboutContent)); ?>
                </p>
            </div>
        </section>

        <!-- Work/Portfolio Section -->
        <section id="work" class="py-16 px-4 md:px-8">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-3xl font-bold text-center mb-12" style="color: <?php echo $primaryColor; ?>;">My Work</h2>
                
                <?php if (!empty($galleryImages)): ?>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ($galleryImages as $image): ?>
                    <div class="group relative rounded-xl overflow-hidden shadow-lg">
                        <img src="<?php echo $imageBasePath . htmlspecialchars($image['image_path']); ?>" 
                             alt="<?php echo htmlspecialchars($image['alt_text'] ?? 'Project'); ?>"
                             class="w-full h-64 object-cover group-hover:scale-105 transition duration-300">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition flex items-end p-6">
                            <?php if (!empty($image['alt_text'])): ?>
                            <h3 class="text-white font-semibold"><?php echo htmlspecialchars($image['alt_text']); ?></h3>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-gray-600 leading-relaxed max-w-3xl mx-auto text-center">
                    <?php echo nl2br(htmlspecialchars($servicesContent)); ?>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="py-16 px-4 md:px-8 bg-gray-50">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-bold mb-8" style="color: <?php echo $primaryColor; ?>;">Let's Work Together</h2>
                <div class="text-gray-600 text-lg leading-relaxed">
                    <?php echo nl2br(htmlspecialchars($contactInfo)); ?>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-8 px-4 text-center text-sm text-gray-500 border-t">
            <?php if (!empty($socialLinks)): ?>
            <div class="flex justify-center space-x-4 mb-4 social-icons-footer">
                <?php foreach ($socialLinks as $link): ?>
                <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo htmlspecialchars($link['label']); ?>" class="social-icon" style="--social-color: <?php echo htmlspecialchars($link['color']); ?>">
                    <i class="<?php echo htmlspecialchars($link['icon']); ?>"></i>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($siteName); ?>. All rights reserved.</p>
        </footer>
    </div>

    <?php elseif ($templateId == 3): // ========== LOCAL SERVICES ========== ?>
    <div class="min-h-screen bg-white">
        <!-- Header -->
        <nav class="bg-white shadow-sm sticky top-0 z-50">
            <div class="max-w-6xl mx-auto px-4 md:px-8 py-4 flex justify-between items-center">
                <?php if ($logoImage): ?>
                <img src="<?php echo $imageBasePath . htmlspecialchars($logoImage['image_path']); ?>" 
                     alt="<?php echo htmlspecialchars($logoImage['alt_text'] ?? $siteName); ?>"
                     class="h-10 w-auto object-contain">
                <?php else: ?>
                <div class="text-xl font-bold" style="color: <?php echo $primaryColor; ?>;">
                    <?php echo htmlspecialchars($siteName); ?>
                </div>
                <?php endif; ?>
                <div class="hidden md:flex space-x-8 text-sm font-medium text-gray-600">
                    <a href="#home" class="hover:text-gray-900">Home</a>
                    <a href="#about" class="hover:text-gray-900">About</a>
                    <a href="#services" class="hover:text-gray-900">Services</a>
                    <a href="#contact" class="hover:text-gray-900">Contact</a>
                </div>
                <div class="flex items-center space-x-4">
                    <?php if (!empty($socialLinks)): ?>
                    <div class="hidden md:flex items-center space-x-3 social-icons-header">
                        <?php foreach (array_slice($socialLinks, 0, 4) as $link): ?>
                        <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo htmlspecialchars($link['label']); ?>" class="social-icon">
                            <i class="<?php echo htmlspecialchars($link['icon']); ?>"></i>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <a href="#contact" class="px-5 py-2 text-white text-sm font-medium rounded-lg" style="background: <?php echo $primaryColor; ?>;">
                        Get Quote
                    </a>
                </div>
            </div>
        </nav>

        <!-- Hero -->
        <section id="home" class="relative py-20 md:py-32 px-4 md:px-8">
            <?php if ($heroImage): ?>
            <div class="absolute inset-0">
                <img src="<?php echo $imageBasePath . htmlspecialchars($heroImage['image_path']); ?>" 
                     alt="<?php echo htmlspecialchars($heroImage['alt_text'] ?? 'Services'); ?>"
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-r from-gray-900/90 to-gray-900/70"></div>
            </div>
            <?php else: ?>
            <div class="absolute inset-0 gradient-hero"></div>
            <?php endif; ?>
            <div class="relative max-w-4xl mx-auto">
                <h1 class="text-4xl md:text-6xl font-bold text-white mb-6">
                    <?php echo htmlspecialchars($heroTitle); ?>
                </h1>
                <p class="text-xl text-gray-200 mb-8 max-w-2xl">
                    <?php echo htmlspecialchars($heroSubtitle); ?>
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="#services" class="px-8 py-3 bg-white font-medium rounded-lg" style="color: <?php echo $primaryColor; ?>;">
                        Our Services
                    </a>
                    <a href="#contact" class="px-8 py-3 border-2 border-white text-white font-medium rounded-lg hover:bg-white hover:text-gray-900 transition">
                        Contact Us
                    </a>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="py-16 px-4 md:px-8">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-bold mb-8" style="color: <?php echo $primaryColor; ?>;">About Us</h2>
                <p class="text-gray-600 text-lg leading-relaxed">
                    <?php echo nl2br(htmlspecialchars($aboutContent)); ?>
                </p>
            </div>
        </section>

        <!-- Services Section -->
        <section id="services" class="py-16 px-4 md:px-8 bg-gray-50">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-3xl font-bold text-center mb-12" style="color: <?php echo $primaryColor; ?>;">Our Services</h2>
                <div class="text-gray-600 leading-relaxed max-w-3xl mx-auto">
                    <?php echo nl2br(htmlspecialchars($servicesContent)); ?>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="py-16 px-4 md:px-8">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-bold mb-8" style="color: <?php echo $primaryColor; ?>;">Contact Us</h2>
                <div class="text-gray-600 text-lg leading-relaxed">
                    <?php echo nl2br(htmlspecialchars($contactInfo)); ?>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-8 px-4 text-center text-sm text-gray-500 border-t">
            <?php if (!empty($socialLinks)): ?>
            <div class="flex justify-center space-x-4 mb-4 social-icons-footer">
                <?php foreach ($socialLinks as $link): ?>
                <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo htmlspecialchars($link['label']); ?>" class="social-icon" style="--social-color: <?php echo htmlspecialchars($link['color']); ?>">
                    <i class="<?php echo htmlspecialchars($link['icon']); ?>"></i>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($siteName); ?>. All rights reserved.</p>
        </footer>
    </div>

    <?php elseif ($templateId == 1): // ========== SARI-SARI STORE ========== ?>
    <div class="min-h-screen" style="background: #FEF3C7;">
        <!-- Header -->
        <nav class="bg-white shadow-md sticky top-0 z-50">
            <div class="max-w-5xl mx-auto px-4 py-3 flex justify-between items-center">
                <?php if ($logoImage): ?>
                <img src="<?php echo $imageBasePath . htmlspecialchars($logoImage['image_path']); ?>" 
                     alt="<?php echo htmlspecialchars($logoImage['alt_text'] ?? $siteName); ?>"
                     class="h-12 w-auto object-contain">
                <?php else: ?>
                <div class="flex items-center gap-2">
                    <i class="fas fa-store text-2xl" style="color: <?php echo $accentColor; ?>;"></i>
                    <span class="text-xl font-bold" style="color: <?php echo $primaryColor; ?>;"><?php echo htmlspecialchars($siteName); ?></span>
                </div>
                <?php endif; ?>
                <div class="hidden md:flex space-x-6 text-sm font-medium">
                    <a href="#products" class="hover:underline" style="color: <?php echo $primaryColor; ?>;">Mga Paninda</a>
                    <a href="#about" class="hover:underline" style="color: <?php echo $primaryColor; ?>;">Tungkol</a>
                    <a href="#contact" class="hover:underline" style="color: <?php echo $primaryColor; ?>;">Contact</a>
                </div>
                <?php if (!empty($socialLinks)): ?>
                <div class="hidden md:flex items-center space-x-3 social-icons-header">
                    <?php foreach (array_slice($socialLinks, 0, 3) as $link): ?>
                    <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo htmlspecialchars($link['label']); ?>" class="social-icon">
                        <i class="<?php echo htmlspecialchars($link['icon']); ?>"></i>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </nav>

        <!-- Hero -->
        <section id="home" class="py-12 px-4">
            <div class="max-w-5xl mx-auto">
                <?php if ($heroImage): ?>
                <div class="rounded-2xl overflow-hidden shadow-lg mb-8">
                    <img src="<?php echo $imageBasePath . htmlspecialchars($heroImage['image_path']); ?>" 
                         alt="<?php echo htmlspecialchars($heroImage['alt_text'] ?? $siteName); ?>"
                         class="w-full h-64 md:h-80 object-cover">
                </div>
                <?php endif; ?>
                <div class="text-center">
                    <h1 class="text-4xl md:text-5xl font-bold mb-4" style="color: <?php echo $primaryColor; ?>;">
                        <?php echo htmlspecialchars($heroTitle); ?>
                    </h1>
                    <p class="text-lg text-gray-700 mb-6">
                        <?php echo htmlspecialchars($heroSubtitle); ?>
                    </p>
                    <a href="#products" class="inline-block px-8 py-3 text-white font-bold rounded-full shadow-lg" style="background: <?php echo $accentColor; ?>;">
                        Tingnan ang Mga Paninda
                    </a>
                </div>
            </div>
        </section>

        <!-- Products Section -->
        <section id="products" class="py-12 px-4 bg-white">
            <div class="max-w-5xl mx-auto">
                <h2 class="text-3xl font-bold text-center mb-8" style="color: <?php echo $primaryColor; ?>;">
                    <i class="fas fa-shopping-basket mr-2"></i>Mga Paninda
                </h2>
                
                <?php if (!empty($galleryImages)): ?>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <?php foreach ($galleryImages as $image): ?>
                    <div class="bg-gray-50 rounded-xl p-3 text-center shadow hover:shadow-lg transition">
                        <img src="<?php echo $imageBasePath . htmlspecialchars($image['image_path']); ?>" 
                             alt="<?php echo htmlspecialchars($image['alt_text'] ?? 'Product'); ?>"
                             class="w-full h-32 object-cover rounded-lg mb-2">
                        <?php if (!empty($image['alt_text'])): ?>
                        <p class="font-medium text-sm" style="color: <?php echo $primaryColor; ?>;"><?php echo htmlspecialchars($image['alt_text']); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-gray-700 leading-relaxed text-center">
                    <?php echo nl2br(htmlspecialchars($servicesContent)); ?>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="py-12 px-4" style="background: <?php echo $primaryColor; ?>15;">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-bold mb-6" style="color: <?php echo $primaryColor; ?>;">Tungkol sa Amin</h2>
                <p class="text-gray-700 text-lg leading-relaxed">
                    <?php echo nl2br(htmlspecialchars($aboutContent)); ?>
                </p>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="py-12 px-4 bg-white">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-bold mb-6" style="color: <?php echo $primaryColor; ?>;">
                    <i class="fas fa-phone-alt mr-2"></i>Contact
                </h2>
                <div class="text-gray-700 text-lg leading-relaxed">
                    <?php echo nl2br(htmlspecialchars($contactInfo)); ?>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-6 px-4 text-center text-sm text-gray-600" style="background: <?php echo $primaryColor; ?>;">
            <?php if (!empty($socialLinks)): ?>
            <div class="flex justify-center space-x-4 mb-3 social-icons-footer">
                <?php foreach ($socialLinks as $link): ?>
                <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo htmlspecialchars($link['label']); ?>" class="social-icon text-white/80 hover:text-white" style="--social-color: <?php echo htmlspecialchars($link['color']); ?>">
                    <i class="<?php echo htmlspecialchars($link['icon']); ?>"></i>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <p class="text-white">&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($siteName); ?>. Lahat ng karapatan ay nakalaan.</p>
        </footer>
    </div>

    <?php else: // ========== DEFAULT / GENERAL BUSINESS ========== ?>
    <div class="min-h-screen bg-white">
        <!-- Header -->
        <nav class="gradient-hero text-white sticky top-0 z-50">
            <div class="max-w-6xl mx-auto px-4 md:px-8 py-4 flex justify-between items-center">
                <?php if ($logoImage): ?>
                <img src="<?php echo $imageBasePath . htmlspecialchars($logoImage['image_path']); ?>" 
                     alt="<?php echo htmlspecialchars($logoImage['alt_text'] ?? $siteName); ?>"
                     class="h-10 w-auto object-contain">
                <?php else: ?>
                <div class="text-xl font-bold">
                    <?php echo htmlspecialchars($siteName); ?>
                </div>
                <?php endif; ?>
                <div class="hidden md:flex space-x-8 text-sm font-medium">
                    <a href="#home" class="hover:text-gray-200">Home</a>
                    <a href="#about" class="hover:text-gray-200">About</a>
                    <a href="#services" class="hover:text-gray-200">Services</a>
                    <a href="#contact" class="hover:text-gray-200">Contact</a>
                </div>
                <?php if (!empty($socialLinks)): ?>
                <div class="hidden md:flex items-center space-x-3 social-icons-header">
                    <?php foreach (array_slice($socialLinks, 0, 4) as $link): ?>
                    <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo htmlspecialchars($link['label']); ?>" class="social-icon text-white/80 hover:text-white">
                        <i class="<?php echo htmlspecialchars($link['icon']); ?>"></i>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </nav>

        <!-- Hero -->
        <section id="home" class="gradient-hero text-white py-20 md:py-32 px-4 md:px-8 relative overflow-hidden">
            <?php if ($heroImage): ?>
            <div class="absolute inset-0">
                <img src="<?php echo $imageBasePath . htmlspecialchars($heroImage['image_path']); ?>" 
                     alt="<?php echo htmlspecialchars($heroImage['alt_text'] ?? 'Hero Image'); ?>"
                     class="w-full h-full object-cover opacity-30">
            </div>
            <?php endif; ?>
            <div class="max-w-4xl mx-auto text-center relative z-10">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">
                    <?php echo htmlspecialchars($heroTitle); ?>
                </h1>
                <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
                    <?php echo htmlspecialchars($heroSubtitle); ?>
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="#services" class="px-8 py-3 bg-white font-medium rounded-lg" style="color: <?php echo $primaryColor; ?>;">
                        Learn More
                    </a>
                    <a href="#contact" class="px-8 py-3 border-2 border-white text-white font-medium rounded-lg hover:bg-white/10 transition">
                        Contact Us
                    </a>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="py-16 px-4 md:px-8">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-bold mb-8" style="color: <?php echo $primaryColor; ?>;">About Us</h2>
                <p class="text-gray-600 text-lg leading-relaxed">
                    <?php echo nl2br(htmlspecialchars($aboutContent)); ?>
                </p>
            </div>
        </section>

        <!-- Services Section -->
        <section id="services" class="py-16 px-4 md:px-8 bg-gray-50">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-3xl font-bold text-center mb-12" style="color: <?php echo $primaryColor; ?>;">Our Services</h2>
                
                <?php if (!empty($galleryImages)): ?>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8">
                    <?php foreach ($galleryImages as $index => $img): ?>
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition">
                        <div class="aspect-square overflow-hidden">
                            <img src="<?php echo $imageBasePath . htmlspecialchars($img['image_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($img['alt_text'] ?? 'Service ' . ($index + 1)); ?>"
                                 class="w-full h-full object-cover hover:scale-105 transition duration-300">
                        </div>
                        <div class="p-3 text-center">
                            <p class="text-sm font-medium text-gray-700"><?php echo htmlspecialchars($img['alt_text'] ?? ''); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($servicesContent)): ?>
                <div class="text-gray-600 leading-relaxed max-w-3xl mx-auto text-center">
                    <?php echo nl2br(htmlspecialchars($servicesContent)); ?>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="py-16 px-4 md:px-8">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-bold mb-8" style="color: <?php echo $primaryColor; ?>;">Contact Us</h2>
                <div class="text-gray-600 text-lg leading-relaxed">
                    <?php echo nl2br(htmlspecialchars($contactInfo)); ?>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="gradient-hero py-8 px-4 text-center text-sm text-white/80">
            <?php if (!empty($socialLinks)): ?>
            <div class="flex justify-center space-x-4 mb-4 social-icons-footer">
                <?php foreach ($socialLinks as $link): ?>
                <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo htmlspecialchars($link['label']); ?>" class="social-icon text-white/70 hover:text-white" style="--social-color: <?php echo htmlspecialchars($link['color']); ?>">
                    <i class="<?php echo htmlspecialchars($link['icon']); ?>"></i>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($siteName); ?>. All rights reserved.</p>
        </footer>
    </div>
    <?php endif; // End template ID check ?>
    
    <?php endif; // End modular template check ?>

    <?php 
    // Inject custom CSS/JS/HTML added by developer
    $customCss = trim($site['custom_css'] ?? '');
    $customJs = trim($site['custom_js'] ?? '');
    $customHtml = trim($site['custom_html'] ?? '');
    
    // Remove template comments from CSS/JS/HTML (lines starting with comment markers only)
    // Keep actual code but strip the template header comments
    $customCss = preg_replace('/^\/\*\*[\s\S]*?\*\/\s*/m', '', $customCss, 1);
    $customJs = preg_replace('/^\/\*\*[\s\S]*?\*\/\s*/m', '', $customJs, 1);
    $customHtml = preg_replace('/^<!--[\s\S]*?-->\s*/m', '', $customHtml, 1);
    
    // Custom CSS (only if has actual content beyond comments)
    if (!empty($customCss) && strlen(preg_replace('/\/\*[\s\S]*?\*\/|\s+/', '', $customCss)) > 10): ?>
    <style id="custom-styles">
    /* Custom CSS */
    <?php echo $customCss; ?>
    </style>
    <?php endif;
    
    // Custom HTML sections
    if (!empty($customHtml) && strlen(preg_replace('/<!--[\s\S]*?-->|\s+/', '', $customHtml)) > 10): ?>
    <!-- Custom HTML -->
    <?php echo $customHtml; ?>
    <?php endif;
    
    // Custom JavaScript
    if (!empty($customJs) && strlen(preg_replace('/\/\*[\s\S]*?\*\/|\/\/.*$/m|\s+/', '', $customJs)) > 20): ?>
    <script id="custom-scripts">
    <?php echo $customJs; ?>
    </script>
    <?php endif; ?>

</body>
</html>
