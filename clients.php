<?php

session_start();

require_once 'api/helpers/inputDefaultValue.php';

if(isset($_GET['do']) && $_GET['do'] === 'logout'){
    require_once 'api/auth/LogoutUser.php';
    require_once 'api/db.php';
    LogoutUser('login.php',$db,$_SESSION['token']);

    exit;
}

require_once 'api/auth/AuthCheck.php';

require_once 'api/db.php';

AuthCheck('','login.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM | Клиенты</title>
    <link rel="stylesheet" href="styles/settings.css">
    <link rel="stylesheet" href="styles/pages/clients.css">
    <link rel="stylesheet" href="styles/modules/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="styles/modules/microModal.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <p class="header_admin">
                <?php
                    require 'api/db.php';
                    require_once 'api/clients/AdminName.php';
                    require_once 'api/helpers/getUserType.php';
                    echo AdminName($_SESSION['token'],$db);
                    
                    // Получаем ID пользователя из сессии
                    $userID = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

                        $userType = getUserType($db);
                        echo " <span style='color: #4CAF50; margin-left: 5px;'>(" . ucfirst($userType) . ")</span>";
                ?>
            </p>
            <ul class="header_links"> 
                <li><a href="clients.php">Клиенты</a></li>
                <li><a href="products.php">Товары</a></li>
                <li><a href="orders.php">Заказы</a></li>
                <?php
                if($userType == 'tech'){
                  echo "<li><a href='tech.php'>Обращение пользователя</a></li>";
                }
                ?>
            </ul>
            <a href = '?do=logout' class="header_logout">Выйти</a>
        </div>
    </header>
    <main>
        <section class="filters">
            <div class="container">
                <form action = "" method = "GET" class = "main_form">
                    <i class="fa fa-address-book" aria-hidden="true"></i>
                    <label for="search">Поиск</label>
                    <input type="text" id="search" name="search" placeholder="Александр" <?php inputDefaultValue("search","");?>>
                    <select value = "email" name="search_name" id="search_name" >
                    <?php 
                    $searchNameOptions = [
                      [
                        'key' => 'name',
                        'value' => 'Поиск по имени'
                    ],
                    [
                      'key' => 'email',
                      'value' => 'Поиск по почте'
                    ]
                    ];
                    selectDefaultValue("search_name",$searchNameOptions,"name");
                    ?>
                    </select>
                    <select name="sort" id="sort">


                        <?php 
                    $searchNameOptions = [
                      [
                        'key' => '',
                        'value' => 'По умолчанию'
                    ],
                    [
                      'key' => 'ASC',
                      'value' => 'По возрастанию'
                    ],
                    [
                      'key' => 'DESC',
                      'value' => 'По убыванию'
                    ]
                    ];
                    selectDefaultValue("sort",$searchNameOptions,"");
                    ?>
                    </select>
                    <button class = "search" type = "submit">Поиск</button>
                    <a class = "search" href="?">Сбросить</a>
                </form>
            </div>
        </section>
        <section class="clients">
            <div class="container">
                        <h2 class="clients_title">Список клиентов</h2>
                     
                        </button>


                        <div class = "pages" >       
                          <button onclick="MicroModal.show('add-modal')" class="clients_add"><i class="fa fa-plus-square fa-2x" aria-hidden="true"></i>
                                

                                               
   
                        <?php
                          require_once 'api/db.php';
                          $totalClientsQuery = $db->query("SELECT COUNT(*) AS total_clients FROM clients");
                          $totalClients = $totalClientsQuery->fetch(PDO::FETCH_ASSOC)['total_clients'];
                          $maxClients = 5;
                          $maxPage = ceil($totalClients / $maxClients);
                          $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

                          // Ограничение текущей страницы
                          if ($currentPage < 1) {
                              $currentPage = 1;
                          } elseif ($currentPage > $maxPage) {
                              $currentPage = $maxPage;
                          }

                          echo "<a href='?page=" . ($currentPage - 1) . "'>
                          <i class='fa fa-arrow-left fa-2x' aria-hidden='true'></i>
                    </a>";
                          echo '<p>' . $currentPage . '...</p>' . '<p>' . $maxPage . '</p>';
                          echo "<a href='?page=" . ($currentPage + 1) . "'>
                        <i class='fa fa-arrow-right fa-2x' aria-hidden='true'></i>
                  </a>";
                        ?> 
                        </div>

                  
                   
                        <div class="table-wrap">
                        <table>
                    <thead>
                        <th>ИД</th>
                        <th>ФИО</th>
                        <th>Почта</th>
                        <th>Телефон</th>
                        <th>День рождения</th>
                        <th>Дата создания</th>
                        <th>История заказа</th>
                        <th>Радактировать</th>
                        <th>Удалить</th>
                    </thead>
                    <tbody>
                        <?php
                            
                            require_once 'api/db.php';
                            require_once 'api/clients/OutputClients.php';
                            require_once 'api/clients/ClientsSearch.php';


                            $clients = ClientsSearch($_GET, $db);
                            
                            // $clients = $db->query(
                            //     "SELECT * FROM clients
                            //      ") ->fetchAll();
                            
                            OutputClients($clients);
                        ?>
                  
                    </tbody>
                </table>
            </div>
            </div>
        </section>
    </main>

    <button class='support-btn'><i class="fa fa-question-circle fa-3x" aria-hidden="true"></i></button>


    <div class="support-create-tickets">
    <form action="api/ticket/CreateTicket.php" method='POST'>
        <label for="type">Тип обращения</label>
        <select name="type" id="type">
            <option value="tech">Техническая неполадка</option>
            <option value="crm">Проблема с CRM</option>
        </select>
        <label for="message">Текст сообщения</label>
        <textarea name="message" id="message"></textarea>
        <input type="file" name='file' id="file">
        <div class="button-group">
            <button type="submit">Создать тикет</button>
            <button type="button" class="my-tickets-btn">Мои обращения</button>
            <button type="button" class="close-create-ticket">Отмена</button>
        </div>
    </form>
</div>

<div class="my-tickets-modal">
    <div class="modal-content">
        <h2>Мои обращения</h2>
        <div class="tickets-list">
            <?php
            try {
                // Проверяем существование таблицы tickets
                $tableCheck = $db->query("SHOW TABLES LIKE 'tickets'")->fetchAll();
                
                if (empty($tableCheck)) {
                    // Создаем таблицу если она не существует
                    $db->exec("CREATE TABLE tickets (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        user_id INT NOT NULL,
                        type VARCHAR(50) NOT NULL,
                        message TEXT NOT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        status VARCHAR(20) DEFAULT 'open',
                        FOREIGN KEY (user_id) REFERENCES users(id)
                    )");
                } else {
                    // Проверяем наличие колонки created_at
                    $columns = $db->query("SHOW COLUMNS FROM tickets LIKE 'created_at'")->fetchAll();
                    if (empty($columns)) {
                        // Добавляем колонку created_at если её нет
                        $db->exec("ALTER TABLE tickets ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER message");
                    }
                }

                // Проверяем существование таблицы ticket_replies
                $tableCheck = $db->query("SHOW TABLES LIKE 'ticket_replies'")->fetchAll();
                
                if (empty($tableCheck)) {
                    // Создаем таблицу для ответов если она не существует
                    $db->exec("CREATE TABLE ticket_replies (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        ticket_id INT NOT NULL,
                        user_id INT NOT NULL,
                        message TEXT NOT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        FOREIGN KEY (ticket_id) REFERENCES tickets(id)
                    )");
                }
                
                // Получаем ID пользователя из токена
                $token = $_SESSION['token'];
                $stmt = $db->prepare("SELECT id FROM users WHERE token = ?");
                $stmt->execute([$token]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                $userID = $user ? $user['id'] : 0;
                
                if ($userID > 0) {
                    try {
                        // Получаем тикеты
                        $stmt = $db->prepare("
                            SELECT t.*, COALESCE(t.created_at, NOW()) as ticket_date 
                            FROM tickets t 
                            WHERE t.user_id = ? 
                            ORDER BY ticket_date DESC
                        ");
                        $stmt->execute([$userID]);
                        $tickets = $stmt->fetchAll();
                        
                        if (empty($tickets)) {
                            echo "<p>У вас пока нет обращений</p>";
                        } else {
                            foreach ($tickets as $ticket) {
                                echo "<div class='ticket-item'>";
                                echo "<div class='ticket-header'>";
                                echo "<span class='ticket-id'>Тикет №" . $ticket['id'] . "</span>";
                                echo "<span class='ticket-type'>Тип: " . ucfirst($ticket['type']) . "</span>";
                                echo "<span class='ticket-date'>Дата: " . $ticket['ticket_date'] . "</span>";
                                echo "<span class='ticket-status' data-status='" . $ticket['status'] . "'>Статус: " . ucfirst($ticket['status']) . "</span>";
                                echo "</div>";
                                echo "<div class='ticket-message'>" . $ticket['message'] . "</div>";
                                
                                // Получаем ответы для текущего тикета
                                $repliesStmt = $db->prepare("
                                    SELECT tr.*, u.type as user_type 
                                    FROM ticket_replies tr 
                                    LEFT JOIN users u ON tr.user_id = u.id 
                                    WHERE tr.ticket_id = ? 
                                    ORDER BY tr.created_at ASC
                                ");
                                $repliesStmt->execute([$ticket['id']]);
                                $replies = $repliesStmt->fetchAll();
                                
                                if (!empty($replies)) {
                                    echo "<div class='ticket-replies'>";
                                    echo "<h4>Ответы:</h4>";
                                    foreach ($replies as $reply) {
                                        $isAdmin = ($reply['user_type'] === 'admin' || $reply['user_type'] === 'tech');
                                        echo "<div class='reply " . ($isAdmin ? 'admin-reply' : 'user-reply') . "'>";
                                        echo "<div class='reply-header'>";
                                        echo "<div class='reply-date'>Дата: " . $reply['created_at'] . "</div>";
                                        echo "<div class='reply-author'>" . ($isAdmin ? 'Администратор' : 'Пользователь') . "</div>";
                                        echo "</div>";
                                        echo "<div class='reply-message'>" . $reply['message'] . "</div>";
                                        echo "</div>";
                                    }
                                    echo "</div>";
                                }
                                
                                // Форма для ответа
                                echo "<div class='reply-form' id='reply-form-" . $ticket['id'] . "' style='display: none;'>";
                                echo "<form action='api/ticket/CreateReply.php' method='POST'>";
                                echo "<input type='hidden' name='ticket_id' value='" . $ticket['id'] . "'>";
                                echo "<textarea name='reply_message' placeholder='Введите ваш ответ...'></textarea>";
                                echo "<button type='submit' class='send-reply-btn'>Отправить</button>";
                                echo "</form>";
                                echo "</div>";
                                
                                echo "<div class='ticket-actions'>";
                                echo "<button class='reply-btn' onclick='toggleReplyForm(" . $ticket['id'] . ")'>Ответить</button>";
                                echo "<button class='message-btn' onclick='window.location.href=\"?msg=" . $ticket['id'] . "\"'>Сообщение</button>";
                                echo "<button class='open-chat-btn' data-ticket-id='" . $ticket['id'] . "'>Открыть чат</button>";
                                echo "</div>";
                                
                                echo "</div>";
                            }
                        }
                    } catch (PDOException $e) {
                        echo "<p>Ошибка при получении данных: " . $e->getMessage() . "</p>";
                        error_log("Tickets error: " . $e->getMessage());
                    }
                } else {
                    echo "<p>Ошибка: не удалось определить ID пользователя</p>";
                }
            } catch (PDOException $e) {
                echo "<p>Произошла ошибка при получении списка обращений</p>";
                error_log("Tickets error: " . $e->getMessage());
            }
            ?>
        </div>
        <button class="close-tickets-modal">Закрыть</button>
    </div>
</div>

<!-- Модальное окно для сообщений -->
<div class="messages-modal" style="display: <?php echo isset($_GET['msg']) ? 'block' : 'none'; ?>">
    <div class="modal-content">
        <h2>Сообщения</h2>
        <div class="messages-list">
            <?php
            if (isset($_GET['msg'])) {
                try {
                    // Проверяем существование таблицы tickets_message
                    $tableCheck = $db->query("SHOW TABLES LIKE 'tickets_message'")->fetchAll();
                    
                    if (empty($tableCheck)) {
                        // Создаем таблицу для сообщений если она не существует
                        $db->exec("CREATE TABLE tickets_message (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            ticket_id INT NOT NULL,
                            user_id INT NOT NULL,
                            message TEXT NOT NULL,
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            FOREIGN KEY (ticket_id) REFERENCES tickets(id),
                            FOREIGN KEY (user_id) REFERENCES users(id)
                        )");
                    }

                    $ticketId = $_GET['msg'];
                    
                    // Получаем сообщения для текущего тикета
                    $stmt = $db->prepare("
                        SELECT tm.*, u.type as user_type 
                        FROM tickets_message tm 
                        LEFT JOIN users u ON tm.user_id = u.id 
                        WHERE tm.ticket_id = ? 
                        ORDER BY tm.created_at ASC
                    ");
                    $stmt->execute([$ticketId]);
                    $messages = $stmt->fetchAll();

                    if (empty($messages)) {
                        echo "<p>Нет сообщений</p>";
                    } else {
                        foreach ($messages as $message) {
                            $isAdmin = ($message['user_type'] === 'admin' || $message['user_type'] === 'tech');
                            echo "<div class='message " . ($isAdmin ? 'admin-message' : 'user-message') . "'>";
                            echo "<div class='message-header'>";
                            echo "<span class='message-author'>" . ($isAdmin ? 'Администратор' : 'Пользователь') . "</span>";
                            echo "<span class='message-date'>" . $message['created_at'] . "</span>";
                            echo "</div>";
                            echo "<div class='message-content'>" . $message['message'] . "</div>";
                            echo "</div>";
                        }
                    }

                    // Форма для отправки нового сообщения
                    echo "<div class='message-form'>";
                    echo "<form action='api/ticket/CreateMessage.php' method='POST'>";
                    echo "<input type='hidden' name='ticket_id' value='" . $ticketId . "'>";
                    echo "<textarea name='message' placeholder='Введите ваше сообщение...'></textarea>";
                    echo "<button type='submit' class='send-message-btn'>Отправить</button>";
                    echo "</form>";
                    echo "</div>";

                } catch (PDOException $e) {
                    echo "<p>Ошибка при получении сообщений: " . $e->getMessage() . "</p>";
                    error_log("Messages error: " . $e->getMessage());
                }
            }
            ?>
        </div>
        <button class="close-messages-modal" onclick="closeMessagesModal()">Закрыть</button>
    </div>
</div>

<script>
    document.querySelector('.support-btn').addEventListener('click', function() {
        document.querySelector('.support-create-tickets').style.display = 'block';
    });

    document.querySelector('.close-create-ticket').addEventListener('click', function() {
        document.querySelector('.support-create-tickets').style.display = 'none';
    });

    // Обработчик для кнопки "Мои обращения"
    document.querySelector('.my-tickets-btn').addEventListener('click', function() {
        showTicketsModal();
    });

    // Закрытие модального окна с обращениями
    document.querySelector('.close-tickets-modal').addEventListener('click', function() {
        document.querySelector('.my-tickets-modal').style.display = 'none';
    });

    // Функция для показа модального окна с тикетами
    function showTicketsModal() {
        document.querySelector('.support-create-tickets').style.display = 'none';
        document.querySelector('.my-tickets-modal').style.display = 'block';
    }

    // Автоматически открываем модальное окно если есть флаг
    <?php if (isset($_SESSION['show_tickets']) && $_SESSION['show_tickets']): ?>
        showTicketsModal();
        <?php unset($_SESSION['show_tickets']); ?>
    <?php endif; ?>

    // Обработчик для кнопок открытия чата
    document.querySelectorAll('.open-chat-btn').forEach(button => {
        button.addEventListener('click', function() {
            const ticketId = this.getAttribute('data-ticket-id');
            // Здесь можно добавить логику открытия чата
            console.log('Opening chat for ticket:', ticketId);
        });
    });

    function toggleReplyForm(ticketId) {
        const form = document.getElementById('reply-form-' + ticketId);
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }

    function closeMessagesModal() {
        document.querySelector('.messages-modal').style.display = 'none';
        // Удаляем параметр msg из URL
        const url = new URL(window.location.href);
        url.searchParams.delete('msg');
        window.history.replaceState({}, '', url);
    }
</script>

<style>
.my-tickets-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
}

.my-tickets-modal .modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    width: 80%;
    max-width: 600px;
    max-height: 80vh;
    overflow-y: auto;
    color: #000;
}

.my-tickets-modal h2 {
    color: #000;
}

.tickets-list {
    margin: 20px 0;
}

.tickets-list p {
    color: #000;
}

.ticket-item {
    border: 1px solid #ddd;
    margin-bottom: 10px;
    padding: 15px;
    border-radius: 4px;
    background-color: #f9f9f9;
}

.ticket-header {
    display: flex;
    justify-content: flex-start;
    gap: 15px;
    margin-bottom: 10px;
}

.ticket-id {
    font-weight: bold;
    color: #000;
    margin-right: 15px;
}

.ticket-type {
    font-weight: bold;
    color: #4CAF50;
}

.ticket-date {
    color: #666;
}

.ticket-message {
    margin-bottom: 10px;
    color: #000;
}

.open-chat-btn {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.open-chat-btn:hover {
    background-color: #45a049;
}

.close-tickets-modal {
    background-color: #f44336;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 10px;
}

.close-tickets-modal:hover {
    background-color: #da190b;
}

.button-group {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.my-tickets-btn {
    background-color: #2196F3;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
}

.my-tickets-btn:hover {
    background-color: #1976D2;
}

.ticket-replies {
    margin: 10px 0;
    padding: 10px;
    background-color: #f5f5f5;
    border-radius: 4px;
}

.ticket-replies h4 {
    margin: 0 0 10px 0;
    color: #000;
}

.reply {
    margin-bottom: 10px;
    padding: 10px;
    background-color: white;
    border-radius: 4px;
    border-left: 3px solid #4CAF50;
}

.reply-date {
    font-size: 0.9em;
    color: #666;
    margin-bottom: 5px;
}

.reply-message {
    color: #000;
}

.reply-form {
    margin-top: 10px;
    padding: 10px;
    background-color: #f5f5f5;
    border-radius: 4px;
}

.reply-form textarea {
    width: 100%;
    min-height: 100px;
    margin-bottom: 10px;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    resize: vertical;
}

.send-reply-btn {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
}

.send-reply-btn:hover {
    background-color: #45a049;
}

.ticket-actions {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.reply-btn {
    background-color: #2196F3;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
}

.reply-btn:hover {
    background-color: #1976D2;
}

.ticket-message {
    margin: 10px 0;
    padding: 10px;
    background-color: #fff;
    border-radius: 4px;
    border-left: 3px solid #2196F3;
}

.ticket-status {
    color: #666;
    font-weight: bold;
}

.reply-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 5px;
}

.reply-author {
    font-weight: bold;
    color: #2196F3;
}

.admin-reply {
    border-left: 3px solid #4CAF50;
    background-color: #f0f7f0;
}

.user-reply {
    border-left: 3px solid #2196F3;
    background-color: #f0f4f8;
}

.ticket-status {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.9em;
}

/* Стили для разных статусов */
.ticket-status[data-status="open"] {
    background-color: #2196F3;
    color: white;
}

.ticket-status[data-status="replied"] {
    background-color: #4CAF50;
    color: white;
}

.ticket-status[data-status="closed"] {
    background-color: #f44336;
    color: white;
}

/* Добавляем стили для кнопки сообщений */
.message-btn {
    background-color: #FF9800;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.message-btn:hover {
    background-color: #F57C00;
}

/* Стили для модального окна сообщений */
.messages-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
}

.messages-modal .modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    width: 80%;
    max-width: 600px;
    max-height: 80vh;
    overflow-y: auto;
    color: #000;
}

.messages-modal h2 {
    color: #000;
}

.messages-list {
    margin: 20px 0;
    color: #000;
}

.messages-list p {
    color: #000;
}

.message {
    margin-bottom: 10px;
    padding: 10px;
    border-radius: 4px;
    color: #000;
}

.message-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
    font-size: 0.9em;
    color: #000;
}

.message-author {
    font-weight: bold;
    color: #000;
}

.message-date {
    color: #666;
}

.message-content {
    color: #000;
}

.message-form {
    margin-top: 20px;
    padding: 10px;
    background-color: #f5f5f5;
    border-radius: 4px;
    color: #000;
}

.message-form textarea {
    width: 100%;
    min-height: 100px;
    margin-bottom: 10px;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    resize: vertical;
    color: #000;
}

.message-form textarea::placeholder {
    color: #666;
}

/* Обновляем стили для всех модальных окон */
.modal__container {
    color: #000;
}

.modal__title {
    color: #000;
}

.modal__content {
    color: #000;
}

.modal__content p {
    color: #000;
}

.modal__content label {
    color: #000;
}

.modal__content input,
.modal__content textarea {
    color: #000;
}

.form-group label {
    color: #000;
}

.form-group input,
.form-group textarea {
    color: #000;
}
</style>
<div class="modal micromodal-slide     
<?php
    if(isset($_SESSION['clients_errors']) && !empty($_SESSION['clients_errors'])){
      echo "open";
    }
    ?>" id="error-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
          <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
            <header class="modal__header">
              <h2 class="modal__title" id="modal-1-title">
                Ошибка
              </h2>
              <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
            </header>
            <main class="modal__content" id="modal-1-content">
              <?php
                 if(isset($_SESSION['clients_errors']) && !empty($_SESSION['clients_errors'])){
                  echo  $_SESSION['clients_errors'];
                  $_SESSION['clients_errors'] = '';
                }
              ?>
            </main>
          </div>
        </div>
      </div>

    
      <div class="modal micromodal-slide     
<?php
    if(isset($_GET['send-email']) && !empty($_GET['send-email'])){
      echo "open";
    }
    ?>" id="send-email-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
          <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
           <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
             <main class="modal__content" id="modal-1-content"> 
              <h2 class="modal__title" id="modal-1-title">
              Рассылка   
              </h2>   
              <?php
              $email = $_GET['send-email'];
              echo  "<p style='color: white;'>" . $_GET['send-email']. "</p>
                <form method = 'POST' action='api/clients/SendEmail.php?email=$email'>
                <div class='form-group'>
                  <label for='header'>Обращение</label>
                  <input type='text'  name = 'header' id='header'>
                </div>
                <div class='form-group'>
                  <label for='main'>Сообщение</label>
                  <textarea name='main' id='main'></textarea>
                </div>
                <div class='form-group'>
                  <label for='footer'>Футер</label>
                  <input type='text' name = 'footer' id='footer'>
                </div>
                <div class='button-group'>
                  <button type='submit' class='create'>Отправить</button>
                </div>
              </form>
              
              ";
              ?>

            </main>


          </div>
        </div>
      </div>
    
    <div class="modal micromodal-slide" id="add-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
          <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
            <header class="modal__header">
              <h2 class="modal__title" id="modal-1-title">
                Добавить клиента
              </h2>
              <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
            </header>
            <main class="modal__content" id="modal-1-content">
                <form action = "api/clients/AddClients.php" method = "POST">
                    <div class="form-group">
                        <label for="fullname">ФИО</label>
                        <input type="text" id="fullname" name="fullname" placeholder="Введите ваше ФИО" >
                    </div>
                    <div class="form-group">
                        <label for="email">Почта</label>
                        <input type="email" id="email" name="email" placeholder="Введите вашу почту" >
                    </div>
                    <div class="form-group">
                        <label for="phone">Телефон</label>
                        <input type="tel" id="phone" name="phone" placeholder="Введите ваш телефон" >
                    </div>
                    <div class="form-group">
                        <label for="birthdate">День рождения</label>
                        <input type="date" id="birthdate" name="birthdate" >
                    </div>
                    <div class="button-group">
                        <button type="submit" class="create">Создать</button>
                        <button type="button" class="cancel" onclick="window.location.reload();">Отменить</button>
                    </div>
                </form>
            </main>
          </div>
        </div>
      </div>

    <div class="modal micromodal-slide" id="delete-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
          <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
            <header class="modal__header">
              <h2 class="modal__title" id="modal-1-title">
                Вы уверены, что хотите удалить клиента ?
              </h2>
              <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
            </header>
            <main class="modal__content" id="modal-1-content">
                <form>
                    <div class="button-group">
                        <button type="submit" class="create">Удалить</button>
                        <button type="button" class="cancel" onclick="window.location.reload();">Отменить</button>
                    </div>
                </div>
                </form>
            </main>
          </div>
        </div>
        <div class="modal micromodal-slide <?php
    if (isset($_GET['edit-user']) && !empty($_GET['edit-user'])) {
        echo 'open';
    }
    ?>" id="edit-user-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
                <header class="modal__header">
                    <h2 class="modal__title" id="modal-1-title">
                        Редактировать
                    </h2>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                    <?php
                    $editUser = $_GET['edit-user'];
                              $clients = $db->query(
                                  "SELECT *  FROM clients   WHERE id = $editUser 
                                  ") ->fetchAll(); 
                    $name;
                    $email;
                    $phone;
                    $birthday;
                foreach($clients as $client){
                    $name = $client['name'];
                    $email = $client['email'];
                    $phone = $client['phone'];
                    $birthday = $client['birthday'];
                }
                    if (isset($_GET['edit-user']) && !empty($_GET['edit-user'])) {
                        echo "<form method='POST' action='api/clients/EditClients.php?id=$editUser'>
    
    <div class='form-group'>
        <label for='name'>Имя пользователя</label>
        <input type='text' id='name' name='name' value = '$name' required>
    </div>
    
    <div class='form-group'>
        <label for='email'>Почта</label>
        <input type='text' id='email' name='email' value = '$email' required>
    </div>
    
    <div class='form-group'>
        <label for='phone'>Телефон</label>
        <input type='text' id='phone' name='phone' value = '$phone' required>
    </div>
    
    <div class='button-group'>
        <button type='submit' class='create'>Изменить</button>
        <button type='button' class='cancel' data-micromodal-close>Отмена</button>
    </div>
</form>

";
                    }
                    ?>
            </div>
        </div>
    </div>

    <div class="modal micromodal-slide" id="history-modal" aria-hidden="true">
            <div class="modal__overlay" tabindex="-1" data-micromodal-close>
              <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
                <header class="modal__header">
                  <h2 class="modal__title" id="modal-1-title">
                    История заказов
                  </h2>
                  <small>Фамилия Имя Отчество</small>
                  <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content" id="modal-1-content">
                    <form>
                        <div class="order">
                                <div class="order_info">
                                    <h3 class="order_number">Заказ №1</h3>
                                    <time class="order_date">Дата оформления :<b> 2025-01-13 09:25:03</b></time>
                                    <p class="order_total">Общая сумма : <b>300.00р</b></p>
                                </div>
                                
                                    <table class="order_items">
                                        <tr>
                                            <th>ИД</th>
                                            <th>Название товара</th>
                                            <th>Количество</th>
                                            <th>Цена</th>
                                        </tr>
                                        <tr>
                                            <td>13</td>
                                            <td>Футболка</td>
                                            <td>10</td>
                                            <td>10000</td>
                                        </tr>
                                    </table>                              
                        </div>
                    </form>
                </main>
              </div>
            </div>
          </div>

          <div class="modal micromodal-slide <?php
          if(isset($_SESSION["clients_errors"]) && !empty($_SESSION["clients_errors"])){
              echo "open";
          }
          
          ?>" id="error-modal" aria-hidden="true" >
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
          <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
            <header class="modal__header">
              <h2 class="modal__title" id="modal-1-title">
               Ошибка
              </h2>
              <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
            </header>
            <main class="modal__content" id="modal-1-content">
                <p>ТЕКСТ ОШИБКИ</p>
            </main>
          </div>
        </div>
      </div>

    <script defer src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>
    <script defer src="scripts/initClientsModal.js"></script>
</body>
</html>