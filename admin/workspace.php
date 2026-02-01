<?php
/**
 * Admin Developer Workspace - Enhanced Version
 * Work on client sites for customization requests
 * 
 * FEATURES:
 * - View request details and client's site
 * - Edit site content (colors, fonts, text)
 * - Upload images (logo, hero, gallery)
 * - Download template code for IDE editing
 * - Upload modified code back to system
 * - Preview changes before notifying client
 * - Notify client when preview is ready
 */

// Handle file download BEFORE any HTML output
require_once '../config/database.php';
require_once '../includes/functions.php';

// Session is started by database.php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// Ensure custom_code columns exist in client_sites (run early for upload handling)
try {
    $pdo = getConnection();
    
    // Check and add columns one by one (compatible with older MySQL)
    $columns = $pdo->query("SHOW COLUMNS FROM client_sites")->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('custom_css', $columns)) {
        $pdo->exec("ALTER TABLE client_sites ADD COLUMN custom_css TEXT");
    }
    if (!in_array('custom_js', $columns)) {
        $pdo->exec("ALTER TABLE client_sites ADD COLUMN custom_js TEXT");
    }
    if (!in_array('custom_html', $columns)) {
        $pdo->exec("ALTER TABLE client_sites ADD COLUMN custom_html TEXT");
    }
    if (!in_array('custom_full_html', $columns)) {
        $pdo->exec("ALTER TABLE client_sites ADD COLUMN custom_full_html LONGTEXT");
    }
} catch (Exception $e) { /* Columns might already exist */ }

$siteId = (int)($_GET['site'] ?? 0);

// Handle code download - must be before ANY output
if (isset($_GET['download']) && $_GET['download'] === 'code' && $siteId > 0) {
    $pdo = getConnection();
    
    // Get site data with all user customizations
    $stmt = $pdo->prepare("
        SELECT cs.*, t.name as template_name, t.id as template_id,
               bp.business_name, bp.contact_phone, bp.contact_email, bp.address
        FROM client_sites cs 
        LEFT JOIN templates t ON cs.template_id = t.id 
        LEFT JOIN business_profiles bp ON cs.user_id = bp.user_id
        WHERE cs.id = ?
    ");
    $stmt->execute([$siteId]);
    $dlSite = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($dlSite) {
        $siteName = $dlSite['site_name'];
        $templateId = $dlSite['template_id'] ?? 1;
        $primaryColor = $dlSite['primary_color'] ?? '#3B82F6';
        $secondaryColor = $dlSite['secondary_color'] ?? '#1E40AF';
        $accentColor = $dlSite['accent_color'] ?? '#F59E0B';
        $fontHeading = $dlSite['font_heading'] ?? 'Inter';
        $fontBody = $dlSite['font_body'] ?? 'Inter';
        $heroTitle = $dlSite['hero_title'] ?? 'Welcome';
        $heroSubtitle = $dlSite['hero_subtitle'] ?? '';
        $aboutContent = $dlSite['about_content'] ?? '';
        $servicesContent = $dlSite['services_content'] ?? '';
        $contactInfo = $dlSite['contact_info'] ?? '';
        
        // Get site images
        $stmt = $pdo->prepare("SELECT * FROM site_images WHERE site_id = ? ORDER BY image_type");
        $stmt->execute([$siteId]);
        $siteImages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $logoUrl = '';
        $heroImageUrl = '';
        $galleryImages = [];
        foreach ($siteImages as $img) {
            $imgPath = 'http://localhost/fildevstudio/' . $img['image_path'];
            if ($img['image_type'] === 'logo') $logoUrl = $imgPath;
            elseif ($img['image_type'] === 'hero') $heroImageUrl = $imgPath;
            elseif ($img['image_type'] === 'gallery') $galleryImages[] = $imgPath;
        }
        
        // Prepare site configuration JSON
        $siteConfig = [
            'site_id' => $siteId,
            'site_name' => $siteName,
            'subdomain' => $dlSite['subdomain'],
            'template' => $dlSite['template_name'],
            'template_id' => $templateId,
            'colors' => [
                'primary' => $primaryColor,
                'secondary' => $secondaryColor,
                'accent' => $accentColor
            ],
            'fonts' => [
                'heading' => $fontHeading,
                'body' => $fontBody
            ],
            'content' => [
                'hero_title' => $heroTitle,
                'hero_subtitle' => $heroSubtitle,
                'about' => $aboutContent,
                'services' => $servicesContent,
                'contact' => $contactInfo
            ],
            'images' => [
                'logo' => $logoUrl,
                'hero' => $heroImageUrl,
                'gallery' => $galleryImages
            ],
            'exported_at' => date('Y-m-d H:i:s')
        ];
        
        // ============================================
        // RENDER THE TEMPLATE WITH USER'S DATA
        // ============================================
        
        // Set up variables that the template expects
        $site = $dlSite;
        $logoImage = $logoUrl ? ['image_path' => str_replace('http://localhost/fildevstudio/', '', $logoUrl)] : null;
        $heroImage = $heroImageUrl ? ['image_path' => str_replace('http://localhost/fildevstudio/', '', $heroImageUrl)] : null;
        
        // Capture the rendered template output
        ob_start();
        $templateFile = __DIR__ . '/../public/templates/template-' . $templateId . '.php';
        if (file_exists($templateFile)) {
            include $templateFile;
        }
        $renderedTemplate = ob_get_clean();
        
        // If template didn't render, create a basic HTML version
        if (empty($renderedTemplate)) {
            $renderedTemplate = generateBasicHtml($dlSite, $logoUrl, $heroImageUrl);
        }
        
        // Get existing custom code
        $customCss = $dlSite['custom_css'] ?? '';
        $customJs = $dlSite['custom_js'] ?? '';
        $customHtml = $dlSite['custom_html'] ?? '';
        
        // Provide starter templates if empty
        if (empty(trim($customCss))) {
            $customCss = "/**
 * Custom CSS for: {$siteName}
 * Template: {$dlSite['template_name']}
 * 
 * COLOR VARIABLES:
 *   --primary: {$primaryColor}
 *   --secondary: {$secondaryColor}
 *   --accent: {$accentColor}
 */

:root {
    --primary: {$primaryColor};
    --secondary: {$secondaryColor};
    --accent: {$accentColor};
}

/* Add your custom styles below */

";
        }
        
        if (empty(trim($customJs))) {
            $customJs = "/**
 * Custom JavaScript for: {$siteName}
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('{$siteName} loaded');
    // Add your custom scripts below
    
});
";
        }
        
        if (empty(trim($customHtml))) {
            $customHtml = "<!-- Custom HTML for: {$siteName} -->
<!-- Add floating buttons, banners, popups here -->

";
        }
        
        // Generate README with user's actual data
        $readme = "# {$siteName} - Customized Site Package

## 🎨 User's Customizations

### Colors
- **Primary:** {$primaryColor}
- **Secondary:** {$secondaryColor}
- **Accent:** {$accentColor}

### Fonts
- **Headings:** {$fontHeading}
- **Body:** {$fontBody}

### Content
- **Hero Title:** {$heroTitle}
- **Hero Subtitle:** {$heroSubtitle}
- **About:** " . substr($aboutContent, 0, 100) . "...
- **Services:** " . substr($servicesContent, 0, 100) . "...
- **Contact:** {$contactInfo}

### Images
- **Logo:** " . ($logoUrl ?: 'Not uploaded') . "
- **Hero Image:** " . ($heroImageUrl ?: 'Not uploaded') . "
- **Gallery:** " . count($galleryImages) . " images

## 📁 Files Included

| File | Description |
|------|-------------|
| **index.html** | Complete rendered site with user's customizations |
| **template-original.php** | Original PHP template (for reference) |
| **config.json** | All site settings and content |
| **custom.css** | Add custom styles here |
| **custom.js** | Add custom scripts here |
| **custom-sections.html** | Add extra HTML sections |

## 🚀 How to Edit

### Quick Edits (Recommended)
1. Open `index.html` in your browser to see the current site
2. Edit `custom.css` to change styles
3. Edit `custom.js` to add interactivity  
4. Edit `custom-sections.html` for extra content
5. ZIP these 3 files + config.json and upload back

### Advanced Edits
1. Edit `index.html` directly for structural changes
2. Update `config.json` if you change colors/content
3. ZIP all files and upload back

## 📤 Upload Instructions

Create a ZIP with your modified files and upload to Developer Workspace.
The system will read:
- `custom.css` → Saved to database, injected into site
- `custom.js` → Saved to database, runs on page load
- `custom-sections.html` → Saved to database, added to page
- `config.json` → Updates colors if changed

---
**Template:** {$dlSite['template_name']}
**Site ID:** {$siteId}
**Exported:** " . date('Y-m-d H:i:s') . "
";
        
        // Get the original template for reference
        $originalTemplate = '';
        if (file_exists($templateFile)) {
            $originalTemplate = file_get_contents($templateFile);
        }
        
        // Create ZIP
        if (class_exists('ZipArchive')) {
            $zipName = 'site_' . $siteId . '_' . preg_replace('/[^a-z0-9]/i', '_', $siteName) . '_' . date('Ymd_His') . '.zip';
            $zipPath = sys_get_temp_dir() . '/' . $zipName;
            
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                // Add the RENDERED HTML with user's customizations
                $zip->addFromString('index.html', $renderedTemplate);
                
                // Add original template for reference
                if (!empty($originalTemplate)) {
                    $zip->addFromString('template-original.php', $originalTemplate);
                }
                
                // Add config and custom files
                $zip->addFromString('config.json', json_encode($siteConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                $zip->addFromString('custom.css', $customCss);
                $zip->addFromString('custom.js', $customJs);
                $zip->addFromString('custom-sections.html', $customHtml);
                $zip->addFromString('README.md', $readme);
                
                $zip->close();
                
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="' . $zipName . '"');
                header('Content-Length: ' . filesize($zipPath));
                header('Cache-Control: no-cache');
                readfile($zipPath);
                unlink($zipPath);
                exit;
            }
        }
        
        // Fallback: Download rendered HTML directly
        header('Content-Type: text/html');
        header('Content-Disposition: attachment; filename="site_' . $siteId . '.html"');
        echo $renderedTemplate;
        exit;
    }
}

