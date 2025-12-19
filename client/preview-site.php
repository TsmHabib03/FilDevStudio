<?php
/**
 * Preview Client Site
 */
$siteId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

require_once '../config/database.php';
require_once '../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get site data
try {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT cs.*, bp.business_name, bp.contact_phone, bp.contact_email, bp.address
                           FROM client_sites cs 
                           LEFT JOIN business_profiles bp ON cs.user_id = bp.user_id 
                           WHERE cs.id = ?");
    $stmt->execute([$siteId]);
    $site = $stmt->fetch();
    
    if (!$site) {
        die('Site not found');
    }
} catch (Exception $e) {
    die('Error loading site');
}

$primaryColor = $site['primary_color'] ?? '#3B82F6';
$secondaryColor = $site['secondary_color'] ?? '#1E40AF';
$accentColor = $site['accent_color'] ?? '#F59E0B';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($site['site_name'] ?? 'My Website'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '<?php echo $primaryColor; ?>',
                        secondary: '<?php echo $secondaryColor; ?>',
                        accent: '<?php echo $accentColor; ?>'
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-hero { background: linear-gradient(135deg, <?php echo $primaryColor; ?> 0%, <?php echo $secondaryColor; ?> 100%); }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Preview Banner -->
    <div class="bg-yellow-500 text-yellow-900 text-center py-2 text-sm">
        <i class="fas fa-eye mr-2"></i>Preview Mode - 
        <a href="edit-site.php?id=<?php echo $siteId; ?>" class="underline font-medium">Edit this site</a>
    </div>

    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold" style="color: <?php echo $primaryColor; ?>">
                        <?php echo htmlspecialchars($site['site_name'] ?? 'My Business'); ?>
                    </span>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#home" class="text-gray-600 hover:text-primary">Home</a>
                    <a href="#about" class="text-gray-600 hover:text-primary">About</a>
                    <a href="#services" class="text-gray-600 hover:text-primary">Services</a>
                    <a href="#contact" class="text-gray-600 hover:text-primary">Contact</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="gradient-hero text-white py-20">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">
                <?php echo htmlspecialchars($site['hero_title'] ?? 'Welcome to ' . ($site['site_name'] ?? 'Our Business')); ?>
            </h1>
            <p class="text-xl text-white/80 mb-8 max-w-2xl mx-auto">
                <?php echo htmlspecialchars($site['hero_subtitle'] ?? 'We provide excellent products and services to help your business grow.'); ?>
            </p>
            <a href="#contact" class="inline-block bg-white px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition" style="color: <?php echo $primaryColor; ?>">
                Get in Touch
            </a>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">About Us</h2>
                <div class="w-20 h-1 mx-auto rounded" style="background: <?php echo $primaryColor; ?>"></div>
            </div>
            <div class="max-w-3xl mx-auto text-center">
                <p class="text-gray-600 text-lg leading-relaxed">
                    <?php echo nl2br(htmlspecialchars($site['about_content'] ?? 'We are a dedicated team committed to providing the best products and services to our customers. With years of experience in the industry, we understand what it takes to deliver quality and value.')); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-16 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Our Services</h2>
                <div class="w-20 h-1 mx-auto rounded" style="background: <?php echo $primaryColor; ?>"></div>
            </div>
            
            <?php if (!empty($site['services_content'])): ?>
                <div class="max-w-3xl mx-auto">
                    <p class="text-gray-600 text-lg leading-relaxed text-center">
                        <?php echo nl2br(htmlspecialchars($site['services_content'])); ?>
                    </p>
                </div>
            <?php else: ?>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="bg-white rounded-xl p-6 shadow-lg text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" style="background: <?php echo $primaryColor; ?>20">
                            <i class="fas fa-star text-2xl" style="color: <?php echo $primaryColor; ?>"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Service One</h3>
                        <p class="text-gray-600">High quality service tailored to your needs.</p>
                    </div>
                    <div class="bg-white rounded-xl p-6 shadow-lg text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" style="background: <?php echo $primaryColor; ?>20">
                            <i class="fas fa-heart text-2xl" style="color: <?php echo $primaryColor; ?>"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Service Two</h3>
                        <p class="text-gray-600">Dedicated support for all our customers.</p>
                    </div>
                    <div class="bg-white rounded-xl p-6 shadow-lg text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" style="background: <?php echo $primaryColor; ?>20">
                            <i class="fas fa-bolt text-2xl" style="color: <?php echo $primaryColor; ?>"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Service Three</h3>
                        <p class="text-gray-600">Fast and reliable solutions.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Contact Us</h2>
                <div class="w-20 h-1 mx-auto rounded" style="background: <?php echo $primaryColor; ?>"></div>
            </div>
            <div class="max-w-xl mx-auto">
                <div class="bg-gray-50 rounded-xl p-8">
                    <?php if (!empty($site['contact_info'])): ?>
                        <p class="text-gray-600 text-center whitespace-pre-line"><?php echo htmlspecialchars($site['contact_info']); ?></p>
                    <?php else: ?>
                        <div class="space-y-4 text-center">
                            <?php if (!empty($site['contact_phone'])): ?>
                                <p><i class="fas fa-phone mr-2" style="color: <?php echo $primaryColor; ?>"></i><?php echo htmlspecialchars($site['contact_phone']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($site['contact_email'])): ?>
                                <p><i class="fas fa-envelope mr-2" style="color: <?php echo $primaryColor; ?>"></i><?php echo htmlspecialchars($site['contact_email']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($site['address'])): ?>
                                <p><i class="fas fa-map-marker-alt mr-2" style="color: <?php echo $primaryColor; ?>"></i><?php echo htmlspecialchars($site['address']); ?></p>
                            <?php endif; ?>
                            <?php if (empty($site['contact_phone']) && empty($site['contact_email']) && empty($site['address'])): ?>
                                <p class="text-gray-500">Contact information will appear here.</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-8" style="background: <?php echo $secondaryColor; ?>">
        <div class="max-w-6xl mx-auto px-4 text-center text-white">
            <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($site['site_name'] ?? 'My Business'); ?>. All rights reserved.</p>
            <p class="text-white/60 text-sm mt-2">Powered by FilDevStudio</p>
        </div>
    </footer>

    <script>
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({ behavior: 'smooth' });
            });
        });
    </script>
</body>
</html>
