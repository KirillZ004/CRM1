<?php

require_once '../db.php';

$id = $_GET['id'];
$status = $_POST['status'];

$request = $db->prepare("
    UPDATE `orders` 
    SET 
        `status` = :status
    WHERE `id` = :id
");

$request->bindParam(':status', $status);
$request->bindParam(':id', $id);

$request->execute();

header("Location: ../../orders.php");

?>
