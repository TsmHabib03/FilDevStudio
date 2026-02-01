<?php
/**
 * Template Gallery Page - FilDevStudio Web Services Platform
 * Focused on 5 SME Templates for Filipino Businesses
 */
$pageTitle = "Templates - FilDevStudio";
require_once 'includes/header.php';
require_once 'includes/functions.php';

// Define the 5 SME-focused templates
$smeTemplates = [
    [
        'id' => 1,
        'name' => 'Sari-Sari Store',
        'category' => 'sarisari',
        'tagline' => 'Para sa Tindahan at Convenience Store',
        'description' => 'Colorful, friendly template perfect for neighborhood sari-sari stores. Show your products, tingi prices, E-Load services, and accept GCash payments.',
        'features' => ['Tingi Price List', 'E-Load & Bills Payment', 'GCash/Maya Ready', 'Piso WiFi Info'],
        'preview_image' => 'assets/images/templates/template-1.svg',
        'color' => 'orange',
        'gradient' => 'from-orange-500 to-amber-500',
        'icon' => 'fas fa-store-alt',
        'badge' => '🏪 Most Popular'
    ],
    [
        'id' => 2,
        'name' => 'Carinderia & Food Business',
        'category' => 'food',
        'tagline' => 'Para sa Karinderya at Food Stall',
        'description' => 'Warm, appetizing design for food businesses. Perfect for catering, karinderya, food stalls, and small restaurants. Show your menu and accept orders.',
        'features' => ['Menu Display', 'Operating Hours', 'Delivery Options', 'Location Map'],
        'preview_image' => 'assets/images/templates/template-2.svg',
        'color' => 'red',
        'gradient' => 'from-red-500 to-orange-500',
        'icon' => 'fas fa-utensils',
        'badge' => '🍜 Food Business'
    ],
    [
        'id' => 3,
        'name' => 'Local Services',
        'category' => 'services',
        'tagline' => 'Para sa Laundry, Repair Shop, Salon',
        'description' => 'Professional template for service-based businesses. Ideal for laundry shops, computer repair, salons, and other local services.',
        'features' => ['Services & Pricing', 'Business Hours', 'Contact Info', 'Customer Reviews'],
        'preview_image' => 'assets/images/templates/template-3.svg',
        'color' => 'teal',
        'gradient' => 'from-teal-500 to-cyan-500',
        'icon' => 'fas fa-tools',
        'badge' => '🔧 Services'
    ],
    [
        'id' => 4,
        'name' => 'Small Retail Shop',
        'category' => 'retail',
        'tagline' => 'Para sa RTW, Ukay-Ukay, Gadget Store',
        'description' => 'Clean, modern design for retail businesses. Perfect for clothing shops, gadget stores, and general merchandise with product showcase.',
        'features' => ['Product Gallery', 'Store Info', 'Payment Methods', 'Social Media'],
        'preview_image' => 'assets/images/templates/template-4.svg',
        'color' => 'blue',
        'gradient' => 'from-blue-500 to-indigo-500',
        'icon' => 'fas fa-shopping-bag',
        'badge' => '🛍️ Retail'
    ],
    [
        'id' => 5,
        'name' => 'Freelancer Portfolio',
        'category' => 'freelance',
        'tagline' => 'Para sa Photographer, Designer, Tutor',
        'description' => 'Minimalist portfolio template for professionals. Showcase your work, skills, and services with a clean, modern design.',
        'features' => ['Work Samples', 'Skills Display', 'Service Rates', 'Contact Form'],
        'preview_image' => 'assets/images/templates/template-5.svg',
        'color' => 'purple',
        'gradient' => 'from-purple-500 to-pink-500',
        'icon' => 'fas fa-briefcase',
        'badge' => '💼 Professional'
    ]
];

// Get templates from database if available, otherwise use predefined
try {
    $pdo = getConnection();
    $stmt = $pdo->query("SELECT * FROM templates WHERE is_active = 1 ORDER BY id LIMIT 5");
    $dbTemplates = $stmt->fetchAll();
    
    // Merge database data with predefined template configs
    if (!empty($dbTemplates)) {
        foreach ($dbTemplates as $dbTemplate) {
            foreach ($smeTemplates as &$template) {
                if ($template['id'] == $dbTemplate['id']) {
                    $template['name'] = $dbTemplate['name'];
                    $template['description'] = $dbTemplate['description'];
                    $template['preview_image'] = $dbTemplate['preview_image'];
                }
            }
            unset($template);
        }
    }
} catch (Exception $e) {
    // Use predefined templates if database fails
}
?>

