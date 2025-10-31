<?php
require_once '../dao/UserDao.php';
require_once '../dao/OrderDao.php';

$userDao = new UserDao();
$orderDao = new OrderDao();

// Insert a new user 
$userDao->insert([
   'first_name' => 'Nadža',
   'last_name' => 'Hasanović',
   'email' => 'nadza@gmail.com',
   'password' => password_hash('nadza123', PASSWORD_DEFAULT),
   'age' => 20
]);

// Insert a new order
$orderDao->insert([
   'user_id' => 1,
   'status' => 'Pending',
   'total_amount' => 30.98,
   'order_date' => date('Y-m-d H:i:s'),
   'address' => '123 Main Street'
]);


$users = $userDao->getAll();
print_r($users);


$orders = $orderDao->getAll();
print_r($orders);
?>
