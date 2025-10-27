<?php


require_once __DIR__ . '/../backend/database/config.php';


require_once __DIR__ . '/../backend/dao/UserDao.php';


$userDao = new UserDao($conn);


$newId = $userDao->createUser("Nadža", "Hasanović", "nadja@example.com", "1234", 21);
echo "✅ New user created with ID: $newId <br><br>";

// ===== READ ALL USERS =====
$users = $userDao->getAllUsers();
echo "📋 All users:<br>";
echo "<pre>";
print_r($users);
echo "</pre>";

// ===== READ ONE USER =====
$oneUser = $userDao->getUserById($newId);
echo "<br>👤 User with ID $newId:<br>";
echo "<pre>";
print_r($oneUser);
echo "</pre>";

// ===== UPDATE USER =====
$userDao->updateUser($newId, "Nadja", "Šefica", "nadja_new@example.com", "4321", 22);
$updatedUser = $userDao->getUserById($newId);
echo "<br>✏️ Updated user:<br>";
echo "<pre>";
print_r($updatedUser);
echo "</pre>";

// ===== DELETE USER =====
// Uncomment the next line if you want to delete the test user
// $userDao->deleteUser($newId);
// echo "<br>🗑️ User with ID $newId deleted.<br>";

?>