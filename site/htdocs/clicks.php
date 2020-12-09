<?php
include(__DIR__.'/../init.php');

$clicks = $db->prepare("SELECT * FROM clicks ORDER BY id DESC LIMIT 20");

$clicks->execute();

$rows = [];
while ($row = $clicks->fetch(PDO::FETCH_ASSOC)) {
    $ip = $row['ip'];
    $parts = explode('.', $ip);
    $parts[0] = '**'; $parts[1] = '**';
    $ip = implode('.', $parts);
    $rows[] = sprintf("%05d % 15s % 3s", $row['id'], $ip, ($row['relay_status'] == '1' ? 'on' : 'off'));
}
echo json_encode([
    'numRows' => count($rows),
    'rows' => $rows,
]);
