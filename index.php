<?php
/**
 * Landing Page - FilDevStudio Web Services Platform
 * Enhanced UI/UX with Modern Design
 */
$pageTitle = "FilDevStudio - Code & Creative Solutions";
require_once 'includes/header.php';
?>

<!-- Hero Section - Enhanced with animated background and better typography -->
<section class="relative gradient-hero text-white py-24 lg:py-32 overflow-hidden">
    <!-- Animated Background Elements -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-20 left-10 w-72 h-72 bg-primary-400/20 rounded-full blur-3xl animate-pulse-slow"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-accent-500/10 rounded-full blur-3xl animate-pulse-slow" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-secondary-500/5 rounded-full blur-3xl"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
            <!-- Hero Content -->
            <div class="animate-fade-in">
                <div class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur rounded-full text-sm font-medium mb-6">
                    <span class="w-2 h-2 bg-secondary-400 rounded-full mr-2 animate-pulse"></span>
                    Web Solutions for SMEs & Freelancers
                </div>
                
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold mb-6 leading-tight">
                    Build Your <span class="text-transparent bg-clip-text bg-gradient-to-r from-secondary-400 to-accent-400">Online Presence</span>
                    <br>with FilDevStudio
                </h1>
                
                <p class="text-xl text-primary-100 mb-8 leading-relaxed max-w-xl">
                    Integrated Web & Brand Identity Packages for Local Businesses. 
                    Professional websites made simple, affordable, and effective — no coding required.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="templates.php" class="inline-flex items-center justify-center px-8 py-4 bg-white text-primary-600 font-bold rounded-xl hover:bg-gray-100 transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                        <i class="fas fa-eye mr-2"></i>View Templates
                    </a>
                    <a href="auth/register.php" class="inline-flex items-center justify-center px-8 py-4 bg-transparent border-2 border-white/50 text-white font-bold rounded-xl hover:bg-white/10 hover:border-white transition-all duration-300">
                        <i class="fas fa-rocket mr-2"></i>Get Started Free
                    </a>
                </div>
                
                <!-- Trust Badges -->
                <div class="mt-10 pt-8 border-t border-white/20">
                    <p class="text-primary-200 text-sm mb-3">Trusted by local businesses</p>
                    <div class="flex items-center gap-6">
                        <div class="text-center">
                            <span class="text-3xl font-bold text-white">50+</span>
                            <span class="block text-sm text-primary-200">Websites Built</span>
                        </div>
                        <div class="w-px h-12 bg-white/20"></div>
                        <div class="text-center">
                            <span class="text-3xl font-bold text-white">5</span>
                            <span class="block text-sm text-primary-200">Template Categories</span>
                        </div>
                        <div class="w-px h-12 bg-white/20"></div>
                        <div class="text-center">
                            <span class="text-3xl font-bold text-white">100%</span>
                            <span class="block text-sm text-primary-200">Satisfaction</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Hero Visual - Browser Mockup -->
            <div class="hidden lg:block animate-slide-up">
                <div class="relative">
                    <!-- Floating decorative elements -->
                    <div class="absolute -top-6 -left-6 w-20 h-20 bg-secondary-500 rounded-2xl opacity-80 rotate-12 animate-pulse-slow"></div>
                    <div class="absolute -bottom-4 -right-4 w-16 h-16 bg-accent-500 rounded-xl opacity-80 -rotate-12 animate-pulse-slow" style="animation-delay: 0.5s;"></div>
                    
                    <!-- Browser Window -->
                    <div class="relative bg-white/10 backdrop-blur-lg rounded-3xl p-6 border border-white/20 shadow-2xl">
                        <div class="bg-white rounded-2xl overflow-hidden shadow-xl">
                            <!-- Browser Top Bar -->
                            <div class="bg-gray-100 px-4 py-3 flex items-center space-x-2 border-b border-gray-200">
                                <div class="flex space-x-2">
                                    <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                    <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                                    <div class="w-3 h-3 rounded-full bg-green-400"></div>
                                </div>
                                <div class="flex-1 mx-4">
                                    <div class="bg-white rounded-lg px-4 py-1.5 text-xs text-gray-400 flex items-center">
                                        <i class="fas fa-lock mr-2 text-green-500"></i>
                                        yourbusiness.fildevstudio.com
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Browser Content -->
                            <div class="p-6 space-y-4">
                                <div class="flex items-center space-x-4 mb-6">
                                    <div class="w-10 h-10 gradient-bg rounded-lg"></div>
                                    <div class="h-4 bg-gray-200 rounded-full w-32"></div>
                                </div>
                                <div class="h-32 bg-gradient-to-br from-primary-100 to-primary-200 rounded-xl"></div>
                                <div class="grid grid-cols-3 gap-3">
                                    <div class="h-20 bg-gray-100 rounded-lg"></div>
                                    <div class="h-20 bg-gray-100 rounded-lg"></div>
                                    <div class="h-20 bg-gray-100 rounded-lg"></div>
                                </div>
                                <div class="space-y-2">
                                    <div class="h-3 bg-gray-200 rounded-full w-full"></div>
                                    <div class="h-3 bg-gray-200 rounded-full w-4/5"></div>
                                    <div class="h-3 bg-gray-200 rounded-full w-3/5"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Wave Divider -->
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 120L60 105C120 90 240 60 360 45C480 30 600 30 720 37.5C840 45 960 60 1080 67.5C1200 75 1320 75 1380 75L1440 75V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="#ffffff"/>
        </svg>
    </div>
