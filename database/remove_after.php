<?php

$dir = __DIR__ . '/migrations';

foreach (glob($dir . '/*.php') as $file) {
    $content = file_get_contents($file);
    if (preg_match('/->after\([^\)]+\)/', $content)) {
        $newContent = preg_replace('/->after\([^\)]+\)/', '', $content);
        file_put_contents($file, $newContent);
        echo "Removed after() from " . basename($file) . "\n";
    }
}
