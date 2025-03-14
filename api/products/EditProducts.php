<?php

require_once '../db.php';

$id = $_GET['id'];
$name = $_POST['name'];
$description = $_POST['description'];
$price = $_POST['price'];
$stock = $_POST['stock'];



$request = $db->prepare("
    UPDATE `products` 
    SET 
        `name`=:name,
        `description`=:description,
        `price`=:price,
        `stock`=:stock
    WHERE `id`=:id
");

$request->bindParam(':name', $name);
$request->bindParam(':description', $description);
$request->bindParam(':price', $price);
$request->bindParam(':stock', $stock);
$request->bindParam(':id', $id);

$request->execute();

header("Location: ../../products.php");


?>