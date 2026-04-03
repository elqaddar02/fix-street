<?php
$content = file_get_contents('resources/lang/en.json');
$lines = file('resources/lang/en.json');

echo "Looking for JSON syntax errors by parsing progressively...\n\n";

// Start with minimal valid JSON
$test = "{\n";
$last_good = 1;

for ($i = 1; $i < count($lines); $i++) {
    $test .= $lines[$i];
    
    // Try to parse with a closing brace
    $test_full = $test . "}";
    $decoded = json_decode($test_full);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "Error at or before line " . ($i + 1) . "\n";
        echo "Error: " . json_last_error_msg() . "\n";
        echo "Last good line: $last_good\n";
        echo "\n===Lines around error===\n";
        for ($j = max(0, $i - 2); $j <= min(count($lines) - 1, $i + 1); $j++) {
            $line_num = $j + 1;
            $prefix = ($j === $i) ? ">>>" : "   ";
            echo "$prefix Line $line_num: ";
            echo str_replace("\n", "\n              ", trim($lines[$j]));
            echo "\n";
        }
        break;
    } else {
        $last_good = $i + 1;
    }
}

echo "\n\nLast successfully parsed: Line $last_good\n";
?>
