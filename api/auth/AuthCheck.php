<?php
require_once __DIR__ . '/../db.php';

function AuthCheck($successPath = '', $errorPath = '') {
   
    require_once 'LogoutUser.php';
   // $_SESSION['token']='';

    // if (isset($_SESSION['token']) && !empty($_SESSION['token'])) {

    //     // Если токен валиден, редиректим на успешный путь
    //     header("Location: $successPath");
    //     exit();
    // } else {
    //     // Если токен отсутствует или пустой, редиректим на путь ошибки
    //     header("Location: $errorPath");
    //     exit();
    // }

    if (!isset($_SESSION['token'])) {
        if($errorPath){
             header("Location: $errorPath");
        }
        // Если токен отсутствует или пустой, редиректим на путь ошибки
       
        return;
    }
    global $db;
    $token=$_SESSION['token'];
    $adminID= $db->query(
        "SELECT id FROM users WHERE token='$token'"
    )->fetchAll();

    //echo json_encode ($adminID);

    if ($db === null) {
        error_log("Ошибка: объект базы данных не инициализирован.");
        return false;
    }

    try {
        // Пример использования $db
        $stmt = $db->query("SELECT * FROM some_table WHERE some_column = 'some_value'");
        // ... остальной код ...
    } catch (PDOException $e) {
        error_log("Ошибка при выполнении запроса: " . $e->getMessage());
        return false;
    }

    if (!empty($adminID) && $successPath) {
        // Если токен валиден, редиректим на успешный путь
        header("Location: $successPath");
        exit();
    } 
    if(empty($adminID) && $errorPath){
        // Если токен отсутствует или пустой, редиректим на путь ошибки

        logoutUser($errorPath,$db);

        header("Location: $errorPath");
        exit();
    }
}

?>
