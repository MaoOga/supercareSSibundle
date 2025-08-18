<?php
require_once 'config.php';

// Set timezone to UTC for consistency
date_default_timezone_set('UTC');

// Handle file download
if (isset($_GET['download']) && !empty($_GET['download'])) {
    $filename = basename($_GET['download']); // Sanitize filename
    $filepath = __DIR__ . '/backups/' . $filename;
    
    if (file_exists($filepath) && strpos($filename, 'ssi_bundle_') === 0) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    }
}

// Handle file deletion
if (isset($_POST['delete']) && !empty($_POST['delete'])) {
    $filename = basename($_POST['delete']); // Sanitize filename
    $filepath = __DIR__ . '/backups/' . $filename;
    
    if (file_exists($filepath) && strpos($filename, 'ssi_bundle_') === 0) {
        $fileSize = formatBytes(filesize($filepath));
        
        if (unlink($filepath)) {
            $deleteMessage = "Backup file deleted successfully.";
            
            // Log audit entry
            require_once 'audit_logger.php';
            $auditLogger = new AuditLogger($pdo);
            $auditLogger->logBackupDelete('ADMIN', $filepath, $fileSize);
        } else {
            $deleteError = "Failed to delete backup file.";
        }
    }
}

// Get backup files
$backupDir = __DIR__ . '/backups/';
$backupFiles = [];

if (is_dir($backupDir)) {
    $files = glob($backupDir . 'ssi_bundle_*.sql*');
    foreach ($files as $file) {
        $backupFiles[] = [
            'name' => basename($file),
            'size' => filesize($file),
            'date' => filectime($file),
            'path' => $file
        ];
    }
    
    // Sort by date (newest first)
    usort($backupFiles, function($a, $b) {
        return $b['date'] - $a['date'];
    });
}

