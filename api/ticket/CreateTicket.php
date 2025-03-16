<?php
session_start();
require_once '../db.php';

$token = $_SESSION['token'];

$admin = '';
$client = $db->query(
    "SELECT id FROM users WHERE token = '$token'
     ") ->fetchAll()[0];

$type = $_POST['type'];
$message = $_POST['message'];

$request = $db->
prepare("
INSERT INTO `tickets`( 
`type`, 
`message`,
`clients`,
`admin`) 
VALUES (?,?,?,?) 
"
)->execute([
    $type,
    $message,
    $client,
    $admin
]);
header("Location: ../../clients.php");

?>