<?php
require __DIR__ . '/../admin/partials/auth.php';

$menu = $pdo->query("
SELECT * FROM weekly_menus
WHERE status = 'published'
ORDER BY week_start DESC
LIMIT 1
")->fetch();

$items = [];

if ($menu) {
    $stmt = $pdo->prepare("
        SELECT * FROM menu_items
        WHERE weekly_menu_id = ?
    ");
    $stmt->execute([$menu['id']]);
    $items = $stmt->fetchAll();
}

require __DIR__ . '/../admin/partials/header.php';
?>

<h1>Selecciona tus comidas</h1>

<?php foreach ($items as $item): ?>
<div style="margin-bottom:20px;">
    <strong><?= $item['day_of_week'] ?> - <?= $item['category'] ?></strong><br>
    <?= $item['name'] ?><br>
    <button>Seleccionar</button>
</div>
<?php endforeach; ?>