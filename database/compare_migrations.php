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
$plexusTables = getTables($dir2);

echo "--- Existing E-Commerce Tables ---\n";
print_r($ecommerceTables);
echo "\n--- NovaMart-Automate Tables ---\n";
print_r($plexusTables);

$intersection = array_intersect($ecommerceTables, $plexusTables);
echo "\n--- Overlapping Tables ---\n";
print_r(array_values($intersection));

$missingInEcommerce = array_diff($plexusTables, $ecommerceTables);
echo "\n--- Missing Tables in E-Commerce (Need to be created) ---\n";
print_r(array_values($missingInEcommerce));