// Helper function to generate basic HTML if template doesn't render
function generateBasicHtml($site, $logoUrl, $heroImageUrl) {
    $primaryColor = $site['primary_color'] ?? '#3B82F6';
    $secondaryColor = $site['secondary_color'] ?? '#1E40AF';
    $accentColor = $site['accent_color'] ?? '#F59E0B';
    $fontHeading = $site['font_heading'] ?? 'Inter';
    $fontBody = $site['font_body'] ?? 'Inter';
    
    return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($site['site_name']) . '</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=' . urlencode($fontHeading) . ':wght@400;600;700&family=' . urlencode($fontBody) . ':wght@400;500&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "' . $primaryColor . '",
                        secondary: "' . $secondaryColor . '",
                        accent: "' . $accentColor . '"
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            --primary: ' . $primaryColor . ';
            --secondary: ' . $secondaryColor . ';
            --accent: ' . $accentColor . ';
        }
        body { font-family: "' . $fontBody . '", system-ui, sans-serif; }
        h1, h2, h3, h4 { font-family: "' . $fontHeading . '", system-ui, sans-serif; }
        .hero-gradient { background: linear-gradient(135deg, ' . $primaryColor . ' 0%, ' . $secondaryColor . ' 100%); }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="hero-gradient text-white">
        <nav class="max-w-6xl mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                ' . ($logoUrl ? '<img src="' . $logoUrl . '" alt="Logo" class="h-12">' : '<span class="text-2xl font-bold">' . htmlspecialchars($site['site_name']) . '</span>') . '
                <div class="hidden md:flex gap-6">
                    <a href="#about" class="hover:text-accent">About</a>
                    <a href="#services" class="hover:text-accent">Services</a>
                    <a href="#contact" class="hover:text-accent">Contact</a>
                </div>
            </div>
        </nav>
        
        <!-- Hero -->
        <div class="max-w-6xl mx-auto px-4 py-20 text-center">
            <h1 class="text-5xl font-bold mb-6">' . htmlspecialchars($site['hero_title'] ?? 'Welcome') . '</h1>
            <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">' . htmlspecialchars($site['hero_subtitle'] ?? '') . '</p>
            <a href="#contact" class="px-8 py-3 bg-white text-primary rounded-full font-semibold hover:bg-gray-100 transition">
                Contact Us
            </a>
        </div>
    </header>
    
    <!-- About -->
    <section id="about" class="py-16">
        <div class="max-w-4xl mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-8">About Us</h2>
            <p class="text-gray-600 text-lg">' . nl2br(htmlspecialchars($site['about_content'] ?? '')) . '</p>
        </div>
    </section>
    
    <!-- Services -->
    <section id="services" class="py-16 bg-white">
        <div class="max-w-4xl mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-8">Our Services</h2>
            <p class="text-gray-600 text-lg">' . nl2br(htmlspecialchars($site['services_content'] ?? '')) . '</p>
        </div>
    </section>
    
    <!-- Contact -->
    <section id="contact" class="py-16">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold mb-8">Contact Us</h2>
            <p class="text-gray-600 text-lg">' . nl2br(htmlspecialchars($site['contact_info'] ?? '')) . '</p>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="hero-gradient text-white py-8 text-center">
        <p>&copy; ' . date('Y') . ' ' . htmlspecialchars($site['site_name']) . '. All rights reserved.</p>
    </footer>
    
    <!-- Custom CSS will be injected here -->
    <style id="custom-styles"></style>
    
    <!-- Custom HTML will be injected here -->
    
    <!-- Custom JS will be injected here -->
    <script id="custom-scripts"></script>
</body>
</html>';
}

// Now include the header for normal page display
$pageTitle = "Developer Workspace - FilDevStudio Admin";
require_once '../includes/header.php';
require_once '../includes/mail.php';
requireAdmin();

$pdo = getConnection();
$error = '';
$success = '';

$requestId = (int)($_GET['request'] ?? 0);
$siteId = (int)($_GET['site'] ?? 0);
$activeTab = $_GET['tab'] ?? 'content';

