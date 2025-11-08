<?php
require 'vendor/autoload.php';

// Enable CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Include all your services
require_once 'services/BaseService.php';
require_once 'services/UserService.php';
require_once 'services/ProductService.php';
require_once 'services/CategoryService.php';
require_once 'services/CartService.php';
require_once 'services/OrderService.php';

// Include all route files
require_once 'routes/UserRoutes.php';
require_once 'routes/ProductRoutes.php';
require_once 'routes/CategoryRoutes.php';
require_once 'routes/CartRoutes.php';
require_once 'routes/OrderRoutes.php';

// Default route
Flight::route('/', function(){
    echo 'Furnistore API is running!';
});

Flight::start();
?>