<?php

function OrdersSearch($params, $db){
        $search = isset($params['search']) ? $params['search'] : '';
        $sort = isset($params['sort']) ? $params['sort'] : '';
        $search_name = isset($params['search_name']) ? $params['search_name'] : 'name';
        $showActive = isset($params['show_active']) ? "": "AND orders.status = '1'" ;

        $search = trim(strtolower($search));
        
        $orderBy = "ORDER BY $search_name $sort";
        
        
        $order = $db->query(
                "SELECT orders.id, clients.name, orders.order_date, orders.total, orders.status, users.name as 'admin',
                    GROUP_CONCAT(CONCAT(products.name, ' - ', order_items.price, ' - ' , order_items.quantity , ' кол-во') SEPARATOR ', ') AS product_names
                    FROM orders 
                    JOIN clients ON orders.client_id = clients.id 
                    JOIN order_items ON orders.id = order_items.order_id 
                    JOIN products ON order_items.product_id = products.id 
                    JOIN users ON orders.admin = users.id 
                    WHERE (LOWER(clients.name) LIKE '%$search%' OR LOWER(products.name) LIKE '%$search%') 
                    $showActive
                    GROUP BY orders.id, clients.name, orders.order_date, orders.total $orderBy") ->fetchAll();

        return $order;


}


?>