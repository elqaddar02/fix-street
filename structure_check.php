<?php
// Let's check if maybe these files got truncated or corrupted
// Try to identify exactly which key-value pair is causing the issue

$files = ['en.json' => 'resources/lang/en.json', 'fr.json' => 'resources/lang/fr.json', 'ar.json' => 'resources/lang/ar.json'];

foreach ($files as $name => $path) {
    echo "\n=== $name ===\n";
    
    $content = file_get_contents($path);
    
    // Count opening and closing braces
    $open_braces = substr_count($content, '{');
    $close_braces = substr_count($content, '}');
    $commas = substr_count($content, ',');
    $colons = substr_count($content, ':');
    $quotes = substr_count($content, '"');
    
    echo "Opening braces: $open_braces\n";
    echo "Closing braces: $close_braces\n";
    echo "Commas: $commas\n";
    echo "Colons: $colons\n";
    echo "Quotes: $quotes\n";
    echo "Quote count is even: " . ($quotes % 2 === 0 ? "YES" : "NO") . "\n";
    
    // Try to find mismatched quotes or structures
    if ($open_braces !== $close_braces && $open_braces === 1 && $close_braces === 1) {
        echo "✓ Brace count OK\n";
    } else if ($open_braces !== 1 || $close_braces !== 1) {
        echo "✗ BRACE COUNT WRONG: Expected 1 opening and 1 closing\n";
    }
    
    // Check file encoding
    $first_bytes = substr($content, 0, 10);
    echo "First 10 bytes: " . bin2hex($first_bytes) . "\n";
}

// Now let's try manually building JSON from scratch
echo "\n=== MANUAL JSON BUILD TEST ===\n";
$simple_json = json_encode([
    "test1" => "value1",
    "test2" => "value2"
]);
echo "Manually built: " . $simple_json . "\n";
$decoded = json_decode($simple_json);
echo "Decode result: " . (json_last_error() === 0 ? "OK" : "ERROR") . "\n";
?>
