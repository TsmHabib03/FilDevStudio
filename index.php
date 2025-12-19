<?php
/**
 * Landing Page - FilDevStudio Web Services Platform
 */
$pageTitle = "FilDevStudio - Code & Creative Solutions";
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="gradient-bg text-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div>
                <h1 class="text-4xl md:text-5xl font-bold mb-6">
                    Build Your Online Presence with <span class="text-yellow-400">FilDevStudio</span>
                </h1>
                <p class="text-xl text-blue-100 mb-8">
                    Integrated Web & Brand Identity Packages for Local Businesses. 
                    Professional websites made simple, affordable, and effective.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="templates.php" class="bg-white text-primary px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition text-center">
                        <i class="fas fa-eye mr-2"></i>View Templates
                    </a>
                    <a href="auth/register.php" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-primary transition text-center">
                        <i class="fas fa-rocket mr-2"></i>Get Started Free
                    </a>
                </div>
            </div>
            <div class="hidden md:block">
                <div class="bg-white/10 rounded-2xl p-8 backdrop-blur">
                    <div class="bg-white rounded-lg p-4 shadow-xl">
                        <div class="flex items-center space-x-2 mb-4">
                            <div class="w-3 h-3 rounded-full bg-red-500"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                            <div class="w-3 h-3 rounded-full bg-green-500"></div>
                        </div>
                        <div class="space-y-3">
                            <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                            <div class="h-4 bg-blue-200 rounded w-full"></div>
                            <div class="h-4 bg-gray-200 rounded w-5/6"></div>
                            <div class="h-20 bg-gradient-to-r from-blue-100 to-blue-200 rounded mt-4"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Problem Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">The Challenge for SMEs & Freelancers</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Many local businesses struggle with establishing their online presence</p>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-red-50 rounded-xl p-6 border-l-4 border-red-500">
                <div class="text-red-500 text-3xl mb-4"><i class="fas fa-globe"></i></div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Lack of Online Presence</h3>
                <p class="text-gray-600">Many SMEs don't have websites, missing out on potential customers searching online.</p>
            </div>
            <div class="bg-orange-50 rounded-xl p-6 border-l-4 border-orange-500">
                <div class="text-orange-500 text-3xl mb-4"><i class="fas fa-money-bill-wave"></i></div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">High Development Costs</h3>
                <p class="text-gray-600">Hiring separate developers and designers is expensive for small business budgets.</p>
            </div>
            <div class="bg-yellow-50 rounded-xl p-6 border-l-4 border-yellow-500">
                <div class="text-yellow-500 text-3xl mb-4"><i class="fas fa-eye-slash"></i></div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Poor Visibility</h3>
                <p class="text-gray-600">Manual operations and lack of digital presence leads to limited market reach.</p>
            </div>
        </div>
    </div>
</section>

