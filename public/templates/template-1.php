<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($site['site_name'] ?? 'Sari-Sari Store'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '<?php echo $site['primary_color'] ?? '#EF4444'; ?>',
                        secondary: '<?php echo $site['secondary_color'] ?? '#F97316'; ?>',
                        accent: '<?php echo $site['accent_color'] ?? '#FCD34D'; ?>',
                    }
                }
            }
        }
    </script>
    <style>
        .hero-gradient { background: linear-gradient(135deg, <?php echo $site['primary_color'] ?? '#EF4444'; ?> 0%, <?php echo $site['secondary_color'] ?? '#F97316'; ?> 100%); }
        body { font-family: '<?php echo $site['font_body'] ?? 'Inter'; ?>', system-ui, sans-serif; }
        h1, h2, h3, h4 { font-family: '<?php echo $site['font_heading'] ?? 'Inter'; ?>', system-ui, sans-serif; }
    </style>
    <?php if (!empty($site['font_heading']) || !empty($site['font_body'])): ?>
    <link href="https://fonts.googleapis.com/css2?family=<?php echo urlencode($site['font_heading'] ?? 'Inter'); ?>:wght@400;600;700&family=<?php echo urlencode($site['font_body'] ?? 'Inter'); ?>:wght@400;500&display=swap" rel="stylesheet">
    <?php endif; ?>
