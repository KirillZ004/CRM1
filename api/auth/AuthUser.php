<?php
session_start();
//вход по логину м паролю админа
require_once '../db.php';
if($_SERVER['REQUEST_METHOD'] === "POST"){
    $login = isset($_POST["login"]) ? $_POST["login"] : '';
    $password = isset($_POST["password"]) ? $_POST["password"] : '';
    
    $_SESSION['login-errors'] = [''];


    if(!$login ){
        $_SESSION['login-errors']['login'] = 'Field is required';
    }
    if(!$password){
        $_SESSION['login-errors']['password'] = 'Field is required';
    }
    if(!$login || !$password){
        header('Location: ../../login.php');
        exit;
    }
    // Функция для очистки входных данных
    function clearData($field) {
        $cleaned = strip_tags($field);
        $cleaned = trim($cleaned);
        $cleaned = preg_replace('/\s+/',' ',$cleaned);
        return $cleaned;
    }
    
    $login = clearData($login);
    $password = clearData($password);

    $adminID= $db->query(
        "SELECT * FROM users WHERE login='$login'"
    )->fetchAll();
 
        if(empty($adminID) ){
            $_SESSION['login-errors']['login'] = 'User not found';
            header('Location: ../../login.php');
            exit;
        }
        $adminID= $db->query(
            "SELECT * FROM users WHERE login='$login' AND password='$password'"
        )->fetchAll();
     
            if(empty($adminID) ){
                $_SESSION['login-errors']['password'] = 'Wrong password';
                header('Location: ../../login.php');
                exit;
            }

                $uniquerString = time();
                $token = base64_encode(
                    "login=$login&password=$password&unique=$uniquerString"
                );

                echo $token;

                $db->query(
                    "UPDATE users SET token = '$token'
                WHERE login = '$login' AND password = '$password' 
                "
            )->fetchAll();
                
            $_SESSION['token'] = $token;

            header('Location: ../../clients.php');
                    
            


} else {
    echo json_encode([
        "error" => "Неверный запрос",
    ]);
}
?>