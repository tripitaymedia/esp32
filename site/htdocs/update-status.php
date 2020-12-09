<?php
include(__DIR__.'/../init.php');

$name  = @$_GET['name'];
$type  = @$_GET['type'];
$value = @$_GET['value'];
$ip    = getIp();

if ($value != '1') {
    $value = 0;
}

if ($type == 'relay') {
    //
    $sql = "UPDATE device SET relay_on = ? WHERE name = ?";
    $db->prepare($sql)->execute([$value, $name]);

    //
    $stmt = $db->prepare("SELECT id FROM device WHERE name = ?");
    $stmt->execute([$name]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $id = $row['id'];
        $db->prepare("INSERT INTO clicks (device_id, ip, relay_status) VALUES (?, ?, ?)")->execute([$id, $ip, $value]);
    }

    echo "relay_on=$value";
}

function getIp() {
	//whether ip is from share internet
	$ip_address = '';
	if (!empty($_SERVER['HTTP_CLIENT_IP']))   {
		$ip_address = $_SERVER['HTTP_CLIENT_IP'];
	}
	//whether ip is from proxy
	elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))  
	{
		$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	//whether ip is from remote address
	else
	{
		$ip_address = $_SERVER['REMOTE_ADDR'];
	}
	return $ip_address;
}
