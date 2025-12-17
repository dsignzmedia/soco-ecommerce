<?php
$logFile = __DIR__ . '/../storage/logs/laravel.log';
$search = 'Razorpay';
$matches = [];

if ($handle = fopen($logFile, "r")) {
    while (($line = fgets($handle)) !== false) {
        if (strpos($line, $search) !== false) {
            $matches[] = $line;
        }
    }
    fclose($handle);
}

// Show last 20 matches
$matches = array_slice($matches, -20);
foreach ($matches as $match) {
    echo $match;
}