// Get request details if provided
$request = null;
if ($requestId > 0) {
    $stmt = $pdo->prepare("
        SELECT cr.*, 
               u.name as client_name, u.email as client_email,
               cs.id as site_id, cs.site_name, cs.subdomain, cs.status as site_status,
               cs.primary_color, cs.secondary_color, cs.accent_color,
               cs.font_heading, cs.font_body, cs.hero_title, cs.hero_subtitle,
               cs.about_content, cs.services_content, cs.contact_info,
               t.name as template_name, t.id as template_id, t.folder_path as template_folder
        FROM custom_requests cr
        LEFT JOIN users u ON cr.user_id = u.id
        LEFT JOIN client_sites cs ON cr.site_id = cs.id
        LEFT JOIN templates t ON cs.template_id = t.id
        WHERE cr.id = ?
    ");
    $stmt->execute([$requestId]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($request && $request['site_id']) {
        $siteId = $request['site_id'];
    }
}

// Get site details
$site = null;
if ($siteId > 0) {
    $stmt = $pdo->prepare("
        SELECT cs.*, 
               u.name as owner_name, u.email as owner_email, u.id as owner_id,
               t.name as template_name, t.category as template_category, t.folder_path as template_folder
        FROM client_sites cs
        LEFT JOIN users u ON cs.user_id = u.id
        LEFT JOIN templates t ON cs.template_id = t.id
        WHERE cs.id = ?
    ");
    $stmt->execute([$siteId]);
    $site = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Create site custom folder if needed
$siteCustomFolder = __DIR__ . '/../uploads/sites/' . $siteId . '/custom';
if ($siteId > 0 && !is_dir($siteCustomFolder)) {
    mkdir($siteCustomFolder, 0755, true);
}

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // Update site content
    if ($_POST['action'] === 'update_site' && $site) {
        try {
            $sql = "UPDATE client_sites SET 
                    hero_title = ?, hero_subtitle = ?, about_content = ?, 
                    services_content = ?, contact_info = ?,
                    primary_color = ?, secondary_color = ?, accent_color = ?,
                    font_heading = ?, font_body = ?,
                    updated_at = NOW()
                    WHERE id = ?";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                sanitize($_POST['hero_title'] ?? ''),
                sanitize($_POST['hero_subtitle'] ?? ''),
                sanitize($_POST['about_content'] ?? ''),
                sanitize($_POST['services_content'] ?? ''),
                sanitize($_POST['contact_info'] ?? ''),
                sanitize($_POST['primary_color'] ?? '#3B82F6'),
                sanitize($_POST['secondary_color'] ?? '#1E40AF'),
                sanitize($_POST['accent_color'] ?? '#F59E0B'),
                sanitize($_POST['font_heading'] ?? 'Inter'),
                sanitize($_POST['font_body'] ?? 'Inter'),
                $siteId
            ]);
            
            logActivity($pdo, $_SESSION['user_id'], 'admin_site_edit', "Admin edited site #$siteId content");
            $success = 'Site content updated successfully!';
            
            // Refresh site data
            $stmt = $pdo->prepare("SELECT cs.*, u.name as owner_name, u.email as owner_email, t.name as template_name, t.folder_path as template_folder FROM client_sites cs LEFT JOIN users u ON cs.user_id = u.id LEFT JOIN templates t ON cs.template_id = t.id WHERE cs.id = ?");
            $stmt->execute([$siteId]);
            $site = $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            $error = 'Failed to update site: ' . $e->getMessage();
        }
    }
    
    // Update custom code (CSS/JS/HTML)
    if ($_POST['action'] === 'update_code' && $site) {
        try {
            $stmt = $pdo->prepare("UPDATE client_sites SET custom_css = ?, custom_js = ?, custom_html = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([
                $_POST['custom_css'] ?? '',
                $_POST['custom_js'] ?? '',
                $_POST['custom_html'] ?? '',
                $siteId
            ]);
            
            logActivity($pdo, $_SESSION['user_id'], 'admin_code_edit', "Admin updated custom code for site #$siteId");
            $success = 'Custom code saved successfully!';
            
            // Refresh
            $stmt = $pdo->prepare("SELECT * FROM client_sites WHERE id = ?");
            $stmt->execute([$siteId]);
            $siteData = $stmt->fetch(PDO::FETCH_ASSOC);
            $site = array_merge($site, $siteData);
            
        } catch (Exception $e) {
            $error = 'Failed to save code: ' . $e->getMessage();
        }
    }
    
    // Upload image
    if ($_POST['action'] === 'upload_image' && $site) {
        $imageType = sanitize($_POST['image_type'] ?? 'gallery');
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($_FILES['image']['type'], $allowedTypes)) {
                $error = 'Invalid image type. Allowed: JPG, PNG, GIF, WebP';
            } elseif ($_FILES['image']['size'] > $maxSize) {
                $error = 'Image too large. Max size: 5MB';
            } else {
                $uploadDir = __DIR__ . '/../uploads/sites/' . $siteId . '/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = $imageType . '_' . time() . '.' . $ext;
                $filepath = $uploadDir . $filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
                    // Save to database
                    $stmt = $pdo->prepare("INSERT INTO site_images (site_id, image_path, image_type, alt_text) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$siteId, 'uploads/sites/' . $siteId . '/' . $filename, $imageType, $_POST['alt_text'] ?? '']);
                    
                    logActivity($pdo, $_SESSION['user_id'], 'admin_image_upload', "Uploaded $imageType image for site #$siteId");
                    $success = ucfirst($imageType) . ' image uploaded successfully!';
                } else {
                    $error = 'Failed to upload image';
                }
            }
        } else {
            $error = 'Please select an image to upload';
        }
    }
    
    // Delete image
    if ($_POST['action'] === 'delete_image' && $site) {
        $imageId = (int)($_POST['image_id'] ?? 0);
        if ($imageId > 0) {
            $stmt = $pdo->prepare("SELECT image_path FROM site_images WHERE id = ? AND site_id = ?");
            $stmt->execute([$imageId, $siteId]);
            $img = $stmt->fetch();
            
            if ($img) {
                $fullPath = __DIR__ . '/../' . $img['image_path'];
                if (file_exists($fullPath)) unlink($fullPath);
                
                $stmt = $pdo->prepare("DELETE FROM site_images WHERE id = ?");
                $stmt->execute([$imageId]);
                $success = 'Image deleted successfully!';
            }
        }
    }
    
    // Upload code package (ZIP or Folder) - Extract and save to database
    if ($_POST['action'] === 'upload_code_package' && $site) {
        $uploadedCss = '';
        $uploadedJs = '';
        $uploadedHtml = '';
        $uploadedFullHtml = '';
        $filesFound = [];
        $configUpdated = false;
        
        // Check if it's a folder upload (multiple files)
        if (isset($_FILES['code_files']) && is_array($_FILES['code_files']['name'])) {
            $fileCount = count($_FILES['code_files']['name']);
            
            for ($i = 0; $i < $fileCount; $i++) {
                if ($_FILES['code_files']['error'][$i] === UPLOAD_ERR_OK) {
                    $fileName = basename($_FILES['code_files']['name'][$i]);
                    $tmpFile = $_FILES['code_files']['tmp_name'][$i];
                    
                    // Process based on filename
                    if ($fileName === 'custom.css') {
                        $uploadedCss = file_get_contents($tmpFile);
                        $filesFound[] = 'custom.css';
                    } elseif ($fileName === 'custom.js') {
                        $uploadedJs = file_get_contents($tmpFile);
                        $filesFound[] = 'custom.js';
                    } elseif ($fileName === 'custom-sections.html') {
                        $uploadedHtml = file_get_contents($tmpFile);
                        $filesFound[] = 'custom-sections.html';
                    } elseif ($fileName === 'index.html') {
                        // Full template override
                        $uploadedFullHtml = file_get_contents($tmpFile);
                        $filesFound[] = 'index.html';
                    } elseif ($fileName === 'config.json') {
                        $configJson = file_get_contents($tmpFile);
                        $config = json_decode($configJson, true);
                        if ($config && isset($config['colors'])) {
                            $stmt = $pdo->prepare("UPDATE client_sites SET 
                                primary_color = COALESCE(?, primary_color),
                                secondary_color = COALESCE(?, secondary_color),
                                accent_color = COALESCE(?, accent_color),
                                updated_at = NOW()
                                WHERE id = ?");
                            $stmt->execute([
                                $config['colors']['primary'] ?? null,
                                $config['colors']['secondary'] ?? null,
                                $config['colors']['accent'] ?? null,
                                $siteId
                            ]);
                            $configUpdated = true;
                        }
                        $filesFound[] = 'config.json';
                    }
                }
            }
            
            // Save custom code to database
            if (!empty($filesFound)) {
                // Only update fields that were uploaded
                $updates = [];
                $params = [];
                
                if (in_array('custom.css', $filesFound)) {
                    $updates[] = "custom_css = ?";
                    $params[] = $uploadedCss;
                }
                if (in_array('custom.js', $filesFound)) {
                    $updates[] = "custom_js = ?";
                    $params[] = $uploadedJs;
                }
                if (in_array('custom-sections.html', $filesFound)) {
                    $updates[] = "custom_html = ?";
                    $params[] = $uploadedHtml;
                }
                if (in_array('index.html', $filesFound)) {
                    $updates[] = "custom_full_html = ?";
                    $params[] = $uploadedFullHtml;
                    
                    // Auto-generate subdomain if empty
                    if (empty($site['subdomain'])) {
                        $baseSubdomain = preg_replace('/[^a-z0-9]+/', '-', strtolower($site['site_name']));
                        $baseSubdomain = trim($baseSubdomain, '-');
                        $subdomain = $baseSubdomain ?: 'site-' . $siteId;
                        
                        // Check uniqueness
                        $checkStmt = $pdo->prepare("SELECT id FROM client_sites WHERE subdomain = ? AND id != ?");
                        $checkStmt->execute([$subdomain, $siteId]);
                        if ($checkStmt->fetch()) {
                            $subdomain = $subdomain . '-' . $siteId;
                        }
                        
                        $updates[] = "subdomain = ?";
                        $params[] = $subdomain;
                        $site['subdomain'] = $subdomain; // Update local var for preview link
                    }
                }
                
                if (!empty($updates)) {
                    $updates[] = "updated_at = NOW()";
                    $params[] = $siteId;
                    $sql = "UPDATE client_sites SET " . implode(', ', $updates) . " WHERE id = ?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);
                    
                    // Refresh site data
                    $stmt = $pdo->prepare("SELECT * FROM client_sites WHERE id = ?");
                    $stmt->execute([$siteId]);
                    $site = array_merge($site, $stmt->fetch(PDO::FETCH_ASSOC));
                }
                
                logActivity($pdo, $_SESSION['user_id'], 'admin_code_upload', "Uploaded folder for site #$siteId: " . implode(', ', $filesFound));
                $success = '✅ Folder uploaded successfully! Files imported: ' . implode(', ', $filesFound);
            } else {
                $error = 'No valid files found in folder. Expected: index.html, custom.css, custom.js, custom-sections.html, or config.json';
            }
        }
        // ZIP file upload (original method)
        elseif (isset($_FILES['code_package']) && $_FILES['code_package']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['code_package']['name'], PATHINFO_EXTENSION));
            
            if ($ext !== 'zip') {
                $error = 'Please upload a ZIP file';
            } else {
                $zip = new ZipArchive();
                if ($zip->open($_FILES['code_package']['tmp_name']) === TRUE) {
                    $tempDir = sys_get_temp_dir() . '/site_upload_' . $siteId . '_' . time();
                    mkdir($tempDir, 0755, true);
                    
                    $zip->extractTo($tempDir);
                    $zip->close();
                    
                    $uploadedCss = '';
                    $uploadedJs = '';
                    $uploadedHtml = '';
                    $uploadedFullHtml = '';
                    $filesFound = [];
                    
                    // Read index.html (full template override)
                    if (file_exists($tempDir . '/index.html')) {
                        $uploadedFullHtml = file_get_contents($tempDir . '/index.html');
                        $filesFound[] = 'index.html';
                    }
                    
                    // Read custom.css
                    if (file_exists($tempDir . '/custom.css')) {
                        $uploadedCss = file_get_contents($tempDir . '/custom.css');
                        $filesFound[] = 'custom.css';
                    }
                    
                    // Read custom.js
                    if (file_exists($tempDir . '/custom.js')) {
                        $uploadedJs = file_get_contents($tempDir . '/custom.js');
                        $filesFound[] = 'custom.js';
                    }
                    
                    // Read custom-sections.html
                    if (file_exists($tempDir . '/custom-sections.html')) {
                        $uploadedHtml = file_get_contents($tempDir . '/custom-sections.html');
                        $filesFound[] = 'custom-sections.html';
                    }
                    
                    // Also check for config.json to update site settings
                    if (file_exists($tempDir . '/config.json')) {
                        $configJson = file_get_contents($tempDir . '/config.json');
                        $config = json_decode($configJson, true);
                        if ($config && isset($config['colors'])) {
                            // Update colors if changed
                            $stmt = $pdo->prepare("UPDATE client_sites SET 
                                primary_color = COALESCE(?, primary_color),
                                secondary_color = COALESCE(?, secondary_color),
                                accent_color = COALESCE(?, accent_color),
                                updated_at = NOW()
                                WHERE id = ?");
                            $stmt->execute([
                                $config['colors']['primary'] ?? null,
                                $config['colors']['secondary'] ?? null,
                                $config['colors']['accent'] ?? null,
                                $siteId
                            ]);
                        }
                        $filesFound[] = 'config.json';
                    }
                    
                    // Save custom code to database
                    if (!empty($filesFound)) {
                        $updates = [];
                        $params = [];
                        
                        if (!empty($uploadedFullHtml)) {
                            $updates[] = "custom_full_html = ?";
                            $params[] = $uploadedFullHtml;
                            
                            // Auto-generate subdomain if empty
                            if (empty($site['subdomain'])) {
                                $baseSubdomain = preg_replace('/[^a-z0-9]+/', '-', strtolower($site['site_name']));
                                $baseSubdomain = trim($baseSubdomain, '-');
                                $subdomain = $baseSubdomain ?: 'site-' . $siteId;
                                
                                // Check uniqueness
                                $checkStmt = $pdo->prepare("SELECT id FROM client_sites WHERE subdomain = ? AND id != ?");
                                $checkStmt->execute([$subdomain, $siteId]);
                                if ($checkStmt->fetch()) {
                                    $subdomain = $subdomain . '-' . $siteId;
                                }
                                
                                $updates[] = "subdomain = ?";
                                $params[] = $subdomain;
                                $site['subdomain'] = $subdomain;
                            }
                        }
                        if (!empty($uploadedCss)) {
                            $updates[] = "custom_css = ?";
                            $params[] = $uploadedCss;
                        }
                        if (!empty($uploadedJs)) {
                            $updates[] = "custom_js = ?";
                            $params[] = $uploadedJs;
                        }
                        if (!empty($uploadedHtml)) {
                            $updates[] = "custom_html = ?";
                            $params[] = $uploadedHtml;
                        }
                        
                        if (!empty($updates)) {
                            $updates[] = "updated_at = NOW()";
                            $params[] = $siteId;
                            $sql = "UPDATE client_sites SET " . implode(', ', $updates) . " WHERE id = ?";
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute($params);
                        }
                        
                        // Refresh site data
                        $stmt = $pdo->prepare("SELECT * FROM client_sites WHERE id = ?");
                        $stmt->execute([$siteId]);
                        $site = array_merge($site, $stmt->fetch(PDO::FETCH_ASSOC));
                    }
                    
                    // Clean up temp directory
                    array_map('unlink', glob($tempDir . '/*'));
                    rmdir($tempDir);
                    
                    logActivity($pdo, $_SESSION['user_id'], 'admin_code_upload', "Uploaded code package for site #$siteId: " . implode(', ', $filesFound));
                    $success = '✅ Code uploaded successfully! Files imported: ' . implode(', ', $filesFound);
                } else {
                    $error = 'Failed to open ZIP file';
                }
            }
        } else {
            $error = 'Please select a ZIP file to upload';
        }
    }
    
    // Notify client that preview is ready
    if ($_POST['action'] === 'notify_preview_ready' && $request && $site) {
        try {
            $stmt = $pdo->prepare("UPDATE custom_requests SET status = 'preview_ready', updated_at = NOW() WHERE id = ?");
            $stmt->execute([$requestId]);
            
            $previewUrl = "http://localhost/fildevstudio/public/site.php?s=" . urlencode($site['subdomain']);
            $refNumber = $request['reference_number'] ?? 'REQ-' . $requestId;
            
            $emailHtml = '
            <!DOCTYPE html>
            <html>
            <head><style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #3B82F6, #1E40AF); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
                .btn { display: inline-block; background: #3B82F6; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
            </style></head>
            <body>
                <div class="container">
                    <div class="header">
                        <h1 style="margin:0;">🎨 Your Preview is Ready!</h1>
                        <p style="margin:10px 0 0;">Reference: ' . htmlspecialchars($refNumber) . '</p>
                    </div>
                    <div class="content">
                        <p>Hi ' . htmlspecialchars($request['client_name']) . ',</p>
                        <p>Great news! Our team has finished working on your customization request. Your site preview is now ready for review!</p>
                        <p style="text-align: center; margin: 30px 0;">
                            <a href="' . $previewUrl . '" class="btn">👁️ View Preview</a>
                        </p>
                        <p>Please review the changes and let us know if you need any modifications.</p>
                        <hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">
                        <p style="color: #6b7280; font-size: 12px;">FilDevStudio Team</p>
                    </div>
                </div>
            </body>
            </html>';
            
            $result = sendEmail($request['client_email'], "Your Preview is Ready! - $refNumber", $emailHtml);
            
            logActivity($pdo, $_SESSION['user_id'], 'preview_ready', "Marked request #$requestId as preview ready");
            $success = 'Client notified! ' . ($result['success'] ? 'Email sent.' : $result['message']);
            
            // Refresh
            $stmt = $pdo->prepare("SELECT cr.*, u.name as client_name, u.email as client_email FROM custom_requests cr LEFT JOIN users u ON cr.user_id = u.id WHERE cr.id = ?");
            $stmt->execute([$requestId]);
            $request = $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            $error = 'Failed to notify client: ' . $e->getMessage();
        }
    }
}

