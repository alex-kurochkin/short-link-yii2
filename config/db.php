<?php
/**
 * Database configuration
 *
 * Reads database settings from environment variables.
 */

$dbConnection = getenv('DB_CONNECTION') ?: 'sqlite';
$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbPort = getenv('DB_PORT') ?: '3306';
$dbDatabase = getenv('DB_DATABASE') ?: 'database.sqlite';
$dbUsername = getenv('DB_USERNAME') ?: 'root';
$dbPassword = getenv('DB_PASSWORD') ?: '';

if ($dbConnection === 'mysql') {
    $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbDatabase}";
} elseif ($dbConnection === 'pgsql') {
    $dsn = "pgsql:host={$dbHost};port={$dbPort};dbname={$dbDatabase}";
} else {
    $dsn = 'sqlite:' . dirname(__DIR__) . '/' . $dbDatabase;
}

return [
    'class' => 'yii\db\Connection',
    'dsn' => $dsn,
    'username' => $dbUsername,
    'password' => $dbPassword,
    'charset' => 'utf8mb4',
    'enableSchemaCache' => true,
    'schemaCacheDuration' => 3600,
    'tablePrefix' => '',
];
