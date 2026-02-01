<?php
/**
 * Template Demo Page - FilDevStudio
 * Renders a template with demo data for preview purposes
 * This uses the same modular templates as actual client sites
 */

$templateId = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// Demo site data for preview (simulates a real client site)
$demoData = [
    1 => [ // Sari-Sari Store
        'site_name' => 'Aling Maria Sari-Sari Store',
        'primary_color' => '#EF4444',
        'secondary_color' => '#F97316',
        'accent_color' => '#FCD34D',
        'hero_title' => 'Tindahan ng Bayan',
        'hero_subtitle' => 'Mura, Matibay, at Maaasahan! Bukas araw-araw para sa inyo.',
        'about_text' => 'Matagal na kaming nagbibigay ng de-kalidad na mga produkto sa ating komunidad. Mula sa mga pang-araw-araw na pangangailangan hanggang sa mga espesyal na okasyon - nasa amin lahat ng kailangan ninyo.',
        'business_address' => '123 Kalye San Jose, Brgy. Maligaya, Manila',
        'business_phone' => '+63 917 123 4567',
        'business_email' => 'alingmaria@email.com',
        'business_hours' => 'Lunes-Sabado: 6AM-9PM, Linggo: 7AM-8PM',
        'font_heading' => 'Inter',
        'font_body' => 'Inter',
    ],
    2 => [ // Carinderia/Food
        'site_name' => 'Kusina ni Nanay',
        'primary_color' => '#78350F',
        'secondary_color' => '#FFFBEB',
        'accent_color' => '#D97706',
        'hero_title' => 'Lutong Bahay na May Puso',
        'hero_subtitle' => 'Authentic Filipino home cooking served with love since 1995',
        'about_text' => 'Started by Nanay Rosa in her small kitchen, our carinderia has grown to become the neighborhood favorite. We serve authentic Filipino dishes made from secret family recipes passed down through generations.',
        'business_address' => '456 Rizal Avenue, Quezon City',
        'business_phone' => '+63 918 234 5678',
        'business_email' => 'kusinaninanay@email.com',
        'business_hours' => 'Daily: 6AM-8PM',
        'font_heading' => 'Playfair Display',
        'font_body' => 'Inter',
    ],
    3 => [ // Local Services
        'site_name' => 'ProFix Solutions',
        'primary_color' => '#0D9488',
        'secondary_color' => '#F0FDFA',
        'accent_color' => '#14B8A6',
        'hero_title' => 'Professional Services You Can Trust',
        'hero_subtitle' => 'Quality workmanship for your home and business needs',
        'about_text' => 'ProFix Solutions provides reliable repair, maintenance, and installation services. With over 15 years of experience and a team of certified professionals, we deliver quality results on time, every time.',
        'business_address' => '789 Service Road, Makati City',
        'business_phone' => '+63 919 345 6789',
        'business_email' => 'info@profixsolutions.com',
        'business_hours' => 'Mon-Sat: 8AM-6PM',
        'font_heading' => 'Inter',
        'font_body' => 'Inter',
    ],
    4 => [ // Small Retail
        'site_name' => 'Urban Essentials',
        'primary_color' => '#1F2937',
        'secondary_color' => '#F9FAFB',
        'accent_color' => '#3B82F6',
        'hero_title' => 'Curated Collection',
        'hero_subtitle' => 'Discover premium products handpicked for the modern lifestyle',
        'about_text' => 'Urban Essentials brings you carefully selected products that combine quality, style, and functionality. From everyday basics to unique finds, our collection is curated for those who appreciate the finer things.',
        'business_address' => '321 Commerce Street, BGC, Taguig',
        'business_phone' => '+63 920 456 7890',
        'business_email' => 'shop@urbanessentials.ph',
        'business_hours' => 'Mon-Sun: 10AM-9PM',
        'font_heading' => 'Playfair Display',
        'font_body' => 'Inter',
    ],
    5 => [ // Freelancer Portfolio
        'site_name' => 'Juan Designer',
        'primary_color' => '#0F172A',
        'secondary_color' => '#8B5CF6',
        'accent_color' => '#EC4899',
        'hero_title' => 'Creative Digital Solutions',
        'hero_subtitle' => 'UI/UX Designer & Full-Stack Developer based in Manila',
        'about_text' => 'I help startups and businesses create beautiful, functional digital experiences. With 8+ years of experience in design and development, I turn ideas into reality through clean code and stunning visuals.',
        'business_address' => 'Remote / Manila, Philippines',
        'business_phone' => '+63 921 567 8901',
        'business_email' => 'hello@juandesigner.com',
        'business_hours' => 'Available Mon-Fri',
        'font_heading' => 'Space Grotesk',
        'font_body' => 'Inter',
    ],
];

// Get demo data for this template
$site = $demoData[$templateId] ?? $demoData[1];
$site['template_id'] = $templateId;
$logoUrl = null;

// Check if template file exists
$templateFile = __DIR__ . '/templates/template-' . $templateId . '.php';
if (!file_exists($templateFile)) {
    // Fallback to template 1 if requested doesn't exist
    $templateFile = __DIR__ . '/templates/template-1.php';
    $site = $demoData[1];
    $site['template_id'] = 1;
}

// Include the actual template
include $templateFile;
