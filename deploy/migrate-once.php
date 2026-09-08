<?php

/**
 * ONE-TIME migration runner for InfinityFree (no SSH / no artisan on server).
 *
 * 1. Deploy your site first via GitHub Actions.
 * 2. Upload this file to htdocs/migrate-once.php (or copy via FTP).
 * 3. Set MIGRATE_TOKEN below to match secret MIGRATE_TOKEN in GitHub (or a strong random string).
 * 4. Visit: https://yourdomain.infinityfreeapp.com/migrate-once.php?token=YOUR_TOKEN
 * 5. DELETE this file immediately after migrations succeed.
 */

$expectedToken = getenv('MIGRATE_TOKEN') ?: 'CHANGE_ME_BEFORE_USE';

if (($_GET['token'] ?? '') !== $expectedToken) {
    http_response_code(403);
    exit('Forbidden');
}

define('LARAVEL_START', microtime(true));

$corePath = __DIR__.'/laravel_core';

require $corePath.'/vendor/autoload.php';

$app = require_once $corePath.'/bootstrap/app.php';
$app->usePublicPath(__DIR__);

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo '<pre>';
echo "Running migrations...\n\n";
$exitCode = $kernel->call('migrate', ['--force' => true]);
echo $kernel->output();
echo "\nExit code: {$exitCode}\n";
echo "\nDELETE migrate-once.php from the server now.\n";
echo '</pre>';
