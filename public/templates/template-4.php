<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($site['site_name'] ?? 'Retail Store'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '<?php echo $site['primary_color'] ?? '#4F46E5'; ?>',
                        secondary: '<?php echo $site['secondary_color'] ?? '#7C3AED'; ?>',
                        accent: '<?php echo $site['accent_color'] ?? '#F59E0B'; ?>',
                    }
                }
            }
        }
    </script>
    <style>
        .hero-gradient { background: linear-gradient(135deg, <?php echo $site['primary_color'] ?? '#4F46E5'; ?> 0%, <?php echo $site['secondary_color'] ?? '#7C3AED'; ?> 100%); }
        body { font-family: '<?php echo $site['font_body'] ?? 'Inter'; ?>', system-ui, sans-serif; }
        h1, h2, h3, h4 { font-family: '<?php echo $site['font_heading'] ?? 'Inter'; ?>', system-ui, sans-serif; }
    </style>
    <?php if (!empty($site['font_heading']) || !empty($site['font_body'])): ?>
    <link href="https://fonts.googleapis.com/css2?family=<?php echo urlencode($site['font_heading'] ?? 'Inter'); ?>:wght@400;600;700&family=<?php echo urlencode($site['font_body'] ?? 'Inter'); ?>:wght@400;500&display=swap" rel="stylesheet">
    <?php endif; ?>
