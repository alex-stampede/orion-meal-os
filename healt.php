<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$app = require __DIR__ . '/config/app.php';
$dbConfig = require __DIR__ . '/config/database.php';

function dbConnect(array $dbConfig): PDO
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

try {
    $pdo = dbConnect($dbConfig);
    $stmt = $pdo->query('SELECT 1 AS ok');
    $result = $stmt->fetch();

    echo json_encode([
        'status' => 'ok',
        'app' => $app['app_name'],
        'database' => $result['ok'] ?? 0,
        'timestamp' => date('c'),
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);

    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'timestamp' => date('c'),
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}