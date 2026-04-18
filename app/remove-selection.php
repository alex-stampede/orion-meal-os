<?php

declare(strict_types=1);

require __DIR__ . '/partials/auth.php';

$selectionId = (int)($_GET['id'] ?? 0);

if ($selectionId > 0) {
    $subStmt = $pdo->prepare("
        SELECT id
        FROM subscriptions
        WHERE user_id = ? AND status = 'active'
        ORDER BY id DESC
        LIMIT 1
    ");
    $subStmt->execute([$userId]);
    $subscription = $subStmt->fetch();

    if ($subscription) {
        $delete = $pdo->prepare("
            DELETE FROM meal_selections
            WHERE id = ? AND subscription_id = ?
        ");
        $delete->execute([$selectionId, (int)$subscription['id']]);
    }
}

header('Location: /app/dashboard.php');
exit;