<?php
// Read the raw file content
$raw = file_get_contents('resources/lang/en.json');

echo "Raw file size: " . strlen($raw) . " bytes\n";
echo "First 500 bytes hex:\n";
echo bin2hex(substr($raw, 0, 500)) . "\n\n";

// Decode directly
$decoded = json_decode($raw);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Decode ERROR: " . json_last_error_msg() . "\n";
    echo "Error code: " . json_last_error() . "\n";
    
    // Let's check if there's a BOM
    $first_3_bytes = bin2hex(substr($raw, 0, 3));
    echo "First 3 bytes: " . $first_3_bytes . "\n";
    if ($first_3_bytes === 'efbbbf') {
        echo "⚠️ UTF-8 BOM detected! This might be the problem.\n";
        echo "Trying to remove BOM...\n";
        $raw_no_bom = substr($raw, 3);
        $decoded = json_decode($raw_no_bom);
        echo "After BOM removal: " . (json_last_error() === 0 ? "SUCCESS" : "Still ERROR: " . json_last_error_msg()) . "\n";
    }
} else {
    echo "Decode: SUCCESS\n";
}
?>
