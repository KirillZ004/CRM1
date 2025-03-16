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
        <button type="submit">Создать тикет</button>
        <button type="button" class="close-create-ticket">Отмена</button>
        <button type="button" class="my-tickets-btn">Мои обращения</button>
    </form>
</div>

<!-- Добавляем модальное окно для списка обращений -->
<div class="my-tickets-modal" id="my-tickets-modal" style="display: none;">
    <h2>Мои обращения</h2>
    <div class="tickets-list">
        <!-- Здесь будет список обращений пользователя -->
        <?php
        // Получаем обращения пользователя из базы данных
        require_once 'api/db.php';
        
        // Проверяем сессию
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Проверяем наличие токена пользователя в сессии
        $userToken = isset($_SESSION['token']) ? $_SESSION['token'] : null;
        
        if ($userToken) {
            try {
                // Сначала получаем ID пользователя по токену
                $userQuery = $db->prepare("SELECT id FROM users WHERE token = ?");
                $userQuery->execute([$userToken]);
                $userData = $userQuery->fetch(PDO::FETCH_ASSOC);
                
                if ($userData && isset($userData['id'])) {
                    $userID = $userData['id'];
                    $_SESSION['user_id'] = $userID; // Сохраняем ID пользователя в сессии
                    
                    // Получаем тикеты пользователя
                    $ticketsQuery = $db->prepare("SELECT * FROM tickets WHERE clients = ? ORDER BY create_at DESC");
                    $ticketsQuery->execute([$userID]);
                    $tickets = $ticketsQuery->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (count($tickets) > 0) {
                        foreach ($tickets as $ticket) {
                            $ticketType = ($ticket['type'] == 'tech') ? 'Техническая неполадка' : 'Проблема с CRM';
                            $ticketStatus = '';
                            
                            switch ($ticket['status']) {
                                case 'waiting':
                                    $ticketStatus = 'В ожидании';
                                    break;
                                case 'work':
                                    $ticketStatus = 'В работе';
                                    break;
                                case 'complete':
                                    $ticketStatus = 'Завершен';
                                    break;
                                default:
                                    $ticketStatus = 'Неизвестно';
                            }
                            
                            echo '<div class="ticket-item">
                                    <div class="ticket-info">
                                        <p><strong>Номер тикета:</strong> #' . $ticket['id'] . '</p>
                                        <p><strong>Тип:</strong> ' . $ticketType . '</p>
                                        <p><strong>Статус:</strong> ' . $ticketStatus . '</p>
                                        <p><strong>Дата:</strong> ' . $ticket['create_at'] . '</p>
                                    </div>
                                    <button class="open-chat" data-ticket-id="' . $ticket['id'] . '">Открыть чат</button>
                                </div>';
                        }
                    } else {
                        echo '<p>У вас пока нет обращений</p>';
                        
                        // Добавляем пример обращения для демонстрации
                        echo '<div class="ticket-item">
                                <div class="ticket-info">
                                    <p><strong>Номер тикета:</strong> #demo-1</p>
                                    <p><strong>Тип:</strong> Техническая неполадка</p>
                                    <p><strong>Статус:</strong> В обработке</p>
                                    <p><strong>Дата:</strong> ' . date('Y-m-d H:i:s') . '</p>
                                </div>
                                <button class="open-chat" data-ticket-id="demo-1">Открыть чат</button>
                            </div>';
                    }
                } else {
                    // Если пользователь не найден по токену
                    echo '<p>Не удалось идентифицировать пользователя. Пожалуйста, перезайдите в систему.</p>';
                    
                    // Добавляем пример обращения для демонстрации
                    echo '<div class="ticket-item">
                            <div class="ticket-info">
                                <p><strong>Номер тикета:</strong> #demo-1</p>
                                <p><strong>Тип:</strong> Техническая неполадка</p>
                                <p><strong>Статус:</strong> В обработке</p>
                                <p><strong>Дата:</strong> ' . date('Y-m-d H:i:s') . '</p>
                            </div>
                            <button class="open-chat" data-ticket-id="demo-1">Открыть чат</button>
                        </div>';
                }
            } catch (PDOException $e) {
                // Если возникла ошибка при работе с базой данных
                echo '<p>Произошла ошибка при получении данных: ' . $e->getMessage() . '</p>';
                
                // Добавляем пример обращения для демонстрации
                echo '<div class="ticket-item">
                        <div class="ticket-info">
                            <p><strong>Номер тикета:</strong> #demo-1</p>
                            <p><strong>Тип:</strong> Техническая неполадка</p>
                            <p><strong>Статус:</strong> В обработке</p>
                            <p><strong>Дата:</strong> ' . date('Y-m-d H:i:s') . '</p>
                        </div>
                        <button class="open-chat" data-ticket-id="demo-1">Открыть чат</button>
                    </div>';
            }
        } else {
            // Если токен пользователя не найден в сессии
            echo '<p>Для просмотра обращений необходимо авторизоваться.</p>';
            
            // Добавляем пример обращения для демонстрации
            echo '<div class="ticket-item">
                    <div class="ticket-info">
                        <p><strong>Номер тикета:</strong> #demo-1</p>
                        <p><strong>Тип:</strong> Техническая неполадка</p>
                        <p><strong>Статус:</strong> В обработке</p>
                        <p><strong>Дата:</strong> ' . date('Y-m-d H:i:s') . '</p>
                    </div>
                    <button class="open-chat" data-ticket-id="demo-1">Открыть чат</button>
                </div>';
        }
        ?>
    </div>
    <button type="button" class="close-my-tickets" onclick="document.querySelector('#my-tickets-modal').style.display = 'none';">Закрыть</button>
