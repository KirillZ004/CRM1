<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crm";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}
$conn->set_charset("utf8");

require '../../vendor/autoload.php';
use Dompdf\Dompdf;

require_once '../db.php';
if($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])){
    

    $clientID = $_GET['id'];

    $dateFROM = $_GET['from'];
    $dateTo = $_GET['to'];

    $_SESSION['clients_errors'] = '';
    if($dateFROM > $dateTo){
        ob_start();

        echo '<ul class="error-list">';
             
        echo '<li><p style="color: white;">' . 'Дата "from" больше чем дата "to"' . '</p></li>';
        
        echo '</ul>';
        $_SESSION['clients_errors'] = ob_get_clean();  
        header("Location: ../../clients.php");
        exit;
    }

    $history = [
        'user' => '',
        'orders' => []
    ];

    // Добавляем запрос для получения заказов по ID клиента
    $query = "SELECT * FROM orders WHERE client_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$clientID]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($orders as $order) {
        // Запрос для получения элементов заказа
        $itemsQuery = "SELECT * FROM order_items WHERE order_id = ?";
        $itemsStmt = $db->prepare($itemsQuery);
        $itemsStmt->execute([$order['id']]);
        $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

        $history['orders'][] = [
            "id" => $order['id'],
            "date" => $order['order_date'],
            "total" => $order['total'],
            "items" => $items // Добавляем элементы заказа
        ];
    }
    $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
<style>
        body {
            font-family: "DejaVu Sans", sans-serif;
            line-height: 1.6;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1><strong>История заказов клиента</strong></h1>';

    foreach ($history['orders'] as $order) {
        $html .= '<div class="order">
            <h2><strong>Заказ ID:</strong> ' . htmlspecialchars($order['id']) . '</h2>
            <p><strong>Дата</strong> : ' . htmlspecialchars($order['date']) . '</p>
            <p><strong>Итоговая цена</strong>: ' . htmlspecialchars($order['total']) . ' rub.</p>
            <div class="items">
                <h3>Order Items:</h3>';

        foreach ($order['items'] as $item) {
            $html .= '<div class="item">
                <p><strong>Продукт ID:</strong> ' . htmlspecialchars($item['product_id']) . '</p>
                <p><strong>Количество</strong>: ' . htmlspecialchars($item['quantity']) . '</p>
                <p><strong>Цена</strong>: ' . htmlspecialchars($item['price']) . ' rub.</p>
            </div>';
        }

        $html .= '</div></div>';
    }

    $html .= '</body></html>';


    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream('history.pdf');

}
?>