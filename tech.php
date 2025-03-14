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
require_once 'api/helpers/getUserType.php';
require_once 'api/db.php';

$userType = getUserType($db);
if($userType != 'tech'){
    header('Location: clients.php');
    exit;
}

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

    <body>
    <?php

require_once 'api/db.php';


try {
    // Handle status change if submitted
    if (isset($_POST['change_status']) && isset($_POST['ticket_id']) && isset($_POST['new_status'])) {
        $ticket_id = $_POST['ticket_id'];
        $new_status = $_POST['new_status'];
        $new_admin = isset($_POST['new_admin']) ? $_POST['new_admin'] : null;
        
        $update_query = "UPDATE tickets SET status = :status";
        $params = [':status' => $new_status, ':id' => $ticket_id];
        
        if ($new_admin) {
            $update_query .= ", admin = :admin";
            $params[':admin'] = $new_admin;
        }
        
        $update_query .= " WHERE id = :id";
        
        $update_stmt = $db->prepare($update_query);
        foreach ($params as $key => $value) {
            $update_stmt->bindValue($key, $value);
        }
        $update_stmt->execute();
        
        // Redirect to prevent form resubmission
        header("Location: " . $_SERVER['PHP_SELF'] . "?" . http_build_query($_GET));
        exit;
    }
    
    // Sorting options
    $sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'create_at';
    $sort_order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
    $filter_status = isset($_GET['status']) ? $_GET['status'] : '';
    $filter_type = isset($_GET['type']) ? $_GET['type'] : '';
    
    // Validate sort parameters
    $allowed_sort_fields = ['id', 'type', 'create_at', 'status'];
    if (!in_array($sort_by, $allowed_sort_fields)) {
        $sort_by = 'create_at';
    }
    
    $allowed_sort_orders = ['ASC', 'DESC'];
    if (!in_array(strtoupper($sort_order), $allowed_sort_orders)) {
        $sort_order = 'DESC';
    }
    
    // Pagination settings
    $records_per_page = 9;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $records_per_page;
    
    // Build the WHERE clause for filters
    $where_clauses = [];
    $params = [];
    
    if (!empty($filter_status)) {
        $where_clauses[] = "t.status = :status";
        $params[':status'] = $filter_status;
    }
    
    if (!empty($filter_type)) {
        $where_clauses[] = "t.type = :type";
        $params[':type'] = $filter_type;
    }
    
    $where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";
    
    // Count total records for pagination with filters
    $count_query = "SELECT COUNT(*) as total FROM tickets t $where_sql";
    $count_stmt = $db->prepare($count_query);
    foreach ($params as $key => $value) {
        $count_stmt->bindValue($key, $value);
    }
    $count_stmt->execute();
    $total_records = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $total_pages = ceil($total_records / $records_per_page);
    
    // Fetch tickets with user and admin information with pagination and sorting
    $query = "
        SELECT 
            t.id, 
            t.type, 
            t.message, 
            u.name AS user_name, 
            u.surname AS user_surname, 
            a.name AS admin_name, 
            a.surname AS admin_surname,
            t.admin AS admin_id,
            t.create_at, 
            t.status,
            CASE 
                WHEN t.type = 'tech' THEN 'Технические шоколадки' 
                ELSE 'Проблема с CRM' 
            END AS type_description
        FROM tickets t
        LEFT JOIN users u ON t.clients = u.id
        LEFT JOIN users a ON t.admin = a.id
        $where_sql
        ORDER BY $sort_by $sort_order
        LIMIT :offset, :records_per_page
    ";

    $stmt = $db->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':records_per_page', $records_per_page, PDO::PARAM_INT);
    $stmt->execute();
    
    // Fetch all tech admins for the dropdown
    $admin_query = "SELECT id, name, surname FROM users WHERE type = 'tech'";
    $admin_stmt = $db->prepare($admin_query);
    $admin_stmt->execute();
    $tech_admins = $admin_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Display filter and sort options
    echo "<div class='filters'>";
    echo "<form method='get' action=''>";
    echo "<div class='filter-row'>";
    
    // Status filter
    echo "<div class='filter-group'>";
    echo "<label for='status'>Статус:</label>";
    echo "<select name='status' id='status'>";
    echo "<option value=''>Все статусы</option>";
    echo "<option value='waiting'" . ($filter_status == 'waiting' ? ' selected' : '') . ">Ожидает</option>";
    echo "<option value='work'" . ($filter_status == 'work' ? ' selected' : '') . ">В работе</option>";
    echo "<option value='complete'" . ($filter_status == 'complete' ? ' selected' : '') . ">Выполнена</option>";
    echo "</select>";
    echo "</div>";
    
    // Type filter
    echo "<div class='filter-group'>";
    echo "<label for='type'>Тип:</label>";
    echo "<select name='type' id='type'>";
    echo "<option value=''>Все типы</option>";
    echo "<option value='tech'" . ($filter_type == 'tech' ? ' selected' : '') . ">Технические шоколадки</option>";
    echo "<option value='crm'" . ($filter_type == 'crm' ? ' selected' : '') . ">Проблема с CRM</option>";
    echo "</select>";
    echo "</div>";
    
    // Sort options
    echo "<div class='filter-group'>";
    echo "<label for='sort'>Сортировать по:</label>";
    echo "<select name='sort' id='sort'>";
    echo "<option value='create_at'" . ($sort_by == 'create_at' ? ' selected' : '') . ">Дате создания</option>";
    echo "<option value='status'" . ($sort_by == 'status' ? ' selected' : '') . ">Статусу</option>";
    echo "<option value='type'" . ($sort_by == 'type' ? ' selected' : '') . ">Типу</option>";
    echo "<option value='id'" . ($sort_by == 'id' ? ' selected' : '') . ">ID</option>";
    echo "</select>";
    echo "</div>";
    
    // Sort order
    echo "<div class='filter-group'>";
    echo "<label for='order'>Порядок:</label>";
    echo "<select name='order' id='order'>";
    echo "<option value='DESC'" . ($sort_order == 'DESC' ? ' selected' : '') . ">По убыванию</option>";
    echo "<option value='ASC'" . ($sort_order == 'ASC' ? ' selected' : '') . ">По возрастанию</option>";
    echo "</select>";
    echo "</div>";
    
    // Keep the page parameter if it exists
    if (isset($_GET['page'])) {
        echo "<input type='hidden' name='page' value='" . htmlspecialchars($_GET['page']) . "'>";
    }
    
    echo "<button type='submit' class='filter-button'>Применить</button>";
    echo "</div>";
    echo "</form>";
    echo "</div>";

    // Check if any tickets were found
    if ($stmt->rowCount() > 0) {
        echo "<div class='ticket-container'>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Determine status class
            $status_class = '';
            switch($row['status']) {
                case 'waiting':
                    $status_class = 'status-waiting';
                    $status_text = 'Ожидает';
                    break;
                case 'work':
                    $status_class = 'status-work';
                    $status_text = 'В работе';
                    break;
                case 'complete':
                    $status_class = 'status-complete';
                    $status_text = 'Выполнена';
                    break;
                default:
                    $status_class = 'status-waiting';
                    $status_text = 'Ожидает';
            }
            
            echo "<div class='ticket-card' onclick='openTicketModal(" . $row['id'] . ")'>";
            echo "<span class='date'>" . date('d.m.Y H:i', strtotime($row['create_at'])) . "</span>";
            echo "<h3>Ticket #" . htmlspecialchars($row['id']) . "</h3>";
            echo "<p><span class='label'>Тип:</span> " . htmlspecialchars($row['type_description']) . "</p>";
            echo "<p class='message-preview'><span class='label'>Сообщение:</span> " . htmlspecialchars(substr($row['message'], 0, 50)) . (strlen($row['message']) > 50 ? '...' : '') . "</p>";
            echo "<p><span class='label'>Пользователь:</span> " . htmlspecialchars($row['user_name']) . " " . htmlspecialchars($row['user_surname']) . "</p>";
            echo "<p><span class='label'>Администратор:</span> " . ($row['admin_name'] ? htmlspecialchars($row['admin_name']) . " " . htmlspecialchars($row['admin_surname']) : "Не назначен") . "</p>";
            echo "<p><span class='status " . $status_class . "'>" . $status_text . "</span></p>";
            echo "</div>";
            
            // Create hidden modal for this ticket
            echo "<div id='ticketModal" . $row['id'] . "' class='modal'>";
            echo "<div class='modal-content'>";
            echo "<span class='close' onclick='closeTicketModal(" . $row['id'] . ")'>&times;</span>";
            echo "<h2>Детали обращения #" . htmlspecialchars($row['id']) . "</h2>";
            
            echo "<div class='ticket-details'>";
            echo "<p><span class='label'>Тип:</span> " . htmlspecialchars($row['type_description']) . "</p>";
            echo "<p><span class='label'>Дата создания:</span> " . date('d.m.Y H:i', strtotime($row['create_at'])) . "</p>";
            echo "<p><span class='label'>Статус:</span> <span class='status " . $status_class . "'>" . $status_text . "</span></p>";
            echo "<p><span class='label'>Пользователь:</span> " . htmlspecialchars($row['user_name']) . " " . htmlspecialchars($row['user_surname']) . "</p>";
            echo "<p><span class='label'>Администратор:</span> " . ($row['admin_name'] ? htmlspecialchars($row['admin_name']) . " " . htmlspecialchars($row['admin_surname']) : "Не назначен") . "</p>";
            echo "<div class='message-box'>";
            echo "<p class='label'>Сообщение:</p>";
            echo "<div class='message-content'>" . nl2br(htmlspecialchars($row['message'])) . "</div>";
            echo "</div>";
            
            // Add status and admin change form
            echo "<form method='post' class='modal-form'>";
            echo "<input type='hidden' name='ticket_id' value='" . $row['id'] . "'>";
            
            echo "<div class='form-group'>";
            echo "<label for='new_status_modal_" . $row['id'] . "'>Изменить статус:</label>";
            echo "<select name='new_status' id='new_status_modal_" . $row['id'] . "'>";
            echo "<option value='waiting'" . ($row['status'] == 'waiting' ? ' selected' : '') . ">Ожидает</option>";
            echo "<option value='work'" . ($row['status'] == 'work' ? ' selected' : '') . ">В работе</option>";
            echo "<option value='complete'" . ($row['status'] == 'complete' ? ' selected' : '') . ">Выполнена</option>";
            echo "</select>";
            echo "</div>";
            
            echo "<div class='form-group'>";
            echo "<label for='new_admin_" . $row['id'] . "'>Назначить администратора:</label>";
            echo "<select name='new_admin' id='new_admin_" . $row['id'] . "'>";
            echo "<option value=''>Не назначен</option>";
            foreach ($tech_admins as $admin) {
                $selected = ($admin['id'] == $row['admin_id']) ? ' selected' : '';
                echo "<option value='" . $admin['id'] . "'" . $selected . ">" . htmlspecialchars($admin['name']) . " " . htmlspecialchars($admin['surname']) . "</option>";
            }
            echo "</select>";
            echo "</div>";
            
            echo "<button type='submit' name='change_status' class='save-button'>Сохранить изменения</button>";
            echo "</form>";
            
            echo "</div>"; // End ticket-details
            echo "</div>"; // End modal-content
            echo "</div>"; // End modal
        }
        echo "</div>";
        
        // Display pagination with all current GET parameters preserved
        if ($total_pages > 1) {
            echo "<div class='pagination'>";
            
            // Build the query string for pagination links
            $query_params = $_GET;
            
            // Previous page link
            if ($page > 1) {
                $query_params['page'] = $page - 1;
                echo "<a href='?" . http_build_query($query_params) . "'>&laquo; Предыдущая</a>";
            } else {
                echo "<span class='disabled'>&laquo; Предыдущая</span>";
            }
            
            // Page numbers
            $start_page = max(1, $page - 2);
            $end_page = min($total_pages, $page + 2);
            
            if ($start_page > 1) {
                $query_params['page'] = 1;
                echo "<a href='?" . http_build_query($query_params) . "'>1</a>";
                if ($start_page > 2) {
                    echo "<span class='disabled'>...</span>";
                }
            }
            
            for ($i = $start_page; $i <= $end_page; $i++) {
                if ($i == $page) {
                    echo "<span class='active'>" . $i . "</span>";
                } else {
                    $query_params['page'] = $i;
                    echo "<a href='?" . http_build_query($query_params) . "'>" . $i . "</a>";
                }
            }
            
            if ($end_page < $total_pages) {
                if ($end_page < $total_pages - 1) {
                    echo "<span class='disabled'>...</span>";
                }
                $query_params['page'] = $total_pages;
                echo "<a href='?" . http_build_query($query_params) . "'>" . $total_pages . "</a>";
            }
            
            // Next page link
            if ($page < $total_pages) {
                $query_params['page'] = $page + 1;
                echo "<a href='?" . http_build_query($query_params) . "'>Следующая &raquo;</a>";
            } else {
                echo "<span class='disabled'>Следующая &raquo;</span>";
            }
            
            echo "</div>";
        }
    } else {
        echo "<p class='no-tickets'>Обращений не найдено.</p>";
    }
    
    // Add JavaScript for modal functionality
    echo "<script>
    function openTicketModal(id) {
        document.getElementById('ticketModal' + id).style.display = 'block';
    }
    
    function closeTicketModal(id) {
        document.getElementById('ticketModal' + id).style.display = 'none';
    }
    
    // Close modal when clicking outside of it
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }
    </script>";
    
} catch (PDOException $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
}
?>
            </body>
</html>