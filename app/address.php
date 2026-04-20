<?php

declare(strict_types=1);

require __DIR__ . '/partials/auth.php';

$pageTitle = 'Mi dirección';
$error = '';
$message = '';

$stmt = $pdo->prepare("
    SELECT *
    FROM customer_addresses
    WHERE user_id = ? AND is_default = 1
    ORDER BY id DESC
    LIMIT 1
");
$stmt->execute([$userId]);
$address = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipientName = trim($_POST['recipient_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $street = trim($_POST['street'] ?? '');
    $extNumber = trim($_POST['ext_number'] ?? '');
    $intNumber = trim($_POST['int_number'] ?? '');
    $neighborhood = trim($_POST['neighborhood'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $postalCode = trim($_POST['postal_code'] ?? '');
    $referencesText = trim($_POST['references_text'] ?? '');

    if ($recipientName === '' || $street === '' || $city === '' || $state === '' || $postalCode === '') {
        $error = 'Completa correctamente los campos obligatorios.';
    } else {
        if ($address) {
            $update = $pdo->prepare("
                UPDATE customer_addresses
                SET recipient_name = ?, phone = ?, street = ?, ext_number = ?, int_number = ?, neighborhood = ?, city = ?, state = ?, postal_code = ?, references_text = ?
                WHERE id = ? AND user_id = ?
            ");
            $update->execute([
                $recipientName,
                $phone ?: null,
                $street,
                $extNumber ?: null,
                $intNumber ?: null,
                $neighborhood ?: null,
                $city,
                $state,
                $postalCode,
                $referencesText ?: null,
                $address['id'],
                $userId
            ]);
        } else {
            $insert = $pdo->prepare("
                INSERT INTO customer_addresses (
                    user_id, label, recipient_name, phone, street, ext_number, int_number,
                    neighborhood, city, state, postal_code, references_text, is_default
                ) VALUES (?, 'Principal', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
            ");
            $insert->execute([
                $userId,
                $recipientName,
                $phone ?: null,
                $street,
                $extNumber ?: null,
                $intNumber ?: null,
                $neighborhood ?: null,
                $city,
                $state,
                $postalCode,
                $referencesText ?: null
            ]);
        }

        header('Location: /app/address.php?saved=1');
        exit;
    }
}

if (isset($_GET['saved'])) {
    $message = 'Dirección guardada correctamente.';
    $stmt->execute([$userId]);
    $address = $stmt->fetch();
}

require __DIR__ . '/partials/header.php';
?>

<section class="card page-card">
    <div class="customer-topbar">
        <div>
            <div class="badge">Cliente</div>
            <h1>Mi dirección de envío</h1>
            <p class="helper-text">Guarda la dirección principal para tus entregas.</p>

            <div class="customer-nav">
                <a class="button-secondary" href="/app/dashboard.php">Mi cuenta</a>
                <a class="button-secondary" href="/app/plans.php">Planes</a>
                <a class="button-secondary" href="/app/select-meals.php">Seleccionar comidas</a>
                <a class="button-secondary" href="/app/address.php">Mi dirección</a>
            </div>
        </div>

        <a class="button-secondary" href="/logout.php">Cerrar sesión</a>
    </div>

    <?php if ($message): ?>
        <div class="message-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="message-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="form-grid">
        <div class="form-group full">
            <label>Nombre de quien recibe</label>
            <input class="input" type="text" name="recipient_name" value="<?= htmlspecialchars((string)($address['recipient_name'] ?? '')) ?>" required>
        </div>

        <div class="form-group">
            <label>Teléfono</label>
            <input class="input" type="text" name="phone" value="<?= htmlspecialchars((string)($address['phone'] ?? '')) ?>">
        </div>

        <div class="form-group">
            <label>Calle</label>
            <input class="input" type="text" name="street" value="<?= htmlspecialchars((string)($address['street'] ?? '')) ?>" required>
        </div>

        <div class="form-group">
            <label>No. exterior</label>
            <input class="input" type="text" name="ext_number" value="<?= htmlspecialchars((string)($address['ext_number'] ?? '')) ?>">
        </div>

        <div class="form-group">
            <label>No. interior</label>
            <input class="input" type="text" name="int_number" value="<?= htmlspecialchars((string)($address['int_number'] ?? '')) ?>">
        </div>

        <div class="form-group">
            <label>Colonia</label>
            <input class="input" type="text" name="neighborhood" value="<?= htmlspecialchars((string)($address['neighborhood'] ?? '')) ?>">
        </div>

        <div class="form-group">
            <label>Ciudad</label>
            <input class="input" type="text" name="city" value="<?= htmlspecialchars((string)($address['city'] ?? '')) ?>" required>
        </div>

        <div class="form-group">
            <label>Estado</label>
            <input class="input" type="text" name="state" value="<?= htmlspecialchars((string)($address['state'] ?? '')) ?>" required>
        </div>

        <div class="form-group">
            <label>Código postal</label>
            <input class="input" type="text" name="postal_code" value="<?= htmlspecialchars((string)($address['postal_code'] ?? '')) ?>" required>
        </div>

        <div class="form-group full">
            <label>Referencias</label>
            <textarea class="textarea" name="references_text"><?= htmlspecialchars((string)($address['references_text'] ?? '')) ?></textarea>
        </div>

        <div class="actions-row">
            <button class="button">Guardar dirección</button>
        </div>
    </form>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
