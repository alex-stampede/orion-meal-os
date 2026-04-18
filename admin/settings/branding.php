<?php

declare(strict_types=1);

require __DIR__ . '/../partials/auth.php';

$pageTitle = 'Branding';
$error = '';
$message = '';

$uploadDir = __DIR__ . '/../../uploads/branding';
$publicDir = '/uploads/branding';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$stmt = $pdo->query("SELECT * FROM business_settings ORDER BY id ASC LIMIT 1");
$settings = $stmt->fetch();

if (!$settings) {
    $pdo->exec("INSERT INTO business_settings (business_name, logo_path) VALUES ('Orion Meal OS', NULL)");
    $stmt = $pdo->query("SELECT * FROM business_settings ORDER BY id ASC LIMIT 1");
    $settings = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $businessName = trim($_POST['business_name'] ?? 'Orion Meal OS');
    $logoPath = $settings['logo_path'] ?? null;

    if (!empty($_FILES['logo']['name'])) {
        $file = $_FILES['logo'];

        if ($file['error'] === UPLOAD_ERR_OK) {
            $tmpName = $file['tmp_name'];
            $originalName = $file['name'];
            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

            $allowed = ['png', 'jpg', 'jpeg', 'webp', 'svg'];

            if (!in_array($ext, $allowed, true)) {
                $error = 'Formato no permitido. Usa PNG, JPG, JPEG, WEBP o SVG.';
            } else {
                $newName = 'logo-' . time() . '.' . $ext;
                $destination = $uploadDir . '/' . $newName;

                if (move_uploaded_file($tmpName, $destination)) {
                    $logoPath = $publicDir . '/' . $newName;
                } else {
                    $error = 'No se pudo subir el archivo.';
                }
            }
        } else {
            $error = 'Error al subir el logo.';
        }
    }

    if ($error === '') {
        $update = $pdo->prepare("
            UPDATE business_settings
            SET business_name = ?, logo_path = ?
            WHERE id = ?
        ");
        $update->execute([$businessName, $logoPath, $settings['id']'] ?? 1]);

        header('Location: /admin/settings/branding.php?saved=1');
        exit;
    }
}

if (isset($_GET['saved'])) {
    $message = 'Branding actualizado correctamente.';
    $stmt = $pdo->query("SELECT * FROM business_settings ORDER BY id ASC LIMIT 1");
    $settings = $stmt->fetch();
}

require __DIR__ . '/../partials/header.php';
?>

<section class="card page-card">
    <div class="page-top">
        <div>
            <div class="badge">Configuración</div>
            <h1>Branding</h1>
            <p>Personaliza el nombre y logo del sistema.</p>
        </div>
    </div>

    <?php if ($message !== ''): ?>
        <div class="message-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <div class="message-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="form-grid">
        <div class="form-group full">
            <label>Nombre del negocio</label>
            <input class="input" type="text" name="business_name" value="<?= htmlspecialchars((string)($settings['business_name'] ?? 'Orion Meal OS')) ?>">
        </div>

        <div class="form-group full">
            <label>Logo</label>
            <input class="input" type="file" name="logo" accept=".png,.jpg,.jpeg,.webp,.svg">
        </div>

        <?php if (!empty($settings['logo_path'])): ?>
            <div class="form-group full">
                <label>Logo actual</label>
                <img src="<?= htmlspecialchars($settings['logo_path']) ?>" alt="Logo actual" style="max-width:220px; max-height:90px; object-fit:contain; background:rgba(255,255,255,0.04); padding:12px; border-radius:16px; border:1px solid rgba(255,255,255,0.08);">
            </div>
        <?php endif; ?>

        <div class="actions-row">
            <button class="button" type="submit">Guardar branding</button>
        </div>
    </form>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>