<!-- Hero Header Section -->
<section class="relative overflow-hidden bg-gradient-to-br from-primary-900 via-primary-800 to-secondary-900 py-16 lg:py-20">
    <!-- Decorative Elements -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-primary-400/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-1/4 w-80 h-80 bg-accent-500/10 rounded-full blur-3xl"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <!-- Badge -->
            <div class="inline-flex items-center px-4 py-1.5 bg-white/10 backdrop-blur-sm rounded-full mb-6">
                <i class="fas fa-palette text-secondary-400 mr-2"></i>
                <span class="text-white text-sm font-medium">5 Templates Designed for Filipino SMEs</span>
            </div>
            
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6">
                Choose Your Template
            </h1>
            <p class="text-xl text-primary-100 max-w-3xl mx-auto mb-8">
                Professionally designed templates for sari-sari stores, food businesses, service shops, 
                retail stores, and freelancers. No coding required — just pick, customize, and launch!
            </p>
            
            <!-- Quick Stats -->
            <div class="flex flex-wrap justify-center gap-8 md:gap-16">
                <div class="text-center">
                    <p class="text-4xl font-bold text-white">5</p>
                    <p class="text-primary-200 text-sm">SME Templates</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl font-bold text-white">100%</p>
                    <p class="text-primary-200 text-sm">Mobile Ready</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl font-bold text-white">Free</p>
                    <p class="text-primary-200 text-sm">To Start</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Templates Grid -->
