<?php
header('Content-Type: application/json');
require_once '../database/config.php';

try {
    $backupDir = __DIR__ . '/backups/';
    $backupFiles = [];
    
    if (is_dir($backupDir)) {
        $files = glob($backupDir . 'ssi_bundle_*.sql*');
        foreach ($files as $file) {
            $backupFiles[] = [
                'name' => basename($file),
                'size' => filesize($file),
                'date' => filectime($file),
                'size_formatted' => formatBytes(filesize($file))
            ];
        }
        
        // Sort by date (newest first)
        usort($backupFiles, function($a, $b) {
            return $b['date'] - $a['date'];
        });
    }
    
    // Get total size of all backups
    $totalSize = 0;
    foreach ($backupFiles as $file) {
        $totalSize += $file['size'];
    }
    
    // Get latest backup info
    $latestBackup = !empty($backupFiles) ? $backupFiles[0] : null;
    
    echo json_encode([
        'success' => true,
        'total_backups' => count($backupFiles),
        'total_size' => formatBytes($totalSize),
        'latest_backup' => $latestBackup ? [
            'name' => $latestBackup['name'],
            'date' => date('Y-m-d H:i:s', $latestBackup['date']),
            'size' => $latestBackup['size_formatted']
        ] : null,
        'backup_files' => array_slice($backupFiles, 0, 5) // Return only last 5 backups
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error getting backup status: ' . $e->getMessage()
    ]);
}

function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}
?>
