<?php

require __DIR__ . '/../partials/auth.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

$addr = $pdo->prepare("
    SELECT *
    FROM customer_addresses
    WHERE user_id = ?
    ORDER BY id DESC
    LIMIT 1
");
$addr->execute([$id]);
$address = $addr->fetch();

require __DIR__ . '/../partials/header.php';
?>

<section class="card page-card">
    <h1><?= $user['first_name'] ?> <?= $user['last_name'] ?></h1>
    <p><?= $user['email'] ?></p>

    <div class="mini-card">
        <span class="label">Dirección</span>

        <?php if ($address): ?>
            <strong><?= $address['street'] ?> <?= $address['ext_number'] ?></strong>
            <p><?= $address['city'] ?>, <?= $address['state'] ?></p>
            <p>CP <?= $address['postal_code'] ?></p>
        <?php else: ?>
            <p>No tiene dirección</p>
        <?php endif; ?>
    </div>
</section>