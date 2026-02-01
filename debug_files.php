<?php
// Simple debug - check files for request #1
require_once __DIR__ . '/config/database.php';
$pdo = getConnection();

$requestId = isset($_GET['id']) ? (int)$_GET['id'] : 1;

echo "<h2>Files for Request #$requestId</h2>";

// Get files from database
$stmt = $pdo->prepare("SELECT * FROM request_files WHERE request_id = ?");
$stmt->execute([$requestId]);
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<p>Found " . count($files) . " file(s)</p>";

if (empty($files)) {
    echo "<p style='color:red'>No files in database!</p>";
    
    // Check disk
    $dir = __DIR__ . '/uploads/requests/' . $requestId;
    echo "<p>Checking disk at: $dir</p>";
    if (is_dir($dir)) {
        echo "<p style='color:green'>Directory exists!</p>";
        $diskFiles = glob($dir . '/*');
        foreach ($diskFiles as $f) {
            echo "<p>Found: " . basename($f) . "</p>";
        }
    } else {
        echo "<p style='color:red'>Directory does not exist</p>";
    }
} else {
    foreach ($files as $file) {
        $isImage = strpos($file['file_type'] ?? '', 'image') !== false;
        $path = $file['file_path'];
        $fullPath = __DIR__ . '/' . $path;
        $exists = file_exists($fullPath);
        
        echo "<div style='border:1px solid #ccc; padding:10px; margin:10px 0;'>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($file['file_name']) . "</p>";
        echo "<p><strong>Path:</strong> " . htmlspecialchars($path) . "</p>";
        echo "<p><strong>Type:</strong> " . htmlspecialchars($file['file_type']) . "</p>";
        echo "<p><strong>Is Image:</strong> " . ($isImage ? 'YES' : 'NO') . "</p>";
        echo "<p><strong>File Exists:</strong> " . ($exists ? '<span style="color:green">YES</span>' : '<span style="color:red">NO - ' . $fullPath . '</span>') . "</p>";
        
        if ($exists && $isImage) {
            echo "<p><strong>Preview:</strong></p>";
            echo "<img src='" . htmlspecialchars($path) . "' style='max-width:200px; max-height:150px; border:1px solid #ddd;'>";
        }
        echo "</div>";
    }
}
?>