</section>

<!-- Problem Section - Enhanced Cards -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 animate-on-scroll">
            <span class="inline-flex items-center px-4 py-1.5 bg-red-100 text-red-700 text-sm font-semibold rounded-full mb-4">
                <i class="fas fa-exclamation-triangle mr-2"></i>The Challenge
            </span>
            <h2 class="text-3xl md:text-4xl font-bold text-dark mb-4">Why SMEs & Freelancers Struggle Online</h2>
            <p class="text-gray-600 max-w-2xl mx-auto text-lg">Many local businesses face common obstacles when establishing their digital presence</p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <!-- Problem Card 1 -->
            <div class="group card p-8 border-l-4 border-red-500 hover:border-l-primary-500 card-hover animate-on-scroll">
                <div class="w-14 h-14 bg-red-100 group-hover:bg-primary-100 rounded-2xl flex items-center justify-center mb-6 transition-colors duration-300">
                    <i class="fas fa-globe text-2xl text-red-500 group-hover:text-primary-500 transition-colors duration-300"></i>
                </div>
                <h3 class="text-xl font-bold text-dark mb-3">Lack of Online Presence</h3>
                <p class="text-gray-600 leading-relaxed">Many SMEs don't have websites, missing out on potential customers actively searching online for their services.</p>
            </div>
            
            <!-- Problem Card 2 -->
            <div class="group card p-8 border-l-4 border-orange-500 hover:border-l-primary-500 card-hover animate-on-scroll" style="animation-delay: 0.1s;">
                <div class="w-14 h-14 bg-orange-100 group-hover:bg-primary-100 rounded-2xl flex items-center justify-center mb-6 transition-colors duration-300">
                    <i class="fas fa-money-bill-wave text-2xl text-orange-500 group-hover:text-primary-500 transition-colors duration-300"></i>
                </div>
                <h3 class="text-xl font-bold text-dark mb-3">High Development Costs</h3>
                <p class="text-gray-600 leading-relaxed">Hiring separate developers and designers is expensive and often beyond small business budgets.</p>
            </div>
            
            <!-- Problem Card 3 -->
            <div class="group card p-8 border-l-4 border-yellow-500 hover:border-l-primary-500 card-hover animate-on-scroll" style="animation-delay: 0.2s;">
                <div class="w-14 h-14 bg-yellow-100 group-hover:bg-primary-100 rounded-2xl flex items-center justify-center mb-6 transition-colors duration-300">
                    <i class="fas fa-eye-slash text-2xl text-yellow-600 group-hover:text-primary-500 transition-colors duration-300"></i>
                </div>
                <h3 class="text-xl font-bold text-dark mb-3">Poor Market Visibility</h3>
                <p class="text-gray-600 leading-relaxed">Manual operations and lack of digital presence leads to limited market reach and growth opportunities.</p>
            </div>
        </div>
    </div>
</section>

