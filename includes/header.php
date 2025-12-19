<?php
/**
 * Header Include - FilDevStudio Web Services Platform
 * Enhanced UI/UX Design with Modern Styling
 * Theme: Code + Creativity | Colors: Indigo/Navy (tech) + Teal/Purple (creative)
 */

require_once __DIR__ . '/../config/database.php';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$userName = $_SESSION['user_name'] ?? '';

// Determine base path for assets (handles subdirectory includes)
$basePath = '';
if (strpos($_SERVER['PHP_SELF'], '/admin/') !== false || 
    strpos($_SERVER['PHP_SELF'], '/client/') !== false || 
    strpos($_SERVER['PHP_SELF'], '/auth/') !== false) {
    $basePath = '../';
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="FilDevStudio - Integrated Web & Brand Identity Packages for Local Businesses">
    <title><?php echo $pageTitle ?? 'FilDevStudio Web Services'; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo $basePath; ?>assets/images/logo.svg">
    
    <!-- Google Fonts - Inter for modern typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Tailwind Config - Enhanced Color Palette -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        // Primary: Dark Blue/Indigo (Technology)
                        primary: {
                            50: '#EEF2FF',
                            100: '#E0E7FF',
                            200: '#C7D2FE',
                            300: '#A5B4FC',
                            400: '#818CF8',
                            500: '#4F46E5',  // Main primary
                            600: '#4338CA',
                            700: '#3730A3',
                            800: '#312E81',
                            900: '#1E1B4B',
                        },
                        // Secondary: Teal (Creativity accent)
                        secondary: {
                            50: '#F0FDFA',
                            100: '#CCFBF1',
                            200: '#99F6E4',
                            300: '#5EEAD4',
                            400: '#2DD4BF',
                            500: '#14B8A6',  // Main secondary
                            600: '#0D9488',
                            700: '#0F766E',
                            800: '#115E59',
                            900: '#134E4A',
                        },
                        // Accent: Purple (Creative flair)
                        accent: {
                            50: '#FAF5FF',
                            100: '#F3E8FF',
                            200: '#E9D5FF',
                            300: '#D8B4FE',
                            400: '#C084FC',
                            500: '#A855F7',  // Main accent
                            600: '#9333EA',
                            700: '#7C3AED',
                            800: '#6B21A8',
                            900: '#581C87',
                        },
                        // Neutrals
                        dark: '#0F172A',
                        light: '#F8FAFC',
                    },
                    boxShadow: {
                        'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04)',
                        'hover': '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
                        'card': '0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03)',
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-out',
                        'slide-up': 'slideUp 0.5s ease-out',
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                    },
                }
            }
        }
    </script>
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Styles - Reusable UI Components -->
    <style>
        /* === GRADIENTS === */
        .gradient-bg {
            background: linear-gradient(135deg, #4F46E5 0%, #3730A3 100%);
        }
        .gradient-creative {
            background: linear-gradient(135deg, #A855F7 0%, #7C3AED 100%);
        }
        .gradient-teal {
            background: linear-gradient(135deg, #14B8A6 0%, #0D9488 100%);
        }
        .gradient-hero {
            background: linear-gradient(135deg, #1E1B4B 0%, #312E81 50%, #4F46E5 100%);
        }
        .gradient-mesh {
            background: 
                radial-gradient(at 40% 20%, rgba(79, 70, 229, 0.15) 0px, transparent 50%),
                radial-gradient(at 80% 0%, rgba(168, 85, 247, 0.1) 0px, transparent 50%),
                radial-gradient(at 0% 50%, rgba(20, 184, 166, 0.1) 0px, transparent 50%);
        }
        
        /* === CARD HOVER EFFECTS === */
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        /* === BUTTONS === */
        .btn-primary {
            @apply inline-flex items-center justify-center px-6 py-3 bg-primary-500 text-white font-semibold rounded-xl
                   hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2
                   transition-all duration-200 shadow-soft hover:shadow-hover;
        }
        .btn-secondary {
            @apply inline-flex items-center justify-center px-6 py-3 bg-white text-primary-600 font-semibold rounded-xl
                   border-2 border-primary-500 hover:bg-primary-50 focus:outline-none focus:ring-2 
                   focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200;
        }
        .btn-ghost {
            @apply inline-flex items-center justify-center px-6 py-3 text-gray-600 font-medium rounded-xl
                   hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-300 
                   transition-all duration-200;
        }
        .btn-danger {
            @apply inline-flex items-center justify-center px-6 py-3 bg-red-500 text-white font-semibold rounded-xl
                   hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2
                   transition-all duration-200;
        }
        
        /* === STATUS BADGES === */
        .badge {
            @apply inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold;
        }
        .badge-pending { @apply bg-yellow-100 text-yellow-800; }
        .badge-progress { @apply bg-blue-100 text-blue-800; }
        .badge-completed { @apply bg-green-100 text-green-800; }
        .badge-rejected { @apply bg-red-100 text-red-800; }
        .badge-draft { @apply bg-gray-100 text-gray-800; }
        .badge-active { @apply bg-emerald-100 text-emerald-800; }
        
        /* === FORM INPUTS === */
        .form-input {
            @apply w-full px-4 py-3 border border-gray-200 rounded-xl bg-white
                   focus:ring-2 focus:ring-primary-500 focus:border-transparent
                   transition-all duration-200 placeholder-gray-400;
        }
        .form-input-icon {
            @apply pl-11;
        }
        .form-label {
            @apply block text-sm font-medium text-gray-700 mb-2;
        }
        
        /* === CARDS === */
        .card {
            @apply bg-white rounded-2xl shadow-card border border-gray-100;
        }
        
        /* === NAVIGATION ACTIVE STATE === */
        .nav-link {
            @apply relative text-gray-600 hover:text-primary-600 font-medium transition-colors duration-200;
        }
        .nav-link::after {
            content: '';
            @apply absolute bottom-0 left-0 w-0 h-0.5 bg-primary-500 transition-all duration-200;
        }
        .nav-link:hover::after {
            @apply w-full;
        }
        .nav-link.active {
            @apply text-primary-600;
        }
        .nav-link.active::after {
            @apply w-full;
        }
        
        /* === SMOOTH SCROLLBAR === */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* === GLASS EFFECT === */
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        
        /* === ANIMATIONS === */
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease-out;
        }
        .animate-on-scroll.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col font-sans antialiased">
    <!-- Navigation - Enhanced with glass effect and better spacing -->
    <nav class="bg-white/95 backdrop-blur-md shadow-soft sticky top-0 z-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-18 py-4">
                <!-- Logo with SVG -->
                <div class="flex items-center">
                    <a href="<?php echo $basePath; ?>index.php" class="flex items-center space-x-3 group">
                        <img src="<?php echo $basePath; ?>assets/images/logo.png" alt="FilDevStudio Logo" 
                             class="w-11 h-11 rounded-xl shadow-soft group-hover:shadow-hover transition-shadow duration-300">
                        <div class="hidden sm:block">
                            <span class="text-xl font-bold text-dark">FilDev<span class="text-primary-500">Studio</span></span>
                            <span class="block text-xs text-gray-500 -mt-1">Code & Creative Solutions</span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-1">
                    <a href="<?php echo $basePath; ?>index.php" class="nav-link px-4 py-2 rounded-lg">
                        <i class="fas fa-home mr-2 text-sm"></i>Home
                    </a>
                    <a href="<?php echo $basePath; ?>templates.php" class="nav-link px-4 py-2 rounded-lg">
                        <i class="fas fa-palette mr-2 text-sm"></i>Templates
                    </a>
                    
                    <?php if ($isLoggedIn): ?>
                        <?php if ($isAdmin): ?>
                            <a href="<?php echo $basePath; ?>admin/dashboard.php" class="nav-link px-4 py-2 rounded-lg">
                                <i class="fas fa-shield-alt mr-2 text-sm"></i>Admin Panel
                            </a>
                        <?php else: ?>
                            <a href="<?php echo $basePath; ?>client/dashboard.php" class="nav-link px-4 py-2 rounded-lg">
                                <i class="fas fa-th-large mr-2 text-sm"></i>Dashboard
                            </a>
                        <?php endif; ?>
                        
                        <div class="flex items-center space-x-3 ml-4 pl-4 border-l border-gray-200">
                            <div class="flex items-center space-x-2">
                                <div class="w-9 h-9 gradient-bg rounded-full flex items-center justify-center shadow-soft">
                                    <span class="text-white font-semibold text-sm"><?php echo strtoupper(substr($userName, 0, 1)); ?></span>
                                </div>
                                <span class="text-gray-700 font-medium"><?php echo htmlspecialchars($userName); ?></span>
                            </div>
                            <a href="<?php echo $basePath; ?>auth/logout.php" 
                               class="inline-flex items-center px-4 py-2 bg-red-50 text-red-600 font-medium rounded-lg hover:bg-red-100 transition-colors duration-200">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="flex items-center space-x-3 ml-4 pl-4 border-l border-gray-200">
                            <a href="<?php echo $basePath; ?>auth/login.php" class="nav-link px-4 py-2 rounded-lg">
                                <i class="fas fa-sign-in-alt mr-2"></i>Login
                            </a>
                            <a href="<?php echo $basePath; ?>auth/register.php" 
                               class="inline-flex items-center px-5 py-2.5 gradient-bg text-white font-semibold rounded-xl hover:opacity-90 transition-all duration-200 shadow-soft hover:shadow-hover">
                                <i class="fas fa-rocket mr-2"></i>Get Started
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-btn" class="p-2 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-primary-600 transition-colors duration-200">
                        <i class="fas fa-bars text-xl" id="menu-icon"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation - Enhanced -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100">
            <div class="px-4 py-4 space-y-2">
                <a href="<?php echo $basePath; ?>index.php" class="flex items-center px-4 py-3 text-gray-700 hover:bg-primary-50 hover:text-primary-600 rounded-xl transition-colors duration-200">
                    <i class="fas fa-home mr-3 w-5 text-center"></i>Home
                </a>
                <a href="<?php echo $basePath; ?>templates.php" class="flex items-center px-4 py-3 text-gray-700 hover:bg-primary-50 hover:text-primary-600 rounded-xl transition-colors duration-200">
                    <i class="fas fa-palette mr-3 w-5 text-center"></i>Templates
                </a>
                
                <?php if ($isLoggedIn): ?>
                    <?php if ($isAdmin): ?>
                        <a href="<?php echo $basePath; ?>admin/dashboard.php" class="flex items-center px-4 py-3 text-gray-700 hover:bg-primary-50 hover:text-primary-600 rounded-xl transition-colors duration-200">
                            <i class="fas fa-shield-alt mr-3 w-5 text-center"></i>Admin Panel
                        </a>
                    <?php else: ?>
                        <a href="<?php echo $basePath; ?>client/dashboard.php" class="flex items-center px-4 py-3 text-gray-700 hover:bg-primary-50 hover:text-primary-600 rounded-xl transition-colors duration-200">
                            <i class="fas fa-th-large mr-3 w-5 text-center"></i>Dashboard
                        </a>
                    <?php endif; ?>
                    
                    <div class="pt-2 mt-2 border-t border-gray-100">
                        <div class="flex items-center px-4 py-3">
                            <div class="w-10 h-10 gradient-bg rounded-full flex items-center justify-center mr-3">
                                <span class="text-white font-semibold"><?php echo strtoupper(substr($userName, 0, 1)); ?></span>
                            </div>
                            <span class="text-gray-700 font-medium"><?php echo htmlspecialchars($userName); ?></span>
                        </div>
                        <a href="<?php echo $basePath; ?>auth/logout.php" class="flex items-center px-4 py-3 text-red-600 hover:bg-red-50 rounded-xl transition-colors duration-200">
                            <i class="fas fa-sign-out-alt mr-3 w-5 text-center"></i>Logout
                        </a>
                    </div>
                <?php else: ?>
                    <div class="pt-2 mt-2 border-t border-gray-100 space-y-2">
                        <a href="<?php echo $basePath; ?>auth/login.php" class="flex items-center px-4 py-3 text-gray-700 hover:bg-primary-50 hover:text-primary-600 rounded-xl transition-colors duration-200">
                            <i class="fas fa-sign-in-alt mr-3 w-5 text-center"></i>Login
                        </a>
                        <a href="<?php echo $basePath; ?>auth/register.php" class="flex items-center justify-center px-4 py-3 gradient-bg text-white rounded-xl font-semibold">
                            <i class="fas fa-rocket mr-2"></i>Get Started
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <main class="flex-grow">
