<?php
/**
 * Template Preview Page - FilDevStudio
 * Each template has a unique, responsive, customizable layout
 */
$pageTitle = "Template Preview - FilDevStudio";
require_once 'includes/header.php';
require_once 'includes/functions.php';

$templateId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Template configurations with customizable colors
$templates = [
    1 => [
        'name' => 'Modern Retail', 
        'category' => 'retail', 
        'description' => 'Elegant, minimal design for luxury retail stores',
        'primary' => '#1F2937',
        'secondary' => '#F5F5F0',
        'accent' => '#D4AF37'
    ],
    2 => [
        'name' => 'Restaurant Pro', 
        'category' => 'food', 
        'description' => 'Warm, appetizing design for restaurants and cafes',
        'primary' => '#451A03',
        'secondary' => '#FFFBEB',
        'accent' => '#D97706'
    ],
    3 => [
        'name' => 'Freelancer Portfolio', 
        'category' => 'freelance', 
        'description' => 'Creative, bold portfolio for designers and creatives',
        'primary' => '#0F172A',
        'secondary' => '#8B5CF6',
        'accent' => '#EC4899'
    ],
    4 => [
        'name' => 'Service Business', 
        'category' => 'services', 
        'description' => 'Professional, trustworthy design for service companies',
        'primary' => '#1E40AF',
        'secondary' => '#F8FAFC',
        'accent' => '#2563EB'
    ],
    5 => [
        'name' => 'General Business', 
        'category' => 'general', 
        'description' => 'Versatile template for any business type',
        'primary' => '#0D9488',
        'secondary' => '#F0FDFA',
        'accent' => '#F97316'
    ],
    6 => [
        'name' => 'E-Commerce Starter', 
        'category' => 'retail', 
        'description' => 'Product-focused design to start selling online',
        'primary' => '#059669',
        'secondary' => '#FFFFFF',
        'accent' => '#EF4444'
    ],
    7 => [
        'name' => 'Urban Streetwear', 
        'category' => 'retail', 
        'description' => 'Edgy, bold design for fashion and streetwear brands',
        'primary' => '#000000',
        'secondary' => '#7C3AED',
        'accent' => '#3B82F6'
    ],
    8 => [
        'name' => 'Tech Startup', 
        'category' => 'general', 
        'description' => 'Clean, modern SaaS landing page design',
        'primary' => '#3B82F6',
        'secondary' => '#EFF6FF',
        'accent' => '#8B5CF6'
    ],
    9 => [
        'name' => 'Boutique Shop', 
        'category' => 'retail', 
        'description' => 'Feminine and elegant design for boutiques and gift shops',
        'primary' => '#BE185D',
        'secondary' => '#FDF2F8',
        'accent' => '#F472B6'
    ],
    10 => [
        'name' => 'Electronics Store', 
        'category' => 'retail', 
        'description' => 'Modern tech-focused design for gadget stores',
        'primary' => '#0F172A',
        'secondary' => '#38BDF8',
        'accent' => '#22D3EE'
    ],
    11 => [
        'name' => 'Grocery & Supermarket', 
        'category' => 'retail', 
        'description' => 'Clean organized layout for grocery stores',
        'primary' => '#15803D',
        'secondary' => '#F0FDF4',
        'accent' => '#FDE047'
    ],
    12 => [
        'name' => 'Sari-Sari Store', 
        'category' => 'sarisari', 
        'description' => 'Colorful, friendly template for neighborhood sari-sari stores',
        'primary' => '#EA580C',
        'secondary' => '#FFF7ED',
        'accent' => '#FACC15'
    ],
    13 => [
        'name' => 'Sari-Sari Plus', 
        'category' => 'sarisari', 
        'description' => 'Modern sari-sari store with online ordering features',
        'primary' => '#0891B2',
        'secondary' => '#ECFEFF',
        'accent' => '#F97316'
    ],
];

$template = $templates[$templateId] ?? null;

if (!$template) {
    redirect('templates.php');
}
?>

<!-- Preview Header -->
<section class="bg-white border-b py-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <a href="templates.php" class="text-gray-500 hover:text-primary mb-2 inline-block">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Templates
                </a>
                <h1 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($template['name']); ?></h1>
                <p class="text-gray-600"><?php echo htmlspecialchars($template['description']); ?></p>
            </div>
            <div class="flex gap-3">
                <?php if (isLoggedIn()): ?>
                    <a href="client/select-template.php?id=<?php echo $templateId; ?>" 
                       class="gradient-bg text-white px-6 py-3 rounded-lg font-semibold hover:opacity-90 transition">
                        <i class="fas fa-check mr-2"></i>Use This Template
                    </a>
                <?php else: ?>
                    <a href="auth/register.php?template=<?php echo $templateId; ?>" 
                       class="gradient-bg text-white px-6 py-3 rounded-lg font-semibold hover:opacity-90 transition">
                        <i class="fas fa-check mr-2"></i>Use This Template
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Color Customizer Preview -->
<section class="py-4 bg-gray-50 border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-center gap-6">
            <span class="text-sm font-medium text-gray-600">Template Colors:</span>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full border-2 border-white shadow" style="background-color: <?php echo $template['primary']; ?>"></div>
                <span class="text-xs text-gray-500">Primary</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full border-2 border-gray-300 shadow" style="background-color: <?php echo $template['secondary']; ?>"></div>
                <span class="text-xs text-gray-500">Secondary</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full border-2 border-white shadow" style="background-color: <?php echo $template['accent']; ?>"></div>
                <span class="text-xs text-gray-500">Accent</span>
            </div>
            <span class="text-xs text-gray-400 italic">* Colors are fully customizable</span>
        </div>
    </div>
</section>