<!-- Solution Section - Our Approach -->
<section class="py-20 bg-gray-50 gradient-mesh">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 animate-on-scroll">
            <span class="inline-flex items-center px-4 py-1.5 bg-primary-100 text-primary-700 text-sm font-semibold rounded-full mb-4">
                <i class="fas fa-lightbulb mr-2"></i>Our Solution
            </span>
            <h2 class="text-3xl md:text-4xl font-bold text-dark mb-4">The "Whole-Team" Approach</h2>
            <p class="text-gray-600 max-w-2xl mx-auto text-lg">FilDevStudio brings together technical expertise and creative talent under one roof</p>
        </div>
        
        <div class="grid lg:grid-cols-2 gap-8">
            <!-- Technical Team Card -->
            <div class="card p-8 card-hover animate-on-scroll">
                <div class="flex items-start space-x-6">
                    <div class="w-20 h-20 gradient-bg rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg">
                        <i class="fas fa-code text-white text-3xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold text-dark mb-2">Technical Team</h3>
                        <p class="text-gray-600 mb-6">Our programmers handle all the technical aspects with expertise and precision.</p>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex items-center space-x-3 p-3 bg-primary-50 rounded-xl">
                                <i class="fas fa-check-circle text-primary-500"></i>
                                <span class="text-sm font-medium text-gray-700">Web Development</span>
                            </div>
                            <div class="flex items-center space-x-3 p-3 bg-primary-50 rounded-xl">
                                <i class="fas fa-check-circle text-primary-500"></i>
                                <span class="text-sm font-medium text-gray-700">Database Design</span>
                            </div>
                            <div class="flex items-center space-x-3 p-3 bg-primary-50 rounded-xl">
                                <i class="fas fa-check-circle text-primary-500"></i>
                                <span class="text-sm font-medium text-gray-700">System Integration</span>
                            </div>
                            <div class="flex items-center space-x-3 p-3 bg-primary-50 rounded-xl">
                                <i class="fas fa-check-circle text-primary-500"></i>
                                <span class="text-sm font-medium text-gray-700">Optimization</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Creative Team Card -->
            <div class="card p-8 card-hover animate-on-scroll" style="animation-delay: 0.1s;">
                <div class="flex items-start space-x-6">
                    <div class="w-20 h-20 gradient-creative rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg">
                        <i class="fas fa-palette text-white text-3xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold text-dark mb-2">Creative & Operations</h3>
                        <p class="text-gray-600 mb-6">Non-coders who bring your brand to life with creativity and care.</p>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex items-center space-x-3 p-3 bg-accent-50 rounded-xl">
                                <i class="fas fa-check-circle text-accent-500"></i>
                                <span class="text-sm font-medium text-gray-700">UI/UX Design</span>
                            </div>
                            <div class="flex items-center space-x-3 p-3 bg-accent-50 rounded-xl">
                                <i class="fas fa-check-circle text-accent-500"></i>
                                <span class="text-sm font-medium text-gray-700">Brand Identity</span>
                            </div>
                            <div class="flex items-center space-x-3 p-3 bg-accent-50 rounded-xl">
                                <i class="fas fa-check-circle text-accent-500"></i>
                                <span class="text-sm font-medium text-gray-700">Content Strategy</span>
                            </div>
                            <div class="flex items-center space-x-3 p-3 bg-accent-50 rounded-xl">
                                <i class="fas fa-check-circle text-accent-500"></i>
                                <span class="text-sm font-medium text-gray-700">Client Support</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section - Enhanced Table -->
