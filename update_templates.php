<?php
/**
 * Database Update Script - Add New Templates
 * Run this file once to add new templates to your database
 * URL: http://localhost/fildevstudio/update_templates.php
 */

require_once 'config/database.php';

echo "<h2>FilDevStudio - Template Update Script</h2>";
echo "<pre>";

try {
    $pdo = getConnection();
    
    // Step 1: Alter ENUM to include 'sarisari'
    echo "Step 1: Adding 'sarisari' category to ENUM...\n";
    $pdo->exec("ALTER TABLE templates MODIFY COLUMN category ENUM('retail', 'food', 'freelance', 'services', 'general', 'sarisari') NOT NULL");
    echo "✓ Category ENUM updated!\n\n";
    
    // Step 2: Check existing templates
    $stmt = $pdo->query("SELECT id, name FROM templates ORDER BY id");
    $existing = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    echo "Existing templates: " . count($existing) . "\n";
    print_r($existing);
    echo "\n";
    
    // Step 3: Define new templates to add
    $newTemplates = [
        6 => ['E-Commerce Starter', 'retail', 'Simple online store template with product showcase', 'assets/templates/ecommerce-starter.jpg', 'templates/ecommerce-starter'],
        7 => ['Urban Streetwear', 'retail', 'Bold streetwear brand template with dark aesthetic', 'assets/templates/urban-streetwear.jpg', 'templates/urban-streetwear'],
        8 => ['Tech Startup', 'services', 'Modern SaaS/startup template with gradient design', 'assets/templates/tech-startup.jpg', 'templates/tech-startup'],
        9 => ['Boutique Shop', 'retail', 'Elegant feminine template for fashion boutiques', 'assets/templates/boutique-shop.jpg', 'templates/boutique-shop'],
        10 => ['Electronics Store', 'retail', 'Dark tech-focused template for gadget shops', 'assets/templates/electronics-store.jpg', 'templates/electronics-store'],
        11 => ['Grocery & Supermarket', 'retail', 'Fresh green template for grocery stores', 'assets/templates/grocery-supermarket.jpg', 'templates/grocery-supermarket'],
        12 => ['Sari-Sari Store', 'sarisari', 'Colorful Filipino neighborhood store template with tingi prices', 'assets/templates/sari-sari-store.jpg', 'templates/sari-sari-store'],
        13 => ['Sari-Sari Plus', 'sarisari', 'Modern sari-sari with delivery and digital payments', 'assets/templates/sari-sari-plus.jpg', 'templates/sari-sari-plus'],
    ];
    
    // Step 4: Insert new templates (skip if already exists)
    echo "Step 2: Adding new templates...\n";
    $stmt = $pdo->prepare("INSERT INTO templates (id, name, category, description, preview_image, folder_path) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE name=VALUES(name), category=VALUES(category), description=VALUES(description)");
    
    $added = 0;
    foreach ($newTemplates as $id => $template) {
        if (!isset($existing[$id])) {
            $stmt->execute([$id, $template[0], $template[1], $template[2], $template[3], $template[4]]);
            echo "✓ Added: ID $id - {$template[0]} ({$template[1]})\n";
            $added++;
        } else {
            // Update existing
            $stmt->execute([$id, $template[0], $template[1], $template[2], $template[3], $template[4]]);
            echo "↺ Updated: ID $id - {$template[0]}\n";
        }
    }
    
    echo "\n✅ Done! Added/Updated $added templates.\n\n";
    
    // Step 5: Show final template list
    echo "Final template list:\n";
    echo str_repeat("-", 60) . "\n";
    $stmt = $pdo->query("SELECT id, name, category FROM templates ORDER BY id");
    while ($row = $stmt->fetch()) {
        printf("ID %2d: %-25s [%s]\n", $row['id'], $row['name'], $row['category']);
    }
    
    echo "\n</pre>";
    echo "<p style='color:green; font-weight:bold;'>✅ Database updated successfully!</p>";
    echo "<p><a href='templates.php'>← Go to Templates Gallery</a></p>";
    echo "<p style='color:red;'>⚠️ Delete this file (update_templates.php) after running!</p>";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "</pre>";
}
?>
