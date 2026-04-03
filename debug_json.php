<?php
$json = file_get_contents('resources/lang/fr.json');
$decoded = json_decode($json, true);

echo "File size: " . strlen($json) . " bytes\n";
echo "Last 200 characters:\n";
echo substr($json, -200) . "\n\n";

if (json_last_error() !== JSON_ERROR_NONE) {
    echo "JSON Error Code: " . json_last_error() . "\n";
    echo "JSON Error Message: " . json_last_error_msg() . "\n";
    
    // Try to find where the error is
    $lines = explode("\n", $json);
    echo "\nLast 10 lines:\n";
    $start = max(0, count($lines) - 10);
    for ($i = $start; $i < count($lines); $i++) {
        echo "Line " . ($i + 1) . ": " . $lines[$i] . "\n";
    }
}
?>
