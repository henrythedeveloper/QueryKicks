<?php
function loadEnv($filePath) {
    if (!file_exists($filePath)) {
        throw new Exception('.env file not found');
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0 || strpos($line, '=') === false) {
            continue; // Skip comments and lines without '='
        }
        
        [$name, $value] = explode('=', $line, 2);
        $_ENV[$name] = trim($value);
    }
}
