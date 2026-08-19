<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$connectionName = (string) config('database.default');
$prefix = trim((string) config("database.connections.{$connectionName}.prefix"));

if ($prefix === '') {
    fwrite(STDERR, "DB prefix is empty. Set DB_PREFIX in .env first.\n");
    exit(1);
}

$databaseName = (string) DB::connection()->getDatabaseName();
if ($databaseName === '') {
    fwrite(STDERR, "Cannot detect active database name.\n");
    exit(1);
}

$rows = DB::select(
    'SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_TYPE = ? ORDER BY TABLE_NAME',
    [$databaseName, 'BASE TABLE']
);

$allTables = array_map(
    static fn($row): string => (string) array_values((array) $row)[0],
    $rows
);

$renameMap = [];
$alreadyPrefixed = [];
$collisions = [];

foreach ($allTables as $tableName) {
    if (str_starts_with($tableName, $prefix)) {
        $alreadyPrefixed[] = $tableName;
        continue;
    }

    $targetName = $prefix . $tableName;
    if (in_array($targetName, $allTables, true)) {
        $collisions[] = [$tableName, $targetName];
        continue;
    }

    $renameMap[$tableName] = $targetName;
}

if ($collisions !== []) {
    fwrite(STDERR, "Collision detected. Resolve manually before running again:\n");
    foreach ($collisions as [$from, $to]) {
        fwrite(STDERR, " - {$from} -> {$to} (target already exists)\n");
    }
    exit(1);
}

if ($renameMap === []) {
    echo "Nothing to rename. Tables are already prefixed with '{$prefix}'.\n";
    exit(0);
}

DB::statement('SET FOREIGN_KEY_CHECKS=0');

try {
    foreach ($renameMap as $from => $to) {
        $fromQuoted = '`' . str_replace('`', '``', $from) . '`';
        $toQuoted = '`' . str_replace('`', '``', $to) . '`';
        DB::statement("RENAME TABLE {$fromQuoted} TO {$toQuoted}");
        echo "Renamed: {$from} -> {$to}\n";
    }
} finally {
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
}

echo "Done. Renamed " . count($renameMap) . " table(s). Already prefixed: " . count($alreadyPrefixed) . ".\n";