<section id="services" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 animate-on-scroll">
            <span class="inline-flex items-center px-4 py-1.5 bg-secondary-100 text-secondary-700 text-sm font-semibold rounded-full mb-4">
                <i class="fas fa-cogs mr-2"></i>What We Offer
            </span>
            <h2 class="text-3xl md:text-4xl font-bold text-dark mb-4">Our Services & Features</h2>
            <p class="text-gray-600 max-w-2xl mx-auto text-lg">Everything you need to establish and grow your online presence</p>
        </div>
        
        <div class="card overflow-hidden animate-on-scroll">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="gradient-hero text-white">
                            <th class="px-8 py-5 text-left font-semibold">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-cube"></i>
                                    <span>Service Feature</span>
                                </div>
                            </th>
                            <th class="px-8 py-5 text-left font-semibold">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-gift"></i>
                                    <span>Benefit to You</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr class="hover:bg-primary-50 transition-colors duration-200">
                            <td class="px-8 py-5">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-laptop-code text-primary-600 text-xl"></i>
                                    </div>
                                    <span class="font-semibold text-dark">Custom Web Development</span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-gray-600">
                                <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 text-sm font-medium rounded-full mr-2">
                                    <i class="fas fa-bolt mr-1"></i>Performance
                                </span>
                                Fast, secure websites built specifically for your needs
                            </td>
                        </tr>
                        <tr class="hover:bg-primary-50 transition-colors duration-200">
                            <td class="px-8 py-5">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-accent-100 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-paint-brush text-accent-600 text-xl"></i>
                                    </div>
                                    <span class="font-semibold text-dark">Professional UI/UX Design</span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-gray-600">
                                <span class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-700 text-sm font-medium rounded-full mr-2">
                                    <i class="fas fa-star mr-1"></i>Credibility
                                </span>
                                Beautiful designs that build trust with your customers
                            </td>
                        </tr>
                        <tr class="hover:bg-primary-50 transition-colors duration-200">
                            <td class="px-8 py-5">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-secondary-100 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-boxes-stacked text-secondary-600 text-xl"></i>
                                    </div>
                                    <span class="font-semibold text-dark">Template-Based System</span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-gray-600">
                                <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 text-sm font-medium rounded-full mr-2">
                                    <i class="fas fa-clock mr-1"></i>Quick Setup
                                </span>
                                Choose from pre-built templates and customize instantly
                            </td>
                        </tr>
                        <tr class="hover:bg-primary-50 transition-colors duration-200">
                            <td class="px-8 py-5">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-bullhorn text-orange-600 text-xl"></i>
                                    </div>
                                    <span class="font-semibold text-dark">Content & Communication Strategy</span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-gray-600">
                                <span class="inline-flex items-center px-3 py-1 bg-orange-100 text-orange-700 text-sm font-medium rounded-full mr-2">
                                    <i class="fas fa-users mr-1"></i>Engagement
                                </span>
                                Connect effectively with your target audience
                            </td>
                        </tr>
                        <tr class="hover:bg-primary-50 transition-colors duration-200">
                            <td class="px-8 py-5">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-handshake text-green-600 text-xl"></i>
                                    </div>
                                    <span class="font-semibold text-dark">Simplified Client Onboarding</span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-gray-600">
                                <span class="inline-flex items-center px-3 py-1 bg-teal-100 text-teal-700 text-sm font-medium rounded-full mr-2">
                                    <i class="fas fa-smile mr-1"></i>Easy Start
                                </span>
                                Get started quickly with our guided setup process
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Team Workflow Section -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 animate-on-scroll">
            <span class="inline-flex items-center px-4 py-1.5 bg-accent-100 text-accent-700 text-sm font-semibold rounded-full mb-4">
                <i class="fas fa-users mr-2"></i>How We Work
            </span>
            <h2 class="text-3xl md:text-4xl font-bold text-dark mb-4">Our Team Workflow</h2>
            <p class="text-gray-600 max-w-2xl mx-auto text-lg">Collaborative expertise delivering exceptional results</p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <!-- Technical Team -->
            <div class="card p-8 text-center card-hover animate-on-scroll">
                <div class="w-24 h-24 mx-auto gradient-bg rounded-3xl flex items-center justify-center mb-6 shadow-lg">
                    <i class="fas fa-code text-white text-4xl"></i>
                </div>
                <h3 class="text-xl font-bold text-dark mb-3">Technical Team</h3>
                <p class="text-gray-600 mb-6">Backend development, database design, system architecture, and performance optimization.</p>
                <div class="flex flex-wrap justify-center gap-2">
                    <span class="px-4 py-2 bg-primary-100 text-primary-700 rounded-full text-sm font-medium">PHP</span>
                    <span class="px-4 py-2 bg-primary-100 text-primary-700 rounded-full text-sm font-medium">MySQL</span>
                    <span class="px-4 py-2 bg-primary-100 text-primary-700 rounded-full text-sm font-medium">JavaScript</span>
                </div>
            </div>
            
            <!-- Creative Team -->
            <div class="card p-8 text-center card-hover animate-on-scroll" style="animation-delay: 0.1s;">
                <div class="w-24 h-24 mx-auto gradient-creative rounded-3xl flex items-center justify-center mb-6 shadow-lg">
                    <i class="fas fa-palette text-white text-4xl"></i>
                </div>
                <h3 class="text-xl font-bold text-dark mb-3">Creative Team</h3>
                <p class="text-gray-600 mb-6">UI/UX design, branding, visual identity, and content creation for compelling user experiences.</p>
                <div class="flex flex-wrap justify-center gap-2">
                    <span class="px-4 py-2 bg-accent-100 text-accent-700 rounded-full text-sm font-medium">UI Design</span>
                    <span class="px-4 py-2 bg-accent-100 text-accent-700 rounded-full text-sm font-medium">Branding</span>
                    <span class="px-4 py-2 bg-accent-100 text-accent-700 rounded-full text-sm font-medium">Content</span>
                </div>
            </div>
            
            <!-- Operations Team -->
            <div class="card p-8 text-center card-hover animate-on-scroll" style="animation-delay: 0.2s;">
                <div class="w-24 h-24 mx-auto gradient-teal rounded-3xl flex items-center justify-center mb-6 shadow-lg">
                    <i class="fas fa-users-cog text-white text-4xl"></i>
                </div>
                <h3 class="text-xl font-bold text-dark mb-3">Operations Team</h3>
                <p class="text-gray-600 mb-6">Client communication, project management, onboarding, and ongoing support services.</p>
                <div class="flex flex-wrap justify-center gap-2">
                    <span class="px-4 py-2 bg-secondary-100 text-secondary-700 rounded-full text-sm font-medium">Support</span>
                    <span class="px-4 py-2 bg-secondary-100 text-secondary-700 rounded-full text-sm font-medium">Management</span>
                    <span class="px-4 py-2 bg-secondary-100 text-secondary-700 rounded-full text-sm font-medium">Training</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <!-- About Content -->
            <div class="animate-on-scroll">
                <span class="inline-flex items-center px-4 py-1.5 bg-primary-100 text-primary-700 text-sm font-semibold rounded-full mb-4">
                    <i class="fas fa-info-circle mr-2"></i>About Us
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-dark mb-6">About FilDevStudio</h2>
                
                <p class="text-gray-600 mb-4 leading-relaxed">
                    <strong class="text-dark">FilDevStudio: Code & Creative Solutions</strong> is a service-oriented ICT company 
                    specializing in integrated web and brand identity packages for local businesses.
                </p>
                <p class="text-gray-600 mb-4 leading-relaxed">
                    We believe every business deserves a professional online presence, regardless of technical 
                    knowledge or budget constraints. Our mission is to bridge the gap between technology and 
                    creativity, making digital solutions accessible to SMEs and freelancers.
                </p>
                <p class="text-gray-600 mb-8 leading-relaxed">
                    With our template-based website builder system, clients can easily choose, customize, and 
                    manage their websites without writing a single line of code.
                </p>
                
                <a href="auth/register.php" class="inline-flex items-center px-8 py-4 gradient-bg text-white font-bold rounded-xl hover:opacity-90 transition-all duration-300 shadow-lg hover:shadow-xl">
                    <i class="fas fa-arrow-right mr-2"></i>Start Your Journey
                </a>
            </div>
            
            <!-- Mission/Vision/Values Cards -->
            <div class="space-y-6 animate-on-scroll" style="animation-delay: 0.1s;">
                <div class="card p-6 flex items-start space-x-4 card-hover">
                    <div class="w-14 h-14 gradient-bg rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                        <i class="fas fa-bullseye text-white text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-dark text-lg mb-1">Our Mission</h4>
                        <p class="text-gray-600">Empower local businesses with affordable, professional digital solutions that drive growth.</p>
                    </div>
                </div>
                
                <div class="card p-6 flex items-start space-x-4 card-hover">
                    <div class="w-14 h-14 gradient-creative rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                        <i class="fas fa-eye text-white text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-dark text-lg mb-1">Our Vision</h4>
                        <p class="text-gray-600">Be the go-to partner for SMEs seeking quality web and branding services in the Philippines.</p>
                    </div>
                </div>
                
                <div class="card p-6 flex items-start space-x-4 card-hover">
                    <div class="w-14 h-14 gradient-teal rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                        <i class="fas fa-heart text-white text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-dark text-lg mb-1">Our Values</h4>
                        <p class="text-gray-600">Quality, Affordability, Accessibility, and Client Success — at the heart of everything we do.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Organizational Chart Section - Arctic Tech Style -->
