<?php

require_once '../db.php';

if(isset($_GET['id']) && !empty($_GET['id'])){

    $id = $_GET['id'];

    $db->query(
        "   UPDATE orders 
            SET status = '0' 
            WHERE id = '$id'"
)->fetchAll();
header("Location: ../../orders.php");
}else{
    header("Location: ../../orders.php");
}

?>