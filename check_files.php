<?php
require_once __DIR__ . '/config/database.php';
$pdo = getConnection();
$stmt = $pdo->query('SELECT * FROM request_files');
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Total files in DB: " . count($files) . "\n";
foreach($files as $f) {
    echo "Request #{$f['request_id']}: {$f['file_path']} (type: {$f['file_type']})\n";
}
