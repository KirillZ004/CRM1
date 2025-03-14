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

    $fields = ['fullname', 'email', 'phone', 'birthdate'];
    $errors = [];
    $_SESSION['clients_errors'] = '';

    foreach ($fields as $field) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            $errors[$field][] = 'Field is required';
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
        $_SESSION['clients_errors'] = ob_get_clean();  
        header("Location: ../../clients.php");
        exit;
    }

    foreach ($formData as $key => $value) {
        $formData[$key] = clearData($value);
    }

    $phone = $formData['phone'];
    $IdClients = $db->query(
        "SELECT id FROM clients WHERE phone = '$phone'
         ") ->fetchAll();

    if(empty($IdClients)){
        $created_at = date('Y-m-d H:i:s');
        $request = $db->
        prepare("
        INSERT INTO `clients`( 
        `name`, 
        `email`,
        `phone`,
        `birthday`,
        `created_at`) 
        VALUES (?,?,?,?,?) 
        "
        )->execute([
            $formData['fullname'],
            $formData['email'],
            $formData['phone'],
            $formData['birthdate'],
            $created_at
        ]);
        header("Location: ../../clients.php");

    }else{
        echo json_encode([
            "error" => 'Такой пользователь уже есть'
        ]);
    }

         
} else {
    echo json_encode([
        "error" => 'Неверный запрос'
    ]);
}
?>
