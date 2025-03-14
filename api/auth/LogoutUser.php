<?php


function LogoutUser($redirect,$db,$token = ''){
 unset($_SESSION['token']);
    if($token){
            $_SESSION['token'] = $token;
            $db->query(
                "UPDATE users SET token = NULL
            WHERE token = '$token' 
            "
        )->fetchAll();       
            
    }
header("Location: $redirect");
}

?>