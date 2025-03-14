<?php
require '../../vendor/autoload.php';
require_once '../db.php';
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
if($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])){

    $id = $_GET['id'];

    $productInfo = $db->query(
        "SELECT * FROM products WHERE id = $id")->fetchAll(PDO::FETCH_ASSOC)[0];

    $productName = $productInfo['name'];
    $productDesc = $productInfo['description'];
    $productPrice = $productInfo['price'];
    $qrText = "
        ИД заказа: $id
        Название товара: $productName
        Описание товара: $productDesc
        Цена товара: $productPrice 
    ";


$QrCode= new QrCode($qrText);
$writer= new PngWriter();
$result= $writer->write($QrCode);
header('Content-Type: '.$result->getMimeType());
echo $result->getString();
}

?>