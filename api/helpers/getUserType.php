<?php

function getUserType($db) {
    $token = $_SESSION['token'];
    $result = $db->query("SELECT type FROM users WHERE token = '$token'")->fetchAll();
    
    if (empty($result)) {
        return null; // or some default value depending on your needs
    }
    
    return $result[0]['type'];
}

?>