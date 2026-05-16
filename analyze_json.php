<?php
$json = file_get_contents('resources/lang/fr.json');

// Find the last occurrence of a double quote followed by optional whitespace, then comma, then optional whitespace, closing brace
$lastQuotePos = strrpos($json, '"');
echo "Last quote position: " . $lastQuotePos . "\n";
echo "Characters from last quote to end:\n";
echo bin2hex(substr($json, $lastQuotePos, 50)) . "\n\n";
echo "Readable:\n";
echo substr($json, $lastQuotePos, 50) . "\n";

// Check for trailing comma before closing brace
if (preg_match('/",\s*}/', $json)) {
    echo "\n❌ Found trailing comma before closing brace\n";
} else {
    echo "\n✓ No trailing comma found (good)\n";
}

// Let's check the actual line count and structure
$lines = file('resources/lang/fr.json');
echo "\nTotal lines: " . count($lines) . "\n";
echo "Last 3 lines:\n";
for ($i = max(0, count($lines) - 3); $i < count($lines); $i++) {
    echo "Line " . ($i + 1) . ": " . bin2hex($lines[$i]) . "\n";
    echo "       " . trim($lines[$i]) . "\n";
}
?>
