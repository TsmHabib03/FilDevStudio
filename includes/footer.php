    </main>
    
    <!-- Footer - Enhanced Design with Modern Styling -->
    <footer class="bg-dark text-white mt-auto relative overflow-hidden">
        <!-- Decorative gradient overlay -->
        <div class="absolute inset-0 gradient-mesh opacity-30"></div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
                
                <!-- Company Info -->
                <div class="lg:col-span-2">
                    <div class="flex items-center space-x-3 mb-6">
                        <img src="<?php echo $basePath ?? ''; ?>assets/images/logo.png" alt="FilDevStudio Logo" class="w-12 h-12 rounded-xl">
                        <div>
                            <span class="text-2xl font-bold">FilDev<span class="text-primary-400">Studio</span></span>
                            <span class="block text-sm text-gray-400">Code & Creative Solutions</span>
                        </div>
                    </div>
                    <p class="text-gray-400 mb-6 max-w-md leading-relaxed">
                        Integrated Web & Brand Identity Packages for Local Businesses. 
                        We bridge the gap between technology and creativity, making professional 
                        digital solutions accessible to SMEs and freelancers.
                    </p>
                    
                    <!-- Social Links -->
                    <div class="flex space-x-3">
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-primary-500 rounded-lg flex items-center justify-center transition-all duration-300 hover:scale-110">
                            <i class="fab fa-facebook-f text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-primary-500 rounded-lg flex items-center justify-center transition-all duration-300 hover:scale-110">
                            <i class="fab fa-twitter text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-primary-500 rounded-lg flex items-center justify-center transition-all duration-300 hover:scale-110">
                            <i class="fab fa-instagram text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-primary-500 rounded-lg flex items-center justify-center transition-all duration-300 hover:scale-110">
                            <i class="fab fa-linkedin-in text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-primary-500 rounded-lg flex items-center justify-center transition-all duration-300 hover:scale-110">
                            <i class="fab fa-github text-white"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-6 flex items-center">
                        <span class="w-8 h-1 bg-primary-500 rounded mr-3"></span>
                        Quick Links
                    </h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="<?php echo $basePath ?? ''; ?>index.php" class="text-gray-400 hover:text-primary-400 transition-colors duration-200 flex items-center group">
                                <i class="fas fa-chevron-right text-xs mr-2 text-primary-500 group-hover:translate-x-1 transition-transform duration-200"></i>
                                Home
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo $basePath ?? ''; ?>templates.php" class="text-gray-400 hover:text-primary-400 transition-colors duration-200 flex items-center group">
                                <i class="fas fa-chevron-right text-xs mr-2 text-primary-500 group-hover:translate-x-1 transition-transform duration-200"></i>
                                Templates
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo $basePath ?? ''; ?>index.php#services" class="text-gray-400 hover:text-primary-400 transition-colors duration-200 flex items-center group">
                                <i class="fas fa-chevron-right text-xs mr-2 text-primary-500 group-hover:translate-x-1 transition-transform duration-200"></i>
                                Services
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo $basePath ?? ''; ?>index.php#about" class="text-gray-400 hover:text-primary-400 transition-colors duration-200 flex items-center group">
                                <i class="fas fa-chevron-right text-xs mr-2 text-primary-500 group-hover:translate-x-1 transition-transform duration-200"></i>
                                About Us
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo $basePath ?? ''; ?>auth/register.php" class="text-gray-400 hover:text-primary-400 transition-colors duration-200 flex items-center group">
                                <i class="fas fa-chevron-right text-xs mr-2 text-primary-500 group-hover:translate-x-1 transition-transform duration-200"></i>
                                Get Started
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="text-lg font-semibold mb-6 flex items-center">
                        <span class="w-8 h-1 bg-secondary-500 rounded mr-3"></span>
                        Contact Us
                    </h3>
                    <ul class="space-y-4">
                        <li>
                            <a href="mailto:hello@fildevstudio.com" class="flex items-start space-x-3 text-gray-400 hover:text-white transition-colors duration-200 group">
                                <span class="w-10 h-10 bg-white/10 group-hover:bg-primary-500 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors duration-200">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <span class="pt-2">hello@fildevstudio.com</span>
                            </a>
                        </li>
                        <li>
                            <a href="tel:+631234567890" class="flex items-start space-x-3 text-gray-400 hover:text-white transition-colors duration-200 group">
                                <span class="w-10 h-10 bg-white/10 group-hover:bg-secondary-500 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors duration-200">
                                    <i class="fas fa-phone"></i>
                                </span>
                                <span class="pt-2">+63 123 456 7890</span>
                            </a>
                        </li>
                        <li>
                            <div class="flex items-start space-x-3 text-gray-400">
                                <span class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-map-marker-alt"></i>
                                </span>
                                <span class="pt-2">Metro Manila, Philippines</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Copyright Bar -->
            <div class="border-t border-white/10 mt-12 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                    <p class="text-gray-400 text-sm">
                        &copy; <?php echo date('Y'); ?> FilDevStudio: Code & Creative Solutions. All rights reserved.
                    </p>
                    <div class="flex items-center space-x-2 px-4 py-2 bg-amber-500/20 rounded-lg">
                        <i class="fas fa-graduation-cap text-amber-400"></i>
                        <span class="text-amber-400 text-sm font-medium">Student Project – For Educational Purposes Only</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Mobile Menu Script - Enhanced with smooth animation -->
    <script>
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuIcon = document.getElementById('menu-icon');

        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
                // Toggle icon between bars and times
                if (menuIcon) {
                    menuIcon.classList.toggle('fa-bars');
                    menuIcon.classList.toggle('fa-times');
                }
            });
        }

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Animate elements on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>
</html>