</head>
<body class="bg-gray-50 min-h-screen">
    
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <nav class="max-w-6xl mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <?php if (!empty($logoUrl)): ?>
                        <img src="<?php echo htmlspecialchars($logoUrl); ?>" alt="Logo" class="h-12 w-auto">
                    <?php else: ?>
                        <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center">
                            <i class="fas fa-shopping-bag text-2xl text-primary"></i>
                        </div>
                    <?php endif; ?>
                    <span class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($site['site_name'] ?? 'Shop Name'); ?></span>
                </div>
                <div class="hidden md:flex items-center gap-6 text-sm">
                    <a href="#products" class="text-gray-600 hover:text-primary transition">Products</a>
                    <a href="#categories" class="text-gray-600 hover:text-primary transition">Categories</a>
                    <a href="#about" class="text-gray-600 hover:text-primary transition">About</a>
                    <a href="#contact" class="px-5 py-2 bg-primary text-white rounded-full hover:bg-primary/90 transition">
                        <i class="fas fa-shopping-cart mr-2"></i>Order Now
                    </a>
                </div>
            </div>
        </nav>
    </header>
    
    <!-- Hero Section -->
    <section class="hero-gradient text-white py-20 md:py-28">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="inline-block px-4 py-1 bg-white/20 rounded-full text-sm mb-6">🛍️ Shop with Confidence</span>
                    <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">
                        <?php echo htmlspecialchars($site['hero_title'] ?? 'Quality Products at Great Prices'); ?>
                    </h1>
                    <p class="text-xl text-white/90 mb-8">
                        <?php echo htmlspecialchars($site['hero_subtitle'] ?? 'Discover amazing deals on your favorite products. Fast delivery, easy returns!'); ?>
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="#products" class="px-8 py-3 bg-white text-primary rounded-full font-semibold hover:bg-gray-100 transition shadow-lg">
                            <i class="fas fa-tags mr-2"></i>Shop Now
                        </a>
                        <a href="#contact" class="px-8 py-3 bg-white/20 border-2 border-white rounded-full font-semibold hover:bg-white/30 transition">
                            <i class="fas fa-phone mr-2"></i>Contact Us
                        </a>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="relative">
                        <div class="absolute -top-4 -left-4 w-72 h-72 bg-white/10 rounded-3xl"></div>
                        <div class="relative bg-white/10 backdrop-blur rounded-3xl p-8 flex items-center justify-center h-72">
                            <div class="text-center">
                                <i class="fas fa-box-open text-8xl text-white/30 mb-4"></i>
                                <p class="text-white/70">Featured Products</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Features Bar -->
    <section class="bg-white py-8 shadow-sm">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center">
                        <i class="fas fa-truck text-primary"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-800 text-sm">Fast Delivery</div>
                        <div class="text-gray-500 text-xs">Same day available</div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-shield-alt text-green-600"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-800 text-sm">Secure Payment</div>
                        <div class="text-gray-500 text-xs">100% protected</div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-undo text-orange-600"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-800 text-sm">Easy Returns</div>
                        <div class="text-gray-500 text-xs">7-day return policy</div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-headset text-blue-600"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-800 text-sm">24/7 Support</div>
                        <div class="text-gray-500 text-xs">Always here to help</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Categories Section -->
    <section id="categories" class="py-16">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-12">
                <span class="px-4 py-1 bg-primary/10 text-primary rounded-full text-sm font-medium">Browse</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mt-4">Shop by Category</h2>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <!-- Category 1 -->
                <a href="#products" class="group">
                    <div class="bg-gradient-to-br from-pink-100 to-rose-100 rounded-2xl p-6 text-center hover:shadow-lg transition">
                        <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition">
                            <i class="fas fa-tshirt text-2xl text-pink-500"></i>
                        </div>
                        <h3 class="font-bold text-gray-800">Clothing</h3>
                        <p class="text-gray-500 text-sm mt-1">50+ items</p>
                    </div>
                </a>
                
                <!-- Category 2 -->
                <a href="#products" class="group">
                    <div class="bg-gradient-to-br from-blue-100 to-cyan-100 rounded-2xl p-6 text-center hover:shadow-lg transition">
                        <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition">
                            <i class="fas fa-mobile-alt text-2xl text-blue-500"></i>
                        </div>
                        <h3 class="font-bold text-gray-800">Electronics</h3>
                        <p class="text-gray-500 text-sm mt-1">30+ items</p>
                    </div>
                </a>
                
                <!-- Category 3 -->
                <a href="#products" class="group">
                    <div class="bg-gradient-to-br from-green-100 to-emerald-100 rounded-2xl p-6 text-center hover:shadow-lg transition">
                        <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition">
                            <i class="fas fa-home text-2xl text-green-500"></i>
                        </div>
                        <h3 class="font-bold text-gray-800">Home & Living</h3>
                        <p class="text-gray-500 text-sm mt-1">40+ items</p>
                    </div>
                </a>
                
                <!-- Category 4 -->
                <a href="#products" class="group">
                    <div class="bg-gradient-to-br from-purple-100 to-violet-100 rounded-2xl p-6 text-center hover:shadow-lg transition">
                        <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition">
                            <i class="fas fa-gift text-2xl text-purple-500"></i>
                        </div>
                        <h3 class="font-bold text-gray-800">Accessories</h3>
                        <p class="text-gray-500 text-sm mt-1">60+ items</p>
                    </div>
                </a>
            </div>
        </div>
    </section>
    
    <!-- Products Section -->
    <section id="products" class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center mb-12">
                <div>
                    <span class="px-4 py-1 bg-primary/10 text-primary rounded-full text-sm font-medium">Products</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mt-4">Featured Products</h2>
                </div>
                <a href="#contact" class="mt-4 md:mt-0 text-primary font-semibold hover:underline">
                    View All <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
            
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Product 1 -->
                <div class="bg-gray-50 rounded-2xl overflow-hidden group">
                    <div class="h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center relative">
                        <i class="fas fa-image text-6xl text-gray-300"></i>
                        <span class="absolute top-3 left-3 px-3 py-1 bg-red-500 text-white text-xs font-bold rounded-full">SALE</span>
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-gray-800 mb-1">Product Name</h3>
                        <p class="text-gray-500 text-sm mb-3">Category</p>
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-lg font-bold text-primary">₱599</span>
                                <span class="text-sm text-gray-400 line-through ml-2">₱799</span>
                            </div>
                            <button class="w-10 h-10 bg-primary text-white rounded-full hover:bg-primary/90 transition">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Product 2 -->
                <div class="bg-gray-50 rounded-2xl overflow-hidden group">
                    <div class="h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center relative">
                        <i class="fas fa-image text-6xl text-gray-300"></i>
                        <span class="absolute top-3 left-3 px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full">NEW</span>
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-gray-800 mb-1">Product Name</h3>
                        <p class="text-gray-500 text-sm mb-3">Category</p>
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-bold text-primary">₱899</span>
                            <button class="w-10 h-10 bg-primary text-white rounded-full hover:bg-primary/90 transition">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Product 3 -->
                <div class="bg-gray-50 rounded-2xl overflow-hidden group">
                    <div class="h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center relative">
                        <i class="fas fa-image text-6xl text-gray-300"></i>
                        <span class="absolute top-3 left-3 px-3 py-1 bg-orange-500 text-white text-xs font-bold rounded-full">HOT</span>
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-gray-800 mb-1">Product Name</h3>
                        <p class="text-gray-500 text-sm mb-3">Category</p>
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-bold text-primary">₱1,299</span>
                            <button class="w-10 h-10 bg-primary text-white rounded-full hover:bg-primary/90 transition">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Product 4 -->
                <div class="bg-gray-50 rounded-2xl overflow-hidden group">
                    <div class="h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                        <i class="fas fa-image text-6xl text-gray-300"></i>
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-gray-800 mb-1">Product Name</h3>
                        <p class="text-gray-500 text-sm mb-3">Category</p>
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-bold text-primary">₱450</span>
                            <button class="w-10 h-10 bg-primary text-white rounded-full hover:bg-primary/90 transition">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($site['services_content'])): ?>
            <div class="mt-12 bg-gray-50 rounded-2xl p-8">
                <h3 class="font-bold text-gray-800 mb-4 text-center">Our Products:</h3>
                <p class="text-gray-600 whitespace-pre-line text-center"><?php echo htmlspecialchars($site['services_content']); ?></p>
            </div>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- Promo Banner -->
    <section class="py-16 bg-gradient-to-r from-primary to-secondary text-white">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                <div>
                    <span class="inline-block px-4 py-1 bg-white/20 rounded-full text-sm mb-4">Limited Time Offer</span>
                    <h2 class="text-3xl md:text-4xl font-bold mb-2">Get 20% Off Your First Order!</h2>
                    <p class="text-white/80">Use code <span class="font-bold text-accent">WELCOME20</span> at checkout</p>
                </div>
                <a href="#contact" class="px-8 py-4 bg-white text-primary rounded-full font-bold hover:bg-gray-100 transition shadow-lg whitespace-nowrap">
                    Shop Now <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>
    
    <!-- About Section -->
    <section id="about" class="py-16">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="order-2 md:order-1">
                    <span class="px-4 py-1 bg-primary/10 text-primary rounded-full text-sm font-medium">About Us</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mt-4 mb-6">
                        Your Trusted Shopping Partner
                    </h2>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        <?php echo nl2br(htmlspecialchars($site['about_content'] ?? 'We are committed to bringing you the best products at the most competitive prices. With a focus on quality and customer satisfaction, we\'ve been serving happy customers for years. Shop with confidence knowing that every purchase is backed by our satisfaction guarantee.')); ?>
                    </p>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-center p-4 bg-gray-50 rounded-xl">
                            <div class="text-2xl font-bold text-primary">1000+</div>
                            <div class="text-gray-500 text-sm">Products</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-xl">
                            <div class="text-2xl font-bold text-primary">5000+</div>
                            <div class="text-gray-500 text-sm">Customers</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-xl">
                            <div class="text-2xl font-bold text-primary">4.9★</div>
                            <div class="text-gray-500 text-sm">Rating</div>
                        </div>
                    </div>
                </div>
                <div class="order-1 md:order-2">
                    <div class="bg-gradient-to-br from-primary/10 to-secondary/10 rounded-3xl p-8 flex items-center justify-center h-80">
                        <div class="text-center">
                            <i class="fas fa-store text-8xl text-primary/30 mb-4"></i>
                            <p class="text-gray-500">Store Image</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Payment Methods -->
    <section class="py-12 bg-white">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-8">
                <h3 class="text-xl font-bold text-gray-800">Accepted Payment Methods</h3>
            </div>
            <div class="flex flex-wrap justify-center items-center gap-6">
                <div class="flex items-center gap-2 px-5 py-3 bg-gray-50 rounded-xl">
                    <div class="w-8 h-8 bg-blue-500 rounded flex items-center justify-center">
                        <span class="text-white font-bold text-xs">G</span>
                    </div>
                    <span class="font-medium text-gray-700">GCash</span>
                </div>
                <div class="flex items-center gap-2 px-5 py-3 bg-gray-50 rounded-xl">
                    <div class="w-8 h-8 bg-green-500 rounded flex items-center justify-center">
                        <span class="text-white font-bold text-xs">M</span>
                    </div>
                    <span class="font-medium text-gray-700">Maya</span>
                </div>
                <div class="flex items-center gap-2 px-5 py-3 bg-gray-50 rounded-xl">
                    <div class="w-8 h-8 bg-gray-700 rounded flex items-center justify-center">
                        <i class="fas fa-money-bill text-white text-xs"></i>
                    </div>
                    <span class="font-medium text-gray-700">COD</span>
                </div>
                <div class="flex items-center gap-2 px-5 py-3 bg-gray-50 rounded-xl">
                    <div class="w-8 h-8 bg-blue-600 rounded flex items-center justify-center">
                        <i class="fas fa-credit-card text-white text-xs"></i>
                    </div>
                    <span class="font-medium text-gray-700">Card</span>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Contact Section -->
    <section id="contact" class="py-16 hero-gradient text-white">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Contact Us</h2>
                <p class="text-white/80">Have questions? We're here to help!</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white/10 backdrop-blur rounded-2xl p-6 text-center">
                    <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-phone text-2xl"></i>
                    </div>
                    <h3 class="font-bold mb-2">Call Us</h3>
                    <p class="text-white/80">0917-123-4567</p>
                </div>
                
                <div class="bg-white/10 backdrop-blur rounded-2xl p-6 text-center">
                    <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fab fa-facebook-messenger text-2xl"></i>
                    </div>
                    <h3 class="font-bold mb-2">Message Us</h3>
                    <p class="text-white/80">Facebook Messenger</p>
                </div>
                
                <div class="bg-white/10 backdrop-blur rounded-2xl p-6 text-center">
                    <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-map-marker-alt text-2xl"></i>
                    </div>
                    <h3 class="font-bold mb-2">Visit Us</h3>
                    <p class="text-white/80 text-sm"><?php echo nl2br(htmlspecialchars($site['contact_info'] ?? 'City, Philippines')); ?></p>
                </div>
            </div>
            
            <!-- Social Links -->
            <?php if (!empty($site['social_facebook']) || !empty($site['social_instagram'])): ?>
            <div class="flex justify-center gap-4 mt-12">
                <?php if (!empty($site['social_facebook'])): ?>
                    <a href="<?php echo htmlspecialchars($site['social_facebook']); ?>" target="_blank" class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center hover:bg-white/30 transition">
                        <i class="fab fa-facebook-f text-xl"></i>
                    </a>
                <?php endif; ?>
                <?php if (!empty($site['social_instagram'])): ?>
                    <a href="<?php echo htmlspecialchars($site['social_instagram']); ?>" target="_blank" class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center hover:bg-white/30 transition">
                        <i class="fab fa-instagram text-xl"></i>
                    </a>
                <?php endif; ?>
                <?php if (!empty($site['social_tiktok'])): ?>
                    <a href="<?php echo htmlspecialchars($site['social_tiktok']); ?>" target="_blank" class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center hover:bg-white/30 transition">
                        <i class="fab fa-tiktok text-xl"></i>
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <div class="flex items-center justify-center gap-3 mb-4">
                <?php if (!empty($logoUrl)): ?>
                    <img src="<?php echo htmlspecialchars($logoUrl); ?>" alt="Logo" class="h-8 w-auto">
                <?php else: ?>
                    <i class="fas fa-shopping-bag text-2xl text-primary"></i>
                <?php endif; ?>
                <span class="font-bold"><?php echo htmlspecialchars($site['site_name'] ?? 'Shop'); ?></span>
            </div>
            <p class="text-gray-400 text-sm">
                © <?php echo date('Y'); ?> <?php echo htmlspecialchars($site['site_name'] ?? 'Shop'); ?>. All rights reserved.
            </p>
            <p class="text-gray-500 text-xs mt-2">Powered by FilDevStudio</p>
        </div>
    </footer>
    
    <!-- Floating Cart/Message Button -->
    <?php if (!empty($site['social_messenger'])): ?>
    <a href="<?php echo htmlspecialchars($site['social_messenger']); ?>" target="_blank" class="fixed bottom-6 right-6 w-14 h-14 bg-blue-600 rounded-full flex items-center justify-center shadow-lg hover:bg-blue-700 transition z-50">
        <i class="fab fa-facebook-messenger text-white text-2xl"></i>
    </a>
    <?php endif; ?>

</body>
</html>
