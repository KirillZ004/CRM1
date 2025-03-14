<?php

function ProductsSearch($params, $db){
        $search = isset($params['search']) ? $params['search'] : '';
        $sort = isset($params['sort']) ? $params['sort'] : '';
        $search_name = isset($params['search_name']) ? $params['search_name'] : 'name';
       // $filter = ; 

        $search = trim(strtolower($search));


            $products = $db->query(
                "SELECT * FROM products  WHERE LOWER(name) LIKE '%$search%' ORDER BY $search_name  $sort
                ") ->fetchAll(); 
        

        return $products;
}


?>