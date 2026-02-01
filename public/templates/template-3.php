<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($site['site_name'] ?? 'Local Services'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '<?php echo $site['primary_color'] ?? '#0D9488'; ?>',
                        secondary: '<?php echo $site['secondary_color'] ?? '#0891B2'; ?>',
                        accent: '<?php echo $site['accent_color'] ?? '#F59E0B'; ?>',
                    }
                }
            }
        }
    </script>
    <style>
        .hero-gradient { background: linear-gradient(135deg, <?php echo $site['primary_color'] ?? '#0D9488'; ?> 0%, <?php echo $site['secondary_color'] ?? '#0891B2'; ?> 100%); }
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
                            <i class="fas fa-tools text-2xl text-primary"></i>
                        </div>
                    <?php endif; ?>
                    <span class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($site['site_name'] ?? 'ProServices'); ?></span>
                </div>
                <div class="hidden md:flex items-center gap-6 text-sm">
                    <a href="#services" class="text-gray-600 hover:text-primary transition">Services</a>
                    <a href="#pricing" class="text-gray-600 hover:text-primary transition">Pricing</a>
                    <a href="#testimonials" class="text-gray-600 hover:text-primary transition">Reviews</a>
                    <a href="#contact" class="px-5 py-2 bg-primary text-white rounded-full hover:bg-primary/90 transition">Contact Us</a>
                </div>
            </div>
        </nav>
    </header>
    
    <!-- Hero Section -->
    <section class="hero-gradient text-white py-20 md:py-28">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="inline-block px-4 py-1 bg-white/20 rounded-full text-sm mb-6">✨ Professional & Reliable</span>
                    <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">
                        <?php echo htmlspecialchars($site['hero_title'] ?? 'Quality Services You Can Trust'); ?>
                    </h1>
                    <p class="text-xl text-white/90 mb-8">
                        <?php echo htmlspecialchars($site['hero_subtitle'] ?? 'Expert solutions for all your needs. Fast, reliable, and affordable.'); ?>
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="#contact" class="px-8 py-3 bg-white text-primary rounded-full font-semibold hover:bg-gray-100 transition shadow-lg">
                            <i class="fas fa-calendar-check mr-2"></i>Book Now
                        </a>
                        <a href="#services" class="px-8 py-3 bg-white/20 border-2 border-white rounded-full font-semibold hover:bg-white/30 transition">
                            <i class="fas fa-list mr-2"></i>Our Services
                        </a>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="bg-white/10 backdrop-blur rounded-3xl p-8">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white/20 rounded-2xl p-6 text-center">
                                <div class="text-4xl font-bold">500+</div>
                                <div class="text-white/80 text-sm mt-1">Happy Clients</div>
                            </div>
                            <div class="bg-white/20 rounded-2xl p-6 text-center">
                                <div class="text-4xl font-bold">10+</div>
                                <div class="text-white/80 text-sm mt-1">Years Experience</div>
                            </div>
                            <div class="bg-white/20 rounded-2xl p-6 text-center">
                                <div class="text-4xl font-bold">24/7</div>
                                <div class="text-white/80 text-sm mt-1">Support</div>
                            </div>
                            <div class="bg-white/20 rounded-2xl p-6 text-center">
                                <div class="text-4xl font-bold">100%</div>
                                <div class="text-white/80 text-sm mt-1">Satisfaction</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Services Section -->
    <section id="services" class="py-16">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-12">
                <span class="px-4 py-1 bg-primary/10 text-primary rounded-full text-sm font-medium">What We Offer</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mt-4">Our Services</h2>
                <p class="text-gray-600 mt-2 max-w-2xl mx-auto">Professional solutions tailored to your needs</p>
            </div>
            
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Service 1 -->
                <div class="bg-white rounded-2xl p-8 shadow-md hover:shadow-xl transition border border-gray-100">
                    <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-wrench text-2xl text-primary"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">General Repair</h3>
                    <p class="text-gray-600 mb-4">Quick fixes and maintenance for your home or office equipment</p>
                    <a href="#contact" class="text-primary font-semibold hover:underline inline-flex items-center">
                        Learn More <i class="fas fa-arrow-right ml-2 text-sm"></i>
                    </a>
                </div>
                
                <!-- Service 2 -->
                <div class="bg-white rounded-2xl p-8 shadow-md hover:shadow-xl transition border border-gray-100">
                    <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-bolt text-2xl text-primary"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Electrical Work</h3>
                    <p class="text-gray-600 mb-4">Safe and certified electrical installations and repairs</p>
                    <a href="#contact" class="text-primary font-semibold hover:underline inline-flex items-center">
                        Learn More <i class="fas fa-arrow-right ml-2 text-sm"></i>
                    </a>
                </div>
                
                <!-- Service 3 -->
                <div class="bg-white rounded-2xl p-8 shadow-md hover:shadow-xl transition border border-gray-100">
                    <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-faucet text-2xl text-primary"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Plumbing</h3>
                    <p class="text-gray-600 mb-4">Expert plumbing services for leaks, clogs, and installations</p>
                    <a href="#contact" class="text-primary font-semibold hover:underline inline-flex items-center">
                        Learn More <i class="fas fa-arrow-right ml-2 text-sm"></i>
                    </a>
                </div>
                
                <!-- Service 4 -->
                <div class="bg-white rounded-2xl p-8 shadow-md hover:shadow-xl transition border border-gray-100">
                    <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-paint-roller text-2xl text-primary"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Painting</h3>
                    <p class="text-gray-600 mb-4">Interior and exterior painting with quality materials</p>
                    <a href="#contact" class="text-primary font-semibold hover:underline inline-flex items-center">
                        Learn More <i class="fas fa-arrow-right ml-2 text-sm"></i>
                    </a>
                </div>
                
                <!-- Service 5 -->
                <div class="bg-white rounded-2xl p-8 shadow-md hover:shadow-xl transition border border-gray-100">
                    <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-snowflake text-2xl text-primary"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Aircon Services</h3>
                    <p class="text-gray-600 mb-4">Installation, cleaning, and repair of air conditioning units</p>
                    <a href="#contact" class="text-primary font-semibold hover:underline inline-flex items-center">
                        Learn More <i class="fas fa-arrow-right ml-2 text-sm"></i>
                    </a>
                </div>
                
                <!-- Service 6 -->
                <div class="bg-white rounded-2xl p-8 shadow-md hover:shadow-xl transition border border-gray-100">
                    <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-broom text-2xl text-primary"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Cleaning Services</h3>
                    <p class="text-gray-600 mb-4">Deep cleaning for homes, offices, and commercial spaces</p>
                    <a href="#contact" class="text-primary font-semibold hover:underline inline-flex items-center">
                        Learn More <i class="fas fa-arrow-right ml-2 text-sm"></i>
                    </a>
                </div>
            </div>
            
            <?php if (!empty($site['services_content'])): ?>
            <div class="mt-12 bg-primary/5 rounded-2xl p-8">
                <h3 class="font-bold text-gray-800 mb-4 text-center">Complete Service List:</h3>
                <p class="text-gray-600 whitespace-pre-line text-center"><?php echo htmlspecialchars($site['services_content']); ?></p>
            </div>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- Pricing Section -->
    <section id="pricing" class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-12">
                <span class="px-4 py-1 bg-primary/10 text-primary rounded-full text-sm font-medium">Pricing</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mt-4">Transparent Pricing</h2>
                <p class="text-gray-600 mt-2">No hidden fees, just honest rates</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Basic -->
                <div class="bg-gray-50 rounded-2xl p-8 border-2 border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Basic Service</h3>
                    <p class="text-gray-500 text-sm mb-6">Small repairs & maintenance</p>
                    <div class="mb-6">
                        <span class="text-4xl font-bold text-gray-800">₱500</span>
                        <span class="text-gray-500">/visit</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center gap-2 text-gray-600">
                            <i class="fas fa-check text-green-500"></i>
                            <span>Quick inspection</span>
                        </li>
                        <li class="flex items-center gap-2 text-gray-600">
                            <i class="fas fa-check text-green-500"></i>
                            <span>Minor repairs</span>
                        </li>
                        <li class="flex items-center gap-2 text-gray-600">
                            <i class="fas fa-check text-green-500"></i>
                            <span>1-hour service</span>
                        </li>
                        <li class="flex items-center gap-2 text-gray-400">
                            <i class="fas fa-times"></i>
                            <span>Parts not included</span>
                        </li>
                    </ul>
                    <a href="#contact" class="block text-center py-3 border-2 border-primary text-primary rounded-full font-semibold hover:bg-primary hover:text-white transition">
                        Book Now
                    </a>
                </div>
                
                <!-- Standard -->
                <div class="bg-primary rounded-2xl p-8 text-white transform md:-translate-y-4 shadow-xl">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-bold mb-2">Standard Service</h3>
                            <p class="text-white/70 text-sm">Most popular choice</p>
                        </div>
                        <span class="px-3 py-1 bg-accent text-gray-900 rounded-full text-xs font-bold">POPULAR</span>
                    </div>
                    <div class="mb-6">
                        <span class="text-4xl font-bold">₱1,500</span>
                        <span class="text-white/70">/service</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center gap-2">
                            <i class="fas fa-check text-accent"></i>
                            <span>Full inspection</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-check text-accent"></i>
                            <span>Complete repairs</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-check text-accent"></i>
                            <span>Half-day service</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-check text-accent"></i>
                            <span>Basic parts included</span>
                        </li>
                    </ul>
                    <a href="#contact" class="block text-center py-3 bg-white text-primary rounded-full font-semibold hover:bg-gray-100 transition">
                        Book Now
                    </a>
                </div>
                
                <!-- Premium -->
                <div class="bg-gray-50 rounded-2xl p-8 border-2 border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Premium Service</h3>
                    <p class="text-gray-500 text-sm mb-6">Complete solutions</p>
                    <div class="mb-6">
                        <span class="text-4xl font-bold text-gray-800">₱3,000</span>
                        <span class="text-gray-500">/project</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center gap-2 text-gray-600">
                            <i class="fas fa-check text-green-500"></i>
                            <span>Comprehensive work</span>
                        </li>
                        <li class="flex items-center gap-2 text-gray-600">
                            <i class="fas fa-check text-green-500"></i>
                            <span>Full-day service</span>
                        </li>
                        <li class="flex items-center gap-2 text-gray-600">
                            <i class="fas fa-check text-green-500"></i>
                            <span>All parts included</span>
                        </li>
                        <li class="flex items-center gap-2 text-gray-600">
                            <i class="fas fa-check text-green-500"></i>
                            <span>30-day warranty</span>
                        </li>
                    </ul>
                    <a href="#contact" class="block text-center py-3 border-2 border-primary text-primary rounded-full font-semibold hover:bg-primary hover:text-white transition">
                        Book Now
                    </a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Testimonials -->
    <section id="testimonials" class="py-16 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-12">
                <span class="px-4 py-1 bg-primary/10 text-primary rounded-full text-sm font-medium">Testimonials</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mt-4">What Our Clients Say</h2>
            </div>
            
            <div class="grid md:grid-cols-3 gap-6">
                <!-- Review 1 -->
                <div class="bg-white rounded-2xl p-6 shadow-md">
                    <div class="flex gap-1 text-yellow-400 mb-4">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="text-gray-600 mb-6">"Excellent service! Very professional and finished the job quickly. Highly recommended!"</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                            <span class="text-primary font-bold">JD</span>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800">Juan Dela Cruz</div>
                            <div class="text-gray-500 text-sm">Homeowner</div>
                        </div>
                    </div>
                </div>
                
                <!-- Review 2 -->
                <div class="bg-white rounded-2xl p-6 shadow-md">
                    <div class="flex gap-1 text-yellow-400 mb-4">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="text-gray-600 mb-6">"Very reasonable prices and quality work. They fixed my aircon in no time. Will definitely call again!"</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                            <span class="text-primary font-bold">MS</span>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800">Maria Santos</div>
                            <div class="text-gray-500 text-sm">Business Owner</div>
                        </div>
                    </div>
                </div>
                
                <!-- Review 3 -->
                <div class="bg-white rounded-2xl p-6 shadow-md">
                    <div class="flex gap-1 text-yellow-400 mb-4">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <p class="text-gray-600 mb-6">"Prompt response and very courteous staff. The plumber arrived on time and fixed the leak quickly."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                            <span class="text-primary font-bold">PR</span>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800">Pedro Reyes</div>
                            <div class="text-gray-500 text-sm">Restaurant Owner</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- About Section -->
    <section id="about" class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="px-4 py-1 bg-primary/10 text-primary rounded-full text-sm font-medium">About Us</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mt-4 mb-6">
                        Why Choose Us?
                    </h2>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        <?php echo nl2br(htmlspecialchars($site['about_content'] ?? 'We are a team of skilled professionals dedicated to providing top-quality services. With years of experience and a commitment to customer satisfaction, we ensure that every job is done right the first time.')); ?>
                    </p>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-shield-alt text-primary text-xl"></i>
                            <span class="text-gray-700 font-medium">Licensed & Insured</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="fas fa-clock text-primary text-xl"></i>
                            <span class="text-gray-700 font-medium">On-Time Service</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="fas fa-thumbs-up text-primary text-xl"></i>
                            <span class="text-gray-700 font-medium">Quality Work</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="fas fa-hand-holding-usd text-primary text-xl"></i>
                            <span class="text-gray-700 font-medium">Fair Pricing</span>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-primary/5 to-secondary/5 rounded-3xl p-8">
                    <div class="bg-white rounded-2xl p-6 shadow-md mb-4">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-headset text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800">24/7 Support</h4>
                                <p class="text-gray-500 text-sm">We're always here to help</p>
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm">Emergency services available around the clock for urgent repairs.</p>
                    </div>
                    <div class="bg-white rounded-2xl p-6 shadow-md">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-medal text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800">Satisfaction Guaranteed</h4>
                                <p class="text-gray-500 text-sm">100% money-back guarantee</p>
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm">If you're not satisfied, we'll make it right or refund your payment.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Contact Section -->
    <section id="contact" class="py-16 hero-gradient text-white">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Get in Touch</h2>
                <p class="text-white/80">Ready to get started? Contact us today!</p>
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
                        <i class="fas fa-envelope text-2xl"></i>
                    </div>
                    <h3 class="font-bold mb-2">Email Us</h3>
                    <p class="text-white/80">info@example.com</p>
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
                © <?php echo date('Y'); ?> <?php echo htmlspecialchars($site['site_name'] ?? 'Local Services'); ?>. All rights reserved.
            </p>
            <p class="text-gray-500 text-xs mt-2">Powered by FilDevStudio</p>
        </div>
    </footer>
    
    <!-- Floating CTA Button -->
    <a href="tel:0917-123-4567" class="fixed bottom-6 right-6 px-6 py-3 bg-primary rounded-full flex items-center gap-2 shadow-lg hover:bg-primary/90 transition z-50 text-white font-semibold">
        <i class="fas fa-phone"></i>
        <span class="hidden sm:inline">Call Now</span>
    </a>

</body>
</html>