<style>
    /* Arctic Tech Animations */
    @keyframes fade-in-up {
        0% { opacity: 0; transform: translateY(30px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    @keyframes float-gentle {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }
    @keyframes pulse-subtle {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
    .arctic-fade-in { animation: fade-in-up 0.8s ease-out forwards; opacity: 0; }
    .arctic-float { animation: float-gentle 6s ease-in-out infinite; }
    .arctic-pulse { animation: pulse-subtle 3s ease-in-out infinite; }
    .arctic-delay-1 { animation-delay: 0.1s; }
    .arctic-delay-2 { animation-delay: 0.2s; }
    .arctic-delay-3 { animation-delay: 0.3s; }
    .arctic-delay-4 { animation-delay: 0.4s; }
    .arctic-delay-5 { animation-delay: 0.5s; }
    .arctic-delay-6 { animation-delay: 0.6s; }
    .arctic-delay-7 { animation-delay: 0.7s; }
    .arctic-delay-8 { animation-delay: 0.8s; }
</style>

<section id="team" class="py-24 relative overflow-hidden bg-slate-50">
    <!-- Subtle Background Pattern -->
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute inset-0 opacity-[0.02]" style="background-image: radial-gradient(circle at 1px 1px, #64748b 1px, transparent 0); background-size: 32px 32px;"></div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <!-- Section Header -->
        <div class="text-center mb-20 arctic-fade-in">
            <p class="text-sm font-medium tracking-widest text-slate-400 uppercase mb-3">Our Team</p>
            <h2 class="text-4xl md:text-5xl font-semibold text-slate-900 tracking-tight">
                Meet the People Behind FilDevStudio
            </h2>
        </div>

        <!-- Org Chart Container with SVG Connectors -->
        <div class="relative">
            
            <!-- SVG Connector Lines (Desktop) -->
            <svg class="hidden lg:block absolute inset-0 w-full h-full pointer-events-none" style="z-index: 0;" preserveAspectRatio="none">
                <!-- CEO to Chief of Staff -->
                <path d="M 50% 120 C 50% 160, 50% 160, 50% 200" 
                      stroke="#cbd5e1" stroke-width="2" fill="none" stroke-linecap="round"
                      style="stroke-dasharray: 8 4;" class="arctic-pulse"/>
                <!-- Chief of Staff to horizontal spread -->
                <path d="M 400 320 C 400 380, 400 380, 400 400 C 400 420, 120 420, 120 460" 
                      stroke="#cbd5e1" stroke-width="2" fill="none" stroke-linecap="round"
                      style="stroke-dasharray: 8 4;" class="arctic-pulse"/>
                <path d="M 400 320 C 400 380, 400 380, 400 460" 
                      stroke="#cbd5e1" stroke-width="2" fill="none" stroke-linecap="round"
                      style="stroke-dasharray: 8 4;" class="arctic-pulse"/>
                <path d="M 400 320 C 400 380, 400 380, 400 400 C 400 420, 680 420, 680 460" 
                      stroke="#cbd5e1" stroke-width="2" fill="none" stroke-linecap="round"
                      style="stroke-dasharray: 8 4;" class="arctic-pulse"/>
            </svg>
            
            <!-- ==================== LEVEL 1: CEO ==================== -->
            <div class="flex justify-center mb-16 arctic-fade-in arctic-delay-1">
                <div class="arctic-float">
                    <div class="bg-white backdrop-blur-sm p-8 rounded-2xl shadow-sm text-center w-72 border border-slate-200/80 hover:shadow-md hover:border-slate-300 transition-all duration-300">
                        <!-- Avatar -->
                        <div class="relative w-24 h-24 mx-auto mb-5">
                            <div class="absolute -inset-1 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full opacity-20 blur-sm"></div>
                            <div class="relative w-24 h-24 rounded-full overflow-hidden ring-2 ring-blue-500/30 ring-offset-2 ring-offset-white">
                                <img src="assets/images/Habib Jaudian.jpg" alt="Habib D. Jaudian" class="w-full h-full object-cover">
                            </div>
                        </div>
                        
                        <h3 class="text-xl font-semibold text-slate-900 mb-1">Habib D. Jaudian</h3>
                        <span class="inline-flex items-center px-3 py-1 bg-blue-50 text-blue-600 text-sm font-medium rounded-full">
                            Chief Executive Officer
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Visual Connector (Mobile-friendly) -->
            <div class="flex justify-center mb-8">
                <div class="w-px h-12 bg-gradient-to-b from-slate-300 to-slate-200"></div>
            </div>

            <!-- ==================== LEVEL 2: Chief of Staff ==================== -->
            <div class="flex justify-center mb-16 arctic-fade-in arctic-delay-2">
                <div class="bg-white backdrop-blur-sm p-6 rounded-xl shadow-sm text-center w-64 border border-slate-200/80 hover:shadow-md hover:border-slate-300 transition-all duration-300">
                    <!-- Avatar -->
                    <div class="relative w-20 h-20 mx-auto mb-4">
                        <div class="relative w-20 h-20 rounded-full overflow-hidden ring-2 ring-slate-200 ring-offset-2 ring-offset-white">
                            <img src="assets/images/Deniel Cruz.jpg" alt="Deniel Cruz" class="w-full h-full object-cover">
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-1">Deniel Cruz</h3>
                    <span class="inline-flex items-center px-3 py-1 bg-slate-100 text-slate-600 text-sm font-medium rounded-full">
                        Chief of Staff
                    </span>
                </div>
            </div>

            <!-- Visual Connector with Branches -->
            <div class="hidden lg:flex justify-center items-center mb-8">
                <div class="flex items-center" style="width: 70%;">
                    <div class="flex-1 h-px bg-gradient-to-r from-transparent via-slate-300 to-slate-300"></div>
                    <div class="w-2 h-2 bg-slate-300 rounded-full"></div>
                    <div class="flex-1 h-px bg-slate-300"></div>
                    <div class="w-2 h-2 bg-slate-300 rounded-full"></div>
                    <div class="flex-1 h-px bg-gradient-to-l from-transparent via-slate-300 to-slate-300"></div>
                </div>
            </div>

            <!-- ==================== LEVEL 3: Departments ==================== -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-6">
                
                <!-- ===== Operations Column ===== -->
                <div class="flex flex-col items-center arctic-fade-in arctic-delay-3">
                    <!-- Department Label -->
                    <div class="mb-4">
                        <span class="text-xs font-semibold tracking-widest text-slate-400 uppercase">Operations</span>
                    </div>
                    <!-- Member Card -->
                    <div class="group bg-white backdrop-blur-sm rounded-xl p-5 w-full max-w-xs shadow-sm border border-slate-200/80 hover:shadow-md hover:border-slate-300 transition-all duration-300">
                        <div class="flex items-center gap-4">
                            <div class="relative w-14 h-14 rounded-full overflow-hidden ring-2 ring-slate-100 flex-shrink-0">
                                <img src="assets/images/George Baltar.jpg" alt="George Baltar" class="w-full h-full object-cover">
                            </div>
                            <div class="min-w-0">
                                <h4 class="font-semibold text-slate-900 truncate">George Baltar</h4>
                                <span class="text-sm text-slate-500">Client Manager</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== Development Column ===== -->
                <div class="flex flex-col items-center arctic-fade-in arctic-delay-4">
                    <!-- Department Label -->
                    <div class="mb-4">
                        <span class="text-xs font-semibold tracking-widest text-slate-400 uppercase">Development</span>
                    </div>
                    <div class="space-y-3 w-full max-w-xs">
                        <!-- Dev 1 -->
                        <div class="group bg-white backdrop-blur-sm rounded-xl p-4 shadow-sm border border-slate-200/80 hover:shadow-md hover:border-slate-300 transition-all duration-300 arctic-fade-in arctic-delay-5">
                            <div class="flex items-center gap-3">
                                <div class="relative w-12 h-12 rounded-full overflow-hidden ring-2 ring-slate-100 flex-shrink-0">
                                    <img src="assets/images/Jermaine Pereja.jpg" alt="Jermaine Pereja" class="w-full h-full object-cover">
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-semibold text-slate-900 truncate text-sm">Jermaine Pereja</h4>
                                    <span class="text-xs text-slate-500">Web Developer</span>
                                </div>
                            </div>
                        </div>
                        <!-- Dev 2 -->
                        <div class="group bg-white backdrop-blur-sm rounded-xl p-4 shadow-sm border border-slate-200/80 hover:shadow-md hover:border-slate-300 transition-all duration-300 arctic-fade-in arctic-delay-6">
                            <div class="flex items-center gap-3">
                                <div class="relative w-12 h-12 rounded-full overflow-hidden ring-2 ring-slate-100 flex-shrink-0">
                                    <img src="assets/images/Kenn Doliguez.jpg" alt="Kenn Jianrie Doliguez" class="w-full h-full object-cover">
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-semibold text-slate-900 truncate text-sm">Kenn Doliguez</h4>
                                    <span class="text-xs text-slate-500">Web Developer</span>
                                </div>
                            </div>
                        </div>
                        <!-- Dev 3 -->
                        <div class="group bg-white backdrop-blur-sm rounded-xl p-4 shadow-sm border border-slate-200/80 hover:shadow-md hover:border-slate-300 transition-all duration-300 arctic-fade-in arctic-delay-7">
                            <div class="flex items-center gap-3">
                                <div class="relative w-12 h-12 rounded-full overflow-hidden ring-2 ring-slate-100 flex-shrink-0">
                                    <img src="assets/images/Prince Sebuc.jpg" alt="Prince Charles Sebuc" class="w-full h-full object-cover">
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-semibold text-slate-900 truncate text-sm">Prince Sebuc</h4>
                                    <span class="text-xs text-slate-500">Web Developer</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== Design Column ===== -->
                <div class="flex flex-col items-center md:col-span-2 lg:col-span-1 arctic-fade-in arctic-delay-4">
                    <!-- Department Label -->
                    <div class="mb-4">
                        <span class="text-xs font-semibold tracking-widest text-slate-400 uppercase">Design</span>
                    </div>
                    <div class="space-y-3 w-full max-w-xs">
                        <!-- Designer 1 -->
                        <div class="group bg-white backdrop-blur-sm rounded-xl p-4 shadow-sm border border-slate-200/80 hover:shadow-md hover:border-slate-300 transition-all duration-300 arctic-fade-in arctic-delay-5">
                            <div class="flex items-center gap-3">
                                <div class="relative w-12 h-12 rounded-full overflow-hidden ring-2 ring-slate-100 flex-shrink-0">
                                    <img src="assets/images/Althea Maglangit.jpg" alt="Althea Maglangit" class="w-full h-full object-cover">
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-semibold text-slate-900 truncate text-sm">Althea Maglangit</h4>
                                    <span class="text-xs text-slate-500">UI/UX Designer</span>
                                </div>
                            </div>
                        </div>
                        <!-- Designer 2 -->
                        <div class="group bg-white backdrop-blur-sm rounded-xl p-4 shadow-sm border border-slate-200/80 hover:shadow-md hover:border-slate-300 transition-all duration-300 arctic-fade-in arctic-delay-6">
                            <div class="flex items-center gap-3">
                                <div class="relative w-12 h-12 rounded-full overflow-hidden ring-2 ring-slate-100 flex-shrink-0">
                                    <img src="assets/images/Cassey Balacuit.jpg" alt="Cassey Jelly Balacuit" class="w-full h-full object-cover">
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-semibold text-slate-900 truncate text-sm">Cassey Balacuit</h4>
                                    <span class="text-xs text-slate-500">UI/UX Designer</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- End Departments -->

        </div>
        <!-- End Org Chart -->
    </div>
</section>

<!-- CTA Section - Enhanced with gradient and better layout -->
<section class="relative py-24 overflow-hidden">
    <!-- Background -->
    <div class="absolute inset-0 gradient-hero"></div>
    <div class="absolute inset-0">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-primary-400/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-accent-500/20 rounded-full blur-3xl"></div>
    </div>
    
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="animate-on-scroll">
            <span class="inline-flex items-center px-4 py-1.5 bg-white/20 backdrop-blur text-white text-sm font-semibold rounded-full mb-6">
                <i class="fas fa-rocket mr-2"></i>Ready to Start?
            </span>
            <h2 class="text-3xl md:text-5xl font-bold text-white mb-6">Ready to Build Your Online Presence?</h2>
            <p class="text-xl text-primary-100 mb-10 max-w-2xl mx-auto">Get started today with our easy-to-use template builder. No coding required — just pick, customize, and launch!</p>
            
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="templates.php" class="inline-flex items-center justify-center px-8 py-4 bg-white text-primary-600 font-bold rounded-xl hover:bg-gray-100 transition-all duration-300 shadow-xl hover:shadow-2xl hover:-translate-y-0.5">
                    <i class="fas fa-palette mr-2"></i>Browse Templates
                </a>
                <a href="auth/register.php" class="inline-flex items-center justify-center px-8 py-4 bg-transparent border-2 border-white text-white font-bold rounded-xl hover:bg-white hover:text-primary-600 transition-all duration-300">
                    <i class="fas fa-user-plus mr-2"></i>Create Free Account
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
