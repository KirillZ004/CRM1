<?php

session_start();

require_once 'api/auth/AuthCheck.php';

AuthCheck('clients.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM | Авторизация</title>
    <link rel="stylesheet" href="styles/settings.css">
    <link rel="stylesheet" href="styles/pages/login.css">
</head>
<body>
    <div class="container">
        <div class="login-form">
        <h2>Вход</h2>
        <form action = "api/auth/AuthUser.php" method = "POST">
            <input type="text" name = "login" id = "login" placeholder="Логин" >
            <p class = 'error'><?php
             if(isset($_SESSION['login-errors'])){
                $errors = $_SESSION['login-errors'];
                $loginError = isset($errors['login']) ? $errors['login'] : "";
            echo $loginError;
             }
             ?></p>
             <input type="password" name = "password" id = "password" placeholder="Пароль" >
             <p class = 'error'><?php
             if(isset($_SESSION['login-errors'])){
                $errors = $_SESSION['login-errors'];
                $loginError = isset($errors['password']) ? $errors['password'] : "";
            echo $loginError;
             }
             ?></p>
            <button type="submit">Войти</button>

        </form>
    </div>
        <!-- форма с полями логин , пароль , кнопка ввойти. Вид пл центру. Стили должны быть изолированы -->
    </div> 
</body>
</html>