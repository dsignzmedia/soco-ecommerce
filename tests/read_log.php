<?php
$logFile = __DIR__ . '/../storage/logs/laravel.log';
if (!file_exists($logFile)) {
    echo "Log file not found.";
    exit;
}

$lines = 50;
$handle = fopen($logFile, "r");
$linecount = 0;
$pos = -2;
$text = " ";

while ($linecount < $lines) {
    fseek($handle, $pos, SEEK_END);
    $char = fgetc($handle);
    if ($pos == -10000) break; // limit lookback
    if ($char == "\n") {
        $linecount++;
    }
    $pos--;
}

while ($char = fgetc($handle)) {
    echo $char;
}
fclose($handle);
