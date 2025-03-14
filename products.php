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
AuthCheck('', 'login.php');
require_once 'api/helpers/inputDefaultValue.php';
require_once 'api/helpers/getUserType.php';


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM | Товары</title>
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
    <main>
        <section class="filters">
            <div class="container">
                <form action=""  method = "GET">
                    <i class="fa fa-address-book" aria-hidden="true"></i>
                    <label for="search">Поиск по названию</label>
                    <input type="text" id="search" name="search" placeholder="Негр" <?php inputDefaultValue("search","");?>>
                    <select name="search_name" id="search_name">
                        <?php 
                    $searchNameOptions = [
                      [
                        'key' => 'name',
                        'value' => 'Название'
                    ],
                        [
                        'key' => 'price',
                        'value' => 'Цена'
                        ],
                        [
                        'key' => 'stock',
                        'value' => 'Количество'
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
                        <h2 class="clients_title">Список товаров</h2>
                        <button onclick="MicroModal.show('add-modal')" class="clients_add"><i class="fa fa-plus-square fa-2x" aria-hidden="true"></i>
                        </button>

                        <?php 
                require 'api/DB.php';
                $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $maxProducts = 5;
                $_SESSION['maxProducts'] = $maxProducts;
                $offset = ($currentPage - 1) * $maxProducts;
                $_SESSION['offset'] = $offset;

                $search = isset($_GET['search']) ? $_GET['search'] : '';
                $search_name = isset($_GET['search_name']) ? $_GET['search_name'] : '';
                $sort = isset($_GET['sort']) ? $_GET['sort'] : '';

                $totalProducts =  $db -> query("
                SELECT COUNT(*) as count FROM products WHERE LOWER(name) LIKE '%$search%'
                ")->fetchAll()[0]['count'];

                $maxPage = ceil($totalProducts / $maxProducts);

                // Проверка на корректность значения текущей страницы
                if ($currentPage < 1) {
                    $currentPage = 1;
                } elseif ($currentPage > $maxPage) {
                    $currentPage = $maxPage;
                }

                $prev = $currentPage - 1;
                if ($currentPage > 1) {
                    echo "  <button><a href='?page=$prev&search=".urlencode($search)."&search_name=$search_name&sort=$sort'><i class='fa fa-chevron-left' aria-hidden='true'></i></a></button>
                            ";
                } else {
                    echo "  <button style='color: gray; cursor: not-allowed;' disabled><i class='fa fa-chevron-left' aria-hidden='true'></i></button>
                            ";
                }
                $next = $currentPage + 1;         

                //echo "  <p>$currentPage / $maxPage</p> ";
                for($i = 1; $i <= $maxPage ;$i++){
                  if($currentPage == $i){
                    echo "<a href='?page=$i&search=".urlencode($search)."&search_name=$search_name&sort=$sort' style='color: green;'>$i</a>";
                  }else{
                    echo "<a href='?page=$i&search=".urlencode($search)."&search_name=$search_name&sort=$sort'style='color: gray; '>$i</a>";
                  }
                  
                }    
                
                if ($currentPage < $maxPage) {
                    echo "  <button><a href='?page=$next&search=".urlencode($search)."&search_name=$search_name&sort=$sort'><i class='fa fa-chevron-right' aria-hidden='true'></i></a></button>
                            ";
                } else {
                    echo "  <button style='color: gray; cursor: not-allowed;' disabled><i class='fa fa-chevron-right' aria-hidden='true'></i></button>
                            ";
                }
      
                ?>
            </div>
                   
                        <div class="table-wrap">
                        <table>
                    <thead>
                        <th>ИД</th>
                        <th>Название</th>
                        <th>Описание</th>
                        <th>Цена</th>
                        <th>Количество</th>
                        <th>Радактировать</th>
                        <th>Удалить</th>
                        <th>СоздатьQR</th>
                        
                        
                    </thead>
                    <tbody>
                    <?php
                            
                            require_once 'api/db.php';
                            require_once 'api/products/OutputProducts.php';
                            require_once 'api/products/ProductsSearch.php';


                            $products = ProductsSearch($_GET, $db);
                            OutputProducts($products);
                        ?>
                        <!-- <tr>
                            <td>0</td>
                            <td>НЕГР</td>
                            <td>Много работает , любит собирать хлопок под полящим солнцем</td>
                            <td>2 долара</td>
                            <td>1 штука</td>
                                <td><i class="fa fa-pencil-square-o fa-1x" aria-hidden="true" onclick="MicroModal.show('edit-modal')"></i>
                            </td>
                                <td><i class="fa fa-trash fa-1x" aria-hidden="true" onclick="MicroModal.show('delete-modal')"></i>
                            </td>
                            <td><i class="fa fa-qrcode fa-1x" aria-hidden="true" onclick="MicroModal.show('history-modal')"></i>
                            </td>
                        </tr> -->

                    </tbody>
                </table>
            </div>
            </div>
        </section>
    </main>
    <div class="modal micromodal-slide" id="add-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
          <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
            <header class="modal__header">
              <h2 class="modal__title" id="modal-1-title">
                Добавить товар
              </h2>
              <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
            </header>
            <main class="modal__content" id="modal-1-content">
                <form action = "api/products/AddProducts.php" method = "POST">
                    <div class="form-group">
                        <label for="name">Название</label>
                        <input type="text" id="name" name="name" placeholder="Введите название товара" >
                    </div>
                    <div class="form-group">
                        <label for="desc">Описание</label>
                        <input type="text" id="desc" name="desc" placeholder="Введите описание товара" >
                    </div>
                    <div class="form-group">
                        <label for="price">Цена</label>
                        <input type="decimal" id="price" name="price" placeholder="Введите цену товара" >
                    </div>
                    <div class="form-group">
                        <label for="stock">Количество</label>
                        <input type="int" id="stock" name="stock" placeholder="Введите количество товара" >
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
      <div class="modal micromodal-slide" id="edit-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
          <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
            <header class="modal__header">
              <h2 class="modal__title" id="modal-1-title">
                Редактировать товар
              </h2>
              <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
            </header>
            <main class="modal__content" id="modal-1-content">
                <form>
                    <div class="form-group">
                        <label for="name">Название</label>
                        <input type="text" id="name" name="name" placeholder="Введите название товара" required>
                    </div>
                    <div class="form-group">
                        <label for="desc">Описание</label>
                        <input type="text" id="desc" name="desc" placeholder="Введите описание товара" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Цена</label>
                        <input type="text" id="price" name="price" placeholder="Введите цену товара" required>
                    </div>
                    <div class="form-group">
                        <label for="cout">Количество</label>
                        <input type="text" id="cout" name="cout" placeholder="Введите количество товара" required>
                    </div>
                    <div class="button-group">
                        <button type="submit" class="create">Изменить</button>
                        <button type="button" class="cancel" onclick="window.location.reload();">Отменить</button>
                    </div>
                </form>
            </main>
          </div>
        </div>
      </div>




      <div class="modal micromodal-slide     
<?php
    if(isset($_SESSION['products_errors']) && !empty($_SESSION['products_errors'])){
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
                 if(isset($_SESSION['products_errors']) && !empty($_SESSION['products_errors'])){
                  echo  $_SESSION['products_errors'];
                  $_SESSION['products_errors'] = '';
                }
              ?>
            </main>
          </div>
        </div>
      </div>

      <div class="modal micromodal-slide <?php
    if (isset($_GET['edit-products']) && !empty($_GET['edit-products'])) {
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
                    $IdProducts = $_GET['edit-products'];
                              $clients = $db->query(
                                  "SELECT *  FROM products   WHERE id = $IdProducts 
                                  ") ->fetchAll(); 
                    $name;
                    $description;
                    $price;
                    $stock;
                foreach($clients as $client){
                    $name = $client['name'];
                    $description = $client['description'];
                    $price = $client['price'];
                    $stock = $client['stock'];
                }
                    if (isset($_GET['edit-products']) && !empty($_GET['edit-products'])) {
                        echo "<form method='POST' action='api/products/EditProducts.php?id=$IdProducts'>
    <div class='form-group'>
    <div class='form-group'>
        <label for='name'>Имя пользователя</label>
        <input type='text' id='name' name='name' value = '$name' required>
    </div>
    
    <div class='form-group'>
        <label for='description'>Описание</label>
        <input type='text' id='description' name='description' value = '$description' required>
    </div>
    
    <div class='form-group'>
        <label for='price'>Цена</label>
        <input type='text' id='price' name='price' value = '$price' required>
    </div>

        <div class='form-group'>
        <label for='stock'>Количество</label>
        <input type='text' id='stock' name='stock' value = '$stock' required>
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







      <script defer src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>
      <script defer src="scripts/initClientsModal.js"></script>

</body>
</html>