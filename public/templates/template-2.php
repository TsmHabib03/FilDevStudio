<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($site['site_name'] ?? 'Carinderia'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '<?php echo $site['primary_color'] ?? '#DC2626'; ?>',
                        secondary: '<?php echo $site['secondary_color'] ?? '#EA580C'; ?>',
                        accent: '<?php echo $site['accent_color'] ?? '#FBBF24'; ?>',
                    }
                }
            }
        }
    </script>
    <style>
        .hero-gradient { background: linear-gradient(135deg, <?php echo $site['primary_color'] ?? '#DC2626'; ?> 0%, <?php echo $site['secondary_color'] ?? '#EA580C'; ?> 100%); }
        body { font-family: '<?php echo $site['font_body'] ?? 'Poppins'; ?>', system-ui, sans-serif; }
        h1, h2, h3, h4 { font-family: '<?php echo $site['font_heading'] ?? 'Poppins'; ?>', system-ui, sans-serif; }
    </style>
    <?php if (!empty($site['font_heading']) || !empty($site['font_body'])): ?>
    <link href="https://fonts.googleapis.com/css2?family=<?php echo urlencode($site['font_heading'] ?? 'Poppins'); ?>:wght@400;600;700&family=<?php echo urlencode($site['font_body'] ?? 'Poppins'); ?>:wght@400;500&display=swap" rel="stylesheet">
    <?php endif; ?>
