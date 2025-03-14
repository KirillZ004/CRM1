<?php

function AdminName($token,$db){
$adminName = $db->query(
    "SELECT name, surname FROM users WHERE token = '$token'
     ") ->fetchAll()[0];
$name = $adminName['name'];
$surname = $adminName['surname'];

return "$name $surname ";
}

?>