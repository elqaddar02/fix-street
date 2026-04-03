<?php
function rebuildJson($path) {
    $lines = file($path, FILE_IGNORE_NEW_LINES);
    $items = [];
    foreach ($lines as $i => $line) {
        $trim = trim($line);
        if ($trim === '{' || $trim === '}' || $trim === '') continue;
        // attempt robust parse
        if (preg_match('/^\s*"(?<key>(?:\\\\.|[^"\\\\])*)"\s*:\s*"(?<value>(?:\\\\.|[^"\\\\])*)"\s*,?\s*$/u', $line, $m)) {
            $key = json_decode('"' . $m['key'] . '"');
            $value = json_decode('"' . $m['value'] . '"');
            if ($key === null && $m['key'] !== 'null') {
                throw new Exception("Invalid key JSON at line " . ($i+1) . ": {$m['key']}");
            }
            if ($value === null && $m['value'] !== 'null') {
                throw new Exception("Invalid value JSON at line " . ($i+1) . ": {$m['value']}");
            }
            $items[$key] = $value;
            continue;
        }
        // Fallback split by the first occurrence of ": "
        $pos = strpos($line, '": "');
        if ($pos !== false) {
            $keyPart = substr($line, 0, $pos+1);
            $valPart = substr($line, $pos+3);
            $valPart = trim($valPart);
            if (substr($valPart, -1) === ',') {
                $valPart = substr($valPart, 0, -1);
            }
            // keyPart and valPart should each be quoted strings now
            try {
                $key = json_decode($keyPart);
                $value = json_decode($valPart);
                if (!is_string($key) || !is_string($value)) {
                    throw new Exception('Cannot parse line '.($i+1));
                }
                $items[$key] = $value;
                continue;
            } catch (Exception $e) {
                // ignore fallback
            }
        }
        throw new Exception("Could not parse line " . ($i+1) . ": $line");
    }
    $newJson = json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($newJson === false) {
        throw new Exception('Failed to encode JSON for ' . $path);
    }
    // Write output with standard newline
    file_put_contents($path, $newJson . "\n");
    echo "Rebuilt and wrote $path\n";
    return json_decode(file_get_contents($path)) !== null;
}

$paths = ['resources/lang/en.json', 'resources/lang/fr.json', 'resources/lang/ar.json'];
foreach ($paths as $path) {
    try {
        $ok = rebuildJson($path);
        echo "$path decode after rebuild: " . ($ok ? 'OK' : 'ERROR') . "\n";
    } catch (Exception $e) {
        echo "Error rebuilding $path: " . $e->getMessage() . "\n";
    }
}
?>