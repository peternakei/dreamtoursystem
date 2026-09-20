<?php
// Preflight only: do not query or modify the database.
if (PHP_VERSION_ID < 80200) {
    fwrite(STDERR, "PHP 8.2 or newer is required.\n");
    exit(1);
}
$backend = dirname(__DIR__) . '/BACKEND';
if (!is_file($backend . '/vendor/autoload.php')) {
    fwrite(STDERR, "Backend dependencies are missing. Run composer install in BACKEND.\n");
    exit(1);
}
require $backend . '/vendor/autoload.php';
$app = require $backend . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$connection = config('database.default');
$driver = config("database.connections.$connection.driver");
$pdoDriver = $driver === 'mariadb' ? 'mysql' : $driver;
if (!class_exists(PDO::class) || !in_array($pdoDriver, PDO::getAvailableDrivers(), true)) {
    fwrite(STDERR, "Missing PDO driver for the configured database: $driver.\n");
    exit(1);
}
echo PHP_BINARY, ' (PHP ', PHP_VERSION, ', PDO ', $pdoDriver, ')', PHP_EOL;
