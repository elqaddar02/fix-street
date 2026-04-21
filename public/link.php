<?php
/**
 * InfinityFree Symlink Workaround
 * 
 * Since we cannot run 'php artisan storage:link' via SSH, 
 * visit this file in your browser: http://your-domain.com/link.php
 */

$target = __DIR__ . '/../laravel/storage/app/public';
$link = __DIR__ . '/storage';

// Check if link already exists
if (is_link($link)) {
    echo "Symlink already exists.";
} else {
    if (symlink($target, $link)) {
        echo "<h1>Success!</h1>";
        echo "<p>The storage symlink has been created successfully.</p>";
        echo "<p><strong>IMPORTANT:</strong> Delete this file (link.php) from your server now for security.</p>";
    } else {
        echo "<h1>Error</h1>";
        echo "<p>Failed to create symlink. Check folder permissions.</p>";
    }
}
