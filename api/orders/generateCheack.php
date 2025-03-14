<?php
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

// Проверка наличия ID заказа
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $orderID = $_GET['id'];
    
    // SQL-запрос для получения информации о заказе
    $orderQuery = "SELECT 
                    orders.id,
                    orders.order_date,
                    orders.total,
                    clients.name as client_name,
                    users.name as admin_name,
                    users.surname as admin_surname
                   FROM orders
                   LEFT JOIN clients ON orders.client_id = clients.id
                   LEFT JOIN users ON users.id = 1
                   WHERE orders.id = ?";

    // Получение данных о заказе
    $stmt = $conn->prepare($orderQuery);
    $stmt->bind_param("i", $orderID);
    $stmt->execute();
    $orderResult = $stmt->get_result()->fetch_assoc();

    // SQL-запрос для получения товаров в заказе
    $itemsQuery = "SELECT 
                    products.name,
                    products.price,
                    order_items.quantity,
                    (order_items.quantity * order_items.price) as total
                   FROM order_items
                   LEFT JOIN products ON order_items.product_id = products.id
                   WHERE order_items.order_id = ?";

$stmt = $conn->prepare($itemsQuery);
$stmt->bind_param("i", $orderID);
$stmt->execute();
$orderItems = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$data = [
 "orderID" => $orderID,
 "orderDate" => $orderResult['order_date'],
 "adminName" => $orderResult['admin_name'] . ' ' . $orderResult['admin_surname'],
 "clientName" => $orderResult['client_name'],
 "orderItems" => $orderItems,
 "total" => $orderResult['total']
];

$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
            line-height: 1.6;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .info {
            margin-bottom: 20px;
        }
        .info-row {
            margin: 5px 0;
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
        .total {
            text-align: right;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Кассовый чек</h1>
    </div>
    
    <div class="info">
        <div class="info-row"><strong>Номер заказа:</strong> ' . $data["orderID"] . '</div>
        <div class="info-row"><strong>Дата:</strong> ' . $data["orderDate"] . '</div>
        <div class="info-row"><strong>Клиент:</strong> ' . $data["clientName"] . '</div>
        <div class="info-row"><strong>Обслуживающий персонал:</strong> ' . $data["adminName"] . '</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Наименование</th>
                <th>Цена</th>
                <th>Количество</th>
                <th>Сумма</th>
            </tr>
        </thead>
        <tbody>';

foreach ($data["orderItems"] as $item) {
    $html .= '
            <tr>
                <td>' . $item["name"] . '</td>
                <td>' . number_format($item["price"], 2) . ' ₽</td>
                <td>' . $item["quantity"] . '</td>
                <td>' . number_format($item["total"], 2) . ' ₽</td>
            </tr>';
}

$html .= '
        </tbody>
    </table>

    <div class="total">
        Итого к оплате: ' . number_format($data["total"], 2) . ' ₽
    </div>
</body>
</html>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('order.pdf');
}
?>