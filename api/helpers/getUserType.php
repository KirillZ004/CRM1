<?php

function getUserType($db) {
    $token = $_SESSION['token'];
    $userType = $db->query(
        "SELECT type FROM users WHERE token = '$token'"
    )->fetchAll();

    return $userType[0]['type'];
}
?>