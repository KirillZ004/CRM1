<?php

function AdminName($token, $db) {
    $result = $db->query("SELECT name, surname FROM users WHERE token = '$token'")->fetchAll();
    
    if (empty($result)) {
        return "Пользователь не найден"; // "User not found" in Russian
    }
    
    $adminName = $result[0];
    return $adminName['name'] . ' ' . $adminName['surname'];
}

?>