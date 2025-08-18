<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Get all possible IP addresses
function getAllIPs() {
    $ips = [];
    
    // Common IP headers to check
    $headers = [
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_REAL_IP', 
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'
    ];
    
    foreach ($headers as $header) {
        if (isset($_SERVER[$header])) {
            $ipList = explode(',', $_SERVER[$header]);
            foreach ($ipList as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    $ips[] = $ip;
                }
            }
        }
    }
    
    // Remove duplicates and return
    return array_unique($ips);
}

// Get IP type
function getIPType($ip) {
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        return 'IPv4';
    } elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
        return 'IPv6';
    } else {
        return 'Unknown';
    }
}

// Check if IP is private/local
function isPrivateIP($ip) {
    return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
}

try {
    $allIPs = getAllIPs();
    $ipInfo = [];
    
    foreach ($allIPs as $ip) {
        $ipInfo[] = [
            'ip' => $ip,
            'type' => getIPType($ip),
            'is_private' => isPrivateIP($ip),
            'description' => isPrivateIP($ip) ? 'Private/Local Network' : 'Public Internet'
        ];
    }
    
    // Get the most likely public IP
    $publicIPs = array_filter($ipInfo, function($info) {
        return !$info['is_private'];
    });
    
    $recommendedIP = null;
    if (!empty($publicIPs)) {
        $recommendedIP = reset($publicIPs);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'IP Address Information',
        'all_ips' => $ipInfo,
        'recommended_public_ip' => $recommendedIP,
        'note' => 'Use the recommended public IP for the secure super admin system',
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error getting IP information: ' . $e->getMessage()
    ]);
}
?>
