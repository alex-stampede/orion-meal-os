<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Mi cuenta') ?> | <?= htmlspecialchars((string)($branding['business_name'] ?? 'Orion Meal OS')) ?></title>
    <link rel="stylesheet" href="/assets/css/theme.css">
    <link rel="stylesheet" href="/assets/css/base.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<main class="shell">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:16px; margin:24px 0;">
        <div style="display:flex; align-items:center; gap:14px;">
            <?php if (!empty($branding['logo_path'])): ?>
                <img src="<?= htmlspecialchars($branding['logo_path']) ?>" alt="Logo" style="max-width:160px; max-height:56px; object-fit:contain;">
            <?php endif; ?>
            <div class="badge"><?= htmlspecialchars((string)($branding['business_name'] ?? 'Orion Meal OS')) ?></div>
        </div>
    </div>