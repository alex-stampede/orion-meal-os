<?php

declare(strict_types=1);

$app = require __DIR__ . '/config/app.php';
$dbConfig = require __DIR__ . '/config/database.php';

date_default_timezone_set($app['timezone']);

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

$dbStatus = 'No conectada';
$dbMessage = '';
$dbServerTime = null;

try {
    $pdo = dbConnect($dbConfig);
    $dbStatus = 'Conectada';

    $stmt = $pdo->query('SELECT NOW() AS server_time');
    $result = $stmt->fetch();
    $dbServerTime = $result['server_time'] ?? null;
} catch (Throwable $e) {
    $dbStatus = 'Error';
    $dbMessage = $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($app['app_name']) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
  <main class="shell">
    <section class="card hero">
      <div class="badge">Orion System Prototype</div>
      <h1><?= htmlspecialchars($app['app_name']) ?></h1>
      <p class="lead">
        Base inicial del sistema para planes de comida a domicilio.
        Este entorno ya está conectado al subdominio y listo para seguir construyéndose.
      </p>

      <div class="grid">
        <div class="mini-card">
          <span class="label">Entorno</span>
          <strong><?= htmlspecialchars($app['app_env']) ?></strong>
        </div>
        <div class="mini-card">
          <span class="label">URL</span>
          <strong><?= htmlspecialchars($app['app_url']) ?></strong>
        </div>
        <div class="mini-card">
          <span class="label">Base de datos</span>
          <strong><?= htmlspecialchars($dbStatus) ?></strong>
        </div>
        <div class="mini-card">
          <span class="label">Hora servidor DB</span>
          <strong><?= htmlspecialchars((string)($dbServerTime ?? '—')) ?></strong>
        </div>
      </div>

      <?php if ($dbStatus === 'Error'): ?>
        <div class="alert error">
          <strong>Error de conexión:</strong>
          <span><?= htmlspecialchars($dbMessage) ?></span>
        </div>
      <?php else: ?>
        <div class="alert success">
          <strong>Todo bien:</strong>
          <span>El proyecto ya está leyendo configuración y conectando a MySQL.</span>
        </div>
      <?php endif; ?>

      <div class="next">
        <h2>Siguiente fase</h2>
        <ul>
          <li>Crear tablas base del sistema</li>
          <li>Montar login de administrador</li>
          <li>Crear dashboard de clientes y menús</li>
          <li>Preparar flujo de selección de platillos</li>
        </ul>
      </div>
    </section>
  </main>
</body>
</html>