<?php
// Read the file and examine the first few lines in detail
$content = file_get_contents('resources/lang/en.json');
$lines = file('resources/lang/en.json');

echo "===FIRST 5 LINES HEX DUMP===\n";
for ($i = 0; $i < 5 && $i < count($lines); $i++) {
    $line = $lines[$i];
    echo "\nLine " . ($i + 1) . " (length: " . strlen($line) . " bytes):\n";
    echo "Hex: " . bin2hex($line) . "\n";
    echo "Text: " . json_encode($line) . "\n";
}

// Try parsing just the first key-value pair
echo "\n\n===TEST PARSING===\n";
$test1 = '{"Madinova - Official City Street Maintenance Portal": "Madinova - Official City Street Maintenance Portal"}';
$r1 = json_decode($test1);
echo "Simple test: " . (json_last_error() === 0 ? "OK" : "ERROR: " . json_last_error_msg()) . "\n";

// Try with actual line 2 content (without the comma)
$line2_clean = trim($lines[1]);
$line2_clean = rtrim($line2_clean, ',');
$test2 = '{' . "\n" . $line2_clean . "\n" . '}';
$r2 = json_decode($test2);
echo "Test with actual line 2: " . (json_last_error() === 0 ? "OK" : "ERROR: " . json_last_error_msg()) . "\n";

// Try with the actual first two lines
$test3 = '{' . "\n" . trim($lines[1]) . "\n" . trim($lines[2]) . "\n}";
$r3 = json_decode($test3);
echo "Test with lines 1-2: " . (json_last_error() === 0 ? "OK" : "ERROR: " . json_last_error_msg()) . "\n";
echo "Test 3 input:\n" . json_encode($test3) . "\n";
?>
