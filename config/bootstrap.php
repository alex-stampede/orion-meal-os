<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$dbConfig = require __DIR__ . '/database.php';

function db(array $dbConfig): PDO
{
    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        $dbConfig['host'],
        $dbConfig['name'],
        $dbConfig['charset']
    );

    return new PDO(
        $dsn,
        $dbConfig['user'],
        $dbConfig['pass'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
}

function getBranding(PDO $pdo): array
{
    try {
        $stmt = $pdo->query("SELECT * FROM business_settings ORDER BY id ASC LIMIT 1");
        $branding = $stmt->fetch();

        if ($branding) {
            return $branding;
        }
    } catch (Throwable $e) {
    }

    return [
        'business_name' => 'Orion Meal OS',
        'logo_path' => null,
    ];
}

$pdo = db($dbConfig);
$branding = getBranding($pdo);