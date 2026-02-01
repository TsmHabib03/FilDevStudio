<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($site['site_name'] ?? 'Portfolio'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '<?php echo $site['primary_color'] ?? '#8B5CF6'; ?>',
                        secondary: '<?php echo $site['secondary_color'] ?? '#EC4899'; ?>',
                        accent: '<?php echo $site['accent_color'] ?? '#F59E0B'; ?>',
                    }
                }
            }
        }
    </script>
    <style>
        .hero-gradient { background: linear-gradient(135deg, <?php echo $site['primary_color'] ?? '#8B5CF6'; ?> 0%, <?php echo $site['secondary_color'] ?? '#EC4899'; ?> 100%); }
        body { font-family: '<?php echo $site['font_body'] ?? 'Inter'; ?>', system-ui, sans-serif; }
        h1, h2, h3, h4 { font-family: '<?php echo $site['font_heading'] ?? 'Inter'; ?>', system-ui, sans-serif; }
    </style>
    <?php if (!empty($site['font_heading']) || !empty($site['font_body'])): ?>
    <link href="https://fonts.googleapis.com/css2?family=<?php echo urlencode($site['font_heading'] ?? 'Inter'); ?>:wght@400;600;700&family=<?php echo urlencode($site['font_body'] ?? 'Inter'); ?>:wght@400;500&display=swap" rel="stylesheet">
    <?php endif; ?>
