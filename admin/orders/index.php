<?php

declare(strict_types=1);

require __DIR__ . '/../partials/auth.php';

$pageTitle = 'Pedidos';

$selectedDay = trim($_GET['day'] ?? '');
$validDays = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];

$dayLabels = [
    'monday' => 'Lunes',
    'tuesday' => 'Martes',
    'wednesday' => 'Miércoles',
    'thursday' => 'Jueves',
    'friday' => 'Viernes',
    'saturday' => 'Sábado',
    'sunday' => 'Domingo',
];

$sql = "
    SELECT
        ms.id AS selection_id,
        ms.delivery_date,
        s.id AS subscription_id,
        s.start_date,
        s.end_date,
        u.id AS customer_id,
        u.first_name,
        u.last_name,
        u.email,
        mp.name AS plan_name,
        mi.id AS menu_item_id,
        mi.day_of_week,
        mi.category,
        mi.name AS meal_name,
        mi.description,
        mi.ingredients,
        mi.calories,
        mi.protein_g,
        mi.carbs_g,
        mi.fats_g,
        ca.recipient_name,
        ca.phone,
        ca.street,
        ca.ext_number,
        ca.int_number,
        ca.neighborhood,
        ca.city,
        ca.state,
        ca.postal_code,
        ca.references_text
    FROM meal_selections ms
    INNER JOIN subscriptions s ON s.id = ms.subscription_id
    INNER JOIN users u ON u.id = s.user_id
    INNER JOIN meal_plans mp ON mp.id = s.meal_plan_id
    INNER JOIN menu_items mi ON mi.id = ms.menu_item_id
    LEFT JOIN customer_addresses ca ON ca.user_id = u.id AND ca.is_default = 1
    WHERE u.role = 'customer'
";

$params = [];

if ($selectedDay !== '' && in_array($selectedDay, $validDays, true)) {
    $sql .= " AND mi.day_of_week = ? ";
    $params[] = $selectedDay;
}

$sql .= "
    ORDER BY
        FIELD(mi.day_of_week,'monday','tuesday','wednesday','thursday','friday','saturday','sunday'),
        u.first_name ASC,
        u.last_name ASC,
        ms.id ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

$grouped = [];

foreach ($rows as $row) {
    $day = $row['day_of_week'];
    $customerKey = $day . '_' . $row['customer_id'];

    if (!isset($grouped[$day])) {
        $grouped[$day] = [];
    }

    if (!isset($grouped[$day][$customerKey])) {
        $grouped[$day][$customerKey] = [
            'customer_id' => (int)$row['customer_id'],
            'customer_name' => trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')),
            'email' => $row['email'],
            'plan_name' => $row['plan_name'],
            'delivery_date' => $row['delivery_date'],
            'address' => [
                'recipient_name' => $row['recipient_name'],
                'phone' => $row['phone'],
                'street' => $row['street'],
                'ext_number' => $row['ext_number'],
                'int_number' => $row['int_number'],
                'neighborhood' => $row['neighborhood'],
                'city' => $row['city'],
                'state' => $row['state'],
                'postal_code' => $row['postal_code'],
                'references_text' => $row['references_text'],
            ],
            'items' => [],
            'totals' => [
                'calories' => 0,
                'protein_g' => 0,
                'carbs_g' => 0,
                'fats_g' => 0,
            ],
        ];
    }

    $grouped[$day][$customerKey]['items'][] = [
        'selection_id' => (int)$row['selection_id'],
        'meal_name' => $row['meal_name'],
        'category' => $row['category'],
        'description' => $row['description'],
        'ingredients' => $row['ingredients'],
        'calories' => $row['calories'],
        'protein_g' => $row['protein_g'],
        'carbs_g' => $row['carbs_g'],
        'fats_g' => $row['fats_g'],
    ];

    $grouped[$day][$customerKey]['totals']['calories'] += (int)($row['calories'] ?? 0);
    $grouped[$day][$customerKey]['totals']['protein_g'] += (float)($row['protein_g'] ?? 0);
    $grouped[$day][$customerKey]['totals']['carbs_g'] += (float)($row['carbs_g'] ?? 0);
    $grouped[$day][$customerKey]['totals']['fats_g'] += (float)($row['fats_g'] ?? 0);
}

require __DIR__ . '/../partials/header.php';
?>

