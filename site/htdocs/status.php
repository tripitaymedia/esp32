<?php
include(__DIR__.'/../init.php');


$stmt = $db->prepare("SELECT 
            d.*,
            IF(l.id IS NULL, 0, 1) AS online,
            ip as last_ip,
            relay_status,
            created AS last_seen
        FROM device d
        LEFT JOIN device_log l ON d.id = l.device_id
        WHERE
            l.created > NOW() - INTERVAL 10 SECOND
        GROUP BY d.id
        ORDER BY d.id
");
$stmt->execute();
$rows = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $rows[] = $row;
}

echo json_encode([
    'numRows' => count($rows),
    'rows' => $rows
]);

