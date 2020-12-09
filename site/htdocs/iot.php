<?php
include(__DIR__.'/../init.php');

$relayStatus = @$_GET['relayStatus'];
$name        = strtolower(@$_GET['name']);
$ip        = strtolower(@$_GET['ip']);

// Get ID
$sql = "INSERT IGNORE INTO device (name) VALUES (?)";
$db->prepare($sql)->execute([$name]);
$stmt = $db->prepare("SELECT * FROM device WHERE name = ?");
$stmt->execute([$name]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$id = $row['id'];

$stmt = $db->prepare("INSERT INTO device_log (created, device_id, ip, relay_status) VALUES (NOW(), ?, ?, ?)");
$stmt->execute([$id, $ip, $relayStatus]);

$data = [];
$data['relayOn'] = $row['relay_on'];
$data['script'] = "";
echo json_encode($data);