<!-- Template Preview Frame -->
<section class="py-8 bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Device Toggle -->
        <div class="flex justify-center gap-4 mb-6">
            <button class="device-toggle active px-4 py-2 bg-white rounded-lg shadow hover:shadow-md transition" data-device="desktop">
                <i class="fas fa-desktop mr-2"></i>Desktop
            </button>
            <button class="device-toggle px-4 py-2 bg-white rounded-lg shadow hover:shadow-md transition" data-device="tablet">
                <i class="fas fa-tablet-alt mr-2"></i>Tablet
            </button>
            <button class="device-toggle px-4 py-2 bg-white rounded-lg shadow hover:shadow-md transition" data-device="mobile">
                <i class="fas fa-mobile-alt mr-2"></i>Mobile
            </button>
        </div>
        
        <!-- Preview Container -->
        <div id="preview-container" class="bg-white rounded-xl shadow-xl overflow-hidden mx-auto transition-all duration-300" style="max-width: 100%;">
            <!-- Browser Chrome -->
            <div class="bg-gray-200 px-4 py-3 flex items-center space-x-2">
                <div class="w-3 h-3 rounded-full bg-red-500"></div>
                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                <div class="flex-1 ml-4">
                    <div class="bg-white rounded-full px-4 py-1 text-sm text-gray-500">
                        www.yourbusiness.com
                    </div>
                </div>
            </div>
            
            <!-- Preview Content -->
            <div class="h-[700px] overflow-y-auto bg-white">
                
                <?php if ($templateId == 1): // ========== MODERN RETAIL ========== ?>
                <div class="font-sans min-h-full" style="background: #FAFAFA;">
                    <!-- Header -->
                    <nav class="bg-white border-b">
                        <div class="max-w-6xl mx-auto px-4 sm:px-8 py-4 flex justify-between items-center">
                            <div class="text-2xl font-serif tracking-wide" style="color: #1F2937;">LUXE<span class="font-light">STORE</span></div>
                            <div class="hidden md:flex space-x-8 text-sm font-medium text-gray-600">
                                <a href="#" class="hover:text-gray-900">New Arrivals</a>
                                <a href="#" class="hover:text-gray-900">Women</a>
                                <a href="#" class="hover:text-gray-900">Men</a>
                                <a href="#" class="hover:text-gray-900">Sale</a>
                            </div>
                            <div class="flex items-center space-x-4 text-gray-600">
                                <i class="fas fa-search cursor-pointer hover:text-gray-900"></i>
                                <i class="fas fa-user cursor-pointer hover:text-gray-900"></i>
                                <div class="relative">
                                    <i class="fas fa-shopping-bag cursor-pointer hover:text-gray-900"></i>
                                    <span class="absolute -top-2 -right-2 w-4 h-4 bg-amber-500 text-white text-xs rounded-full flex items-center justify-center">2</span>
                                </div>
                            </div>
                        </div>
                    </nav>

                    <!-- Hero Section - Split Layout -->
                    <section class="bg-stone-100">
                        <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-0 items-center">
                            <div class="p-8 md:p-16">
                                <span class="text-amber-600 text-sm font-medium tracking-widest uppercase">New Season</span>
                                <h1 class="text-4xl md:text-5xl font-serif mt-4 mb-6 leading-tight" style="color: #1F2937;">Timeless Elegance</h1>
                                <p class="text-gray-600 mb-8 leading-relaxed">Discover our curated collection of premium essentials designed for the modern lifestyle.</p>
                                <div class="flex flex-wrap gap-4">
                                    <button class="px-8 py-3 text-white font-medium hover:opacity-90 transition" style="background: #1F2937;">Shop Collection</button>
                                    <button class="px-8 py-3 border-2 font-medium hover:bg-gray-100 transition" style="border-color: #1F2937; color: #1F2937;">Learn More</button>
                                </div>
                            </div>
                            <div class="h-64 md:h-96 bg-stone-200 flex items-center justify-center">
                                <div class="text-center text-stone-400">
                                    <i class="fas fa-image text-6xl mb-4"></i>
                                    <p class="text-sm">Hero Image</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Features Bar -->
                    <section class="bg-white border-y">
                        <div class="max-w-6xl mx-auto px-4 py-6 grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-truck text-amber-600 mb-2"></i>
                                <span class="text-xs font-medium text-gray-700">Free Shipping</span>
                            </div>
                            <div class="flex flex-col items-center">
                                <i class="fas fa-undo text-amber-600 mb-2"></i>
                                <span class="text-xs font-medium text-gray-700">30-Day Returns</span>
                            </div>
                            <div class="flex flex-col items-center">
                                <i class="fas fa-shield-alt text-amber-600 mb-2"></i>
                                <span class="text-xs font-medium text-gray-700">Secure Payment</span>
                            </div>
                            <div class="flex flex-col items-center">
                                <i class="fas fa-headset text-amber-600 mb-2"></i>
                                <span class="text-xs font-medium text-gray-700">24/7 Support</span>
                            </div>
                        </div>
                    </section>

                    <!-- Product Grid -->
                    <section class="py-12 px-4">
                        <div class="max-w-6xl mx-auto">
                            <h2 class="text-2xl font-serif text-center mb-8" style="color: #1F2937;">Featured Products</h2>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                                <?php for ($i = 1; $i <= 4; $i++): ?>
                                <div class="group cursor-pointer">
                                    <div class="aspect-[3/4] bg-stone-100 mb-3 overflow-hidden flex items-center justify-center">
                                        <i class="fas fa-tshirt text-3xl text-stone-300 group-hover:scale-110 transition"></i>
                                    </div>
                                    <h3 class="font-medium text-sm text-gray-800">Product Name</h3>
                                    <p class="text-amber-600 font-medium">$<?php echo rand(49, 199); ?>.00</p>
                                </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </section>

                    <!-- Footer -->
                    <footer class="py-8 px-4 border-t bg-white">
                        <div class="max-w-6xl mx-auto text-center text-gray-500 text-sm">
                            <p>&copy; 2024 LuxeStore. All rights reserved.</p>
                        </div>
                    </footer>
                </div>

                <?php elseif ($templateId == 2): // ========== RESTAURANT PRO ========== ?>
                <div class="font-sans min-h-full" style="background: #FFFBEB;">
                    <!-- Header -->
                    <nav class="bg-amber-900 text-white">
                        <div class="max-w-6xl mx-auto px-4 sm:px-8 py-4 flex justify-between items-center">
                            <div class="text-2xl font-serif">La <span class="text-amber-400">Cucina</span></div>
                            <div class="hidden md:flex space-x-8 text-sm">
                                <a href="#" class="hover:text-amber-400">Home</a>
                                <a href="#" class="hover:text-amber-400">Menu</a>
                                <a href="#" class="hover:text-amber-400">About</a>
                                <a href="#" class="hover:text-amber-400">Contact</a>
                            </div>
                            <button class="px-5 py-2 bg-amber-500 text-amber-900 font-semibold rounded hover:bg-amber-400 transition text-sm">
                                Reserve Table
                            </button>
                        </div>
                    </nav>

                    <!-- Hero -->
                    <section class="relative h-80 md:h-96 bg-amber-800 flex items-center justify-center overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-amber-900/90 to-amber-800/70"></div>
                        <div class="relative z-10 text-center text-white px-4">
                            <span class="text-amber-300 text-sm tracking-widest uppercase">Welcome to</span>
                            <h1 class="text-4xl md:text-6xl font-serif mt-2 mb-4">La Cucina</h1>
                            <p class="text-amber-100 mb-6 max-w-md mx-auto">Experience authentic Italian cuisine in the heart of the city</p>
                            <div class="flex justify-center gap-4">
                                <button class="px-6 py-3 bg-amber-500 text-amber-900 font-semibold rounded hover:bg-amber-400">View Menu</button>
                                <button class="px-6 py-3 border-2 border-white text-white font-semibold rounded hover:bg-white/10">Book Now</button>
                            </div>
                        </div>
                    </section>

                    <!-- Info Bar -->
                    <section class="bg-amber-900 text-amber-100 py-4">
                        <div class="max-w-4xl mx-auto px-4 flex flex-wrap justify-center gap-8 text-sm">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-clock text-amber-400"></i>
                                <span>Mon-Sun: 11AM - 11PM</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-phone text-amber-400"></i>
                                <span>(555) 123-4567</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-map-marker-alt text-amber-400"></i>
                                <span>123 Main Street</span>
                            </div>
                        </div>
                    </section>

                    <!-- Menu Highlights -->
                    <section class="py-12 px-4">
                        <div class="max-w-6xl mx-auto">
                            <h2 class="text-3xl font-serif text-center text-amber-900 mb-2">Our Menu</h2>
                            <p class="text-center text-amber-700 mb-8">Handcrafted with love and tradition</p>
                            <div class="grid md:grid-cols-3 gap-6">
                                <?php 
                                $dishes = [
                                    ['name' => 'Antipasti', 'desc' => 'Start your journey', 'icon' => 'fa-cheese'],
                                    ['name' => 'Pasta', 'desc' => 'Traditional recipes', 'icon' => 'fa-utensils'],
                                    ['name' => 'Dolci', 'desc' => 'Sweet endings', 'icon' => 'fa-ice-cream']
                                ];
                                foreach ($dishes as $dish): ?>
                                <div class="bg-white rounded-lg p-6 text-center shadow-md hover:shadow-lg transition cursor-pointer">
                                    <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas <?php echo $dish['icon']; ?> text-2xl"></i>
                                    </div>
                                    <h3 class="font-serif text-xl text-amber-900 mb-2"><?php echo $dish['name']; ?></h3>
                                    <p class="text-amber-700 text-sm"><?php echo $dish['desc']; ?></p>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </section>

                    <!-- About Section -->
                    <section class="py-12 px-4 bg-amber-900 text-white">
                        <div class="max-w-4xl mx-auto text-center">
                            <h2 class="text-3xl font-serif mb-4">Our Story</h2>
                            <p class="text-amber-100 leading-relaxed">Since 1985, we've been serving authentic Italian cuisine made with the freshest ingredients and recipes passed down through generations.</p>
                        </div>
                    </section>

                    <!-- Footer -->
                    <footer class="py-6 px-4 bg-amber-950 text-amber-200 text-center text-sm">
                        <p>&copy; 2024 La Cucina. All rights reserved.</p>
                    </footer>
                </div>

                <?php elseif ($templateId == 3): // ========== FREELANCER PORTFOLIO ========== ?>
                <div class="font-sans min-h-full" style="background: #0F172A; color: #E2E8F0;">
                    <!-- Header -->
                    <nav class="px-4 sm:px-8 py-6">
                        <div class="max-w-6xl mx-auto flex justify-between items-center">
                            <div class="text-xl font-bold">
                                <span class="text-purple-400">&lt;</span>Dev<span class="text-pink-400">/&gt;</span>
                            </div>
                            <div class="hidden md:flex space-x-8 text-sm text-gray-400">
                                <a href="#" class="hover:text-white">About</a>
                                <a href="#" class="hover:text-white">Work</a>
                                <a href="#" class="hover:text-white">Services</a>
                                <a href="#" class="hover:text-white">Contact</a>
                            </div>
                            <button class="px-5 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white font-medium rounded-full text-sm hover:opacity-90">
                                Hire Me
                            </button>
                        </div>
                    </nav>

                    <!-- Hero -->
                    <section class="py-16 px-4">
                        <div class="max-w-4xl mx-auto text-center">
                            <div class="w-24 h-24 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full mx-auto mb-6 flex items-center justify-center">
                                <i class="fas fa-user text-3xl text-white"></i>
                            </div>
                            <span class="text-purple-400 text-sm tracking-widest uppercase">Hello, I'm</span>
                            <h1 class="text-4xl md:text-6xl font-bold mt-2 mb-4 text-white">John Designer</h1>
                            <p class="text-xl text-gray-400 mb-8">Creative Developer & UI/UX Designer</p>
                            <div class="flex justify-center gap-4 mb-8">
                                <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center text-gray-400 hover:text-purple-400 hover:bg-gray-700 transition">
                                    <i class="fab fa-github"></i>
                                </a>
                                <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center text-gray-400 hover:text-purple-400 hover:bg-gray-700 transition">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                                <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center text-gray-400 hover:text-purple-400 hover:bg-gray-700 transition">
                                    <i class="fab fa-dribbble"></i>
                                </a>
                            </div>
                        </div>
                    </section>

                    <!-- Skills -->
                    <section class="py-12 px-4 bg-slate-900/50">
                        <div class="max-w-4xl mx-auto">
                            <h2 class="text-2xl font-bold text-center text-white mb-8">What I Do</h2>
                            <div class="grid md:grid-cols-3 gap-6">
                                <?php 
                                $skills = [
                                    ['title' => 'Web Design', 'icon' => 'fa-palette', 'color' => 'purple'],
                                    ['title' => 'Development', 'icon' => 'fa-code', 'color' => 'pink'],
                                    ['title' => 'Branding', 'icon' => 'fa-pen-nib', 'color' => 'blue']
                                ];
                                foreach ($skills as $skill): ?>
                                <div class="bg-slate-800/50 backdrop-blur rounded-xl p-6 border border-slate-700 hover:border-purple-500/50 transition">
                                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center mb-4">
                                        <i class="fas <?php echo $skill['icon']; ?> text-white"></i>
                                    </div>
                                    <h3 class="font-bold text-white mb-2"><?php echo $skill['title']; ?></h3>
                                    <p class="text-gray-400 text-sm">Creating beautiful and functional digital experiences.</p>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </section>

                    <!-- Projects -->
                    <section class="py-12 px-4">
                        <div class="max-w-4xl mx-auto">
                            <h2 class="text-2xl font-bold text-center text-white mb-8">Recent Projects</h2>
                            <div class="grid md:grid-cols-2 gap-4">
                                <?php for ($i = 1; $i <= 4; $i++): ?>
                                <div class="aspect-video bg-gradient-to-br from-purple-900/50 to-pink-900/50 rounded-xl flex items-center justify-center border border-slate-700 hover:border-purple-500/50 transition cursor-pointer group">
                                    <div class="text-center">
                                        <i class="fas fa-folder text-3xl text-purple-400 group-hover:scale-110 transition"></i>
                                        <p class="text-gray-400 text-sm mt-2">Project <?php echo $i; ?></p>
                                    </div>
                                </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </section>

                    <!-- Footer -->
                    <footer class="py-6 px-4 border-t border-slate-800 text-center text-gray-500 text-sm">
                        <p>&copy; 2024 John Designer. All rights reserved.</p>
                    </footer>
                </div>

                <?php elseif ($templateId == 4): // ========== SERVICE BUSINESS ========== ?>
                <div class="font-sans min-h-full bg-white">
                    <!-- Header -->
                    <nav class="bg-white shadow-sm">
                        <div class="max-w-6xl mx-auto px-4 sm:px-8 py-4 flex justify-between items-center">
                            <div class="text-xl font-bold text-blue-600 flex items-center">
                                <div class="w-8 h-8 bg-blue-600 rounded-lg mr-2 flex items-center justify-center">
                                    <i class="fas fa-building text-white text-sm"></i>
                                </div>
                                ServicePro
                            </div>
                            <div class="hidden md:flex space-x-8 text-sm font-medium text-gray-600">
                                <a href="#" class="hover:text-blue-600">Home</a>
                                <a href="#" class="hover:text-blue-600">Services</a>
                                <a href="#" class="hover:text-blue-600">About</a>
                                <a href="#" class="hover:text-blue-600">Contact</a>
                            </div>
                            <div class="flex items-center gap-4">
                                <a href="tel:" class="hidden md:flex items-center text-sm text-gray-600">
                                    <i class="fas fa-phone mr-2 text-blue-600"></i>
                                    (555) 123-4567
                                </a>
                                <button class="px-5 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 text-sm">
                                    Get Quote
                                </button>
                            </div>
                        </div>
                    </nav>

                    <!-- Hero -->
                    <section class="bg-gradient-to-br from-blue-600 to-blue-800 text-white py-16 px-4">
                        <div class="max-w-4xl mx-auto text-center">
                            <h1 class="text-4xl md:text-5xl font-bold mb-6">Professional Services You Can Trust</h1>
                            <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">We deliver excellence in every project. Over 10 years of experience serving businesses like yours.</p>
                            <div class="flex flex-wrap justify-center gap-4">
                                <button class="px-8 py-3 bg-white text-blue-600 font-semibold rounded-lg hover:bg-gray-100">Our Services</button>
                                <button class="px-8 py-3 border-2 border-white text-white font-semibold rounded-lg hover:bg-white/10">Contact Us</button>
                            </div>
                        </div>
                    </section>

                    <!-- Trust Badges -->
                    <section class="py-6 bg-gray-50 border-b">
                        <div class="max-w-4xl mx-auto px-4 flex flex-wrap justify-center gap-8 text-gray-500 text-sm">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-award text-blue-600"></i>
                                <span>Award Winning</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-users text-blue-600"></i>
                                <span>500+ Clients</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-check-circle text-blue-600"></i>
                                <span>Licensed & Insured</span>
                            </div>
                        </div>
                    </section>

                    <!-- Services -->
                    <section class="py-12 px-4">
                        <div class="max-w-6xl mx-auto">
                            <h2 class="text-3xl font-bold text-center text-gray-800 mb-2">Our Services</h2>
                            <p class="text-center text-gray-600 mb-8">Comprehensive solutions for your business needs</p>
                            <div class="grid md:grid-cols-3 gap-6">
                                <?php 
                                $services = [
                                    ['title' => 'Consulting', 'icon' => 'fa-lightbulb'],
                                    ['title' => 'Development', 'icon' => 'fa-cogs'],
                                    ['title' => 'Support', 'icon' => 'fa-headset']
                                ];
                                foreach ($services as $service): ?>
                                <div class="bg-white border rounded-xl p-6 hover:shadow-lg transition">
                                    <div class="w-14 h-14 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mb-4">
                                        <i class="fas <?php echo $service['icon']; ?> text-2xl"></i>
                                    </div>
                                    <h3 class="font-bold text-gray-800 mb-2"><?php echo $service['title']; ?></h3>
                                    <p class="text-gray-600 text-sm">Professional <?php echo strtolower($service['title']); ?> services tailored to your needs.</p>
                                    <a href="#" class="text-blue-600 text-sm font-medium mt-4 inline-block hover:underline">Learn More →</a>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </section>

                    <!-- CTA -->
                    <section class="py-12 px-4 bg-gray-50">
                        <div class="max-w-2xl mx-auto text-center">
                            <h2 class="text-2xl font-bold text-gray-800 mb-4">Ready to Get Started?</h2>
                            <p class="text-gray-600 mb-6">Contact us today for a free consultation</p>
                            <button class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700">
                                Contact Us Now
                            </button>
                        </div>
                    </section>

                    <!-- Footer -->
                    <footer class="py-6 px-4 bg-gray-800 text-gray-400 text-center text-sm">
                        <p>&copy; 2024 ServicePro. All rights reserved.</p>
                    </footer>
                </div>

                <?php elseif ($templateId == 5): // ========== GENERAL BUSINESS ========== ?>
                <div class="font-sans min-h-full bg-white">
                    <!-- Header -->
                    <nav class="bg-white border-b">
                        <div class="max-w-6xl mx-auto px-4 sm:px-8 py-4 flex justify-between items-center">
                            <div class="text-xl font-bold text-teal-600 flex items-center">
                                <div class="w-8 h-8 bg-gradient-to-r from-teal-500 to-emerald-500 rounded-full mr-2"></div>
                                FlexBiz
                            </div>
                            <div class="hidden md:flex space-x-8 text-sm font-medium text-gray-600">
                                <a href="#" class="hover:text-teal-600">Features</a>
                                <a href="#" class="hover:text-teal-600">Pricing</a>
                                <a href="#" class="hover:text-teal-600">About</a>
                            </div>
                            <button class="px-5 py-2 bg-gradient-to-r from-teal-500 to-emerald-500 text-white font-medium rounded-full text-sm hover:opacity-90">
                                Get Started
                            </button>
                        </div>
                    </nav>

                    <!-- Hero -->
                    <section class="py-16 px-4 bg-gradient-to-br from-teal-50 to-emerald-50">
                        <div class="max-w-4xl mx-auto text-center">
                            <span class="inline-block px-4 py-1 bg-teal-100 text-teal-700 text-sm font-medium rounded-full mb-4">✨ New Features Available</span>
                            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">Grow Your Business <span class="text-teal-600">Smarter</span></h1>
                            <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">Everything you need to manage and scale your business in one powerful platform.</p>
                            <div class="flex flex-wrap justify-center gap-4">
                                <button class="px-8 py-3 bg-teal-600 text-white font-semibold rounded-lg hover:bg-teal-700 shadow-lg shadow-teal-600/30">Start Free Trial</button>
                                <button class="px-8 py-3 bg-white text-gray-700 font-semibold rounded-lg border hover:bg-gray-50 flex items-center">
                                    <i class="fas fa-play-circle mr-2 text-teal-600"></i> Watch Demo
                                </button>
                            </div>
                        </div>
                    </section>

                    <!-- Features -->
                    <section class="py-12 px-4">
                        <div class="max-w-6xl mx-auto">
                            <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">Why Choose Us</h2>
                            <div class="grid md:grid-cols-3 gap-6">
                                <?php 
                                $features = [
                                    ['title' => 'Easy Setup', 'icon' => 'fa-rocket', 'color' => 'teal'],
                                    ['title' => 'Powerful Tools', 'icon' => 'fa-tools', 'color' => 'orange'],
                                    ['title' => 'Great Support', 'icon' => 'fa-life-ring', 'color' => 'purple']
                                ];
                                foreach ($features as $feature): ?>
                                <div class="text-center p-6">
                                    <div class="w-14 h-14 bg-<?php echo $feature['color']; ?>-100 text-<?php echo $feature['color']; ?>-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                                        <i class="fas <?php echo $feature['icon']; ?> text-2xl"></i>
                                    </div>
                                    <h3 class="font-bold text-gray-800 mb-2"><?php echo $feature['title']; ?></h3>
                                    <p class="text-gray-600 text-sm">Get started quickly with our intuitive interface.</p>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </section>

                    <!-- Pricing Preview -->
                    <section class="py-12 px-4 bg-gray-50">
                        <div class="max-w-3xl mx-auto text-center">
                            <h2 class="text-2xl font-bold text-gray-800 mb-4">Simple Pricing</h2>
                            <div class="bg-white rounded-xl shadow-lg p-8 border-2 border-teal-500">
                                <span class="text-teal-600 font-medium">POPULAR</span>
                                <div class="text-4xl font-bold text-gray-900 my-4">$29<span class="text-lg text-gray-500">/mo</span></div>
                                <p class="text-gray-600 mb-6">Everything you need to grow</p>
                                <button class="w-full px-6 py-3 bg-teal-600 text-white font-semibold rounded-lg hover:bg-teal-700">Get Started</button>
                            </div>
                        </div>
                    </section>

                    <!-- Footer -->
                    <footer class="py-6 px-4 bg-gray-900 text-gray-400 text-center text-sm">
                        <p>&copy; 2024 FlexBiz. All rights reserved.</p>
                    </footer>
                </div>

                <?php elseif ($templateId == 6): // ========== E-COMMERCE STARTER ========== ?>
                <div class="font-sans min-h-full bg-white">
                    <!-- Header -->
                    <nav class="bg-white border-b">
                        <div class="max-w-6xl mx-auto px-4 sm:px-8 py-3 flex justify-between items-center">
                            <div class="text-xl font-bold text-emerald-600">ShopEasy</div>
                            <div class="hidden md:flex space-x-6 text-sm">
                                <a href="#" class="text-gray-600 hover:text-emerald-600">Categories</a>
                                <a href="#" class="text-gray-600 hover:text-emerald-600">Deals</a>
                                <a href="#" class="text-gray-600 hover:text-emerald-600">New</a>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="hidden md:flex items-center bg-gray-100 rounded-lg px-3 py-2">
                                    <i class="fas fa-search text-gray-400 mr-2"></i>
                                    <input type="text" placeholder="Search..." class="bg-transparent text-sm outline-none w-32">
                                </div>
                                <i class="fas fa-heart text-gray-400 cursor-pointer hover:text-red-500"></i>
                                <div class="relative cursor-pointer">
                                    <i class="fas fa-shopping-cart text-gray-600 hover:text-emerald-600"></i>
                                    <span class="absolute -top-2 -right-2 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
                                </div>
                            </div>
                        </div>
                    </nav>

                    <!-- Hero Banner -->
                    <section class="bg-gradient-to-r from-emerald-500 to-teal-500 py-12 px-4">
                        <div class="max-w-4xl mx-auto text-center text-white">
                            <span class="inline-block px-3 py-1 bg-white/20 rounded-full text-sm mb-4">🔥 Summer Sale</span>
                            <h1 class="text-4xl md:text-5xl font-bold mb-4">Up to 50% Off</h1>
                            <p class="text-emerald-100 mb-6">Shop the biggest deals of the season</p>
                            <button class="px-8 py-3 bg-white text-emerald-600 font-semibold rounded-lg hover:bg-gray-100">Shop Now</button>
                        </div>
                    </section>

                    <!-- Categories -->
                    <section class="py-8 px-4 border-b">
                        <div class="max-w-6xl mx-auto flex justify-center flex-wrap gap-4">
                            <?php 
                            $cats = ['Electronics', 'Fashion', 'Home', 'Sports'];
                            foreach ($cats as $cat): ?>
                            <button class="px-4 py-2 bg-gray-100 rounded-full text-sm font-medium hover:bg-emerald-100 hover:text-emerald-700"><?php echo $cat; ?></button>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <!-- Products -->
                    <section class="py-12 px-4">
                        <div class="max-w-6xl mx-auto">
                            <h2 class="text-2xl font-bold text-gray-800 mb-6">Featured Products</h2>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <?php for ($i = 1; $i <= 4; $i++): ?>
                                <div class="bg-white border rounded-lg overflow-hidden hover:shadow-lg transition group cursor-pointer">
                                    <div class="aspect-square bg-gray-100 relative flex items-center justify-center">
                                        <i class="fas fa-box text-3xl text-gray-300"></i>
                                        <?php if ($i == 1): ?>
                                        <span class="absolute top-2 left-2 px-2 py-1 bg-red-500 text-white text-xs font-bold rounded">SALE</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="p-4">
                                        <h3 class="font-medium text-gray-800 text-sm mb-1">Product Name</h3>
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-emerald-600">$<?php echo rand(19, 99); ?></span>
                                            <?php if ($i == 1): ?>
                                            <span class="text-gray-400 text-sm line-through">$129</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </section>

                    <!-- Footer -->
                    <footer class="py-6 px-4 bg-gray-900 text-gray-400 text-center text-sm">
                        <p>&copy; 2024 ShopEasy. All rights reserved.</p>
                    </footer>
                </div>

                <?php elseif ($templateId == 7): // ========== URBAN STREETWEAR ========== ?>
                <div class="font-sans min-h-full bg-black text-white">
                    <!-- Header -->
                    <nav class="border-b border-gray-800">
                        <div class="max-w-6xl mx-auto px-4 sm:px-8 py-5 flex justify-between items-center">
                            <div class="text-2xl font-black tracking-tighter">URBAN<span class="text-purple-500">.</span></div>
                            <div class="hidden md:flex space-x-8 text-sm uppercase tracking-widest font-medium">
                                <a href="#" class="text-gray-400 hover:text-white">New</a>
                                <a href="#" class="text-gray-400 hover:text-white">Men</a>
                                <a href="#" class="text-gray-400 hover:text-white">Women</a>
                                <a href="#" class="text-gray-400 hover:text-white">Drops</a>
                            </div>
                            <div class="flex items-center space-x-4">
                                <i class="fas fa-search text-gray-400 hover:text-white cursor-pointer"></i>
                                <div class="relative">
                                    <i class="fas fa-shopping-bag text-gray-400 hover:text-white cursor-pointer"></i>
                                    <span class="absolute -top-2 -right-2 w-4 h-4 bg-purple-500 text-white text-xs rounded-full flex items-center justify-center">1</span>
                                </div>
                            </div>
                        </div>
                    </nav>

                    <!-- Hero -->
                    <section class="relative min-h-[400px] flex items-center justify-center overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-purple-900/50 via-black to-blue-900/50"></div>
                        <div class="absolute inset-0 opacity-20">
                            <div class="absolute top-10 left-10 w-32 h-32 border border-purple-500 rotate-45"></div>
                            <div class="absolute bottom-10 right-10 w-24 h-24 border border-blue-500 rotate-12"></div>
                        </div>
                        <div class="relative z-10 text-center px-4">
                            <span class="text-purple-400 text-sm tracking-[0.3em] uppercase mb-4 block">New Collection</span>
                            <h1 class="text-5xl md:text-8xl font-black tracking-tighter mb-6">STREET<br>CULTURE</h1>
                            <button class="px-10 py-4 bg-white text-black uppercase font-bold tracking-widest hover:bg-purple-500 hover:text-white transition">
                                Shop Now
                            </button>
                        </div>
                    </section>

                    <!-- Categories -->
                    <section class="grid grid-cols-1 md:grid-cols-2">
                        <div class="h-64 bg-gradient-to-br from-gray-900 to-gray-800 flex items-center justify-center cursor-pointer hover:from-purple-900/50 hover:to-gray-900 transition group">
                            <div class="text-center">
                                <h2 class="text-3xl font-black uppercase tracking-tight group-hover:text-purple-400 transition">Menswear</h2>
                                <span class="text-gray-500 text-sm uppercase tracking-widest">Explore →</span>
                            </div>
                        </div>
                        <div class="h-64 bg-gradient-to-br from-gray-800 to-gray-900 flex items-center justify-center cursor-pointer hover:from-gray-900 hover:to-purple-900/50 transition group">
                            <div class="text-center">
                                <h2 class="text-3xl font-black uppercase tracking-tight group-hover:text-purple-400 transition">Womenswear</h2>
                                <span class="text-gray-500 text-sm uppercase tracking-widest">Explore →</span>
                            </div>
                        </div>
                    </section>

                    <!-- Featured -->
                    <section class="py-12 px-4">
                        <div class="max-w-6xl mx-auto">
                            <h2 class="text-xl font-black uppercase tracking-widest text-center mb-8">Latest Drops</h2>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <?php for ($i = 1; $i <= 4; $i++): ?>
                                <div class="group cursor-pointer">
                                    <div class="aspect-[3/4] bg-gray-900 mb-3 flex items-center justify-center overflow-hidden">
                                        <i class="fas fa-tshirt text-3xl text-gray-700 group-hover:scale-110 group-hover:text-purple-500 transition"></i>
                                    </div>
                                    <h3 class="font-bold text-sm uppercase">Item Name</h3>
                                    <p class="text-gray-500">$<?php echo rand(89, 299); ?></p>
                                </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </section>

                    <!-- Footer -->
                    <footer class="py-6 px-4 border-t border-gray-800 text-center">
                        <div class="flex justify-center space-x-6 mb-4">
                            <i class="fab fa-instagram text-gray-500 hover:text-white cursor-pointer"></i>
                            <i class="fab fa-tiktok text-gray-500 hover:text-white cursor-pointer"></i>
                            <i class="fab fa-twitter text-gray-500 hover:text-white cursor-pointer"></i>
                        </div>
                        <p class="text-gray-600 text-sm">&copy; 2024 URBAN. All rights reserved.</p>
                    </footer>
                </div>

                <?php elseif ($templateId == 8): // ========== TECH STARTUP ========== ?>
                <div class="font-sans min-h-full bg-white">
                    <!-- Header -->
                    <nav class="bg-white border-b">
                        <div class="max-w-6xl mx-auto px-4 sm:px-8 py-4 flex justify-between items-center">
                            <div class="text-xl font-bold text-blue-600 flex items-center">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg mr-2"></div>
                                TechFlow
                            </div>
                            <div class="hidden md:flex space-x-8 text-sm font-medium text-gray-600">
                                <a href="#" class="hover:text-blue-600">Product</a>
                                <a href="#" class="hover:text-blue-600">Features</a>
                                <a href="#" class="hover:text-blue-600">Pricing</a>
                                <a href="#" class="hover:text-blue-600">About</a>
                            </div>
                            <div class="flex items-center gap-3">
                                <a href="#" class="text-gray-600 hover:text-blue-600 text-sm font-medium">Sign In</a>
                                <button class="px-5 py-2 bg-blue-600 text-white font-medium rounded-full text-sm hover:bg-blue-700">
                                    Start Free
                                </button>
                            </div>
                        </div>
                    </nav>

                    <!-- Hero -->
                    <section class="py-16 px-4 bg-gradient-to-b from-blue-50 to-white">
                        <div class="max-w-4xl mx-auto text-center">
                            <span class="inline-flex items-center px-4 py-1 bg-blue-100 text-blue-700 text-sm font-medium rounded-full mb-6">
                                <i class="fas fa-sparkles mr-2"></i> Version 2.0 is here
                            </span>
                            <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6 leading-tight">
                                Manage your workflow<br><span class="text-blue-600">without the chaos.</span>
                            </h1>
                            <p class="text-xl text-gray-500 mb-10 max-w-2xl mx-auto">
                                The all-in-one platform that helps teams collaborate, plan, and ship faster than ever before.
                            </p>
                            <div class="flex flex-wrap justify-center gap-4">
                                <button class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 shadow-lg shadow-blue-600/30">
                                    Start Free Trial
                                </button>
                                <button class="px-8 py-3 bg-white text-gray-700 font-semibold rounded-lg border hover:bg-gray-50 flex items-center">
                                    <i class="fas fa-play mr-2 text-blue-600"></i> Watch Demo
                                </button>
                            </div>
                        </div>
                    </section>

                    <!-- Dashboard Preview -->
                    <section class="px-4 -mt-4 mb-12">
                        <div class="max-w-4xl mx-auto">
                            <div class="bg-gray-900 rounded-xl shadow-2xl p-6 border border-gray-700">
                                <div class="flex items-center gap-2 mb-4">
                                    <i class="fas fa-chart-line text-4xl text-blue-500"></i>
                                    <h2 class="text-xl font-bold text-white">Dashboard Interface Preview</h2>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="bg-gray-800 rounded-lg p-4">
                                        <h3 class="text-lg font-semibold text-gray-200 mb-2">Sales Overview</h3>
                                        <div class="flex items-center justify-between text-gray-400 text-sm">
                                            <span>Today</span>
                                            <span class="font-medium text-white">$120.00</span>
                                        </div>
                                        <div class="h-2.5 bg-gray-700 rounded-full mt-2">
                                            <div class="h-2.5 bg-blue-500 rounded-full" style="width: 75%"></div>
                                        </div>
                                    </div>
                                    <div class="bg-gray-800 rounded-lg p-4">
                                        <h3 class="text-lg font-semibold text-gray-200 mb-2">Traffic Sources</h3>
                                        <div class="flex items-center justify-between text-gray-400 text-sm">
                                            <span>Direct</span>
                                            <span class="font-medium text-white">45%</span>
                                        </div>
                                        <div class="h-2.5 bg-gray-700 rounded-full mt-2">
                                            <div class="h-2.5 bg-green-500 rounded-full" style="width: 45%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <?php elseif ($templateId == 9): // ========== BOUTIQUE SHOP ========== ?>
                <div class="font-sans min-h-full" style="background: #FDF2F8;">
                    <!-- Header -->
                    <nav class="bg-white/80 backdrop-blur-sm border-b border-pink-100">
                        <div class="max-w-6xl mx-auto px-4 sm:px-8 py-4 flex justify-between items-center">
                            <div class="text-xl font-serif italic text-pink-700">Bella Boutique</div>
                            <div class="hidden md:flex space-x-8 text-sm font-medium text-gray-600">
                                <a href="#" class="hover:text-pink-600">Home</a>
                                <a href="#" class="hover:text-pink-600">Shop</a>
                                <a href="#" class="hover:text-pink-600">Collections</a>
                                <a href="#" class="hover:text-pink-600">About</a>
                            </div>
                            <div class="flex items-center space-x-4 text-pink-600">
                                <i class="fas fa-search cursor-pointer"></i>
                                <i class="fas fa-heart cursor-pointer"></i>
                                <i class="fas fa-shopping-bag cursor-pointer"></i>
                            </div>
                        </div>
                    </nav>

                    <!-- Hero -->
                    <section class="py-12 px-4">
                        <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-8 items-center">
                            <div>
                                <span class="text-pink-500 text-sm font-medium tracking-widest uppercase">New Arrivals</span>
                                <h1 class="text-4xl md:text-5xl font-serif text-gray-800 mt-3 mb-6 leading-tight">
                                    Discover Your <em class="text-pink-600">Perfect Style</em>
                                </h1>
                                <p class="text-gray-600 mb-8">Curated pieces that celebrate femininity and elegance. Shop our latest collection.</p>
                                <button class="px-8 py-3 bg-pink-600 text-white font-medium rounded-full hover:bg-pink-700 transition">
                                    Shop Collection
                                </button>
                            </div>
                            <div class="h-64 bg-gradient-to-br from-pink-200 to-pink-300 rounded-3xl flex items-center justify-center">
                                <i class="fas fa-tshirt text-6xl text-pink-400"></i>
                            </div>
                        </div>
                    </section>

                    <!-- Categories -->
                    <section class="py-12 px-4 bg-white">
                        <div class="max-w-6xl mx-auto">
                            <h2 class="text-2xl font-serif text-center text-gray-800 mb-8">Shop by Category</h2>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <?php 
                                $cats = ['Dresses', 'Accessories', 'Bags', 'Jewelry'];
                                foreach($cats as $cat): ?>
                                <div class="group cursor-pointer">
                                    <div class="aspect-square bg-pink-50 rounded-2xl mb-3 flex items-center justify-center group-hover:bg-pink-100 transition">
                                        <i class="fas fa-gem text-3xl text-pink-300"></i>
                                    </div>
                                    <p class="text-center font-medium text-gray-700"><?php echo $cat; ?></p>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </section>

                    <!-- Featured Products -->
                    <section class="py-12 px-4">
                        <div class="max-w-6xl mx-auto">
                            <h2 class="text-2xl font-serif text-center text-gray-800 mb-8">Bestsellers</h2>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                                <?php for($i = 1; $i <= 4; $i++): ?>
                                <div class="group cursor-pointer">
                                    <div class="aspect-[3/4] bg-pink-50 rounded-xl mb-3 flex items-center justify-center relative overflow-hidden">
                                        <i class="fas fa-tag text-3xl text-pink-200"></i>
                                        <div class="absolute top-2 right-2 w-8 h-8 bg-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                            <i class="fas fa-heart text-pink-500 text-sm"></i>
                                        </div>
                                    </div>
                                    <h3 class="font-medium text-gray-800 text-sm">Product Name</h3>
                                    <p class="text-pink-600 font-semibold">₱1,299</p>
                                </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </section>

                    <!-- Footer -->
                    <footer class="py-8 px-4 bg-pink-700 text-white text-center">
                        <p class="font-serif text-lg italic mb-2">Bella Boutique</p>
                        <p class="text-pink-200 text-sm">© 2024 All rights reserved. Powered by FilDevStudio</p>
                    </footer>
                </div>

                <?php elseif ($templateId == 10): // ========== ELECTRONICS STORE ========== ?>
                <div class="font-sans min-h-full bg-slate-900 text-white">
                    <!-- Header -->
                    <nav class="bg-slate-950 border-b border-slate-800">
                        <div class="max-w-6xl mx-auto px-4 sm:px-8 py-4 flex justify-between items-center">
                            <div class="text-xl font-bold text-cyan-400">TechZone</div>
                            <div class="hidden md:flex space-x-6 text-sm text-gray-400">
                                <a href="#" class="hover:text-cyan-400">Phones</a>
                                <a href="#" class="hover:text-cyan-400">Laptops</a>
                                <a href="#" class="hover:text-cyan-400">Accessories</a>
                                <a href="#" class="hover:text-cyan-400">Gaming</a>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="hidden md:flex items-center bg-slate-800 rounded-full px-4 py-2">
                                    <i class="fas fa-search text-gray-500 mr-2"></i>
                                    <span class="text-gray-500 text-sm">Search products...</span>
                                </div>
                                <i class="fas fa-shopping-cart text-gray-400 cursor-pointer"></i>
                            </div>
                        </div>
                    </nav>

                    <!-- Hero -->
                    <section class="py-12 px-4 bg-gradient-to-r from-slate-900 via-cyan-900/30 to-slate-900">
                        <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-8 items-center">
                            <div>
                                <span class="inline-flex items-center px-3 py-1 bg-cyan-500/20 text-cyan-400 text-xs font-medium rounded-full mb-4">
                                    <i class="fas fa-bolt mr-2"></i>NEW RELEASE
                                </span>
                                <h1 class="text-4xl md:text-5xl font-bold mb-4 leading-tight">
                                    Latest Tech <span class="text-cyan-400">Deals</span>
                                </h1>
                                <p class="text-gray-400 mb-6">Get the newest gadgets at unbeatable prices. Free shipping on orders over ₱5,000.</p>
                                <button class="px-8 py-3 bg-cyan-500 text-slate-900 font-bold rounded-lg hover:bg-cyan-400 transition">
                                    Shop Now
                                </button>
                            </div>
                            <div class="h-64 bg-gradient-to-br from-cyan-500/20 to-blue-500/20 rounded-2xl flex items-center justify-center border border-cyan-500/30">
                                <i class="fas fa-laptop text-6xl text-cyan-500/50"></i>
                            </div>
                        </div>
                    </section>

                    <!-- Categories -->
                    <section class="py-8 px-4 border-y border-slate-800">
                        <div class="max-w-6xl mx-auto flex flex-wrap justify-center gap-4">
                            <?php 
                            $techs = [
                                ['icon' => 'fa-mobile-alt', 'name' => 'Phones'],
                                ['icon' => 'fa-laptop', 'name' => 'Laptops'],
                                ['icon' => 'fa-headphones', 'name' => 'Audio'],
                                ['icon' => 'fa-gamepad', 'name' => 'Gaming'],
                                ['icon' => 'fa-camera', 'name' => 'Cameras'],
                            ];
                            foreach($techs as $tech): ?>
                            <div class="flex items-center gap-2 px-4 py-2 bg-slate-800 rounded-lg hover:bg-slate-700 cursor-pointer transition">
                                <i class="fas <?php echo $tech['icon']; ?> text-cyan-400"></i>
                                <span class="text-sm"><?php echo $tech['name']; ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <!-- Products -->
                    <section class="py-12 px-4">
                        <div class="max-w-6xl mx-auto">
                            <h2 class="text-2xl font-bold mb-8">Featured Products</h2>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <?php for($i = 1; $i <= 4; $i++): ?>
                                <div class="bg-slate-800 rounded-xl p-4 hover:bg-slate-750 transition cursor-pointer">
                                    <div class="aspect-square bg-slate-700 rounded-lg mb-3 flex items-center justify-center">
                                        <i class="fas fa-microchip text-3xl text-cyan-500/50"></i>
                                    </div>
                                    <h3 class="font-medium text-sm mb-1">Tech Product <?php echo $i; ?></h3>
                                    <div class="flex items-center gap-1 mb-2">
                                        <?php for($s=0; $s<5; $s++): ?>
                                        <i class="fas fa-star text-yellow-400 text-xs"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <p class="text-cyan-400 font-bold">₱12,999</p>
                                </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </section>

                    <!-- Footer -->
                    <footer class="py-6 px-4 bg-slate-950 border-t border-slate-800 text-center">
                        <p class="text-cyan-400 font-bold mb-2">TechZone</p>
                        <p class="text-gray-500 text-sm">© 2024 All rights reserved. Powered by FilDevStudio</p>
                    </footer>
                </div>

                <?php elseif ($templateId == 11): // ========== GROCERY & SUPERMARKET ========== ?>
                <div class="font-sans min-h-full bg-green-50">
                    <!-- Header -->
                    <nav class="bg-green-700 text-white">
                        <div class="max-w-6xl mx-auto px-4 sm:px-8 py-4 flex justify-between items-center">
                            <div class="text-xl font-bold flex items-center gap-2">
                                <i class="fas fa-leaf"></i>
                                FreshMart
                            </div>
                            <div class="hidden md:flex space-x-6 text-sm">
                                <a href="#" class="hover:text-green-200">Fresh Produce</a>
                                <a href="#" class="hover:text-green-200">Meat & Seafood</a>
                                <a href="#" class="hover:text-green-200">Bakery</a>
                                <a href="#" class="hover:text-green-200">Beverages</a>
                            </div>
                            <div class="flex items-center space-x-4">
                                <i class="fas fa-search cursor-pointer"></i>
                                <div class="relative">
                                    <i class="fas fa-shopping-cart cursor-pointer"></i>
                                    <span class="absolute -top-2 -right-2 w-5 h-5 bg-yellow-400 text-green-800 rounded-full text-xs flex items-center justify-center font-bold">3</span>
                                </div>
                            </div>
                        </div>
                    </nav>

                    <!-- Promo Banner -->
                    <div class="bg-yellow-400 text-green-800 text-center py-2 text-sm font-medium">
                        <i class="fas fa-tag mr-2"></i>WEEKLY DEALS: Up to 50% OFF on selected items! Free delivery over ₱1,500
                    </div>

                    <!-- Hero -->
                    <section class="py-12 px-4 bg-gradient-to-r from-green-600 to-green-700 text-white">
                        <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-8 items-center">
                            <div>
                                <h1 class="text-4xl md:text-5xl font-bold mb-4">Fresh & Healthy <span class="text-yellow-300">Everyday</span></h1>
                                <p class="text-green-100 mb-6">Quality groceries delivered to your doorstep. Shop from the comfort of your home.</p>
                                <button class="px-8 py-3 bg-yellow-400 text-green-800 font-bold rounded-lg hover:bg-yellow-300 transition">
                                    Shop Now
                                </button>
                            </div>
                            <div class="h-48 bg-green-500/50 rounded-2xl flex items-center justify-center">
                                <i class="fas fa-apple-alt text-6xl text-green-300"></i>
                            </div>
                        </div>
                    </section>

                    <!-- Categories -->
                    <section class="py-8 px-4 bg-white">
                        <div class="max-w-6xl mx-auto">
                            <div class="flex flex-wrap justify-center gap-4">
                                <?php 
                                $groceries = [
                                    ['icon' => 'fa-carrot', 'name' => 'Vegetables', 'color' => 'bg-orange-100 text-orange-600'],
                                    ['icon' => 'fa-apple-alt', 'name' => 'Fruits', 'color' => 'bg-red-100 text-red-600'],
                                    ['icon' => 'fa-drumstick-bite', 'name' => 'Meat', 'color' => 'bg-pink-100 text-pink-600'],
                                    ['icon' => 'fa-fish', 'name' => 'Seafood', 'color' => 'bg-blue-100 text-blue-600'],
                                    ['icon' => 'fa-bread-slice', 'name' => 'Bakery', 'color' => 'bg-amber-100 text-amber-600'],
                                    ['icon' => 'fa-wine-bottle', 'name' => 'Drinks', 'color' => 'bg-purple-100 text-purple-600'],
                                ];
                                foreach($groceries as $g): ?>
                                <div class="flex flex-col items-center gap-2 cursor-pointer group">
                                    <div class="w-16 h-16 <?php echo $g['color']; ?> rounded-full flex items-center justify-center group-hover:scale-110 transition">
                                        <i class="fas <?php echo $g['icon']; ?> text-xl"></i>
                                    </div>
                                    <span class="text-sm text-gray-600"><?php echo $g['name']; ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </section>

                    <!-- Products -->
                    <section class="py-12 px-4">
                        <div class="max-w-6xl mx-auto">
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="text-2xl font-bold text-green-800">Today's Deals</h2>
                                <a href="#" class="text-green-600 text-sm hover:underline">View All →</a>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <?php for($i = 1; $i <= 4; $i++): ?>
                                <div class="bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition cursor-pointer">
                                    <div class="relative">
                                        <div class="aspect-square bg-green-100 rounded-lg mb-3 flex items-center justify-center">
                                            <i class="fas fa-box text-3xl text-green-300"></i>
                                        </div>
                                        <span class="absolute top-2 left-2 px-2 py-1 bg-red-500 text-white text-xs font-bold rounded">-20%</span>
                                    </div>
                                    <h3 class="font-medium text-gray-800 text-sm mb-1">Fresh Product</h3>
                                    <div class="flex items-center gap-2">
                                        <span class="text-green-600 font-bold">₱89</span>
                                        <span class="text-gray-400 text-sm line-through">₱110</span>
                                    </div>
                                </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </section>

                    <!-- Footer -->
                    <footer class="py-8 px-4 bg-green-800 text-white text-center">
                        <p class="font-bold text-lg mb-2"><i class="fas fa-leaf mr-2"></i>FreshMart</p>
                        <p class="text-green-200 text-sm">© 2024 All rights reserved. Powered by FilDevStudio</p>
                    </footer>
                </div>

                <?php elseif ($templateId == 12): // ========== SARI-SARI STORE ========== ?>
                <div class="font-sans min-h-full" style="background: linear-gradient(180deg, #FFF7ED 0%, #FFEDD5 100%);">
                    <!-- Header -->
                    <nav class="bg-orange-500 text-white shadow-lg">
                        <div class="max-w-6xl mx-auto px-4 sm:px-8 py-4 flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <div class="w-10 h-10 bg-yellow-400 rounded-full flex items-center justify-center">
                                    <i class="fas fa-store text-orange-600"></i>
                                </div>
                                <div>
                                    <div class="font-bold">Aling Nena's Store</div>
                                    <div class="text-xs text-orange-100">Sari-Sari & General Merchandise</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 text-sm">
                                <span class="hidden md:flex items-center gap-1">
                                    <i class="fas fa-phone"></i> 0917-XXX-XXXX
                                </span>
                                <span class="bg-yellow-400 text-orange-700 px-3 py-1 rounded-full font-bold text-xs">
                                    OPEN NOW
                                </span>
                            </div>
                        </div>
                    </nav>

                    <!-- Promo Banner -->
                    <div class="bg-yellow-400 text-orange-800 py-3 overflow-hidden">
                        <div class="animate-marquee whitespace-nowrap text-sm font-medium">
                            🎉 MERON KAMI: Load • Bills Payment • Yelo • Softdrinks • Snacks • Groceries • At marami pa! 🛒 PISO WIFI AVAILABLE! 📶
                        </div>
                    </div>
                    <style>.animate-marquee { animation: marquee 15s linear infinite; } @keyframes marquee { 0% { transform: translateX(100%); } 100% { transform: translateX(-100%); } }</style>

                    <!-- Hero -->
                    <section class="py-8 px-4">
                        <div class="max-w-6xl mx-auto">
                            <div class="bg-gradient-to-r from-orange-500 to-red-500 rounded-3xl p-8 text-white text-center">
                                <h1 class="text-3xl md:text-4xl font-bold mb-2">
                                    Malapit, Mura, at Kumpleto!
                                </h1>
                                <p class="text-orange-100 mb-4">Your neighborhood one-stop shop for all your daily needs</p>
                                <div class="flex flex-wrap justify-center gap-4">
                                    <span class="bg-white/20 px-4 py-2 rounded-full text-sm flex items-center gap-2">
                                        <i class="fas fa-mobile-alt"></i> E-Load
                                    </span>
                                    <span class="bg-white/20 px-4 py-2 rounded-full text-sm flex items-center gap-2">
                                        <i class="fas fa-receipt"></i> Bills Payment
                                    </span>
                                    <span class="bg-white/20 px-4 py-2 rounded-full text-sm flex items-center gap-2">
                                        <i class="fas fa-wifi"></i> Piso WiFi
                                    </span>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Tingi Products -->
                    <section class="py-8 px-4">
                        <div class="max-w-6xl mx-auto">
                            <h2 class="text-2xl font-bold text-orange-800 mb-6 flex items-center gap-2">
                                <i class="fas fa-tags text-yellow-500"></i> Presyo ng Tingi
                            </h2>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <?php 
                                $products = [
                                    ['name' => 'Bigas (1 kilo)', 'price' => '₱55'],
                                    ['name' => 'Itlog (1 pc)', 'price' => '₱9'],
                                    ['name' => 'Kape 3-in-1', 'price' => '₱8'],
                                    ['name' => 'Sardinas Lata', 'price' => '₱25'],
                                    ['name' => 'Shampoo Sachet', 'price' => '₱6'],
                                    ['name' => 'Sabon Panglaba', 'price' => '₱12'],
                                    ['name' => 'Softdrinks Mismo', 'price' => '₱15'],
                                    ['name' => 'Yelo (bag)', 'price' => '₱5'],
                                ];
                                foreach($products as $p): ?>
                                <div class="bg-white rounded-xl p-4 shadow-sm border-2 border-orange-100 hover:border-orange-300 transition cursor-pointer">
                                    <div class="aspect-square bg-orange-50 rounded-lg mb-3 flex items-center justify-center">
                                        <i class="fas fa-box-open text-2xl text-orange-300"></i>
                                    </div>
                                    <h3 class="font-medium text-gray-800 text-sm"><?php echo $p['name']; ?></h3>
                                    <p class="text-orange-600 font-bold text-lg"><?php echo $p['price']; ?></p>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </section>

                    <!-- Services -->
                    <section class="py-8 px-4 bg-orange-500">
                        <div class="max-w-6xl mx-auto text-center text-white">
                            <h2 class="text-2xl font-bold mb-6">Iba Pang Serbisyo</h2>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="bg-white/20 backdrop-blur rounded-xl p-4">
                                    <i class="fas fa-mobile-alt text-3xl text-yellow-300 mb-2"></i>
                                    <h3 class="font-bold">E-Load</h3>
                                    <p class="text-xs text-orange-100">All networks</p>
                                </div>
                                <div class="bg-white/20 backdrop-blur rounded-xl p-4">
                                    <i class="fas fa-bolt text-3xl text-yellow-300 mb-2"></i>
                                    <h3 class="font-bold">Bills Payment</h3>
                                    <p class="text-xs text-orange-100">Meralco, Water, etc.</p>
                                </div>
                                <div class="bg-white/20 backdrop-blur rounded-xl p-4">
                                    <i class="fas fa-money-bill-wave text-3xl text-yellow-300 mb-2"></i>
                                    <h3 class="font-bold">GCash/Maya</h3>
                                    <p class="text-xs text-orange-100">Cash-in/Cash-out</p>
                                </div>
                                <div class="bg-white/20 backdrop-blur rounded-xl p-4">
                                    <i class="fas fa-wifi text-3xl text-yellow-300 mb-2"></i>
                                    <h3 class="font-bold">Piso WiFi</h3>
                                    <p class="text-xs text-orange-100">Fast internet</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Location -->
                    <section class="py-8 px-4">
                        <div class="max-w-6xl mx-auto text-center">
                            <h2 class="text-xl font-bold text-orange-800 mb-4">📍 Bisitahin Kami!</h2>
                            <p class="text-gray-600">123 Sampaguita Street, Brgy. Masaya, Your City</p>
                            <p class="text-orange-600 font-medium mt-2"><i class="fas fa-clock mr-2"></i>Bukas: 6AM - 10PM Daily</p>
                        </div>
                    </section>

                    <!-- Footer -->
                    <footer class="py-6 px-4 bg-orange-600 text-white text-center">
                        <p class="font-bold text-lg mb-1">Aling Nena's Sari-Sari Store</p>
                        <p class="text-orange-200 text-sm">© 2024 Powered by FilDevStudio</p>
                    </footer>
                </div>

                <?php elseif ($templateId == 13): // ========== SARI-SARI PLUS (MODERN) ========== ?>
                <div class="font-sans min-h-full bg-cyan-50">
                    <!-- Header -->
                    <nav class="bg-white shadow-sm sticky top-0 z-50">
                        <div class="max-w-6xl mx-auto px-4 sm:px-8 py-3 flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <div class="w-10 h-10 bg-gradient-to-br from-cyan-500 to-orange-500 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-store text-white"></i>
                                </div>
                                <span class="font-bold text-gray-800">Tindahan<span class="text-cyan-600">Online</span></span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="hidden md:flex items-center gap-1 text-sm text-gray-500">
                                    <i class="fas fa-map-marker-alt text-cyan-500"></i> Brgy. Sample
                                </span>
                                <button class="bg-cyan-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-cyan-600 transition">
                                    <i class="fas fa-shopping-basket mr-1"></i> Order Now
                                </button>
                            </div>
                        </div>
                    </nav>

                    <!-- Hero -->
                    <section class="py-8 px-4">
                        <div class="max-w-6xl mx-auto">
                            <div class="bg-gradient-to-r from-cyan-600 to-cyan-700 rounded-2xl p-8 text-white">
                                <div class="grid md:grid-cols-2 gap-6 items-center">
                                    <div>
                                        <span class="inline-flex items-center gap-2 bg-white/20 px-3 py-1 rounded-full text-sm mb-4">
                                            <i class="fas fa-motorcycle"></i> Delivery Available
                                        </span>
                                        <h1 class="text-3xl md:text-4xl font-bold mb-3">
                                            Your Neighborhood Store, Now Online!
                                        </h1>
                                        <p class="text-cyan-100 mb-6">Order groceries, load, and more. Delivered to your door or ready for pickup.</p>
                                        <div class="flex flex-wrap gap-3">
                                            <button class="bg-orange-500 px-6 py-3 rounded-xl font-bold hover:bg-orange-600 transition">
                                                <i class="fas fa-paper-plane mr-2"></i>Order via Chat
                                            </button>
                                            <button class="bg-white/20 px-6 py-3 rounded-xl font-medium hover:bg-white/30 transition">
                                                <i class="fas fa-phone mr-2"></i>Call Us
                                            </button>
                                        </div>
                                    </div>
                                    <div class="hidden md:flex justify-center">
                                        <div class="w-48 h-48 bg-white/10 rounded-full flex items-center justify-center">
                                            <i class="fas fa-shopping-cart text-6xl text-cyan-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Quick Services -->
                    <section class="py-6 px-4">
                        <div class="max-w-6xl mx-auto">
                            <div class="grid grid-cols-4 gap-3">
                                <?php
                                $services = [
                                    ['icon' => 'fa-mobile-alt', 'name' => 'E-Load', 'color' => 'bg-blue-500'],
                                    ['icon' => 'fa-receipt', 'name' => 'Bills', 'color' => 'bg-green-500'],
                                    ['icon' => 'fa-money-bill-wave', 'name' => 'GCash', 'color' => 'bg-cyan-500'],
                                    ['icon' => 'fa-wifi', 'name' => 'WiFi', 'color' => 'bg-orange-500'],
                                ];
                                foreach($services as $s): ?>
                                <div class="text-center cursor-pointer group">
                                    <div class="w-14 h-14 <?php echo $s['color']; ?> rounded-2xl flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition shadow-lg">
                                        <i class="fas <?php echo $s['icon']; ?> text-white text-xl"></i>
                                    </div>
                                    <span class="text-xs font-medium text-gray-600"><?php echo $s['name']; ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </section>

                    <!-- Products -->
                    <section class="py-8 px-4">
                        <div class="max-w-6xl mx-auto">
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="text-xl font-bold text-gray-800">Available Items</h2>
                                <a href="#" class="text-cyan-600 text-sm font-medium">View All</a>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <?php 
                                $items = [
                                    ['name' => 'Bigas Premium', 'price' => '₱60/kilo', 'stock' => 'In Stock'],
                                    ['name' => 'Cooking Oil', 'price' => '₱85/liter', 'stock' => 'In Stock'],
                                    ['name' => 'Canned Goods', 'price' => 'From ₱25', 'stock' => 'Many'],
                                    ['name' => 'Snacks Pack', 'price' => 'From ₱5', 'stock' => 'Many'],
                                ];
                                foreach($items as $item): ?>
                                <div class="bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition cursor-pointer">
                                    <div class="aspect-square bg-cyan-100 rounded-xl mb-3 flex items-center justify-center">
                                        <i class="fas fa-box text-3xl text-cyan-300"></i>
                                    </div>
                                    <h3 class="font-medium text-gray-800 text-sm mb-1"><?php echo $item['name']; ?></h3>
                                    <p class="text-cyan-600 font-bold"><?php echo $item['price']; ?></p>
                                    <span class="text-xs text-green-600"><i class="fas fa-check-circle mr-1"></i><?php echo $item['stock']; ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </section>

                    <!-- Payment Methods -->
                    <section class="py-6 px-4 bg-white">
                        <div class="max-w-6xl mx-auto text-center">
                            <p class="text-sm text-gray-500 mb-3">Accepted Payments</p>
                            <div class="flex justify-center gap-6 text-2xl text-gray-400">
                                <i class="fas fa-money-bill-alt" title="Cash"></i>
                                <span class="font-bold text-blue-500 text-lg">GCash</span>
                                <span class="font-bold text-purple-500 text-lg">Maya</span>
                            </div>
                        </div>
                    </section>

                    <!-- Footer -->
                    <footer class="py-8 px-4 bg-cyan-700 text-white text-center">
                        <p class="font-bold text-lg mb-2">TindahanOnline</p>
                        <p class="text-cyan-200 text-sm mb-4">Your modern neighborhood sari-sari store</p>
                        <div class="flex justify-center gap-4">
                            <a href="#" class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center hover:bg-white/30 transition">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center hover:bg-white/30 transition">
                                <i class="fab fa-instagram"></i>
                            </a>
                        </div>
                        <p class="text-cyan-300 text-xs mt-4">© 2024 Powered by FilDevStudio</p>
                    </footer>
                </div>

                <?php else: ?>
                    <!-- Default Generic Layout -->
                    <div class="gradient-bg text-white py-16 px-8">
                        <div class="max-w-3xl mx-auto text-center">
                            <h1 class="text-4xl font-bold mb-4">Your Business Name</h1>
                            <p class="text-xl text-blue-100 mb-8">Your tagline or value proposition goes here</p>
                            <button class="bg-white text-primary px-8 py-3 rounded-lg font-semibold">Get Started</button>
                        </div>
                    </div>
                    
                    <div class="py-12 px-8">
                        <div class="max-w-3xl mx-auto">
                            <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">Our Services</h2>
                            <div class="grid md:grid-cols-3 gap-6">
                                <div class="text-center p-6 bg-gray-50 rounded-xl">
                                    <div class="w-12 h-12 gradient-bg rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-star text-white"></i>
                                    </div>
                                    <h3 class="font-semibold mb-2">Service One</h3>
                                    <p class="text-gray-600 text-sm">Description of your first service</p>
                                </div>
                                <div class="text-center p-6 bg-gray-50 rounded-xl">
                                    <div class="w-12 h-12 gradient-bg rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-heart text-white"></i>
                                    </div>
                                    <h3 class="font-semibold mb-2">Service Two</h3>
                                    <p class="text-gray-600 text-sm">Description of your second service</p>
                                </div>
                                <div class="text-center p-6 bg-gray-50 rounded-xl">
                                    <div class="w-12 h-12 gradient-bg rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-bolt text-white"></i>
                                    </div>
                                    <h3 class="font-semibold mb-2">Service Three</h3>
                                    <p class="text-gray-600 text-sm">Description of your third service</p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Template Features -->
