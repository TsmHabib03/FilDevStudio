<?php
/**
 * Debug and Fix Request Files
 * Run this to check and fix the request_files table
 */
require_once __DIR__ . '/../config/database.php';

$pdo = getConnection();

echo "<h1>Request Files Debug</h1>";

// 1. Check if request_files table exists and show its structure
echo "<h2>1. Table Structure</h2>";
try {
    $stmt = $pdo->query("DESCRIBE request_files");
    echo "<pre>";
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    echo "</pre>";
} catch (Exception $e) {
    echo "<p style='color:red'>Table doesn't exist! Creating...</p>";
    $pdo->exec("CREATE TABLE IF NOT EXISTS request_files (
        id INT AUTO_INCREMENT PRIMARY KEY,
        request_id INT NOT NULL,
        file_name VARCHAR(255) NOT NULL,
        file_path VARCHAR(500) NOT NULL,
        file_type VARCHAR(100),
        file_size INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_request_files (request_id)
    ) ENGINE=InnoDB");
    echo "<p style='color:green'>Table created!</p>";
}

// 2. Show current entries in request_files
echo "<h2>2. Current Entries in request_files</h2>";
try {
    $stmt = $pdo->query("SELECT * FROM request_files ORDER BY request_id, id");
    $files = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($files);
    echo "</pre>";
    echo "<p>Total entries: " . count($files) . "</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}

// 3. Check custom_requests table
echo "<h2>3. Custom Requests</h2>";
try {
    $stmt = $pdo->query("SELECT id, reference_number, description, status, created_at FROM custom_requests ORDER BY id DESC LIMIT 10");
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($requests);
    echo "</pre>";
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}

// 4. Scan uploads/requests folder and show what files exist
echo "<h2>4. Files on Disk (uploads/requests/)</h2>";
$uploadDir = __DIR__ . '/../uploads/requests/';
if (is_dir($uploadDir)) {
    $folders = glob($uploadDir . '*', GLOB_ONLYDIR);
    foreach ($folders as $folder) {
        $requestId = basename($folder);
        echo "<h3>Request ID: $requestId</h3>";
        $files = glob($folder . '/*');
        if (empty($files)) {
            echo "<p>No files</p>";
        } else {
            echo "<ul>";
            foreach ($files as $file) {
                $filename = basename($file);
                $relPath = 'uploads/requests/' . $requestId . '/' . $filename;
                $filesize = filesize($file);
                $filetype = mime_content_type($file);
                echo "<li>";
                echo "$filename (Size: " . number_format($filesize/1024, 1) . "KB, Type: $filetype)";
                echo " - <a href='../$relPath' target='_blank'>View</a>";
                echo "</li>";
            }
            echo "</ul>";
        }
    }
} else {
    echo "<p style='color:red'>Upload directory doesn't exist!</p>";
}

// 5. Option to sync files to database
if (isset($_GET['sync'])) {
    echo "<h2>5. Syncing Files to Database...</h2>";
    $folders = glob($uploadDir . '*', GLOB_ONLYDIR);
    $synced = 0;
    
    foreach ($folders as $folder) {
        $requestId = (int)basename($folder);
        if ($requestId <= 0) continue;
        
        // Check if request exists
        $stmt = $pdo->prepare("SELECT id FROM custom_requests WHERE id = ?");
        $stmt->execute([$requestId]);
        if (!$stmt->fetch()) {
            echo "<p style='color:orange'>Request ID $requestId doesn't exist in database, skipping</p>";
            continue;
        }
        
        $files = glob($folder . '/*');
        foreach ($files as $file) {
            $filename = basename($file);
            $relPath = 'uploads/requests/' . $requestId . '/' . $filename;
            
            // Check if already in database
            $stmt = $pdo->prepare("SELECT id FROM request_files WHERE request_id = ? AND file_path = ?");
            $stmt->execute([$requestId, $relPath]);
            if ($stmt->fetch()) {
                echo "<p>Already exists: $relPath</p>";
                continue;
            }
            
            // Insert into database
            $stmt = $pdo->prepare("INSERT INTO request_files (request_id, file_name, file_path, file_type, file_size, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([
                $requestId,
                $filename,
                $relPath,
                mime_content_type($file),
                filesize($file)
            ]);
            echo "<p style='color:green'>Synced: $relPath</p>";
            $synced++;
        }
    }
    echo "<h3>Synced $synced files!</h3>";
} else {
    echo "<h2>5. Sync Files</h2>";
    echo "<p><a href='?sync=1' style='padding: 10px 20px; background: #3B82F6; color: white; text-decoration: none; border-radius: 5px;'>Click here to sync files from disk to database</a></p>";
}

// 6. Check admin_notifications
echo "<h2>6. Admin Notifications</h2>";
try {
    $stmt = $pdo->query("SELECT * FROM admin_notifications ORDER BY created_at DESC LIMIT 5");
    $notifs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($notifs);
    echo "</pre>";
} catch (Exception $e) {
    echo "<p style='color:red'>Error or table doesn't exist: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; padding: 20px; max-width: 1200px; margin: 0 auto; }
h1 { color: #1E40AF; }
h2 { color: #3B82F6; border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-top: 30px; }
pre { background: #f5f5f5; padding: 15px; overflow-x: auto; border-radius: 5px; }
</style>
