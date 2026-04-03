<?php
$en_content = file_get_contents('resources/lang/en.json');
$fr_content = file_get_contents('resources/lang/fr.json');

echo "=== ENGLISH FILE ===\n";
echo "First 100 chars:\n";
echo substr($en_content, 0, 100) . "\n\n";
echo "Last 100 chars:\n";
echo substr($en_content, -100) . "\n\n";

// Test decoding
$en_decoded = json_decode($en_content);
echo "EN Decode result: " . (is_null($en_decoded) ? "NULL" : "Object with " . count($en_decoded) . " keys") . "\n";
echo "EN Last error: " . json_last_error_msg() . "\n";
echo "EN Last error code: " . json_last_error() . "\n";

// Check for common issues
if (strpos($en_content, '"},') === false) {
    echo "WARNING: No '},  found - might be missing commas\n";
}
if (substr(trim($en_content), -1) !== '}') {
    echo "WARNING: File doesn't end with }\n";
}

echo "\n=== FRENCH FILE (first 200 chars) ===\n";
echo substr($fr_content, 0, 200) . "\n";
?>