<!-- Solution Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Our "Whole-Team" Approach</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">FilDevStudio brings together technical expertise and creative talent under one roof</p>
        </div>
        <div class="grid md:grid-cols-2 gap-8">
            <div class="bg-white rounded-xl p-8 shadow-lg card-hover">
                <div class="w-16 h-16 gradient-bg rounded-xl flex items-center justify-center mb-6">
                    <i class="fas fa-code text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-4">Technical Team</h3>
                <p class="text-gray-600 mb-4">Our programmers handle all the technical aspects:</p>
                <ul class="space-y-2 text-gray-600">
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Custom Web Development</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Database Management</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>System Integration</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Performance Optimization</li>
                </ul>
            </div>
            <div class="bg-white rounded-xl p-8 shadow-lg card-hover">
                <div class="w-16 h-16 bg-gradient-to-r from-pink-500 to-purple-500 rounded-xl flex items-center justify-center mb-6">
                    <i class="fas fa-palette text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-4">Creative & Operations Team</h3>
                <p class="text-gray-600 mb-4">Non-coders who bring your brand to life:</p>
                <ul class="space-y-2 text-gray-600">
                    <li><i class="fas fa-check text-green-500 mr-2"></i>UI/UX Design</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Brand Identity</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Content Strategy</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Client Communication</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section id="services" class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Our Services & Features</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Everything you need to establish and grow your online presence</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full bg-white rounded-xl shadow-lg overflow-hidden">
                <thead class="gradient-bg text-white">
                    <tr>
                        <th class="px-6 py-4 text-left">Product / Service Feature</th>
                        <th class="px-6 py-4 text-left">Benefit to Customers</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <i class="fas fa-laptop-code text-primary mr-3"></i>
                                <span class="font-semibold">Custom Web Development</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">Reliability & Performance - Fast, secure websites built for your needs</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <i class="fas fa-paint-brush text-purple-500 mr-3"></i>
                                <span class="font-semibold">Professional UI/UX Design</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">Brand Credibility - Beautiful designs that build trust with customers</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <i class="fas fa-boxes text-green-500 mr-3"></i>
                                <span class="font-semibold">Automated Inventory Systems</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">Operational Efficiency - Streamline your business operations</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <i class="fas fa-bullhorn text-orange-500 mr-3"></i>
                                <span class="font-semibold">Content & Communication Strategy</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">Customer Engagement - Connect effectively with your audience</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <i class="fas fa-handshake text-blue-500 mr-3"></i>
                                <span class="font-semibold">Simplified Client Onboarding</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">Easy Adoption - Get started quickly with guided setup</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Team Workflow Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Our Team Workflow</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">How we work together to deliver exceptional results</p>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white rounded-xl p-6 shadow-lg text-center card-hover">
                <div class="w-20 h-20 mx-auto gradient-bg rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-code text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Technical Team</h3>
                <p class="text-gray-600 mb-4">Backend development, database design, system architecture, and performance optimization.</p>
                <div class="flex flex-wrap justify-center gap-2">
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">PHP</span>
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">MySQL</span>
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">JavaScript</span>
                </div>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-lg text-center card-hover">
                <div class="w-20 h-20 mx-auto bg-gradient-to-r from-pink-500 to-purple-500 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-palette text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Creative Team</h3>
                <p class="text-gray-600 mb-4">UI/UX design, branding, visual identity, and content creation for compelling user experiences.</p>
                <div class="flex flex-wrap justify-center gap-2">
                    <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm">UI Design</span>
                    <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm">Branding</span>
                    <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm">Content</span>
                </div>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-lg text-center card-hover">
                <div class="w-20 h-20 mx-auto bg-gradient-to-r from-green-500 to-teal-500 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-users-cog text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Operations Team</h3>
                <p class="text-gray-600 mb-4">Client communication, project management, onboarding, and ongoing support services.</p>
                <div class="flex flex-wrap justify-center gap-2">
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">Support</span>
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">Management</span>
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">Training</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl font-bold text-gray-800 mb-6">About FilDevStudio</h2>
                <p class="text-gray-600 mb-4">
                    <strong>FilDevStudio: Code & Creative Solutions</strong> is a service-oriented ICT company 
                    specializing in integrated web and brand identity packages for local businesses.
                </p>
                <p class="text-gray-600 mb-4">
                    We believe every business deserves a professional online presence, regardless of technical 
                    knowledge or budget constraints. Our mission is to bridge the gap between technology and 
                    creativity, making digital solutions accessible to SMEs and freelancers.
                </p>
                <p class="text-gray-600 mb-6">
                    With our template-based website builder system, clients can easily choose, customize, and 
                    manage their websites without writing a single line of code.
                </p>
                <a href="auth/register.php" class="inline-flex items-center gradient-bg text-white px-6 py-3 rounded-lg font-semibold hover:opacity-90 transition">
                    <i class="fas fa-arrow-right mr-2"></i>Start Your Journey
                </a>
            </div>
            <div class="bg-gray-100 rounded-2xl p-8">
                <div class="space-y-6">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 gradient-bg rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-bullseye text-white"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800">Our Mission</h4>
                            <p class="text-gray-600 text-sm">Empower local businesses with affordable, professional digital solutions.</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 gradient-bg rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-eye text-white"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800">Our Vision</h4>
                            <p class="text-gray-600 text-sm">Be the go-to partner for SMEs seeking quality web and branding services.</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 gradient-bg rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-heart text-white"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800">Our Values</h4>
                            <p class="text-gray-600 text-sm">Quality, Affordability, Accessibility, and Client Success.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="gradient-bg py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold text-white mb-4">Ready to Build Your Online Presence?</h2>
        <p class="text-blue-100 mb-8">Get started today with our easy-to-use template builder. No coding required!</p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="templates.php" class="bg-white text-primary px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                Browse Templates
            </a>
            <a href="auth/register.php" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-primary transition">
                Create Free Account
            </a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
