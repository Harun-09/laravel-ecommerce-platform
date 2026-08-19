<?php

$dir1 = __DIR__ . '/migrations';
$dir2 = __DIR__ . '/migrations_new';

function getTables($dir) {
    $tables = [];
    foreach (glob($dir . '/*.php') as $file) {
        $content = file_get_contents($file);
        preg_match_all("/Schema::create\(\s*['\"]([^'\"]+)['\"]/", $content, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $table) {
                $tables[] = $table;
            }
        }
    }
    return array_unique($tables);
}

$ecommerceTables = getTables($dir1);
// Manually add renamed tables to ecommerce tables
$ecommerceTables[] = 'suppliers'; // we rename vendors to suppliers

foreach (glob($dir2 . '/*.php') as $file) {
    $content = file_get_contents($file);
    preg_match_all("/Schema::create\(\s*['\"]([^'\"]+)['\"]/", $content, $matches);
    $createsOverlapping = false;
    if (!empty($matches[1])) {
        foreach ($matches[1] as $table) {
            if (in_array($table, $ecommerceTables)) {
                $createsOverlapping = true;
                break;
            }
        }
    }
    
    // Check if it's a migration that doesn't create tables but ALTERS overlapping tables?
    // Let's just copy the ones that only create new tables, OR are not creating overlapping ones.
    if (!$createsOverlapping) {
        echo "Copying " . basename($file) . "\n";
        copy($file, $dir1 . '/' . basename($file));
    } else {
        echo "Skipping " . basename($file) . " (overlaps)\n";
    }
}
