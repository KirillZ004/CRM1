<?php

require_once 'api/helpers/convertdate.php';

function OutputClients($clients){
    function convertParams($arr){
        $params = [];
        foreach ($arr as $key => $value) {
            $params[] = "$key=$value";
    }
        return implode('&', $params);
    }
    foreach($clients as $client){
        $id = $client['id'];
        $clients_name = $client['name'];
        $email = $client['email'];
        $phone = $client['phone'];
        $birthday = $client['birthday'];
        $created_at = $client['created_at'];

        $birthday = formatOrderDate($birthday);
        $created_at = formatOrderDate($created_at);

               
        // $copyParams = $_GET;
        // $copyParams['send-email'] = $email;
        // $queryParams = convertParams($copyParams);

        echo "<tr>
        <td>$id</td>
        <td>$clients_name</td>
        <td><a href='?send-email=$email'>$email</a></td>
        <td>$phone</td>
        <td>$birthday</td>
        <td>$created_at</td>
                <td>
                <form class = 'main_form' action = 'api/clients/ClientsHistory.php?id=$id'>
                <input value='$id' name = 'id' hidden>
                <input type='date' id = 'from' name = 'from'>
                <input type='date' id = 'to' name = 'to'>
                <button type='submit' class = 'historyBitton'>OK</button>
                </form>
                </td>
                <td><a href='?edit-user=$id'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a></td>
                <td><a href='api/clients/DeleteClients.php?id=$id'>
                 <i class='fa fa-trash' aria-hidden='true' ></i>
                </a>
                 </td>
            </tr>
        ";
    }
}

// <td><i class='fa fa-trash' aria-hidden='true' onclick='MicroModal.show(\"delete-modal\")'></i></td>

?>