</div>

<!-- Добавляем модальное окно для чата -->
<div class="ticket-chat-modal" id="ticket-chat-modal" style="display: none;">
    <h2>Чат обращения <span id="chat-ticket-id"></span></h2>
    <div class="chat-messages" id="chat-messages">
        <!-- Здесь будут сообщения чата -->
    </div>
    <div class="chat-form">
        <form id="chat-form">
            <textarea id="chat-message" placeholder="Введите ваше сообщение..."></textarea>
            <button type="submit">Отправить</button>
        </form>
    </div>
    <button type="button" class="close-chat-modal" onclick="document.querySelector('#ticket-chat-modal').style.display = 'none';">Закрыть</button>
</div>

<script>
    document.querySelector('.support-btn').addEventListener('click', function() {
        document.querySelector('.support-create-tickets').style.display = 'block';
    });

    document.querySelector('.close-create-ticket').addEventListener('click', function() {
        document.querySelector('.support-create-tickets').style.display = 'none';
    });
    
    // Добавляем новый скрипт для модального окна "Мои обращения"
    document.querySelector('.my-tickets-btn').addEventListener('click', function() {
        document.querySelector('#my-tickets-modal').style.display = 'block';
    });
    
    document.querySelector('.close-my-tickets').addEventListener('click', function() {
        document.querySelector('#my-tickets-modal').style.display = 'none';
    });
    
    // Добавляем обработчик для кнопок "Открыть чат"
    document.addEventListener('DOMContentLoaded', function() {
        // Находим все кнопки "Открыть чат"
        const chatButtons = document.querySelectorAll('.open-chat');
        
        // Добавляем обработчик события для каждой кнопки
        chatButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Получаем ID тикета из атрибута data-ticket-id
                const ticketId = this.getAttribute('data-ticket-id');
                
                // Устанавливаем ID тикета в заголовке чата
                document.getElementById('chat-ticket-id').textContent = ticketId;
                
                // Загружаем сообщения чата
                loadChatMessages(ticketId);
                
                // Отображаем модальное окно чата
                document.getElementById('ticket-chat-modal').style.display = 'block';
                
                // Закрываем модальное окно со списком обращений
                document.getElementById('my-tickets-modal').style.display = 'none';
            });
        });
        
        // Обработчик отправки сообщения
        document.getElementById('chat-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const ticketId = document.getElementById('chat-ticket-id').textContent;
            const messageText = document.getElementById('chat-message').value;
            
            if (messageText.trim() !== '') {
                sendChatMessage(ticketId, messageText);
                document.getElementById('chat-message').value = '';
            }
        });
    });
    
    // Функция для загрузки сообщений чата
    function loadChatMessages(ticketId) {
        const chatMessages = document.getElementById('chat-messages');
        chatMessages.innerHTML = '<p class="loading">Загрузка сообщений...</p>';
        
        fetch(`api/ticket/GetMessages.php?ticket_id=${ticketId}`)
            .then(response => response.json())
            .then(data => {
                chatMessages.innerHTML = '';
                
                if (data.success && data.messages && data.messages.length > 0) {
                    // Отображаем сообщения
                    data.messages.forEach(message => {
                        const messageElement = document.createElement('div');
                        messageElement.className = message.is_support ? 'message support' : 'message user';
                        messageElement.innerHTML = `
                            <div class="message-info">
                                <span class="message-author">${message.user_name}</span>
                                <span class="message-date">${new Date(message.created_at).toLocaleString()}</span>
                            </div>
                            <div class="message-text">${message.message}</div>
                        `;
                        chatMessages.appendChild(messageElement);
                    });
                } else {
                    // Если сообщений нет или произошла ошибка
                    chatMessages.innerHTML = '<p class="no-messages">Сообщений пока нет. Напишите первое сообщение!</p>';
                }
                
                // Прокручиваем чат вниз
                chatMessages.scrollTop = chatMessages.scrollHeight;
            })
            .catch(error => {
                console.error('Ошибка при загрузке сообщений:', error);
                chatMessages.innerHTML = '<p class="error">Ошибка при загрузке сообщений. Пожалуйста, попробуйте позже.</p>';
            });
    }
    
    // Функция для отправки сообщения
    function sendChatMessage(ticketId, messageText) {
        // Добавляем сообщение пользователя в чат (оптимистичное обновление UI)
        const chatMessages = document.getElementById('chat-messages');
        const userMessage = document.createElement('div');
        userMessage.className = 'message user';
        userMessage.innerHTML = `
            <div class="message-info">
                <span class="message-author">Вы</span>
                <span class="message-date">${new Date().toLocaleString()}</span>
            </div>
            <div class="message-text">${messageText}</div>
        `;
        chatMessages.appendChild(userMessage);
        
        // Прокручиваем чат вниз
        chatMessages.scrollTop = chatMessages.scrollHeight;
        
        // Отправляем AJAX-запрос для сохранения сообщения
        const formData = new FormData();
        formData.append('ticket_id', ticketId);
        formData.append('message', messageText);
        
        fetch('api/ticket/SendMessage.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error('Ошибка при отправке сообщения:', data.error);
                // Можно добавить уведомление об ошибке
            }
        })
        .catch(error => {
            console.error('Ошибка при отправке сообщения:', error);
            // Можно добавить уведомление об ошибке
        });
    }
</script>
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