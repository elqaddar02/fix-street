<?php
// Read the file and try to find the exact error using a JSON validator
$json_str = file_get_contents('resources/lang/fr.json');

// Use a more sophisticated error locator
$errors = [];

// Try decoding with more verbose error reporting
json_decode($json_str,true);
$error_code = json_last_error();
$error_msg = json_last_error_msg();

echo "Error Code: $error_code\n";
echo "Error Message: $error_msg\n\n";

// Try to narrow down the error location
// Split JSON and find which part causes the error
$json_lines = explode("\n", $json_str);
$test_json = "{\n";

for ($i = 0; $i < count($json_lines); $i++) {
    if ($i == 0) continue; // Skip first line which is just {
    if ($i == count($json_lines) - 1) continue; // Skip last line which is just }
    
    $test_json .= $json_lines[$i] . "\n";
    
    // Try to close and parse
    $try = $test_json . "\n}";
    
    json_decode($try, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "Error found on or before line " . ($i + 1) . ": " . json_last_error_msg() . "\n";
        echo "Problem line content:\n";
        echo $json_lines[$i] . "\n";
        echo "\n";
        
        // Show the portion around the error
        echo "Context (lines " . max(1, $i-1) . " to " . ($i + 2) . "):\n";
        for ($j = max(0, $i-1); $j < min(count($json_lines), $i+2); $j++) {
            echo "Line " . ($j + 1) . ": " . $json_lines[$j] . "\n";
        }
        break;
    }
}
?>
