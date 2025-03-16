<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['ticket_id'])) {
    echo json_encode([]);
    exit;
}

$userId = $_SESSION['user_id'];
$ticketId = (int)$_GET['ticket_id'];

// Проверяем, принадлежит ли тикет пользователю
$ticketCheck = $db->prepare("SELECT * FROM tickets WHERE id = ? AND clients = ?");
$ticketCheck->execute([$ticketId, $userId]);

if ($ticketCheck->rowCount() === 0) {
    echo json_encode(['error' => 'Доступ запрещен']);
    exit;
}

// Получаем сообщения
$messagesQuery = $db->prepare("
    SELECT tm.*, u.name, u.surname 
    FROM ticket_message tm
    LEFT JOIN users u ON tm.user_id = u.id
    WHERE tm.ticket_id = ?
    ORDER BY tm.created_at ASC
");
$messagesQuery->execute([$ticketId]);
$messages = $messagesQuery->fetchAll(PDO::FETCH_ASSOC);

$formattedMessages = [];
foreach ($messages as $message) {
    $formattedMessages[] = [
        'id' => $message['id'],
        'message' => $message['message'],
        'author' => $message['name'] . ' ' . $message['surname'],
        'time' => date('d.m.Y H:i', strtotime($message['created_at'])),
        'is_mine' => $message['user_id'] == $userId
    ];
}

echo json_encode($formattedMessages);
?> 