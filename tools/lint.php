<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$files = [$root . '/bin/hash-generator'];
foreach (['src', 'config', 'tests', 'examples', 'tools'] as $directory) {
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/' . $directory)) as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $files[] = $file->getPathname();
        }
    }
}
foreach ($files as $file) {
    passthru(escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($file), $code);
    if ($code !== 0) {
        exit($code);
    }
}