// Get request files
$requestFiles = [];
if ($requestId > 0) {
    $uploadDir = __DIR__ . '/../uploads/requests/' . $requestId;
    if (is_dir($uploadDir)) {
        $files = glob($uploadDir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                $requestFiles[] = [
                    'file_path' => 'uploads/requests/' . $requestId . '/' . basename($file),
                    'original_name' => basename($file),
                    'file_size' => filesize($file)
                ];
            }
        }
    }
}

// Get site images
$siteImages = [];
if ($siteId > 0) {
    $stmt = $pdo->prepare("SELECT * FROM site_images WHERE site_id = ? ORDER BY image_type, display_order");
    $stmt->execute([$siteId]);
    $siteImages = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get custom files
$customFiles = [];
if (is_dir($siteCustomFolder)) {
    $files = glob($siteCustomFolder . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            $customFiles[] = [
                'name' => basename($file),
                'path' => $file,
                'size' => filesize($file),
                'modified' => filemtime($file)
            ];
        }
    }
}

// Font options
$fontOptions = ['Inter', 'Poppins', 'Roboto', 'Open Sans', 'Lato', 'Montserrat', 'Nunito', 'Raleway', 'Playfair Display', 'Merriweather'];
?>

<!-- Page Header -->
<section class="gradient-bg py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <a href="requests.php" class="text-blue-200 hover:text-white mb-1 inline-block text-sm">
                    <i class="fas fa-arrow-left mr-1"></i>Back to Requests
                </a>
                <h1 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-laptop-code mr-3"></i>Developer Workspace
                </h1>
                <?php if ($site): ?>
                <p class="text-blue-200 text-sm mt-1">
                    Working on: <strong class="text-white"><?php echo htmlspecialchars($site['site_name']); ?></strong>
                    <?php if ($request): ?>
                    • Request #<?php echo htmlspecialchars($request['reference_number'] ?? $requestId); ?>
                    <?php endif; ?>
                </p>
                <?php endif; ?>
            </div>
            <div class="flex gap-2">
                <?php if ($site && $site['subdomain']): ?>
                <a href="../public/site.php?s=<?php echo urlencode($site['subdomain']); ?>&preview=1" target="_blank" 
                   class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                    <i class="fas fa-eye"></i>Preview
                </a>
                <a href="?request=<?php echo $requestId; ?>&site=<?php echo $siteId; ?>&download=code" 
                   class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                    <i class="fas fa-download"></i>Download Code
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php if (!$site && !$request): ?>
<!-- No site selected -->
<section class="py-12">
    <div class="max-w-xl mx-auto px-4 text-center">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <i class="fas fa-folder-open text-6xl text-gray-300 mb-4"></i>
            <h2 class="text-xl font-semibold text-gray-700 mb-2">No Site Selected</h2>
            <p class="text-gray-500 mb-6">Open a request from the requests page to start working.</p>
            <a href="requests.php" class="inline-flex items-center px-6 py-3 bg-primary text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-list mr-2"></i>View Requests
            </a>
        </div>
    </div>
