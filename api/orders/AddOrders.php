<?php
session_start();
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $formData = $_POST;

    $fields = ['client', 'products'];
    $errors = [];
    $_SESSION['orders_errors'] = '';

    if ($formData['client'] === 'new') {
        $fields[] = 'email';
    }

    foreach ($fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            $errors[$field][] = 'Field is required';
        }
    }

    // Start a transaction
    $db->beginTransaction();

    try {
        $clienID = $formData['client'] === 'new' ? time() : $formData['client'];

        if ($formData['client'] === 'new') {
            // Check if email is provided
            $email = isset($formData['email']) ? $formData['email'] : '';

            $stmt = $db->prepare("
                insert into clients(id,name,email,phone) value(?,?,?,?)
            ");
            $stmt->execute([
                $clienID,
                'USER#' . $clienID,
                $email,
                '0 (000)000 00 00',
            ]);
        }  // End of new client creation

        // ***CRITICAL: Verify client ID exists***
        $stmt = $db->prepare("SELECT id FROM clients WHERE id = ?");
        $stmt->execute([$clienID]);
        $existingClient = $stmt->fetch();

        if (!$existingClient) {
            throw new Exception("Client ID {$clienID} does not exist.");
        }

        $productIds = $formData['products'];
        $allProducts = $db->query("
            SELECT id, name, price, stock 
            FROM products
            WHERE id IN (" . implode(',', $productIds) . ")
        ")->fetchAll();

        // Подсчитываем общую сумму заказа

        $total = $db->query("SELECT SUM(price) FROM products WHERE id IN (" . implode(',',$formData['products']) . ")")->fetchColumn();
        $adminID = $db->query("SELECT id FROM users WHERE token = '$token' ")->fetchAll(PDO::FETCH_ASSOC)[0]['id'];
        $orders = [
            'id' => time(),
            'client_id' => $clienID, // Use the verified $clienID
            'total' => $total,
            'status' => '1',
            'admin' => $adminID
        ];

        if (!empty($errors)) {
            ob_start();

            echo '<ul class="error-list">';
            foreach ($errors as $field => $messages) {
                foreach ($messages as $message) {
                    echo '<li><p style="color: white;">' . htmlspecialchars($field) . ' : ' . htmlspecialchars($message) . '</p></li>';
                }
            }
            echo '</ul>';
            $_SESSION['orders_errors'] = ob_get_clean();
            header("Location: ../../orders.php");
            exit;
        }

        // Добавляем заказ в базу данных
        $stmt = $db->prepare("
            INSERT INTO orders (id,client_id, total,status, admin) 
            VALUES (:id,:client_id, :total, :status, :admin)
        ");

        $stmt->execute($orders);

        // Добавляем элементы заказа в таблицу order_items
        $order_id = $orders['id'];
        $stmt_items = $db->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, price) 
            VALUES (:order_id, :product_id, :quantity, :price)
        ");

        foreach ($productIds as $product_id) {

            $product = array_filter($allProducts, function ($p) use ($product_id) {
                return $p['id'] == $product_id;
            });
            $product = reset($product);
            $item_data = [
                'order_id' => $order_id,
                'product_id' => $product_id,
                'quantity' => 1,
                'price' => $product['price']
            ];

            $stmt_items->execute($item_data);
        }

        // Commit the transaction
        $db->commit();

        header("Location: ../../orders.php");
        exit;
    } catch (Exception $e) {
        // Rollback the transaction on error
        $db->rollBack();
        echo "Error: " . $e->getMessage();  // For debugging.  Don't display in production!
        // Consider logging the error and redirecting to an error page.
        $_SESSION['orders_errors'] = '<ul class="error-list"><li><p style="color: white;">' . htmlspecialchars($e->getMessage()) . '</p></li></ul>';
        header("Location: ../../orders.php");
        exit;
    }
} else {
    echo json_encode([
        "error" => 'Неверный запрос'
    ]);
}
?>
