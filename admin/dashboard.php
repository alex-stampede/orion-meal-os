<?php

declare(strict_types=1);

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$userName = $_SESSION['user_name'] ?? 'Usuario';
$userRole = $_SESSION['role'] ?? 'admin';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Orion Meal OS</title>
    <style>
        :root {
            --bg: #061510;
            --card: rgba(255,255,255,0.06);
            --stroke: rgba(255,255,255,0.1);
            --text: #edf4f1;
            --muted: #a5b7b0;
            --accent: #0f6b57;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            padding: 32px;
            font-family: Inter, Arial, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(15, 107, 87, 0.25), transparent 30%),
                linear-gradient(180deg, #04110d 0%, #081a15 100%);
            color: var(--text);
        }
        .wrap {
            max-width: 1100px;
            margin: 0 auto;
        }
        .card {
            background: var(--card);
            border: 1px solid var(--stroke);
            border-radius: 24px;
            padding: 28px;
            box-shadow: 0 20px 60px rgba(0,0,0,.35);
            backdrop-filter: blur(14px);
        }
        .top {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: center;
            margin-bottom: 24px;
        }
        .badge {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(15, 107, 87, 0.18);
            border: 1px solid rgba(125,255,207,.12);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }
        h1 {
            margin: 10px 0 8px;
            font-size: 2.2rem;
        }
        p {
            color: var(--muted);
            line-height: 1.6;
        }
        .logout {
            display: inline-block;
            padding: 12px 16px;
            border-radius: 14px;
            background: var(--accent);
            color: white;
            text-decoration: none;
            font-weight: 700;
        }
        .grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 16px;
  margin-bottom: 24px;
}

@media (max-width: 900px) {
  .grid {
    grid-template-columns: 1fr;
  }
}
        .mini {
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--stroke);
            border-radius: 18px;
            padding: 18px;
        }
        .mini span {
            display: block;
            color: var(--muted);
            margin-bottom: 8px;
            font-size: .9rem;
        }
        .mini strong {
            font-size: 1.1rem;
        }
        @media (max-width: 900px) {
            .grid { grid-template-columns: 1fr; }
            .top { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>
    <div class="wrap">
        <section class="card">
            <div class="top">
                <div>
                    <div class="badge">Panel interno</div>
                    <h1>Bienvenido, <?= htmlspecialchars($userName) ?></h1>
                    <p>Ya estás dentro de Orion Meal OS. Desde aquí vamos a construir planes, menús, clientes y suscripciones.</p>
                </div>
                <a class="logout" href="/logout.php">Cerrar sesión</a>
            </div>

<div class="grid">
    <div class="mini">
        <span>Rol</span>
        <strong><?= htmlspecialchars($userRole) ?></strong>
    </div>

    <div class="mini">
        <span>Módulo</span>
        <strong><a href="/admin/plans/index.php" style="color: white;">Gestión de planes</a></strong>
    </div>

    <div class="mini">
        <span>Clientes</span>
        <strong><a href="/admin/customers/index.php" style="color: white;">Ver clientes</a></strong>
    </div>

    <div class="mini">
        <span>Menús</span>
        <strong><a href="/admin/menus/index.php" style="color: white;">Ver menús</a></strong>
    </div>

    <div class="mini">
        <span>Sistema</span>
        <strong>Activo</strong>
    </div>
</div>
        </section>
    </div>
</body>
</html>