function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SSI Bundle - Backup Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        :root {
            --bg-body: #f3f4f6;
            --bg-header: #ffffff;
            --bg-card: #ffffff;
            --bg-table-header: #fee2e2;
            --text-primary: #111827;
            --text-secondary: #6b7280;
            --text-icon: #dc2626;
            --border-primary: #fecaca;
            --border-secondary: #e5e7eb;
            --button-bg: #dc2626;
            --button-bg-hover: #b91c1c;
            --button-text: #ffffff;
            --link-text: #dc2626;
            --link-text-hover: #b91c1c;
            --table-row-hover: #fef2f2;
            --dropdown-bg: #ffffff;
            --dropdown-border: #e5e7eb;
            --dropdown-hover-bg: #fee2e2;
            --dropdown-hover-text: #b91c1c;
            --label-color: #ef4444;
            --search-icon: #9ca3af;
            --clear-button-bg: #e5e7eb;
            --clear-button-bg-hover: #d1d5db;
            --clear-button-text: #374151;
        }
        body {
            font-family: "Inter", sans-serif;
            background-color: var(--bg-body);
            color: var(--text-primary);
            overflow-x: hidden;
        }
        .mobile-bottom-nav {
            -webkit-tap-highlight-color: transparent;
            backdrop-filter: blur(4px);
        }
        .mobile-bottom-nav .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 10px 6px;
            color: var(--text-primary);
            text-decoration: none;
        }
        .mobile-bottom-nav .nav-item i {
            font-size: 18px;
            line-height: 1;
        }
        .mobile-bottom-nav .nav-item span {
            font-size: 11px;
            margin-top: 2px;
        }
        .mobile-bottom-nav .nav-item.is-active {
            color: var(--button-bg);
        }
        @media (max-width: 640px) {
            .container {
                padding-bottom: 80px;
            }
            .header-buttons {
                flex-direction: column;
                gap: 0.5rem;
            }
            .header-buttons a,
            .header-buttons button {
                width: 100%;
                justify-content: center;
            }
            header .max-w-7xl {
                padding-top: 0.75rem !important;
                padding-bottom: 0.75rem !important;
            }
            header img[alt="Hospital Logo"] {
                height: 56px !important;
                width: 56px !important;
            }
            header h1 {
                font-size: 1.125rem !important;
                line-height: 1.5 !important;
            }
            header p {
                font-size: 0.875rem !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Header -->
    <header
        class="shadow-lg sticky top-0 z-20 border-b"
        style="
            background-color: var(--bg-header);
            border-color: var(--border-primary);
        "
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <img
                        src="supercare-hospital_logo.png"
                        alt="Hospital Logo"
                        style="height: 70px; width: 70px"
                    />
                    <div>
                        <h1
                            class="text-xl sm:text-2xl font-bold"
                            style="color: var(--text-primary)"
                        >
                            Supercare Hospital SSI Backup Manager
                        </h1>
                        <p
                            class="text-sm hidden sm:block"
                            style="color: var(--text-secondary)"
                        >
                            Manage database backups and system maintenance
                        </p>
                    </div>
                </div>
                <div class="hidden sm:flex items-center gap-3 sm:gap-4">

                    <a
                        href="admin.html"
                        class="px-3 sm:px-4 py-2 rounded-lg flex items-center justify-center gap-2 text-sm sm:text-base hover:bg-[var(--button-bg-hover)]"
                        style="
                            background-color: var(--button-bg);
                            color: var(--button-text);
                        "
                    >
                        <i class="fas fa-shield-halved"></i>
                        <span>Admin Panel</span>
                    </a>
                    <a
                        href="audit_log.html"
                        class="px-3 sm:px-4 py-2 rounded-lg flex items-center justify-center gap-2 text-sm sm:text-base hover:bg-[var(--button-bg-hover)]"
                        style="
                            background-color: var(--button-bg);
                            color: var(--button-text);
                        "
                    >
                        <i class="fas fa-chart-line"></i>
                        <span>Audit Log</span>
                    </a>
                    <button
                        onclick="createBackup()"
                        class="px-3 sm:px-4 py-2 rounded-lg flex items-center justify-center gap-2 text-sm sm:text-base hover:bg-green-600 transition-colors"
                        style="background-color: #10b981; color: white;"
                    >
                        <i class="fas fa-plus"></i>
                        <span>Create Backup</span>
                    </button>
                    <button
                        class="px-3 sm:px-4 py-2 rounded-lg flex items-center justify-center gap-2 text-sm sm:text-base border"
                        style="
                            border-color: var(--border-secondary);
                            color: var(--text-primary);
                        "
                    >
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8">
        <!-- Mobile Header Buttons -->
        <div class="sm:hidden flex flex-col gap-2 mb-6">
            <button onclick="createBackup()" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Create Backup
            </button>
            <a href="audit_log.html" class="bg-purple-500 text-white px-4 py-2 rounded-lg hover:bg-purple-600 transition-colors text-center">
                <i class="fas fa-chart-line mr-2"></i>
                Audit Log
            </a>
        </div>

        <!-- Messages -->
        <?php if (isset($deleteMessage)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <i class="fas fa-check-circle mr-2"></i>
                <?php echo $deleteMessage; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($deleteError)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <?php echo $deleteError; ?>
            </div>
        <?php endif; ?>

        <!-- Backup Files Table -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Backup Files</h2>
                <p class="text-sm text-gray-600">Total backups: <?php echo count($backupFiles); ?></p>
            </div>
            
            <?php if (empty($backupFiles)): ?>
                <div class="p-8 text-center">
                    <i class="fas fa-database text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">No backup files found.</p>
                    <p class="text-sm text-gray-400 mt-2">Create your first backup to get started.</p>
                </div>
            <?php else: ?>
                <!-- Desktop Table View -->
                <div class="hidden sm:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($backupFiles as $file): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i class="fas fa-file-archive text-blue-500 mr-3"></i>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900"><?php echo $file['name']; ?></div>
                                                <div class="text-sm text-gray-500">Database backup</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo formatBytes($file['size']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="server-time" data-timestamp="<?php echo $file['date']; ?>">
                                            <?php echo date('M j, Y g:i A', $file['date']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex gap-2">
                                            <a href="?download=<?php echo urlencode($file['name']); ?>" 
                                               class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-download mr-1"></i>
                                                Download
                                            </a>
                                            <button onclick="deleteBackup('<?php echo $file['name']; ?>')" 
                                                    class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash mr-1"></i>
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="sm:hidden space-y-4 p-4">
                    <?php foreach ($backupFiles as $file): ?>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center">
                                    <i class="fas fa-file-archive text-blue-500 mr-3 text-lg"></i>
                                    <div>
                                        <div class="font-medium text-gray-900 text-sm"><?php echo $file['name']; ?></div>
                                        <div class="text-xs text-gray-500">Database backup</div>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Size:</span>
                                    <span class="font-medium"><?php echo formatBytes($file['size']); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Created:</span>
                                    <span class="font-medium server-time" data-timestamp="<?php echo $file['date']; ?>">
                                        <?php echo date('M j, Y g:i A', $file['date']); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="flex gap-2 mt-4">
                                <a href="?download=<?php echo urlencode($file['name']); ?>" 
                                   class="flex-1 bg-blue-500 text-white text-center py-2 px-3 rounded text-sm hover:bg-blue-600 transition-colors">
                                    <i class="fas fa-download mr-1"></i>
                                    Download
                                </a>
                                <button onclick="deleteBackup('<?php echo $file['name']; ?>')" 
                                        class="flex-1 bg-red-500 text-white py-2 px-3 rounded text-sm hover:bg-red-600 transition-colors">
                                    <i class="fas fa-trash mr-1"></i>
                                    Delete
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Backup Log -->
        <div class="mt-8 bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Backup Log</h2>
            </div>
            <div class="p-6">
                <?php
                $logFile = __DIR__ . '/backup_log.log';
                if (file_exists($logFile)) {
                    $logContent = file_get_contents($logFile);
                    $logLines = array_slice(explode("\n", $logContent), -20); // Show last 20 lines
                    $logLines = array_filter($logLines); // Remove empty lines
                    
                    if (!empty($logLines)) {
                        echo '<div class="bg-gray-900 text-green-400 p-4 rounded font-mono text-sm overflow-x-auto">';
                        foreach ($logLines as $line) {
                            echo htmlspecialchars($line) . "\n";
                        }
                        echo '</div>';
                    } else {
                        echo '<p class="text-gray-500">No log entries found.</p>';
                    }
                } else {
                    echo '<p class="text-gray-500">Backup log file not found.</p>';
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="flex items-center mb-4">
                <i class="fas fa-exclamation-triangle text-red-500 text-2xl mr-3"></i>
                <h3 class="text-lg font-semibold text-gray-900">Confirm Delete</h3>
            </div>
            <p class="text-gray-600 mb-6">Are you sure you want to delete this backup file? This action cannot be undone.</p>
            <div class="flex justify-end gap-3">
                <button onclick="closeDeleteModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                    Cancel
                </button>
                <form id="deleteForm" method="POST" class="inline">
                    <input type="hidden" name="delete" id="deleteFileName">
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function createBackup() {
            const button = event.target;
            const originalText = button.innerHTML;
            
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating Backup...';
            button.disabled = true;
            
            fetch('backup_system.php?ajax=1')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Backup created successfully!\nFile: ' + data.file + '\nSize: ' + data.size);
                        location.reload();
                    } else {
                        alert('Backup failed: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while creating the backup.');
                })
                .finally(() => {
                    button.innerHTML = originalText;
                    button.disabled = false;
                });
        }

        function deleteBackup(filename) {
            document.getElementById('deleteFileName').value = filename;
            document.getElementById('deleteModal').classList.remove('hidden');
            document.getElementById('deleteModal').classList.add('flex');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.getElementById('deleteModal').classList.remove('flex');
        }

        // Close modal when clicking outside
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });

        // Convert server times to local timezone
        function convertServerTimesToLocal() {
            const serverTimeElements = document.querySelectorAll('.server-time');
            
            serverTimeElements.forEach(element => {
                const timestamp = parseInt(element.getAttribute('data-timestamp'));
                if (timestamp) {
                    const date = new Date(timestamp * 1000); // Convert Unix timestamp to milliseconds
                    const localTime = date.toLocaleString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                        hour: 'numeric',
                        minute: '2-digit',
                        hour12: true
                    });
                    
                    // Add timezone indicator
                    const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
                    const timezoneAbbr = getTimezoneAbbreviation(timezone);
                    
                    element.textContent = localTime + ' (' + timezoneAbbr + ')';
                    element.title = 'Server time converted to your local timezone: ' + timezone;
                }
            });
        }

        // Get timezone abbreviation
        function getTimezoneAbbreviation(timezone) {
            const date = new Date();
            const options = { timeZoneName: 'short' };
            return new Intl.DateTimeFormat('en-US', options).formatToParts(date)
                .find(part => part.type === 'timeZoneName')?.value || timezone;
        }

        // Convert times when page loads
        document.addEventListener('DOMContentLoaded', function() {
            convertServerTimesToLocal();
        });
    </script>

    <!-- Mobile Bottom Navigation -->
    <nav
        class="mobile-bottom-nav sm:hidden fixed bottom-0 left-0 right-0 border-t shadow-lg z-30"
        style="
            background-color: var(--bg-card);
            border-color: var(--border-secondary);
            padding-bottom: max(env(safe-area-inset-bottom), 8px);
        "
    >
        <div class="grid grid-cols-4 items-stretch">
            <a href="admin.html" class="nav-item">
                <i class="fas fa-shield-halved"></i>
                <span>Admin</span>
            </a>
            <a href="backup_manager.php" class="nav-item is-active">
                <i class="fas fa-database"></i>
                <span>Backup</span>
            </a>
            <a href="audit_log.html" class="nav-item">
                <i class="fas fa-chart-line"></i>
                <span>Audit</span>
            </a>
            <a href="logout.php" class="nav-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </nav>
</body>
</html>
