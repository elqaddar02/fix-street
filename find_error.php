<?php
$json = file_get_contents('resources/lang/fr.json');

// Try to find_which line has the problem by parsing line-by-line JSON structure
$lines = file('resources/lang/fr.json');

// Check for common JSON issues
echo "Checking for common issues:\n\n";

// 1. Check for unescaped quotes in keys and values
$issues = [];
for ($i = 0; $i < count($lines); $i++) {
    $line = $lines[$i];
    
    // Check for quotes that might not be escaped
    // This is tricky because we need to properly parse the JSON structure
    if (preg_match('/"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)":\s*"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"/', $line, $matches)) {
        // This line looks like a properly formed JSON key-value pair
        continue;
    }
}

// Let's try parsing the JSON and catching the actual error
echo "Attempting JSON parse with error catching...\n";

try {
    $json = json_decode(file_get_contents('resources/lang/fr.json'), true);
    echo "Success!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Let's check if there are any duplicate keys
$data = json_decode(file_get_contents('resources/lang/fr.json'), true, 512, JSON_INVALID_UTF8_IGNORE);
echo "\nJSON last error: " . json_last_error_msg() . "\n";

// Check encoding
echo "\nFile encoding check:\n";
$raw = file_get_contents('resources/lang/fr.json', false, null, 0, 500);
if (! mb_check_encoding($raw, 'UTF-8')) {
    echo "Possible encoding issue found\n";
} else {
    echo "Encoding appears to be UTF-8\n";
}

// Check for problematic characters
echo "\nSearching for problematic characters...\n";
$lines = file('resources/lang/fr.json');
for ($i = 0; $i < count($lines); $i++) {
    $line = $lines[$i];
    // Look for unescaped double quotes  within string values
    // Find patterns like ": "...something"text..."
    if (preg_match_all('/":\s*"([^"]*)"([^"]*)"/', $line, $matches)) {
        echo "Line " . ($i + 1) . " may have unescaped quote: " . trim($line) . "\n";
    }
}
?>
