<?php
session_start();
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $type = $_POST['type'];
        $message = $_POST['message'];
        
        if (empty($message)) {
            $_SESSION['tickets_error'] = 'Сообщение не может быть пустым';
            header('Location: ../../clients.php');
            exit;
        }

        // Получаем ID пользователя из токена
        $token = $_SESSION['token'];
        $stmt = $db->prepare("SELECT id FROM users WHERE token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $userId = $user ? $user['id'] : 0;

        if ($userId > 0) {
            // Создаем тикет
            $stmt = $db->prepare("INSERT INTO tickets (user_id, type, message, status) VALUES (?, ?, ?, 'open')");
            $stmt->execute([$userId, $type, $message]);

            // Устанавливаем флаг для открытия модального окна
            $_SESSION['show_tickets'] = true;
            
            header('Location: ../../clients.php');
            exit;
        } else {
            $_SESSION['tickets_error'] = 'Ошибка авторизации';
            header('Location: ../../clients.php');
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['tickets_error'] = 'Произошла ошибка при создании тикета';
        error_log("Ticket creation error: " . $e->getMessage());
        header('Location: ../../clients.php');
        exit;
    }
} else {
    header('Location: ../../clients.php');
    exit;
}

?>