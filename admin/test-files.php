<?php
/**
 * Quick test to debug file display issue
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/database.php';
$pdo = getConnection();

$requestId = isset($_GET['id']) ? (int)$_GET['id'] : 1;

echo "<h1>Debug File Display for Request #$requestId</h1>";

// 1. Check upload directory
$uploadDir = __DIR__ . '/../uploads/requests/' . $requestId;
echo "<h2>1. Upload Directory</h2>";
echo "<p>Path: $uploadDir</p>";
echo "<p>Exists: " . (is_dir($uploadDir) ? '<span style="color:green">YES</span>' : '<span style="color:red">NO</span>') . "</p>";

if (is_dir($uploadDir)) {
    echo "<h3>Files on disk:</h3><ul>";
    $files = glob($uploadDir . '/*');
    if (empty($files)) {
        echo "<li>No files found</li>";
    } else {
        foreach ($files as $file) {
            $name = basename($file);
            $size = filesize($file);
            $mime = mime_content_type($file);
            echo "<li>$name (Size: " . number_format($size/1024, 1) . " KB, MIME: $mime)</li>";
        }
    }
    echo "</ul>";
}

// 2. Check if table exists
echo "<h2>2. Database Table Check</h2>";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'request_files'");
    $tableExists = $stmt->rowCount() > 0;
    echo "<p>Table 'request_files' exists: " . ($tableExists ? '<span style="color:green">YES</span>' : '<span style="color:red">NO</span>') . "</p>";
    
    if ($tableExists) {
        $stmt = $pdo->query("DESCRIBE request_files");
        echo "<h3>Table structure:</h3><pre>";
        print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
        echo "</pre>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}

// 3. Check entries for this request
echo "<h2>3. Database Entries for Request #$requestId</h2>";
try {
    $stmt = $pdo->prepare("SELECT * FROM request_files WHERE request_id = ?");
    $stmt->execute([$requestId]);
    $files = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<p>Found " . count($files) . " file(s) in database</p>";
    if (!empty($files)) {
        echo "<pre>";
        print_r($files);
        echo "</pre>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}

// 4. Try to manually scan and insert
echo "<h2>4. Manual Scan & Insert Test</h2>";
if (is_dir($uploadDir)) {
    $insertCount = 0;
    $files = glob($uploadDir . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            $filename = basename($file);
            $relativePath = 'uploads/requests/' . $requestId . '/' . $filename;
            
            // Check if exists
            $stmt = $pdo->prepare("SELECT id FROM request_files WHERE request_id = ? AND file_path = ?");
            $stmt->execute([$requestId, $relativePath]);
            if ($stmt->fetch()) {
                echo "<p>Already in DB: $relativePath</p>";
                continue;
            }
            
            // Insert
            try {
                $stmt = $pdo->prepare("INSERT INTO request_files (request_id, file_path, original_name, file_name, file_size, mime_type) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $requestId,
                    $relativePath,
                    $filename,
                    $filename,
                    filesize($file),
                    mime_content_type($file)
                ]);
                echo "<p style='color:green'>Inserted: $relativePath</p>";
                $insertCount++;
            } catch (Exception $e) {
                echo "<p style='color:red'>Failed to insert: " . $e->getMessage() . "</p>";
            }
        }
    }
    echo "<p><strong>Inserted $insertCount new file(s)</strong></p>";
} else {
    echo "<p>Upload directory doesn't exist</p>";
}

// 5. Final check
echo "<h2>5. Final Check - Files in DB</h2>";
try {
    $stmt = $pdo->prepare("SELECT * FROM request_files WHERE request_id = ?");
    $stmt->execute([$requestId]);
    $files = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<p>Total files: " . count($files) . "</p>";
    foreach ($files as $f) {
        $fullPath = __DIR__ . '/../' . $f['file_path'];
        $exists = file_exists($fullPath);
        echo "<div style='margin: 10px 0; padding: 10px; background: #f5f5f5; border-radius: 5px;'>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($f['file_path']) . "</p>";
        echo "<p><strong>Exists on disk:</strong> " . ($exists ? 'YES' : 'NO') . "</p>";
        if ($exists && strpos($f['mime_type'] ?? '', 'image') !== false) {
            echo "<p><img src='../" . htmlspecialchars($f['file_path']) . "' style='max-width: 200px; max-height: 150px;'></p>";
        }
        echo "</div>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}

echo "<hr><p><a href='requests.php?view=$requestId'>Go back to Request View</a></p>";
?>
