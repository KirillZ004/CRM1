<?php
session_start();
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $ticketId = $_POST['ticket_id'];
        $message = $_POST['message'];
        
        if (empty($message)) {
            $_SESSION['message_error'] = 'Сообщение не может быть пустым';
            header('Location: ../../clients.php?msg=' . $ticketId);
            exit;
        }

        // Получаем ID пользователя из токена
        $token = $_SESSION['token'];
        $stmt = $db->prepare("SELECT id FROM users WHERE token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $userId = $user ? $user['id'] : 0;

        if ($userId > 0) {
            // Сохраняем сообщение
            $stmt = $db->prepare("INSERT INTO tickets_message (ticket_id, user_id, message) VALUES (?, ?, ?)");
            $stmt->execute([$ticketId, $userId, $message]);
            
            header('Location: ../../clients.php?msg=' . $ticketId);
            exit;
        } else {
            $_SESSION['message_error'] = 'Ошибка авторизации';
            header('Location: ../../clients.php?msg=' . $ticketId);
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['message_error'] = 'Произошла ошибка при отправке сообщения';
        error_log("Message creation error: " . $e->getMessage());
        header('Location: ../../clients.php?msg=' . $ticketId);
        exit;
    }
} else {
    header('Location: ../../clients.php');
    exit;
} 