<section class="card page-card">
    <div class="page-top">
        <div>
            <div class="badge">Operación</div>
            <h1>Pedidos / Entregas</h1>
            <p class="helper-text">Consulta qué entregar, a quién y a dónde, agrupado por día.</p>
        </div>
    </div>

    <div class="actions-row">
        <a class="button-secondary" href="/admin/orders/index.php">Todos</a>
        <a class="button-secondary" href="/admin/orders/index.php?day=monday">Lunes</a>
        <a class="button-secondary" href="/admin/orders/index.php?day=tuesday">Martes</a>
        <a class="button-secondary" href="/admin/orders/index.php?day=wednesday">Miércoles</a>
        <a class="button-secondary" href="/admin/orders/index.php?day=thursday">Jueves</a>
        <a class="button-secondary" href="/admin/orders/index.php?day=friday">Viernes</a>
        <a class="button-secondary" href="/admin/orders/index.php?day=saturday">Sábado</a>
        <a class="button-secondary" href="/admin/orders/index.php?day=sunday">Domingo</a>
    </div>

    <?php if (!$grouped): ?>
        <div class="empty-state">
            <p>No hay selecciones registradas todavía.</p>
        </div>
    <?php else: ?>
        <?php foreach ($grouped as $day => $customers): ?>
            <section class="week-section">
                <h2><?= htmlspecialchars($dayLabels[$day] ?? $day) ?></h2>

                <div class="week-list">
                    <?php foreach ($customers as $customer): ?>
                        <article class="card day-card">
                            <div class="page-top" style="margin-bottom: 16px;">
                                <div>
                                    <h3 style="margin: 0 0 8px;">
                                        <?= htmlspecialchars($customer['customer_name'] ?: 'Cliente sin nombre') ?>
                                    </h3>
                                    <p class="helper-text" style="margin: 0;">
                                        <?= htmlspecialchars($customer['email']) ?> ·
                                        Plan: <strong><?= htmlspecialchars($customer['plan_name']) ?></strong>
                                    </p>
                                    <?php if (!empty($customer['delivery_date'])): ?>
                                        <p class="helper-text" style="margin: 6px 0 0;">
                                            Fecha de entrega registrada: <strong><?= htmlspecialchars($customer['delivery_date']) ?></strong>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <a class="button-secondary" href="/admin/customers/view.php?id=<?= (int)$customer['customer_id'] ?>">
                                    Ver cliente
                                </a>
                            </div>

                            <div class="customer-grid" style="margin-top: 0; margin-bottom: 18px;">
                                <div class="mini-card">
                                    <span class="label">Dirección</span>

                                    <?php if (!empty($customer['address']['street'])): ?>
                                        <strong>
                                            <?= htmlspecialchars((string)$customer['address']['street']) ?>
                                            <?= htmlspecialchars((string)$customer['address']['ext_number']) ?>
                                            <?php if (!empty($customer['address']['int_number'])): ?>
                                                Int. <?= htmlspecialchars((string)$customer['address']['int_number']) ?>
                                            <?php endif; ?>
                                        </strong>
                                        <p class="helper-text" style="margin: 8px 0 0;">
                                            <?= htmlspecialchars((string)$customer['address']['neighborhood']) ?><br>
                                            <?= htmlspecialchars((string)$customer['address']['city']) ?>,
                                            <?= htmlspecialchars((string)$customer['address']['state']) ?><br>
                                            CP <?= htmlspecialchars((string)$customer['address']['postal_code']) ?>
                                        </p>
                                        <?php if (!empty($customer['address']['references_text'])): ?>
                                            <p class="helper-text" style="margin: 8px 0 0;">
                                                <strong>Referencias:</strong>
                                                <?= htmlspecialchars((string)$customer['address']['references_text']) ?>
                                            </p>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <p class="helper-text">No tiene dirección registrada.</p>
                                    <?php endif; ?>
                                </div>

                                <div class="mini-card">
                                    <span class="label">Contacto</span>
                                    <strong><?= htmlspecialchars((string)($customer['address']['recipient_name'] ?: $customer['customer_name'])) ?></strong>
                                    <p class="helper-text" style="margin: 8px 0 0;">
                                        <?= htmlspecialchars((string)($customer['address']['phone'] ?: 'Sin teléfono')) ?>
                                    </p>
                                </div>

                                <div class="mini-card">
                                    <span class="label">Totales del pedido</span>
                                    <div class="meal-meta">
                                        <span><?= (int)$customer['totals']['calories'] ?> kcal</span>
                                        <span><?= number_format((float)$customer['totals']['protein_g'], 2) ?>g proteína</span>
                                        <span><?= number_format((float)$customer['totals']['carbs_g'], 2) ?>g carbs</span>
                                        <span><?= number_format((float)$customer['totals']['fats_g'], 2) ?>g grasas</span>
                                    </div>
                                </div>
                            </div>

                            <div class="table-wrap">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Platillo</th>
                                            <th>Categoría</th>
                                            <th>Descripción</th>
                                            <th>Macros</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($customer['items'] as $item): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= htmlspecialchars($item['meal_name']) ?></strong>
                                                    <?php if (!empty($item['ingredients'])): ?>
                                                        <p class="helper-text" style="margin: 8px 0 0;">
                                                            <strong>Ingredientes:</strong>
                                                            <?= htmlspecialchars((string)$item['ingredients']) ?>
                                                        </p>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars((string)$item['category']) ?></td>
                                                <td><?= htmlspecialchars((string)($item['description'] ?: '—')) ?></td>
                                                <td>
                                                    <?= (int)($item['calories'] ?? 0) ?> kcal ·
                                                    <?= number_format((float)($item['protein_g'] ?? 0), 2) ?>p ·
                                                    <?= number_format((float)($item['carbs_g'] ?? 0), 2) ?>c ·
                                                    <?= number_format((float)($item['fats_g'] ?? 0), 2) ?>g
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>