</head>
<body class="bg-orange-50 min-h-screen">
    
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <nav class="max-w-6xl mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <?php if (!empty($logoUrl)): ?>
                        <img src="<?php echo htmlspecialchars($logoUrl); ?>" alt="Logo" class="h-12 w-auto">
                    <?php else: ?>
                        <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center">
                            <i class="fas fa-utensils text-2xl text-primary"></i>
                        </div>
                    <?php endif; ?>
                    <span class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($site['site_name'] ?? 'Aling Nena\'s Carinderia'); ?></span>
                </div>
                <div class="hidden md:flex items-center gap-6 text-sm">
                    <a href="#menu" class="text-gray-600 hover:text-primary transition">Menu</a>
                    <a href="#about" class="text-gray-600 hover:text-primary transition">About</a>
                    <a href="#location" class="text-gray-600 hover:text-primary transition">Location</a>
                    <a href="#contact" class="px-4 py-2 bg-primary text-white rounded-full hover:bg-primary/90 transition">Order Now</a>
                </div>
            </div>
        </nav>
    </header>
    
    <!-- Hero Section -->
    <section class="hero-gradient text-white py-20 md:py-28">
        <div class="max-w-6xl mx-auto px-4">
            <div class="max-w-2xl">
                <span class="inline-block px-4 py-1 bg-white/20 rounded-full text-sm mb-6">🍲 Masarap at Mura!</span>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                    <?php echo htmlspecialchars($site['hero_title'] ?? 'Lutong Bahay, Lasa ng Pagmamahal'); ?>
                </h1>
                <p class="text-xl text-white/90 mb-8">
                    <?php echo htmlspecialchars($site['hero_subtitle'] ?? 'Fresh cooked Filipino dishes everyday. Affordable meals para sa lahat!'); ?>
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="#menu" class="px-8 py-3 bg-white text-primary rounded-full font-semibold hover:bg-gray-100 transition shadow-lg">
                        <i class="fas fa-book-open mr-2"></i>View Menu
                    </a>
                    <a href="tel:0917-123-4567" class="px-8 py-3 bg-white/20 border-2 border-white rounded-full font-semibold hover:bg-white/30 transition">
                        <i class="fas fa-phone mr-2"></i>Call for Delivery
                    </a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Features -->
    <section class="py-12 bg-white">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="text-center p-4">
                    <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-leaf text-green-600 text-xl"></i>
                    </div>
                    <p class="font-semibold text-gray-800 text-sm">Fresh Ingredients</p>
                </div>
                <div class="text-center p-4">
                    <div class="w-14 h-14 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-fire text-orange-600 text-xl"></i>
                    </div>
                    <p class="font-semibold text-gray-800 text-sm">Luto Daily</p>
                </div>
                <div class="text-center p-4">
                    <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-peso-sign text-blue-600 text-xl"></i>
                    </div>
                    <p class="font-semibold text-gray-800 text-sm">Affordable Price</p>
                </div>
                <div class="text-center p-4">
                    <div class="w-14 h-14 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-motorcycle text-purple-600 text-xl"></i>
                    </div>
                    <p class="font-semibold text-gray-800 text-sm">Delivery Available</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Menu Section -->
    <section id="menu" class="py-16">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-12">
                <span class="px-4 py-1 bg-primary/10 text-primary rounded-full text-sm font-medium">Our Menu</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mt-4">Masarap na Ulam</h2>
                <p class="text-gray-600 mt-2">Fresh cooked everyday, may rice kasama!</p>
            </div>
            
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Menu Item 1 -->
                <div class="bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition">
                    <div class="h-40 bg-gradient-to-br from-red-100 to-orange-100 flex items-center justify-center">
                        <i class="fas fa-drumstick-bite text-6xl text-primary/30"></i>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-gray-800">Adobo</h3>
                            <span class="text-primary font-bold">₱60</span>
                        </div>
                        <p class="text-gray-500 text-sm">Chicken or pork adobo with rice</p>
                        <span class="inline-block mt-3 px-3 py-1 bg-green-100 text-green-700 text-xs rounded-full">Best Seller</span>
                    </div>
                </div>
                
                <!-- Menu Item 2 -->
                <div class="bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition">
                    <div class="h-40 bg-gradient-to-br from-yellow-100 to-orange-100 flex items-center justify-center">
                        <i class="fas fa-fish text-6xl text-primary/30"></i>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-gray-800">Sinigang</h3>
                            <span class="text-primary font-bold">₱70</span>
                        </div>
                        <p class="text-gray-500 text-sm">Pork or fish sinigang with veggies</p>
                    </div>
                </div>
                
                <!-- Menu Item 3 -->
                <div class="bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition">
                    <div class="h-40 bg-gradient-to-br from-green-100 to-teal-100 flex items-center justify-center">
                        <i class="fas fa-leaf text-6xl text-green-300"></i>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-gray-800">Pinakbet</h3>
                            <span class="text-primary font-bold">₱55</span>
                        </div>
                        <p class="text-gray-500 text-sm">Mixed vegetables with bagoong</p>
                        <span class="inline-block mt-3 px-3 py-1 bg-green-100 text-green-700 text-xs rounded-full">Healthy</span>
                    </div>
                </div>
                
                <!-- Menu Item 4 -->
                <div class="bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition">
                    <div class="h-40 bg-gradient-to-br from-amber-100 to-yellow-100 flex items-center justify-center">
                        <i class="fas fa-bacon text-6xl text-amber-300"></i>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-gray-800">Lechon Kawali</h3>
                            <span class="text-primary font-bold">₱85</span>
                        </div>
                        <p class="text-gray-500 text-sm">Crispy pork belly with rice</p>
                        <span class="inline-block mt-3 px-3 py-1 bg-orange-100 text-orange-700 text-xs rounded-full">Popular</span>
                    </div>
                </div>
                
                <!-- Menu Item 5 -->
                <div class="bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition">
                    <div class="h-40 bg-gradient-to-br from-purple-100 to-pink-100 flex items-center justify-center">
                        <i class="fas fa-egg text-6xl text-purple-300"></i>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-gray-800">Tortang Talong</h3>
                            <span class="text-primary font-bold">₱45</span>
                        </div>
                        <p class="text-gray-500 text-sm">Eggplant omelette with rice</p>
                        <span class="inline-block mt-3 px-3 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">Budget Meal</span>
                    </div>
                </div>
                
                <!-- Menu Item 6 -->
                <div class="bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition">
                    <div class="h-40 bg-gradient-to-br from-red-100 to-rose-100 flex items-center justify-center">
                        <i class="fas fa-pepper-hot text-6xl text-red-300"></i>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-gray-800">Bicol Express</h3>
                            <span class="text-primary font-bold">₱75</span>
                        </div>
                        <p class="text-gray-500 text-sm">Spicy pork in coconut milk</p>
                        <span class="inline-block mt-3 px-3 py-1 bg-red-100 text-red-700 text-xs rounded-full">🌶️ Spicy</span>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($site['services_content'])): ?>
            <div class="mt-12 bg-white rounded-2xl p-8 shadow-md">
                <h3 class="font-bold text-gray-800 mb-4 text-center">Complete Menu:</h3>
                <p class="text-gray-600 whitespace-pre-line text-center"><?php echo htmlspecialchars($site['services_content']); ?></p>
            </div>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- About Section -->
    <section id="about" class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="bg-gradient-to-br from-primary/10 to-secondary/10 rounded-3xl p-8 md:p-12">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white rounded-2xl p-6 text-center shadow-sm">
                            <div class="text-3xl font-bold text-primary">15+</div>
                            <div class="text-gray-600 text-sm">Years Serving</div>
                        </div>
                        <div class="bg-white rounded-2xl p-6 text-center shadow-sm">
                            <div class="text-3xl font-bold text-primary">1000+</div>
                            <div class="text-gray-600 text-sm">Happy Customers</div>
                        </div>
                        <div class="bg-white rounded-2xl p-6 text-center shadow-sm">
                            <div class="text-3xl font-bold text-primary">20+</div>
                            <div class="text-gray-600 text-sm">Ulam Varieties</div>
                        </div>
                        <div class="bg-white rounded-2xl p-6 text-center shadow-sm">
                            <div class="text-3xl font-bold text-primary">5⭐</div>
                            <div class="text-gray-600 text-sm">Customer Rating</div>
                        </div>
                    </div>
                </div>
                <div>
                    <span class="px-4 py-1 bg-primary/10 text-primary rounded-full text-sm font-medium">Our Story</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mt-4 mb-6">
                        Luto ng May Puso
                    </h2>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        <?php echo nl2br(htmlspecialchars($site['about_content'] ?? 'Simula pa noong 2008, ang aming carinderia ay patuloy na nagsisilbi ng masarap at abot-kayang pagkain sa aming mga kababayan. Bawat ulam ay inihahanda ng may pagmamahal at dedication, gamit ang pinakasariwa at pinakamasarap na sangkap.')); ?>
                    </p>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span class="text-gray-700">Fresh ingredients every morning</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span class="text-gray-700">Home-cooked Filipino recipes</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span class="text-gray-700">Generous servings, affordable prices</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Operating Hours & Location -->
    <section id="location" class="py-16 bg-orange-50">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-12">
                <span class="px-4 py-1 bg-primary/10 text-primary rounded-full text-sm font-medium">Visit Us</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mt-4">Store Hours & Location</h2>
            </div>
            
            <div class="grid md:grid-cols-2 gap-8">
                <!-- Hours Card -->
                <div class="bg-white rounded-2xl p-8 shadow-lg">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center">
                            <i class="fas fa-clock text-primary text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Operating Hours</h3>
                    </div>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b">
                            <span class="text-gray-600">Monday - Saturday</span>
                            <span class="font-semibold text-gray-800">6:00 AM - 8:00 PM</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b">
                            <span class="text-gray-600">Sunday</span>
                            <span class="font-semibold text-gray-800">7:00 AM - 6:00 PM</span>
                        </div>
                        <div class="flex justify-between items-center py-3">
                            <span class="text-gray-600">Holidays</span>
                            <span class="font-semibold text-green-600">Special Hours</span>
                        </div>
                    </div>
                    <div class="mt-6 p-4 bg-yellow-50 rounded-xl">
                        <p class="text-yellow-800 text-sm"><i class="fas fa-info-circle mr-2"></i>Best time to visit: 11 AM - 1 PM for freshly cooked ulam!</p>
                    </div>
                </div>
                
                <!-- Location Card -->
                <div class="bg-white rounded-2xl p-8 shadow-lg">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center">
                            <i class="fas fa-map-marker-alt text-primary text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Location</h3>
                    </div>
                    <div class="bg-gray-100 rounded-xl h-48 flex items-center justify-center mb-4">
                        <div class="text-center text-gray-500">
                            <i class="fas fa-map text-4xl mb-2"></i>
                            <p class="text-sm">Map Placeholder</p>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-4">
                        <?php echo nl2br(htmlspecialchars($site['contact_info'] ?? '123 Main Street, Barangay Centro\nCity Name, Province 1234')); ?>
                    </p>
                    <a href="#" class="inline-flex items-center text-primary font-semibold hover:underline">
                        <i class="fas fa-directions mr-2"></i>Get Directions
                    </a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Contact Section -->
    <section id="contact" class="py-16 hero-gradient text-white">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Order Now!</h2>
            <p class="text-white/80 mb-8">Para sa orders, reservations, at catering inquiries</p>
            
            <div class="flex flex-wrap justify-center gap-4">
                <a href="tel:0917-123-4567" class="px-8 py-4 bg-white text-primary rounded-full font-semibold hover:bg-gray-100 transition shadow-lg flex items-center gap-2">
                    <i class="fas fa-phone"></i>
                    <span>0917-123-4567</span>
                </a>
                <?php if (!empty($site['social_messenger'])): ?>
                <a href="<?php echo htmlspecialchars($site['social_messenger']); ?>" target="_blank" class="px-8 py-4 bg-blue-600 text-white rounded-full font-semibold hover:bg-blue-700 transition shadow-lg flex items-center gap-2">
                    <i class="fab fa-facebook-messenger"></i>
                    <span>Message Us</span>
                </a>
                <?php endif; ?>
            </div>
            
            <!-- Social Links -->
            <?php if (!empty($site['social_facebook'])): ?>
            <div class="mt-8">
                <a href="<?php echo htmlspecialchars($site['social_facebook']); ?>" target="_blank" class="inline-flex items-center text-white/80 hover:text-white">
                    <i class="fab fa-facebook mr-2"></i>Follow us on Facebook for daily menu updates!
                </a>
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
                    <i class="fas fa-utensils text-2xl text-primary"></i>
                <?php endif; ?>
                <span class="font-bold"><?php echo htmlspecialchars($site['site_name'] ?? 'Carinderia'); ?></span>
            </div>
            <p class="text-gray-400 text-sm">
                © <?php echo date('Y'); ?> <?php echo htmlspecialchars($site['site_name'] ?? 'Carinderia'); ?>. All rights reserved.
            </p>
            <p class="text-gray-500 text-xs mt-2">Powered by FilDevStudio</p>
        </div>
    </footer>
    
    <!-- Floating Order Button -->
    <a href="tel:0917-123-4567" class="fixed bottom-6 right-6 w-14 h-14 bg-primary rounded-full flex items-center justify-center shadow-lg hover:bg-primary/90 transition z-50 md:hidden">
        <i class="fas fa-phone text-white text-xl"></i>
    </a>

</body>
</html>