</head>
<body class="bg-orange-50 min-h-screen">
    
    <!-- Header -->
    <header class="hero-gradient text-white">
        <nav class="max-w-6xl mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <?php if (!empty($logoUrl)): ?>
                        <img src="<?php echo htmlspecialchars($logoUrl); ?>" alt="Logo" class="h-12 w-auto">
                    <?php else: ?>
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-store-alt text-2xl"></i>
                        </div>
                    <?php endif; ?>
                    <span class="text-xl font-bold"><?php echo htmlspecialchars($site['site_name'] ?? 'My Sari-Sari Store'); ?></span>
                </div>
                <div class="hidden md:flex items-center gap-6 text-sm">
                    <a href="#products" class="hover:text-accent transition">Mga Paninda</a>
                    <a href="#about" class="hover:text-accent transition">About</a>
                    <a href="#contact" class="hover:text-accent transition">Contact</a>
                </div>
            </div>
        </nav>
        
        <!-- Hero Section -->
        <div class="max-w-6xl mx-auto px-4 py-16 md:py-24 text-center">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6">
                <?php echo htmlspecialchars($site['hero_title'] ?? 'Tindahan ng Bayan'); ?>
            </h1>
            <p class="text-xl md:text-2xl text-white/90 mb-8 max-w-2xl mx-auto">
                <?php echo htmlspecialchars($site['hero_subtitle'] ?? 'Mura, Matibay, at Maaasahan! Bukas araw-araw para sa inyo.'); ?>
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="#contact" class="px-8 py-3 bg-white text-primary rounded-full font-semibold hover:bg-gray-100 transition shadow-lg">
                    <i class="fas fa-phone mr-2"></i>Tawagan Kami
                </a>
                <a href="#products" class="px-8 py-3 bg-white/20 border-2 border-white rounded-full font-semibold hover:bg-white/30 transition">
                    <i class="fas fa-list mr-2"></i>Tingnan Paninda
                </a>
            </div>
        </div>
    </header>
    
    <!-- Services/Products Section -->
    <section id="products" class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-12">
                <span class="px-4 py-1 bg-primary/10 text-primary rounded-full text-sm font-medium">Mga Paninda</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mt-4">Available sa Aming Tindahan</h2>
            </div>
            
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Product Card 1 -->
                <div class="bg-orange-50 rounded-2xl p-6 text-center hover:shadow-lg transition">
                    <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-cookie-bite text-2xl text-primary"></i>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">Snacks & Junk Foods</h3>
                    <p class="text-gray-600 text-sm">Chichirya, biscuits, at iba pa</p>
                </div>
                
                <!-- Product Card 2 -->
                <div class="bg-orange-50 rounded-2xl p-6 text-center hover:shadow-lg transition">
                    <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-bottle-water text-2xl text-primary"></i>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">Drinks & Beverages</h3>
                    <p class="text-gray-600 text-sm">Softdrinks, juice, tubig</p>
                </div>
                
                <!-- Product Card 3 -->
                <div class="bg-orange-50 rounded-2xl p-6 text-center hover:shadow-lg transition">
                    <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-mobile-screen text-2xl text-primary"></i>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">E-Load & WiFi</h3>
                    <p class="text-gray-600 text-sm">All networks, Piso WiFi</p>
                </div>
                
                <!-- Product Card 4 -->
                <div class="bg-orange-50 rounded-2xl p-6 text-center hover:shadow-lg transition">
                    <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-box-open text-2xl text-primary"></i>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">Sachet Items</h3>
                    <p class="text-gray-600 text-sm">Shampoo, sabon, kape tingi</p>
                </div>
            </div>
            
            <?php if (!empty($site['services_content'])): ?>
            <div class="mt-12 bg-gray-50 rounded-2xl p-8">
                <h3 class="font-bold text-gray-800 mb-4">Kumpletong Lista ng Paninda:</h3>
                <p class="text-gray-600 whitespace-pre-line"><?php echo htmlspecialchars($site['services_content']); ?></p>
            </div>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- About Section -->
    <section id="about" class="py-16 bg-orange-50">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="px-4 py-1 bg-primary/10 text-primary rounded-full text-sm font-medium">Tungkol sa Amin</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mt-4 mb-6">
                        <?php echo htmlspecialchars($site['site_name'] ?? 'Aming Tindahan'); ?>
                    </h2>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        <?php echo nl2br(htmlspecialchars($site['about_content'] ?? 'Naglilingkod sa komunidad ng maraming taon na. Ang aming sari-sari store ay kilala sa magandang serbisyo, abot-kayang presyo, at masayang karanasan sa pamimili. Dito, hindi ka lang customer—parte ka ng pamilya!')); ?>
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <div class="flex items-center gap-2 text-gray-700">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span>Bukas Araw-araw</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-700">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span>Mababang Presyo</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-700">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span>Suki Discount</span>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-8 shadow-lg">
                    <h3 class="font-bold text-gray-800 mb-6 text-center">Oras ng Tindahan</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-gray-600">Lunes - Sabado</span>
                            <span class="font-semibold text-gray-800">6:00 AM - 10:00 PM</span>
                        </div>
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-gray-600">Linggo</span>
                            <span class="font-semibold text-gray-800">7:00 AM - 9:00 PM</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600">Holidays</span>
                            <span class="font-semibold text-green-600">Open!</span>
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
                <h2 class="text-2xl font-bold text-gray-800">Accepted Payments</h2>
            </div>
            <div class="flex flex-wrap justify-center items-center gap-8">
                <div class="flex items-center gap-3 px-6 py-3 bg-blue-50 rounded-xl">
                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-sm">G</span>
                    </div>
                    <span class="font-semibold text-gray-700">GCash</span>
                </div>
                <div class="flex items-center gap-3 px-6 py-3 bg-green-50 rounded-xl">
                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-sm">M</span>
                    </div>
                    <span class="font-semibold text-gray-700">Maya</span>
                </div>
                <div class="flex items-center gap-3 px-6 py-3 bg-gray-100 rounded-xl">
                    <div class="w-10 h-10 bg-gray-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-money-bill text-white"></i>
                    </div>
                    <span class="font-semibold text-gray-700">Cash</span>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Contact Section -->
    <section id="contact" class="py-16 hero-gradient text-white">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Contact Us</h2>
                <p class="text-white/80">Tawagan o puntahan kami anytime!</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-map-marker-alt text-2xl"></i>
                    </div>
                    <h3 class="font-bold mb-2">Address</h3>
                    <p class="text-white/80 text-sm"><?php echo nl2br(htmlspecialchars($site['contact_info'] ?? '123 Barangay Street, City, Philippines')); ?></p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-phone text-2xl"></i>
                    </div>
                    <h3 class="font-bold mb-2">Phone</h3>
                    <p class="text-white/80 text-sm">0917-123-4567</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fab fa-facebook-messenger text-2xl"></i>
                    </div>
                    <h3 class="font-bold mb-2">Messenger</h3>
                    <p class="text-white/80 text-sm">Message us on Facebook</p>
                </div>
            </div>
            
            <!-- Social Links -->
            <?php if (!empty($site['social_facebook']) || !empty($site['social_messenger'])): ?>
            <div class="flex justify-center gap-4 mt-12">
                <?php if (!empty($site['social_facebook'])): ?>
                    <a href="<?php echo htmlspecialchars($site['social_facebook']); ?>" target="_blank" class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center hover:bg-white/30 transition">
                        <i class="fab fa-facebook-f text-xl"></i>
                    </a>
                <?php endif; ?>
                <?php if (!empty($site['social_messenger'])): ?>
                    <a href="<?php echo htmlspecialchars($site['social_messenger']); ?>" target="_blank" class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center hover:bg-white/30 transition">
                        <i class="fab fa-facebook-messenger text-xl"></i>
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <p class="text-gray-400 text-sm">
                © <?php echo date('Y'); ?> <?php echo htmlspecialchars($site['site_name'] ?? 'Sari-Sari Store'); ?>. All rights reserved.
            </p>
            <p class="text-gray-500 text-xs mt-2">Powered by FilDevStudio</p>
        </div>
    </footer>
    
    <!-- Floating WhatsApp/Messenger Button -->
    <?php if (!empty($site['social_whatsapp']) || !empty($site['social_messenger'])): ?>
    <a href="<?php echo !empty($site['social_messenger']) ? htmlspecialchars($site['social_messenger']) : 'https://wa.me/' . preg_replace('/[^0-9]/', '', $site['social_whatsapp']); ?>" 
       target="_blank"
       class="fixed bottom-6 right-6 w-14 h-14 bg-green-500 rounded-full flex items-center justify-center shadow-lg hover:bg-green-600 transition z-50">
        <i class="<?php echo !empty($site['social_messenger']) ? 'fab fa-facebook-messenger' : 'fab fa-whatsapp'; ?> text-white text-2xl"></i>
    </a>
    <?php endif; ?>

</body>
</html>
