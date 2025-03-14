<?php

require_once '../db.php';

if(isset($_GET['id']) && !empty($_GET['id'])){

    $id = $_GET['id'];

    $db->query(
        "DELETE FROM products WHERE id = '$id'"
)->fetchAll();
header("Location: ../../products.php");
}else{
    header("Location: ../../products.php");
}

?>