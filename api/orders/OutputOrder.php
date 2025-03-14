<?php


function OutputOrders($clients) {
    require_once 'api/helpers/convertdate.php';
    foreach ($clients as $client) {
        // Извлекаем данные клиента
        $id = htmlspecialchars($client['id']);
        $name = htmlspecialchars($client['name']);
        $order_date = formatOrderDate($client['order_date']);
        $total = htmlspecialchars($client['total']);
        $product_names = str_replace(",", "<br/>",$client['product_names']); 
        $status = '';
        if(htmlspecialchars($client['status']) === '0'){
            $status = 'Не активен';
        }
        if(htmlspecialchars($client['status']) === '1'){
            $status = 'Активен';
        }
        $adminName =  htmlspecialchars($client['admin']); ;
       

        // Выводим строку таблицы с данными клиента
        echo "
            <tr>
                    <tr>
                            <td>$id</td>
                            <td>$name</td>
                            <td>$order_date</td>
                            <td>$total</td>
                            <td>$product_names</td>
                            <td>$status</td>
                            <td>$adminName</td>
                            <td><a href='?edit-orders=$id'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a></td>
                            
                            <td><a href='api/orders/DeleteOrders.php?id=$id'><i class='fa fa-trash fa-1x' aria-hidden='true' ></i>
                            </a></td>
                            <td><a href='api/orders/generateCheack.php?id=$id''><i class='fa fa-qrcode fa-1x' aria-hidden='true'></i></a>
                            </td>
                            <td><i class='fa fa-info-circle fa-1x' aria-hidden='true' onclick=\"MicroModal.show('info-modal')\"></i>
                            </td>
                        </tr>
        ";
    }
}

// <td><i class='fa fa-trash' aria-hidden='true' onclick='MicroModal.show(\"delete-modal\")'></i></td>

?>