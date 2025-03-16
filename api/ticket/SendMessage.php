<?php
// Начинаем сессию
session_start();

// Подключаем файл с подключением к базе данных
require_once '../../api/db.php';

// Устанавливаем заголовок для JSON-ответа
header('Content-Type: application/json');

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    // Если токен есть, пробуем получить пользователя по токену
    if (isset($_SESSION['token'])) {
        $userQuery = $db->prepare("SELECT id FROM users WHERE token = ?");
        $userQuery->execute([$_SESSION['token']]);
        $userData = $userQuery->fetch(PDO::FETCH_ASSOC);
        
        if ($userData && isset($userData['id'])) {
            $_SESSION['user_id'] = $userData['id'];
        } else {
            echo json_encode(['success' => false, 'error' => 'Пользователь не авторизован']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Пользователь не авторизован']);
        exit;
    }
}

// Проверяем, переданы ли необходимые параметры
if (!isset($_POST['ticket_id']) || !isset($_POST['message']) || empty($_POST['message'])) {
    echo json_encode(['success' => false, 'error' => 'Не указаны необходимые параметры']);
    exit;
}

$ticketId = $_POST['ticket_id'];
$message = $_POST['message'];
$userId = $_SESSION['user_id'];

// Если это демо-тикет, создаем реальный тикет и сохраняем сообщение
if ($ticketId == 'demo-1' || $ticketId == 'demo-2') {
    try {
        // Создаем новый тикет
        $insertTicketQuery = $db->prepare("
            INSERT INTO tickets (clients, type, status, create_at) 
            VALUES (?, ?, 'waiting', NOW())
        ");
        $ticketType = ($ticketId == 'demo-1') ? 'tech' : 'crm';
        $insertTicketQuery->execute([$userId, $ticketType]);
        
        // Получаем ID созданного тикета
        $ticketId = $db->lastInsertId();
        
        // Сохраняем сообщение
        $insertMessageQuery = $db->prepare("
            INSERT INTO ticket_message (ticket_id, user_id, message, created_at) 
            VALUES (?, ?, ?, NOW())
        ");
        $insertMessageQuery->execute([$ticketId, $userId, $message]);
        
        $messageId = $db->lastInsertId();
        
        echo json_encode(['success' => true, 'message_id' => $messageId, 'ticket_id' => $ticketId]);
        exit;
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Ошибка базы данных: ' . $e->getMessage()]);
        exit;
    }
}

try {
    // Проверяем, существует ли тикет с таким ID
    $ticketQuery = $db->prepare("SELECT * FROM tickets WHERE id = ?");
    $ticketQuery->execute([$ticketId]);
    $ticket = $ticketQuery->fetch(PDO::FETCH_ASSOC);
    
    if (!$ticket) {
        echo json_encode(['success' => false, 'error' => 'Тикет не найден']);
        exit;
    }
    
    // Сохраняем сообщение в базу данных
    $insertQuery = $db->prepare("
        INSERT INTO ticket_message (ticket_id, user_id, message, created_at) 
        VALUES (?, ?, ?, NOW())
    ");
    $insertQuery->execute([$ticketId, $userId, $message]);
    
    $messageId = $db->lastInsertId();
    
    // Обновляем статус тикета, если он был в статусе "waiting"
    if ($ticket['status'] == 'waiting') {
        $updateQuery = $db->prepare("UPDATE tickets SET status = 'work' WHERE id = ?");
        $updateQuery->execute([$ticketId]);
    }
    
    echo json_encode(['success' => true, 'message_id' => $messageId]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Ошибка базы данных: ' . $e->getMessage()]);
}
?> 