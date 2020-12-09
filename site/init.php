<?php
error_reporting(E_ALL);

$host     = 'localhost';
$database = 'iot';
$username = 'iot';
$password = '--db-password-here--';

$db = new PDO("mysql:host=$host;dbname=$database", $username, $password);

