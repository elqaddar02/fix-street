<?php
// unzip.php
// Secure unpacker for uploaded laravel_core.zip archives on shared hosts

// -----------------------------
// CONFIGURATION
// -----------------------------
// Your GitHub UNZIP_TOKEN secret value matches this exactly
$EXPECTED_TOKEN = 'MySuperSecretKey123';

// Path to the zip file (defaults to parent directory)
$ZIP_PATH = __DIR__ . '/../laravel_core.zip';

// Also allow alternate locations (web root or a deploy_zip folder) — script will pick the first found
$ZIP_CANDIDATES = [
    __DIR__ . '/../laravel_core.zip',
    __DIR__ . '/laravel_core.zip',
    __DIR__ . '/../deploy_zip/laravel_core.zip',
    __DIR__ . '/deploy_zip/laravel_core.zip',
];

// Target directory to extract into (defaults to parent/laravel_core/)
$TARGET_DIR = __DIR__ . '/../laravel_core/';

// Whether to delete the zip after successful extraction
$DELETE_ZIP_AFTER = true;

// Whether to delete this script after successful extraction
$DELETE_SELF_AFTER = false;

// -----------------------------
// SAFETY / LIMITS
// -----------------------------
error_reporting(E_ALL);
@set_time_limit(300); // 300 seconds
@ini_set('memory_limit', '512M');

// Helper: send response and exit
function respond($code, $message)
{
    http_response_code($code);
    header('Content-Type: text/plain; charset=utf-8');
    echo $message;
    exit;
}

// Verify token
$token = isset($_GET['token']) ? $_GET['token'] : null;
if (!$token || !hash_equals($EXPECTED_TOKEN, $token)) {
    respond(403, "Forbidden: missing or invalid token.");
}

// Check ZipArchive extension
if (!extension_loaded('zip')) {
    respond(500, "ZipArchive extension missing. Enable the PHP zip extension.");
}

// Normalize target dir
function normalize_dir($dir)
{
    $dir = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $dir);
    // remove trailing separators
    return rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
}

$ZIP_PATH = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $ZIP_PATH);
$found = null;
foreach ($ZIP_CANDIDATES as $candidate) {
    $candidate = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $candidate);
    if (file_exists($candidate)) {
        $found = $candidate;
        break;
    }
}
if ($found !== null) {
    $ZIP_PATH = $found;
} else {
    respond(404, "File not found. Checked paths: " . implode(', ', $ZIP_CANDIDATES));
}
$TARGET_DIR = normalize_dir($TARGET_DIR);

// Confirm zip file exists
if (!file_exists($ZIP_PATH)) {
    respond(404, "File not found at path: {$ZIP_PATH}");
}

// Ensure target directory exists and is writable (create if needed)
if (!is_dir($TARGET_DIR)) {
    if (!@mkdir($TARGET_DIR, 0755, true)) {
        respond(500, "Directory not writable and could not be created: {$TARGET_DIR}");
    }
}
if (!is_writable($TARGET_DIR)) {
    respond(500, "Target directory not writable: {$TARGET_DIR}");
}

// Normalize a path (resolve . and .. without requiring file to exist)
function normalize_path_components($path)
{
    $parts = preg_split('#[\\/]+#', $path);
    $out = [];
    foreach ($parts as $part) {
        if ($part === '' || $part === '.') continue;
        if ($part === '..') {
            array_pop($out);
            continue;
        }
        $out[] = $part;
    }
    return implode(DIRECTORY_SEPARATOR, $out);
}

$targetDirNorm = normalize_dir(realpath(dirname($TARGET_DIR)) ? realpath($TARGET_DIR) : $TARGET_DIR);

$zip = new ZipArchive();
$res = $zip->open($ZIP_PATH);
if ($res !== true) {
    // Map common errors
    $map = [
        ZipArchive::ER_MULTIDISK => 'Multi-disk zip archives not supported',
        ZipArchive::ER_RENAME => 'Renaming temporary file failed',
        ZipArchive::ER_CLOSE => 'Closing zip archive failed',
        ZipArchive::ER_SEEK => 'Seek error',
        ZipArchive::ER_READ => 'Read error',
        ZipArchive::ER_WRITE => 'Write error',
        ZipArchive::ER_CRC => 'CRC error',
        ZipArchive::ER_ZIPCLOSED => 'Zip archive closed',
        ZipArchive::ER_INTERNAL => 'Internal error',
        ZipArchive::ER_INCONS => 'Zip archive inconsistent',
        ZipArchive::ER_INVALID => 'Invalid argument',
        ZipArchive::ER_NOZIP => 'Not a zip archive',
        ZipArchive::ER_MEMORY => 'Memory allocation failed',
        ZipArchive::ER_NOENT => 'No such file',
        ZipArchive::ER_EXISTS => 'File already exists',
    ];
    $msg = isset($map[$res]) ? $map[$res] : "Zip open error code: {$res}";
    respond(500, "Failed to open zip: {$msg}");
}

$count = $zip->numFiles;
// Extract entries one-by-one with path traversal protection
for ($i = 0; $i < $count; $i++) {
    $stat = $zip->statIndex($i);
    if ($stat === false) {
        $zip->close();
        respond(500, "Failed reading zip entry index: {$i}");
    }
    $name = $stat['name'];
    // Skip empty names
    if ($name === '') continue;
    // Normalize entry name to prevent absolute paths or traversal
    $name = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $name);
    $normalized = normalize_path_components($name);
    $destPath = $TARGET_DIR . $normalized;

    // Ensure destination is within the target dir
    $destNorm = normalize_path_components($destPath);
    if (strpos($destNorm, ltrim($TARGET_DIR, DIRECTORY_SEPARATOR)) !== 0 && strpos($destPath, $TARGET_DIR) !== 0) {
        $zip->close();
        respond(400, "Zip contains illegal path: {$name}");
    }

    // If entry is a directory
    if (substr($name, -1) === DIRECTORY_SEPARATOR || substr($name, -1) === '/') {
        if (!is_dir($destPath) && !@mkdir($destPath, 0755, true)) {
            $zip->close();
            respond(500, "Failed creating directory: {$destPath}");
        }
        continue;
    }

    // Ensure parent directory exists
    $parent = dirname($destPath);
    if (!is_dir($parent)) {
        if (!@mkdir($parent, 0755, true)) {
            $zip->close();
            respond(500, "Failed creating directory: {$parent}");
        }
    }

    // Extract file via stream to avoid issues
    $stream = $zip->getStream($stat['name']);
    if ($stream === false) {
        $zip->close();
        respond(500, "Failed opening stream for entry: {$name}");
    }
    $outHandle = @fopen($destPath, 'wb');
    if ($outHandle === false) {
        fclose($stream);
        $zip->close();
        respond(500, "Failed writing file: {$destPath}");
    }
    while (!feof($stream)) {
        $data = fread($stream, 8192);
        if ($data === false) break;
        fwrite($outHandle, $data);
    }
    fclose($stream);
    fclose($outHandle);
}

$zip->close();

// Optionally remove the zip file to save space
if ($DELETE_ZIP_AFTER) {
    if (!@unlink($ZIP_PATH)) {
        respond(500, "Extraction succeeded but failed to delete zip: {$ZIP_PATH}");
    }
}

$successMsg = "Extraction completed successfully to: {$TARGET_DIR}";

// Optionally delete this script
if ($DELETE_SELF_AFTER) {
    $self = __FILE__;
    echo $successMsg . "\n";
    if (!@unlink($self)) {
        respond(500, "Extraction succeeded but failed to delete self: {$self}");
    }
    exit;
}

respond(200, $successMsg);
?>