<?php
require_once '../dao/UserDao.php';
require_once '../dao/OrderDao.php';



$userDao = new UserDao();
$orderDao = new OrderDao();


// Insert a new user (Customer)
$userDao->insert([
   'name' => 'John Doe',
   'email' => 'john@example.com',
   'password' => password_hash('password123', PASSWORD_DEFAULT),
   'role' => 'Customer'
]);


// Insert a new order
$orderDao->insert([
   'user_id' => 1,
   'status' => 'Pending',
   'total_price' => 25.98,
   'order_date' => date('Y-m-d H:i:s')
]);


// Fetch all users
$users = $userDao->getAll();
print_r($users);


// Fetch all orders
$orders = $orderDao->getAll();
print_r($orders);


?>
