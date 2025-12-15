<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . "/middleware/AuthMiddleware.php";
require_once __DIR__ . '/data/Roles.php'; // ✅ include Roles class

Flight::register('auth_middleware', 'AuthMiddleware');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include services
require_once __DIR__ . '/services/BaseService.php';
require_once __DIR__ . '/services/UserService.php';
Flight::register('user_service', 'UserService');
require_once __DIR__ . '/services/ProductService.php';
Flight::register('product_service', 'ProductService');
require_once __DIR__ . '/services/CategoryService.php';
Flight::register('category_service', 'CategoryService');
require_once __DIR__ . '/services/CartService.php';
Flight::register('cart_service', 'CartService');
require_once __DIR__ . '/services/OrderService.php';
Flight::register('order_service', 'OrderService');
require_once __DIR__ . '/services/AuthService.php';
Flight::register('auth_service', "AuthService");


Flight::route('/*', function() {
    $url = Flight::request()->url;
    if(strpos($url, '/auth/login') === 0 || strpos($url, '/auth/register') === 0) {
        return TRUE;
    }
    try {
        
        $authHeader = Flight::request()->getHeader("Authorization");
        $token = str_replace('Bearer ', '', $authHeader);
        Flight::auth_middleware()->verifyToken($token);
    } catch (\Exception $e) {
        Flight::halt(401, $e->getMessage());
    }
});

// Include routes
require_once __DIR__ . '/routes/UserRoutes.php';
require_once __DIR__ . '/routes/ProductRoutes.php';
require_once __DIR__ . '/routes/CategoryRoutes.php';
require_once __DIR__ . '/routes/CartRoutes.php';
require_once __DIR__ . '/routes/OrderRoutes.php';
require_once __DIR__ . '/routes/AuthRoutes.php';


Flight::route('/', function(){
    echo 'Furnistore API is running!';
});

Flight::start();
?>