</head>
<body class="bg-gray-900 min-h-screen text-white">
    
    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-gray-900/80 backdrop-blur-lg border-b border-white/10">
        <nav class="max-w-6xl mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <?php if (!empty($logoUrl)): ?>
                        <img src="<?php echo htmlspecialchars($logoUrl); ?>" alt="Logo" class="h-10 w-auto">
                    <?php else: ?>
                        <div class="w-10 h-10 hero-gradient rounded-xl flex items-center justify-center">
                            <span class="font-bold text-lg"><?php echo strtoupper(substr($site['site_name'] ?? 'P', 0, 1)); ?></span>
                        </div>
                    <?php endif; ?>
                    <span class="text-lg font-bold"><?php echo htmlspecialchars($site['site_name'] ?? 'Portfolio'); ?></span>
                </div>
                <div class="hidden md:flex items-center gap-6 text-sm">
                    <a href="#about" class="text-gray-400 hover:text-white transition">About</a>
                    <a href="#services" class="text-gray-400 hover:text-white transition">Services</a>
                    <a href="#portfolio" class="text-gray-400 hover:text-white transition">Portfolio</a>
                    <a href="#contact" class="px-5 py-2 hero-gradient rounded-full hover:opacity-90 transition">Hire Me</a>
                </div>
            </div>
        </nav>
    </header>
    
    <!-- Hero Section -->
    <section class="min-h-screen flex items-center pt-20">
        <div class="max-w-6xl mx-auto px-4 py-20">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="inline-block px-4 py-1 bg-primary/20 text-primary rounded-full text-sm mb-6">👋 Available for work</span>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                        <?php echo htmlspecialchars($site['hero_title'] ?? 'Creative Freelancer & Designer'); ?>
                    </h1>
                    <p class="text-xl text-gray-400 mb-8">
                        <?php echo htmlspecialchars($site['hero_subtitle'] ?? 'I help brands and businesses stand out through creative design and strategic thinking.'); ?>
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="#contact" class="px-8 py-3 hero-gradient rounded-full font-semibold hover:opacity-90 transition shadow-lg shadow-primary/30">
                            <i class="fas fa-envelope mr-2"></i>Get in Touch
                        </a>
                        <a href="#portfolio" class="px-8 py-3 border border-white/20 rounded-full font-semibold hover:bg-white/10 transition">
                            <i class="fas fa-briefcase mr-2"></i>View Work
                        </a>
                    </div>
                    
                    <!-- Social Links -->
                    <div class="flex gap-4 mt-8">
                        <?php if (!empty($site['social_facebook'])): ?>
                            <a href="<?php echo htmlspecialchars($site['social_facebook']); ?>" target="_blank" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-white/20 transition">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($site['social_instagram'])): ?>
                            <a href="<?php echo htmlspecialchars($site['social_instagram']); ?>" target="_blank" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-white/20 transition">
                                <i class="fab fa-instagram"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($site['social_linkedin'])): ?>
                            <a href="<?php echo htmlspecialchars($site['social_linkedin']); ?>" target="_blank" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-white/20 transition">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($site['social_twitter'])): ?>
                            <a href="<?php echo htmlspecialchars($site['social_twitter']); ?>" target="_blank" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-white/20 transition">
                                <i class="fab fa-twitter"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="relative hidden md:block">
                    <div class="absolute inset-0 hero-gradient rounded-full blur-3xl opacity-30"></div>
                    <div class="relative bg-gradient-to-br from-white/10 to-white/5 rounded-3xl p-8 border border-white/10">
                        <div class="aspect-square rounded-2xl bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center">
                            <div class="text-center">
                                <i class="fas fa-user text-8xl text-white/20 mb-4"></i>
                                <p class="text-gray-500">Profile Photo</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- About Section -->
    <section id="about" class="py-20 bg-gray-800/50">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="px-4 py-1 bg-primary/20 text-primary rounded-full text-sm font-medium">About Me</span>
                    <h2 class="text-3xl md:text-4xl font-bold mt-4 mb-6">
                        Passionate About Creating Amazing Experiences
                    </h2>
                    <p class="text-gray-400 leading-relaxed mb-6">
                        <?php echo nl2br(htmlspecialchars($site['about_content'] ?? 'I am a creative professional with years of experience in design and development. I love turning ideas into reality and helping businesses achieve their goals through thoughtful design and strategic solutions.')); ?>
                    </p>
                    
                    <!-- Skills -->
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm mb-2">
                                <span>Graphic Design</span>
                                <span class="text-primary">95%</span>
                            </div>
                            <div class="h-2 bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full hero-gradient rounded-full" style="width: 95%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-2">
                                <span>UI/UX Design</span>
                                <span class="text-primary">90%</span>
                            </div>
                            <div class="h-2 bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full hero-gradient rounded-full" style="width: 90%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-2">
                                <span>Web Development</span>
                                <span class="text-primary">85%</span>
                            </div>
                            <div class="h-2 bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full hero-gradient rounded-full" style="width: 85%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-800 rounded-2xl p-6 border border-white/10">
                        <div class="text-4xl font-bold text-primary mb-2">5+</div>
                        <div class="text-gray-400">Years Experience</div>
                    </div>
                    <div class="bg-gray-800 rounded-2xl p-6 border border-white/10">
                        <div class="text-4xl font-bold text-secondary mb-2">100+</div>
                        <div class="text-gray-400">Projects Completed</div>
                    </div>
                    <div class="bg-gray-800 rounded-2xl p-6 border border-white/10">
                        <div class="text-4xl font-bold text-primary mb-2">50+</div>
                        <div class="text-gray-400">Happy Clients</div>
                    </div>
                    <div class="bg-gray-800 rounded-2xl p-6 border border-white/10">
                        <div class="text-4xl font-bold text-secondary mb-2">24/7</div>
                        <div class="text-gray-400">Support</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Services Section -->
    <section id="services" class="py-20">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-12">
                <span class="px-4 py-1 bg-primary/20 text-primary rounded-full text-sm font-medium">Services</span>
                <h2 class="text-3xl md:text-4xl font-bold mt-4">What I Offer</h2>
                <p class="text-gray-400 mt-2 max-w-xl mx-auto">Quality services tailored to your needs</p>
            </div>
            
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Service 1 -->
                <div class="bg-gray-800/50 rounded-2xl p-8 border border-white/10 hover:border-primary/50 transition group">
                    <div class="w-14 h-14 hero-gradient rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition">
                        <i class="fas fa-paint-brush text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Graphic Design</h3>
                    <p class="text-gray-400 mb-4">Logos, branding, marketing materials, social media graphics, and more.</p>
                    <span class="text-primary font-semibold">Starting at ₱2,500</span>
                </div>
                
                <!-- Service 2 -->
                <div class="bg-gray-800/50 rounded-2xl p-8 border border-white/10 hover:border-primary/50 transition group">
                    <div class="w-14 h-14 hero-gradient rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition">
                        <i class="fas fa-laptop-code text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Web Design</h3>
                    <p class="text-gray-400 mb-4">Modern, responsive websites that look great on all devices.</p>
                    <span class="text-primary font-semibold">Starting at ₱5,000</span>
                </div>
                
                <!-- Service 3 -->
                <div class="bg-gray-800/50 rounded-2xl p-8 border border-white/10 hover:border-primary/50 transition group">
                    <div class="w-14 h-14 hero-gradient rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition">
                        <i class="fas fa-mobile-alt text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">UI/UX Design</h3>
                    <p class="text-gray-400 mb-4">User-centered design for apps and websites.</p>
                    <span class="text-primary font-semibold">Starting at ₱8,000</span>
                </div>
                
                <!-- Service 4 -->
                <div class="bg-gray-800/50 rounded-2xl p-8 border border-white/10 hover:border-primary/50 transition group">
                    <div class="w-14 h-14 hero-gradient rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition">
                        <i class="fas fa-video text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Video Editing</h3>
                    <p class="text-gray-400 mb-4">Professional video editing for social media and marketing.</p>
                    <span class="text-primary font-semibold">Starting at ₱1,500</span>
                </div>
                
                <!-- Service 5 -->
                <div class="bg-gray-800/50 rounded-2xl p-8 border border-white/10 hover:border-primary/50 transition group">
                    <div class="w-14 h-14 hero-gradient rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition">
                        <i class="fas fa-bullhorn text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Social Media</h3>
                    <p class="text-gray-400 mb-4">Content creation and management for your social accounts.</p>
                    <span class="text-primary font-semibold">Starting at ₱3,000/mo</span>
                </div>
                
                <!-- Service 6 -->
                <div class="bg-gray-800/50 rounded-2xl p-8 border border-white/10 hover:border-primary/50 transition group">
                    <div class="w-14 h-14 hero-gradient rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition">
                        <i class="fas fa-camera text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Photography</h3>
                    <p class="text-gray-400 mb-4">Product photography and professional photo editing.</p>
                    <span class="text-primary font-semibold">Starting at ₱2,000</span>
                </div>
            </div>
            
            <?php if (!empty($site['services_content'])): ?>
            <div class="mt-12 bg-gray-800/50 rounded-2xl p-8 border border-white/10">
                <h3 class="font-bold mb-4 text-center">All Services:</h3>
                <p class="text-gray-400 whitespace-pre-line text-center"><?php echo htmlspecialchars($site['services_content']); ?></p>
            </div>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- Portfolio Section -->
    <section id="portfolio" class="py-20 bg-gray-800/50">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-12">
                <span class="px-4 py-1 bg-primary/20 text-primary rounded-full text-sm font-medium">Portfolio</span>
                <h2 class="text-3xl md:text-4xl font-bold mt-4">Recent Works</h2>
                <p class="text-gray-400 mt-2">Some of my best projects</p>
            </div>
            
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Portfolio Item 1 -->
                <div class="group relative overflow-hidden rounded-2xl">
                    <div class="aspect-square bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center">
                        <i class="fas fa-image text-6xl text-white/20"></i>
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-6">
                        <div>
                            <h4 class="font-bold text-lg">Project Title</h4>
                            <p class="text-gray-400 text-sm">Brand Identity</p>
                        </div>
                    </div>
                </div>
                
                <!-- Portfolio Item 2 -->
                <div class="group relative overflow-hidden rounded-2xl">
                    <div class="aspect-square bg-gradient-to-br from-blue-500/20 to-cyan-500/20 flex items-center justify-center">
                        <i class="fas fa-image text-6xl text-white/20"></i>
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-6">
                        <div>
                            <h4 class="font-bold text-lg">Project Title</h4>
                            <p class="text-gray-400 text-sm">Web Design</p>
                        </div>
                    </div>
                </div>
                
                <!-- Portfolio Item 3 -->
                <div class="group relative overflow-hidden rounded-2xl">
                    <div class="aspect-square bg-gradient-to-br from-orange-500/20 to-red-500/20 flex items-center justify-center">
                        <i class="fas fa-image text-6xl text-white/20"></i>
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-6">
                        <div>
                            <h4 class="font-bold text-lg">Project Title</h4>
                            <p class="text-gray-400 text-sm">UI/UX Design</p>
                        </div>
                    </div>
                </div>
                
                <!-- Portfolio Item 4 -->
                <div class="group relative overflow-hidden rounded-2xl">
                    <div class="aspect-square bg-gradient-to-br from-green-500/20 to-emerald-500/20 flex items-center justify-center">
                        <i class="fas fa-image text-6xl text-white/20"></i>
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-6">
                        <div>
                            <h4 class="font-bold text-lg">Project Title</h4>
                            <p class="text-gray-400 text-sm">Marketing</p>
                        </div>
                    </div>
                </div>
                
                <!-- Portfolio Item 5 -->
                <div class="group relative overflow-hidden rounded-2xl">
                    <div class="aspect-square bg-gradient-to-br from-pink-500/20 to-rose-500/20 flex items-center justify-center">
                        <i class="fas fa-image text-6xl text-white/20"></i>
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-6">
                        <div>
                            <h4 class="font-bold text-lg">Project Title</h4>
                            <p class="text-gray-400 text-sm">Social Media</p>
                        </div>
                    </div>
                </div>
                
                <!-- Portfolio Item 6 -->
                <div class="group relative overflow-hidden rounded-2xl">
                    <div class="aspect-square bg-gradient-to-br from-yellow-500/20 to-amber-500/20 flex items-center justify-center">
                        <i class="fas fa-image text-6xl text-white/20"></i>
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-6">
                        <div>
                            <h4 class="font-bold text-lg">Project Title</h4>
                            <p class="text-gray-400 text-sm">Photography</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Testimonials -->
    <section class="py-20">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-12">
                <span class="px-4 py-1 bg-primary/20 text-primary rounded-full text-sm font-medium">Testimonials</span>
                <h2 class="text-3xl md:text-4xl font-bold mt-4">Client Reviews</h2>
            </div>
            
            <div class="grid md:grid-cols-3 gap-6">
                <!-- Testimonial 1 -->
                <div class="bg-gray-800/50 rounded-2xl p-6 border border-white/10">
                    <div class="flex gap-1 text-yellow-400 mb-4">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="text-gray-400 mb-6">"Outstanding work! Delivered exactly what I needed and exceeded my expectations. Highly recommended!"</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 hero-gradient rounded-full flex items-center justify-center">
                            <span class="font-bold text-sm">JD</span>
                        </div>
                        <div>
                            <div class="font-semibold">John Doe</div>
                            <div class="text-gray-500 text-sm">Business Owner</div>
                        </div>
                    </div>
                </div>
                
                <!-- Testimonial 2 -->
                <div class="bg-gray-800/50 rounded-2xl p-6 border border-white/10">
                    <div class="flex gap-1 text-yellow-400 mb-4">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="text-gray-400 mb-6">"Very professional and creative. The designs were exactly what our brand needed. Will work with again!"</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 hero-gradient rounded-full flex items-center justify-center">
                            <span class="font-bold text-sm">MS</span>
                        </div>
                        <div>
                            <div class="font-semibold">Maria Santos</div>
                            <div class="text-gray-500 text-sm">Marketing Manager</div>
                        </div>
                    </div>
                </div>
                
                <!-- Testimonial 3 -->
                <div class="bg-gray-800/50 rounded-2xl p-6 border border-white/10">
                    <div class="flex gap-1 text-yellow-400 mb-4">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <p class="text-gray-400 mb-6">"Fast turnaround and excellent communication. Made the whole process easy and stress-free."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 hero-gradient rounded-full flex items-center justify-center">
                            <span class="font-bold text-sm">PR</span>
                        </div>
                        <div>
                            <div class="font-semibold">Pedro Reyes</div>
                            <div class="text-gray-500 text-sm">Startup Founder</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-gray-800/50">
        <div class="max-w-4xl mx-auto px-4">
            <div class="text-center mb-12">
                <span class="px-4 py-1 bg-primary/20 text-primary rounded-full text-sm font-medium">Contact</span>
                <h2 class="text-3xl md:text-4xl font-bold mt-4">Let's Work Together</h2>
                <p class="text-gray-400 mt-2">Ready to start your project? Get in touch!</p>
            </div>
            
            <div class="bg-gray-900 rounded-3xl p-8 md:p-12 border border-white/10">
                <div class="grid md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-xl font-bold mb-6">Contact Info</h3>
                        <div class="space-y-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-primary/20 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-envelope text-primary"></i>
                                </div>
                                <div>
                                    <div class="text-gray-400 text-sm">Email</div>
                                    <div>hello@example.com</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-primary/20 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-phone text-primary"></i>
                                </div>
                                <div>
                                    <div class="text-gray-400 text-sm">Phone</div>
                                    <div>0917-123-4567</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-primary/20 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-map-marker-alt text-primary"></i>
                                </div>
                                <div>
                                    <div class="text-gray-400 text-sm">Location</div>
                                    <div><?php echo nl2br(htmlspecialchars($site['contact_info'] ?? 'Philippines')); ?></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Social Links -->
                        <div class="flex gap-3 mt-8">
                            <?php if (!empty($site['social_facebook'])): ?>
                                <a href="<?php echo htmlspecialchars($site['social_facebook']); ?>" target="_blank" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-primary/50 transition">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($site['social_instagram'])): ?>
                                <a href="<?php echo htmlspecialchars($site['social_instagram']); ?>" target="_blank" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-primary/50 transition">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($site['social_linkedin'])): ?>
                                <a href="<?php echo htmlspecialchars($site['social_linkedin']); ?>" target="_blank" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-primary/50 transition">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($site['social_messenger'])): ?>
                                <a href="<?php echo htmlspecialchars($site['social_messenger']); ?>" target="_blank" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-primary/50 transition">
                                    <i class="fab fa-facebook-messenger"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-xl font-bold mb-6">Quick Message</h3>
                        <div class="space-y-4">
                            <a href="mailto:hello@example.com" class="block w-full py-4 px-6 hero-gradient rounded-xl text-center font-semibold hover:opacity-90 transition">
                                <i class="fas fa-envelope mr-2"></i>Send Email
                            </a>
                            <?php if (!empty($site['social_messenger'])): ?>
                            <a href="<?php echo htmlspecialchars($site['social_messenger']); ?>" target="_blank" class="block w-full py-4 px-6 bg-blue-600 rounded-xl text-center font-semibold hover:bg-blue-700 transition">
                                <i class="fab fa-facebook-messenger mr-2"></i>Message on Messenger
                            </a>
                            <?php endif; ?>
                            <a href="tel:0917-123-4567" class="block w-full py-4 px-6 bg-white/10 border border-white/20 rounded-xl text-center font-semibold hover:bg-white/20 transition">
                                <i class="fas fa-phone mr-2"></i>Call Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="bg-gray-900 border-t border-white/10 py-8">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <div class="flex items-center justify-center gap-3 mb-4">
                <?php if (!empty($logoUrl)): ?>
                    <img src="<?php echo htmlspecialchars($logoUrl); ?>" alt="Logo" class="h-8 w-auto">
                <?php else: ?>
                    <div class="w-8 h-8 hero-gradient rounded-lg flex items-center justify-center">
                        <span class="font-bold"><?php echo strtoupper(substr($site['site_name'] ?? 'P', 0, 1)); ?></span>
                    </div>
                <?php endif; ?>
                <span class="font-bold"><?php echo htmlspecialchars($site['site_name'] ?? 'Portfolio'); ?></span>
            </div>
            <p class="text-gray-500 text-sm">
                © <?php echo date('Y'); ?> <?php echo htmlspecialchars($site['site_name'] ?? 'Portfolio'); ?>. All rights reserved.
            </p>
            <p class="text-gray-600 text-xs mt-2">Powered by FilDevStudio</p>
        </div>
    </footer>

</body>
</html>
