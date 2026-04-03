<?php
$files = ['en.json', 'fr.json', 'ar.json'];
$base_path = 'resources/lang/';

foreach ($files as $file) {
    $content = file_get_contents($base_path . $file);
    json_decode($content);
    $status = json_last_error() === JSON_ERROR_NONE ? '✓ Valid' : '✗ ERROR: ' . json_last_error_msg();
    echo "$file: $status\n";
}
?>