<section class="py-16 lg:py-20 bg-gradient-to-b from-gray-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Section Header -->
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Templates for Every Filipino Business</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Each template is optimized for mobile, includes GCash/payment info sections, and is designed with Filipino business needs in mind.
            </p>
        </div>
        
        <!-- Template Cards Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8" id="templates-grid">
            <?php foreach ($smeTemplates as $template): ?>
                <div class="template-card group bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-2xl hover:border-<?php echo $template['color']; ?>-200 transition-all duration-300 hover:-translate-y-2">
                    
                    <!-- Template Preview Image -->
                    <div class="relative h-56 bg-gradient-to-br <?php echo $template['gradient']; ?> overflow-hidden">
                        <!-- Check if actual image exists, otherwise show placeholder -->
                        <?php 
                        $imagePath = $template['preview_image'];
                        $imageExists = file_exists($imagePath);
                        ?>
                        
                        <?php if ($imageExists): ?>
                            <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                                 alt="<?php echo htmlspecialchars($template['name']); ?> Preview"
                                 class="w-full h-full object-cover">
                        <?php else: ?>
                            <!-- Placeholder with icon and pattern -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center text-white">
                                    <i class="<?php echo $template['icon']; ?> text-6xl opacity-40 mb-2"></i>
                                    <div class="absolute inset-0 opacity-10">
                                        <div class="absolute top-4 left-4 w-24 h-24 border-2 border-white rounded-lg"></div>
                                        <div class="absolute bottom-4 right-4 w-20 h-20 border-2 border-white rounded-full"></div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Hover Overlay -->
                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <div class="flex flex-col gap-3">
                                <a href="template-preview.php?id=<?php echo $template['id']; ?>" 
                                   class="inline-flex items-center px-6 py-3 bg-white text-gray-900 font-semibold rounded-xl hover:bg-gray-100 transition transform translate-y-4 group-hover:translate-y-0 duration-300">
                                    <i class="fas fa-eye mr-2"></i>Live Preview
                                </a>
                            </div>
                        </div>
                        
                        <!-- Category Badge -->
                        <div class="absolute top-4 left-4 z-10">
                            <span class="inline-flex items-center px-3 py-1.5 bg-white/95 backdrop-blur-sm text-gray-800 text-xs font-bold rounded-lg shadow-sm">
                                <?php echo $template['badge']; ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Template Info -->
                    <div class="p-6">
                        <div class="mb-1">
                            <span class="text-xs font-medium text-<?php echo $template['color']; ?>-600 uppercase tracking-wide">
                                <?php echo $template['tagline']; ?>
                            </span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-<?php echo $template['color']; ?>-600 transition-colors">
                            <?php echo htmlspecialchars($template['name']); ?>
                        </h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                            <?php echo htmlspecialchars($template['description']); ?>
                        </p>
                        
                        <!-- Features Tags -->
                        <div class="flex flex-wrap gap-2 mb-5">
                            <?php foreach ($template['features'] as $feature): ?>
                                <span class="inline-flex items-center px-2.5 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-md">
                                    <i class="fas fa-check text-<?php echo $template['color']; ?>-500 mr-1.5 text-[10px]"></i>
                                    <?php echo $feature; ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex gap-3">
                            <a href="template-preview.php?id=<?php echo $template['id']; ?>" 
                               class="flex-1 text-center border-2 border-gray-200 text-gray-700 px-4 py-2.5 rounded-xl font-medium hover:border-<?php echo $template['color']; ?>-500 hover:text-<?php echo $template['color']; ?>-600 hover:bg-<?php echo $template['color']; ?>-50 transition-all duration-200">
                                <i class="fas fa-eye mr-1.5"></i>Preview
                            </a>
                            <?php if (isLoggedIn()): ?>
                                <a href="client/select-template.php?id=<?php echo $template['id']; ?>" 
                                   class="flex-1 text-center bg-gradient-to-r <?php echo $template['gradient']; ?> text-white px-4 py-2.5 rounded-xl font-medium hover:opacity-90 shadow-lg transition-all duration-200">
                                    <i class="fas fa-rocket mr-1.5"></i>Use This
                                </a>
                            <?php else: ?>
                                <a href="auth/register.php?template=<?php echo $template['id']; ?>" 
                                   class="flex-1 text-center bg-gradient-to-r <?php echo $template['gradient']; ?> text-white px-4 py-2.5 rounded-xl font-medium hover:opacity-90 shadow-lg transition-all duration-200">
                                    <i class="fas fa-rocket mr-1.5"></i>Use This
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Why These Templates Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Designed for Filipino SMEs</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Our templates are specifically designed for the needs of small and medium businesses in the Philippines.
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center p-6">
                <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-mobile-alt text-2xl text-blue-600"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Mobile-First Design</h3>
                <p class="text-gray-600 text-sm">Looks perfect on any phone or tablet - because your customers browse on mobile.</p>
            </div>
            
            <div class="text-center p-6">
                <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-qrcode text-2xl text-green-600"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">GCash & Maya Ready</h3>
                <p class="text-gray-600 text-sm">Easy sections to display your payment QR codes and accepted payment methods.</p>
            </div>
            
            <div class="text-center p-6">
                <div class="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fab fa-facebook-messenger text-2xl text-purple-600"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Social Media Integration</h3>
                <p class="text-gray-600 text-sm">Connect your Facebook Page, Messenger, Instagram, and Viber for easy customer contact.</p>
            </div>
            
            <div class="text-center p-6">
                <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-clock text-2xl text-orange-600"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Quick Setup</h3>
                <p class="text-gray-600 text-sm">Get your website up and running in minutes. No coding knowledge required!</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 lg:py-20 bg-gradient-to-br from-primary-900 via-primary-800 to-accent-900 relative overflow-hidden">
    <!-- Decorative -->
    <div class="absolute inset-0">
        <div class="absolute top-0 right-0 w-96 h-96 bg-secondary-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-accent-500/10 rounded-full blur-3xl"></div>
    </div>
    
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center px-4 py-1.5 bg-white/10 backdrop-blur-sm rounded-full mb-6">
            <i class="fas fa-question-circle text-secondary-400 mr-2"></i>
            <span class="text-white text-sm font-medium">Need Something Custom?</span>
        </div>
        
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
            Can't Find the Perfect Template?
        </h2>
        <p class="text-xl text-primary-200 mb-8 max-w-2xl mx-auto">
            Our creative team can design a custom website tailored specifically to your brand and business needs.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="<?php echo isLoggedIn() ? 'client/custom-request.php' : 'auth/register.php'; ?>" 
               class="inline-flex items-center justify-center px-8 py-4 bg-white text-primary-600 rounded-xl font-semibold hover:bg-gray-100 transition-all duration-200 shadow-xl">
                <i class="fas fa-palette mr-2"></i>Request Custom Design
            </a>
            <a href="<?php echo isLoggedIn() ? 'client/dashboard.php' : 'auth/register.php'; ?>" 
               class="inline-flex items-center justify-center px-8 py-4 bg-white/10 backdrop-blur-sm text-white border border-white/20 rounded-xl font-semibold hover:bg-white/20 transition-all duration-200">
                <i class="fas fa-rocket mr-2"></i>Get Started Free
            </a>
        </div>
        
        <!-- Trust Indicators -->
        <div class="mt-12 flex flex-wrap justify-center gap-6 text-primary-200 text-sm">
            <span class="flex items-center"><i class="fas fa-check-circle text-secondary-400 mr-2"></i>Free to start</span>
            <span class="flex items-center"><i class="fas fa-check-circle text-secondary-400 mr-2"></i>No coding needed</span>
            <span class="flex items-center"><i class="fas fa-check-circle text-secondary-400 mr-2"></i>Publish anytime</span>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