</section>
<?php else: ?>

<section class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if ($error): echo displayAlert('error', $error); endif; ?>
        <?php if ($success): echo displayAlert('success', $success); endif; ?>
        
        <!-- Tabs Navigation -->
        <div class="bg-white rounded-t-xl shadow-lg border-b">
            <nav class="flex overflow-x-auto">
                <a href="?request=<?php echo $requestId; ?>&site=<?php echo $siteId; ?>&tab=content" 
                   class="px-6 py-4 font-medium border-b-2 whitespace-nowrap <?php echo $activeTab === 'content' ? 'border-primary text-primary bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?>">
                    <i class="fas fa-edit mr-2"></i>Content
                </a>
                <a href="?request=<?php echo $requestId; ?>&site=<?php echo $siteId; ?>&tab=images" 
                   class="px-6 py-4 font-medium border-b-2 whitespace-nowrap <?php echo $activeTab === 'images' ? 'border-primary text-primary bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?>">
                    <i class="fas fa-images mr-2"></i>Images
                </a>
                <a href="?request=<?php echo $requestId; ?>&site=<?php echo $siteId; ?>&tab=code" 
                   class="px-6 py-4 font-medium border-b-2 whitespace-nowrap <?php echo $activeTab === 'code' ? 'border-primary text-primary bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?>">
                    <i class="fas fa-code mr-2"></i>Custom Code
                </a>
                <a href="?request=<?php echo $requestId; ?>&site=<?php echo $siteId; ?>&tab=request" 
                   class="px-6 py-4 font-medium border-b-2 whitespace-nowrap <?php echo $activeTab === 'request' ? 'border-primary text-primary bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?>">
                    <i class="fas fa-file-alt mr-2"></i>Request Details
                </a>
            </nav>
        </div>
        
        <div class="bg-white rounded-b-xl shadow-lg">
            
            <!-- CONTENT TAB -->
            <?php if ($activeTab === 'content'): ?>
            <form method="POST" class="p-6">
                <input type="hidden" name="action" value="update_site">
                
                <div class="grid lg:grid-cols-3 gap-6">
                    <!-- Left: Colors & Fonts -->
                    <div class="space-y-6">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-palette mr-2 text-primary"></i>Color Scheme
                            </h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Primary</label>
                                    <div class="flex gap-2">
                                        <input type="color" name="primary_color" value="<?php echo htmlspecialchars($site['primary_color'] ?? '#3B82F6'); ?>" class="w-12 h-10 rounded border cursor-pointer">
                                        <input type="text" value="<?php echo htmlspecialchars($site['primary_color'] ?? '#3B82F6'); ?>" class="flex-1 px-3 py-2 border rounded-lg font-mono text-sm" readonly>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Secondary</label>
                                    <div class="flex gap-2">
                                        <input type="color" name="secondary_color" value="<?php echo htmlspecialchars($site['secondary_color'] ?? '#1E40AF'); ?>" class="w-12 h-10 rounded border cursor-pointer">
                                        <input type="text" value="<?php echo htmlspecialchars($site['secondary_color'] ?? '#1E40AF'); ?>" class="flex-1 px-3 py-2 border rounded-lg font-mono text-sm" readonly>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Accent</label>
                                    <div class="flex gap-2">
                                        <input type="color" name="accent_color" value="<?php echo htmlspecialchars($site['accent_color'] ?? '#F59E0B'); ?>" class="w-12 h-10 rounded border cursor-pointer">
                                        <input type="text" value="<?php echo htmlspecialchars($site['accent_color'] ?? '#F59E0B'); ?>" class="flex-1 px-3 py-2 border rounded-lg font-mono text-sm" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-font mr-2 text-primary"></i>Typography
                            </h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Heading Font</label>
                                    <select name="font_heading" class="w-full px-3 py-2 border rounded-lg">
                                        <?php foreach ($fontOptions as $font): ?>
                                        <option value="<?php echo $font; ?>" <?php echo ($site['font_heading'] ?? 'Inter') === $font ? 'selected' : ''; ?>><?php echo $font; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Body Font</label>
                                    <select name="font_body" class="w-full px-3 py-2 border rounded-lg">
                                        <?php foreach ($fontOptions as $font): ?>
                                        <option value="<?php echo $font; ?>" <?php echo ($site['font_body'] ?? 'Inter') === $font ? 'selected' : ''; ?>><?php echo $font; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg p-4 border border-green-200">
                            <h4 class="font-semibold text-green-800 mb-3">
                                <i class="fas fa-bolt mr-2"></i>Quick Actions
                            </h4>
                            <?php if ($request && $request['status'] !== 'preview_ready' && $request['status'] !== 'completed'): ?>
                            <button type="submit" formaction="?request=<?php echo $requestId; ?>&site=<?php echo $siteId; ?>&tab=content" 
                                    name="action" value="notify_preview_ready"
                                    onclick="return confirm('Save changes and notify client that preview is ready?')"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition font-medium mb-2">
                                <i class="fas fa-paper-plane"></i>Mark Ready & Notify
                            </button>
                            <?php elseif ($request && $request['status'] === 'preview_ready'): ?>
                            <div class="text-center py-3 bg-green-100 rounded-lg text-green-700 mb-2">
                                <i class="fas fa-check-circle mr-2"></i>Client Notified
                            </div>
                            <?php endif; ?>
                            <a href="../public/site.php?s=<?php echo urlencode($site['subdomain'] ?? ''); ?>&preview=1" target="_blank"
                               class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition">
                                <i class="fas fa-eye"></i>Preview Site
                            </a>
                        </div>
                    </div>
                    
                    <!-- Right: Content Fields -->
                    <div class="lg:col-span-2 space-y-6">
                        <div>
                            <label class="block font-medium text-gray-700 mb-2">
                                <i class="fas fa-heading mr-2 text-primary"></i>Hero Title
                            </label>
                            <input type="text" name="hero_title" value="<?php echo htmlspecialchars($site['hero_title'] ?? ''); ?>"
                                   class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary/30 focus:border-primary"
                                   placeholder="Main headline...">
                        </div>
                        
                        <div>
                            <label class="block font-medium text-gray-700 mb-2">
                                <i class="fas fa-align-left mr-2 text-primary"></i>Hero Subtitle
                            </label>
                            <textarea name="hero_subtitle" rows="2"
                                      class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary/30 focus:border-primary"
                                      placeholder="Supporting text..."><?php echo htmlspecialchars($site['hero_subtitle'] ?? ''); ?></textarea>
                        </div>
                        
                        <div>
                            <label class="block font-medium text-gray-700 mb-2">
                                <i class="fas fa-info-circle mr-2 text-primary"></i>About Content
                            </label>
                            <textarea name="about_content" rows="4"
                                      class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary/30 focus:border-primary"
                                      placeholder="About the business..."><?php echo htmlspecialchars($site['about_content'] ?? ''); ?></textarea>
                        </div>
                        
                        <div>
                            <label class="block font-medium text-gray-700 mb-2">
                                <i class="fas fa-concierge-bell mr-2 text-primary"></i>Services / Products
                            </label>
                            <textarea name="services_content" rows="4"
                                      class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary/30 focus:border-primary"
                                      placeholder="List of services..."><?php echo htmlspecialchars($site['services_content'] ?? ''); ?></textarea>
                        </div>
                        
                        <div>
                            <label class="block font-medium text-gray-700 mb-2">
                                <i class="fas fa-address-card mr-2 text-primary"></i>Contact Information
                            </label>
                            <textarea name="contact_info" rows="3"
                                      class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary/30 focus:border-primary"
                                      placeholder="Address, phone, email..."><?php echo htmlspecialchars($site['contact_info'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="flex justify-end pt-4 border-t">
                            <button type="submit" class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-blue-700 transition font-medium flex items-center gap-2">
                                <i class="fas fa-save"></i>Save Content Changes
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            <?php endif; ?>
            
            <!-- IMAGES TAB -->
            <?php if ($activeTab === 'images'): ?>
            <div class="p-6">
                <div class="grid lg:grid-cols-2 gap-6">
                    <!-- Upload Form -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h4 class="font-semibold text-gray-700 mb-4">
                            <i class="fas fa-cloud-upload-alt mr-2 text-primary"></i>Upload New Image
                        </h4>
                        <form method="POST" enctype="multipart/form-data" class="space-y-4">
                            <input type="hidden" name="action" value="upload_image">
                            
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Image Type</label>
                                <select name="image_type" class="w-full px-3 py-2 border rounded-lg">
                                    <option value="logo">Logo</option>
                                    <option value="hero">Hero Background</option>
                                    <option value="gallery">Gallery</option>
                                    <option value="product">Product</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Alt Text (optional)</label>
                                <input type="text" name="alt_text" class="w-full px-3 py-2 border rounded-lg" placeholder="Image description...">
                            </div>
                            
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Select Image</label>
                                <input type="file" name="image" accept="image/*" required
                                       class="w-full px-3 py-2 border rounded-lg bg-white">
                                <p class="text-xs text-gray-500 mt-1">Max 5MB. JPG, PNG, GIF, WebP</p>
                            </div>
                            
                            <button type="submit" class="w-full px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition">
                                <i class="fas fa-upload mr-2"></i>Upload Image
                            </button>
                        </form>
                    </div>
                    
                    <!-- Current Images -->
                    <div>
                        <h4 class="font-semibold text-gray-700 mb-4">
                            <i class="fas fa-images mr-2 text-primary"></i>Current Images (<?php echo count($siteImages); ?>)
                        </h4>
                        <?php if (empty($siteImages)): ?>
                        <div class="text-center py-8 bg-gray-50 rounded-lg">
                            <i class="fas fa-image text-4xl text-gray-300 mb-2"></i>
                            <p class="text-gray-500">No images uploaded yet</p>
                        </div>
                        <?php else: ?>
                        <div class="grid grid-cols-2 gap-4 max-h-96 overflow-auto">
                            <?php foreach ($siteImages as $img): ?>
                            <div class="relative group rounded-lg overflow-hidden border">
                                <img src="../<?php echo htmlspecialchars($img['image_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($img['alt_text'] ?? ''); ?>"
                                     class="w-full h-32 object-cover">
                                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                                    <a href="../<?php echo htmlspecialchars($img['image_path']); ?>" target="_blank"
                                       class="p-2 bg-white rounded-full text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    <form method="POST" class="inline" onsubmit="return confirm('Delete this image?');">
                                        <input type="hidden" name="action" value="delete_image">
                                        <input type="hidden" name="image_id" value="<?php echo $img['id']; ?>">
                                        <button type="submit" class="p-2 bg-red-500 rounded-full text-white hover:bg-red-600">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                <div class="absolute bottom-0 left-0 right-0 bg-black/70 text-white text-xs px-2 py-1">
                                    <?php echo ucfirst($img['image_type']); ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- CODE TAB -->
            <?php if ($activeTab === 'code'): ?>
            <div class="p-6">
                <!-- Workflow Guide -->
                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg p-4 mb-6 border border-indigo-200">
                    <h4 class="font-semibold text-indigo-800 mb-2">
                        <i class="fas fa-lightbulb mr-2"></i>Developer Workflow
                    </h4>
                    <div class="grid md:grid-cols-4 gap-4 text-sm">
                        <div class="flex items-start gap-2">
                            <span class="w-6 h-6 rounded-full bg-indigo-500 text-white flex items-center justify-center text-xs font-bold">1</span>
                            <div>
                                <p class="font-medium text-indigo-700">Download Code</p>
                                <p class="text-indigo-600">Get the site package</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="w-6 h-6 rounded-full bg-indigo-500 text-white flex items-center justify-center text-xs font-bold">2</span>
                            <div>
                                <p class="font-medium text-indigo-700">Edit in IDE</p>
                                <p class="text-indigo-600">Modify CSS/JS/HTML</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="w-6 h-6 rounded-full bg-indigo-500 text-white flex items-center justify-center text-xs font-bold">3</span>
                            <div>
                                <p class="font-medium text-indigo-700">Upload Package</p>
                                <p class="text-indigo-600">ZIP and upload</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="w-6 h-6 rounded-full bg-indigo-500 text-white flex items-center justify-center text-xs font-bold">4</span>
                            <div>
                                <p class="font-medium text-indigo-700">Notify Client</p>
                                <p class="text-indigo-600">Preview ready!</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="grid lg:grid-cols-3 gap-6">
                    <!-- Download/Upload Panel -->
                    <div class="space-y-4">
                        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                            <h4 class="font-semibold text-green-800 mb-3">
                                <i class="fas fa-download mr-2"></i>Download Code
                            </h4>
                            <p class="text-sm text-green-700 mb-3">Download the site code package to edit in your IDE.</p>
                            <a href="?request=<?php echo $requestId; ?>&site=<?php echo $siteId; ?>&download=code" 
                               class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                                <i class="fas fa-file-archive"></i>Download ZIP
                            </a>
                        </div>
                        
                        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                            <h4 class="font-semibold text-blue-800 mb-3">
                                <i class="fas fa-upload mr-2"></i>Upload Modified Code
                            </h4>
                            
                            <!-- Upload Type Toggle -->
                            <div class="flex mb-3 bg-blue-100 rounded-lg p-1">
                                <button type="button" onclick="toggleUploadType('zip')" id="btn-zip"
                                        class="flex-1 px-3 py-1.5 text-sm rounded-md bg-white text-blue-700 font-medium shadow-sm transition">
                                    <i class="fas fa-file-archive mr-1"></i>ZIP File
                                </button>
                                <button type="button" onclick="toggleUploadType('folder')" id="btn-folder"
                                        class="flex-1 px-3 py-1.5 text-sm rounded-md text-blue-600 hover:bg-blue-50 transition">
                                    <i class="fas fa-folder mr-1"></i>Folder
                                </button>
                            </div>
                            
                            <!-- ZIP Upload Form -->
                            <form method="POST" enctype="multipart/form-data" id="form-zip">
                                <input type="hidden" name="action" value="upload_code_package">
                                <input type="file" name="code_package" accept=".zip" required
                                       class="w-full px-3 py-2 border rounded-lg bg-white mb-2 text-sm">
                                <button type="submit" class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                                    <i class="fas fa-cloud-upload-alt mr-2"></i>Upload ZIP
                                </button>
                            </form>
                            
                            <!-- Folder Upload Form -->
                            <form method="POST" enctype="multipart/form-data" id="form-folder" class="hidden">
                                <input type="hidden" name="action" value="upload_code_package">
                                <input type="file" name="code_files[]" webkitdirectory directory multiple
                                       class="w-full px-3 py-2 border rounded-lg bg-white mb-2 text-sm">
                                <p class="text-xs text-blue-600 mb-2">
                                    <i class="fas fa-info-circle mr-1"></i>Accepts: index.html, custom.css, custom.js, custom-sections.html, config.json
                                </p>
                                <button type="submit" class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                                    <i class="fas fa-folder-open mr-2"></i>Upload Folder
                                </button>
                            </form>
                        </div>
                        
                        <!-- Uploaded Custom Files -->
                        <?php if (!empty($customFiles)): ?>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-700 mb-3">
                                <i class="fas fa-folder-open mr-2"></i>Custom Files
                            </h4>
                            <div class="space-y-2 text-sm">
                                <?php foreach ($customFiles as $cf): ?>
                                <div class="flex items-center justify-between p-2 bg-white rounded border">
                                    <span class="truncate"><?php echo htmlspecialchars($cf['name']); ?></span>
                                    <span class="text-gray-400 text-xs"><?php echo round($cf['size']/1024, 1); ?>KB</span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Code Editors -->
                    <div class="lg:col-span-2">
                        <form method="POST">
                            <input type="hidden" name="action" value="update_code">
                            
                            <!-- Custom CSS -->
                            <div class="mb-4">
                                <label class="flex items-center justify-between mb-2">
                                    <span class="font-medium text-gray-700"><i class="fab fa-css3 mr-2 text-blue-500"></i>Custom CSS</span>
                                    <span class="text-xs text-gray-500">Styles applied to the site</span>
                                </label>
                                <textarea name="custom_css" rows="8" 
                                          class="w-full px-4 py-3 border rounded-lg font-mono text-sm bg-gray-900 text-green-400 focus:ring-2 focus:ring-primary/30"
                                          placeholder="/* Add custom CSS here */"><?php echo htmlspecialchars($site['custom_css'] ?? ''); ?></textarea>
                            </div>
                            
                            <!-- Custom JS -->
                            <div class="mb-4">
                                <label class="flex items-center justify-between mb-2">
                                    <span class="font-medium text-gray-700"><i class="fab fa-js mr-2 text-yellow-500"></i>Custom JavaScript</span>
                                    <span class="text-xs text-gray-500">Scripts executed on page load</span>
                                </label>
                                <textarea name="custom_js" rows="8" 
                                          class="w-full px-4 py-3 border rounded-lg font-mono text-sm bg-gray-900 text-green-400 focus:ring-2 focus:ring-primary/30"
                                          placeholder="// Add custom JavaScript here"><?php echo htmlspecialchars($site['custom_js'] ?? ''); ?></textarea>
                            </div>
                            
                            <!-- Custom HTML -->
                            <div class="mb-4">
                                <label class="flex items-center justify-between mb-2">
                                    <span class="font-medium text-gray-700"><i class="fab fa-html5 mr-2 text-orange-500"></i>Custom HTML Sections</span>
                                    <span class="text-xs text-gray-500">Extra HTML injected into the page</span>
                                </label>
                                <textarea name="custom_html" rows="6" 
                                          class="w-full px-4 py-3 border rounded-lg font-mono text-sm bg-gray-900 text-green-400 focus:ring-2 focus:ring-primary/30"
                                          placeholder="<!-- Add custom HTML sections here -->"><?php echo htmlspecialchars($site['custom_html'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="submit" class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-blue-700 transition font-medium flex items-center gap-2">
                                    <i class="fas fa-save"></i>Save Custom Code
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- REQUEST DETAILS TAB -->
            <?php if ($activeTab === 'request'): ?>
            <div class="p-6">
                <?php if ($request): ?>
                <div class="grid lg:grid-cols-2 gap-6">
                    <!-- Request Info -->
                    <div class="space-y-4">
                        <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-lg p-4 border border-orange-200">
                            <h4 class="font-semibold text-orange-800 mb-3">
                                <i class="fas fa-file-alt mr-2"></i>Request Information
                            </h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Reference:</span>
                                    <span class="font-mono font-bold"><?php echo htmlspecialchars($request['reference_number'] ?? 'REQ-' . $requestId); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Status:</span>
                                    <?php
                                    $statusColors = ['pending' => 'bg-yellow-100 text-yellow-700', 'in_progress' => 'bg-blue-100 text-blue-700', 'preview_ready' => 'bg-purple-100 text-purple-700', 'completed' => 'bg-green-100 text-green-700', 'rejected' => 'bg-red-100 text-red-700'];
                                    ?>
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium <?php echo $statusColors[$request['status']] ?? 'bg-gray-100'; ?>">
                                        <?php echo ucwords(str_replace('_', ' ', $request['status'])); ?>
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Type:</span>
                                    <span><?php echo ucwords($request['request_type'] ?? 'Custom'); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Submitted:</span>
                                    <span><?php echo date('M j, Y g:i A', strtotime($request['created_at'])); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                            <h4 class="font-semibold text-blue-800 mb-3">
                                <i class="fas fa-user mr-2"></i>Client
                            </h4>
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-r from-blue-400 to-indigo-400 flex items-center justify-center text-white font-bold text-lg">
                                    <?php echo strtoupper(substr($request['client_name'] ?? 'U', 0, 1)); ?>
                                </div>
                                <div>
                                    <p class="font-semibold"><?php echo htmlspecialchars($request['client_name'] ?? 'Unknown'); ?></p>
                                    <p class="text-sm text-gray-500"><?php echo htmlspecialchars($request['client_email'] ?? ''); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Request Description -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-700 mb-2">Request Description</h4>
                            <div class="text-sm text-gray-700 whitespace-pre-wrap">
                                <?php echo htmlspecialchars($request['request_details'] ?? $request['description'] ?? 'No description provided'); ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Attached Files -->
                    <div>
                        <h4 class="font-semibold text-gray-700 mb-4">
                            <i class="fas fa-paperclip mr-2"></i>Attached Files (<?php echo count($requestFiles); ?>)
                        </h4>
                        <?php if (empty($requestFiles)): ?>
                        <div class="text-center py-8 bg-gray-50 rounded-lg">
                            <i class="fas fa-file-alt text-4xl text-gray-300 mb-2"></i>
                            <p class="text-gray-500">No files attached</p>
                        </div>
                        <?php else: ?>
                        <div class="grid grid-cols-2 gap-4">
                            <?php foreach ($requestFiles as $file): 
                                $fileName = $file['original_name'] ?? basename($file['file_path']);
                                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                            ?>
                            <a href="../<?php echo htmlspecialchars($file['file_path']); ?>" target="_blank"
                               class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition border">
                                <?php if ($isImage): ?>
                                <img src="../<?php echo htmlspecialchars($file['file_path']); ?>" class="w-12 h-12 object-cover rounded">
                                <?php else: ?>
                                <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                                    <i class="fas fa-file text-gray-400 text-xl"></i>
                                </div>
                                <?php endif; ?>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium truncate"><?php echo htmlspecialchars($fileName); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo round(($file['file_size'] ?? 0) / 1024, 1); ?> KB</p>
                                </div>
                                <i class="fas fa-external-link-alt text-gray-400"></i>
                            </a>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="text-center py-12">
                    <i class="fas fa-info-circle text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">No request linked to this workspace session.</p>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
        </div>
    </div>
</section>
<?php endif; ?>

<script>
// Sync color picker with text input
document.querySelectorAll('input[type="color"]').forEach(colorInput => {
    colorInput.addEventListener('input', function() {
        const textInput = this.nextElementSibling;
        if (textInput) textInput.value = this.value;
    });
});

// Toggle between ZIP and Folder upload
function toggleUploadType(type) {
    const formZip = document.getElementById('form-zip');
    const formFolder = document.getElementById('form-folder');
    const btnZip = document.getElementById('btn-zip');
    const btnFolder = document.getElementById('btn-folder');
    
    if (type === 'zip') {
        formZip.classList.remove('hidden');
        formFolder.classList.add('hidden');
        btnZip.classList.add('bg-white', 'text-blue-700', 'font-medium', 'shadow-sm');
        btnZip.classList.remove('text-blue-600', 'hover:bg-blue-50');
        btnFolder.classList.remove('bg-white', 'text-blue-700', 'font-medium', 'shadow-sm');
        btnFolder.classList.add('text-blue-600', 'hover:bg-blue-50');
    } else {
        formZip.classList.add('hidden');
        formFolder.classList.remove('hidden');
        btnFolder.classList.add('bg-white', 'text-blue-700', 'font-medium', 'shadow-sm');
        btnFolder.classList.remove('text-blue-600', 'hover:bg-blue-50');
        btnZip.classList.remove('bg-white', 'text-blue-700', 'font-medium', 'shadow-sm');
        btnZip.classList.add('text-blue-600', 'hover:bg-blue-50');
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>
