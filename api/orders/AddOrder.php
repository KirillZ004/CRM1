<?php
session_start();
require_once '../db.php';

function clearData($field) {
    $cleaned = strip_tags($field);
    $cleaned = trim($cleaned);
    $cleaned = preg_replace('/\s+/', ' ', $cleaned);
    return $cleaned;
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $formData = $_POST;
    $fields = ['name', 'desc', 'price', 'stock'];
    $errors = [];
    $_SESSION['products_errors'] = '';

    foreach ($fields as $field) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            $errors[$field][] = 'Поле обязательно для заполнения';
        }
    }

    if (!empty($errors)) {
        ob_start();
        echo '<ul class="error-list">';
        foreach ($errors as $field => $messages) {
            foreach ($messages as $message) {
                echo '<li><p style="color: white;">' . htmlspecialchars($field) . ' : ' . htmlspecialchars($message) . '</p></li>';
            }
        }
        echo '</ul>';
        $_SESSION['products_errors'] = ob_get_clean();  
        header("Location: ../../products.php");
        exit;
    }

    foreach ($formData as $key => $value) {
        $formData[$key] = clearData($value);
    }

    $productName = $formData['name'];
    $existingProduct = $db->query(
        "SELECT id FROM products WHERE name = '$productName'"
    )->fetchAll();

    if (empty($existingProduct)) {
        $request = $db->prepare("
            INSERT INTO products (name, desc, price, stock) 
            VALUES (?, ?, ?, ?)
        ")->execute([
            $formData['name'],
            $formData['desc'],
            floatval($formData['price']),
            intval($formData['stock'])
        ]);
        
        header("Location: ../../products.php");
        exit;
        
    } else {
        echo json_encode([
            "error" => 'Продукт с таким именем уже существует'
        ]);
    }

} else {
    echo json_encode([
        "error" => 'Неверный запрос'
    ]);
}
?>
