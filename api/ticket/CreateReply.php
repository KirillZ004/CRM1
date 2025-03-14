<?php
session_start();
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $ticketId = $_POST['ticket_id'];
        $message = $_POST['reply_message'];
        
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
            // Добавляем ответ
            $stmt = $db->prepare("INSERT INTO ticket_replies (ticket_id, user_id, message) VALUES (?, ?, ?)");
            $stmt->execute([$ticketId, $userId, $message]);

            // Обновляем статус тикета
            $stmt = $db->prepare("UPDATE tickets SET status = 'replied' WHERE id = ?");
            $stmt->execute([$ticketId]);

            header('Location: ../../clients.php');
            exit;
        } else {
            $_SESSION['tickets_error'] = 'Ошибка авторизации';
            header('Location: ../../clients.php');
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['tickets_error'] = 'Произошла ошибка при создании ответа';
        error_log("Reply creation error: " . $e->getMessage());
        header('Location: ../../clients.php');
        exit;
    }
} else {
    header('Location: ../../clients.php');
    exit;
} 