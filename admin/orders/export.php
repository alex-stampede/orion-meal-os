<?php

declare(strict_types=1);

require __DIR__ . '/../partials/auth.php';

$selectedDay = trim($_GET['day'] ?? '');
$validDays = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];

$dayLabels = [
    'monday' => 'lunes',
    'tuesday' => 'martes',
    'wednesday' => 'miercoles',
    'thursday' => 'jueves',
    'friday' => 'viernes',
    'saturday' => 'sabado',
    'sunday' => 'domingo',
];

$sql = "
    SELECT
        ms.id AS selection_id,
        ms.delivery_date,
        u.id AS customer_id,
        u.first_name,
        u.last_name,
        u.email,
        mp.name AS plan_name,
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

$filename = 'pedidos';
if ($selectedDay !== '' && isset($dayLabels[$selectedDay])) {
    $filename .= '-' . $dayLabels[$selectedDay];
}
$filename .= '-' . date('Y-m-d-His') . '.csv';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

fwrite($output, "\xEF\xBB\xBF");

fputcsv($output, [
    'ID Selección',
    'Día',
    'Fecha de entrega',
    'Cliente',
    'Correo',
    'Plan',
    'Platillo',
    'Categoría',
    'Descripción',
    'Ingredientes',
    'Calorías',
    'Proteína (g)',
    'Carbs (g)',
    'Grasas (g)',
    'Recibe',
    'Teléfono',
    'Calle',
    'No. exterior',
    'No. interior',
    'Colonia',
    'Ciudad',
    'Estado',
    'CP',
    'Referencias',
]);

foreach ($rows as $row) {
    fputcsv($output, [
        $row['selection_id'],
        $dayLabels[$row['day_of_week']] ?? $row['day_of_week'],
        $row['delivery_date'],
        trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')),
        $row['email'],
        $row['plan_name'],
        $row['meal_name'],
        $row['category'],
        $row['description'],
        $row['ingredients'],
        $row['calories'],
        $row['protein_g'],
        $row['carbs_g'],
        $row['fats_g'],
        $row['recipient_name'],
        $row['phone'],
        $row['street'],
        $row['ext_number'],
        $row['int_number'],
        $row['neighborhood'],
        $row['city'],
        $row['state'],
        $row['postal_code'],
        $row['references_text'],
    ]);
}

fclose($output);
exit;