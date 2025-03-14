<?php

function ClientsSearch($params, $db){
        $search_name = isset($params['search_name']) ? $params['search_name'] : 'name';
        $search = isset($params['search']) ? $params['search'] : '';
        $sort = isset($params['sort']) ? $params['sort'] : '';

        $search = trim(strtolower($search));

            $clients = $db->query(
                "SELECT * FROM clients   WHERE LOWER($search_name) LIKE '%$search%' ORDER BY $search_name $sort
                ") ->fetchAll(); 

        return $clients;
}


?>