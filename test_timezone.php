<?php
// Set timezone to UTC for consistency
date_default_timezone_set('UTC');

$currentTime = time();
$serverTime = date('M j, Y g:i A', $currentTime);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timezone Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold mb-6">Timezone Test</h1>
        
        <div class="space-y-4">
            <div class="bg-blue-50 p-4 rounded-lg">
                <h2 class="font-semibold text-blue-800">Server Time (UTC)</h2>
                <p class="text-blue-600"><?php echo $serverTime; ?></p>
                <p class="text-sm text-blue-500">Unix timestamp: <?php echo $currentTime; ?></p>
            </div>
            
            <div class="bg-green-50 p-4 rounded-lg">
                <h2 class="font-semibold text-green-800">Client Time (Your Local Timezone)</h2>
                <p class="text-green-600" id="clientTime"></p>
                <p class="text-sm text-green-500" id="clientTimezone"></p>
            </div>
            
            <div class="bg-purple-50 p-4 rounded-lg">
                <h2 class="font-semibold text-purple-800">Converted Time</h2>
                <p class="text-purple-600" id="convertedTime"></p>
                <p class="text-sm text-purple-500">Server time converted to your local timezone</p>
            </div>
        </div>
        
        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <h3 class="font-semibold mb-2">How it works:</h3>
            <ul class="text-sm text-gray-600 space-y-1">
                <li>• Server stores times in UTC for consistency</li>
                <li>• JavaScript converts UTC times to your local timezone</li>
                <li>• Your browser automatically detects your timezone</li>
                <li>• Times are displayed with timezone abbreviation</li>
            </ul>
        </div>
    </div>

    <script>
        function updateTimes() {
            const now = new Date();
            const clientTime = now.toLocaleString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
            
            const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            const timezoneAbbr = new Intl.DateTimeFormat('en-US', { timeZoneName: 'short' }).formatToParts(now)
                .find(part => part.type === 'timeZoneName')?.value || timezone;
            
            // Convert server timestamp to local time
            const serverTimestamp = <?php echo $currentTime; ?>;
            const serverDate = new Date(serverTimestamp * 1000);
            const convertedTime = serverDate.toLocaleString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            }) + ' (' + timezoneAbbr + ')';
            
            document.getElementById('clientTime').textContent = clientTime;
            document.getElementById('clientTimezone').textContent = 'Timezone: ' + timezone;
            document.getElementById('convertedTime').textContent = convertedTime;
        }
        
        // Update times when page loads
        updateTimes();
        
        // Update every second
        setInterval(updateTimes, 1000);
    </script>
</body>
</html>