<section class="py-12 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">Template Features</h2>
        <div class="grid md:grid-cols-2 gap-6">
            <div class="flex items-start space-x-3">
                <i class="fas fa-check-circle text-green-500 text-xl mt-1"></i>
                <div>
                    <h3 class="font-semibold text-gray-800">Fully Responsive</h3>
                    <p class="text-gray-600 text-sm">Looks great on all devices</p>
                </div>
            </div>
            <div class="flex items-start space-x-3">
                <i class="fas fa-check-circle text-green-500 text-xl mt-1"></i>
                <div>
                    <h3 class="font-semibold text-gray-800">Easy Customization</h3>
                    <p class="text-gray-600 text-sm">Change colors, fonts, and content</p>
                </div>
            </div>
            <div class="flex items-start space-x-3">
                <i class="fas fa-check-circle text-green-500 text-xl mt-1"></i>
                <div>
                    <h3 class="font-semibold text-gray-800">SEO Optimized</h3>
                    <p class="text-gray-600 text-sm">Built for search engines</p>
                </div>
            </div>
            <div class="flex items-start space-x-3">
                <i class="fas fa-check-circle text-green-500 text-xl mt-1"></i>
                <div>
                    <h3 class="font-semibold text-gray-800">Fast Loading</h3>
                    <p class="text-gray-600 text-sm">Optimized for performance</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Device toggle functionality
document.querySelectorAll('.device-toggle').forEach(button => {
    button.addEventListener('click', function() {
        const device = this.dataset.device;
        const container = document.getElementById('preview-container');
        
        // Update active button
        document.querySelectorAll('.device-toggle').forEach(btn => btn.classList.remove('active', 'gradient-bg', 'text-white'));
        this.classList.add('active', 'gradient-bg', 'text-white');
        
        // Update preview size
        switch(device) {
            case 'mobile':
                container.style.maxWidth = '375px';
                break;
            case 'tablet':
                container.style.maxWidth = '768px';
                break;
            default:
                container.style.maxWidth = '100%';
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
