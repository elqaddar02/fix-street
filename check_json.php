<?php
$json = file_get_contents('resources/lang/fr.json');
$decoded = json_decode($json, true);
if (json_last_error() === JSON_ERROR_NONE) {
    echo "JSON is valid\n";
} else {
    echo "JSON Error: " . json_last_error_msg() . "\n";
}
?>
