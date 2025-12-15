<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use OpenApi\Annotations as OA;

require_once __DIR__ . '/../data/Roles.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../services/OrderService.php';

/**
 * @OA\Tag(
 *     name="Orders",
 *     description="Order management endpoints"
 * )
 */

Flight::group('/orders', function() {

    /**
     * @OA\Get(
     *     path="/orders",
     *     tags={"Orders"},
     *     summary="Get user orders",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Orders list")
     * )
     */
    Flight::route('GET /', function() {
        $token = str_replace('Bearer ', '', Flight::request()->headers['Authorization'] ?? '');
        Flight::auth_middleware()->verifyToken($token);

        $orders = (new OrderService())->getUserOrders(Flight::get('user')->id);
        Flight::json(['success' => true, 'data' => $orders]);
    });

    /**
     * @OA\Get(
     *     path="/orders/{id}",
     *     tags={"Orders"},
     *     summary="Get order by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Order details")
     * )
     */
    Flight::route('GET /@id', function($id) {
        $token = str_replace('Bearer ', '', Flight::request()->headers['Authorization'] ?? '');
        Flight::auth_middleware()->verifyToken($token);

        $order = (new OrderService())->getOrderWithDetails($id);
        Flight::json(['success' => true, 'data' => $order]);
    });

    /**
     * @OA\Post(
     *     path="/orders",
     *     tags={"Orders"},
     *     summary="Create order from cart",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Order created")
     * )
     */
    Flight::route('POST /', function() {
        $token = str_replace('Bearer ', '', Flight::request()->headers['Authorization'] ?? '');
        Flight::auth_middleware()->verifyToken($token);
        Flight::auth_middleware()->authorizeRole(Roles::USER);

        $orderId = (new OrderService())->createOrderFromCart(
            Flight::get('user')->id,
            Flight::request()->data->getData()
        );

        Flight::json(['success' => true, 'data' => ['order_id' => $orderId]]);
    });

    /**
     * @OA\Put(
     *     path="/orders/{id}/status",
     *     tags={"Orders"},
     *     summary="Update order status (ADMIN)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Status updated")
     * )
     */
    Flight::route('PUT /@id/status', function($id) {
        $token = str_replace('Bearer ', '', Flight::request()->headers['Authorization'] ?? '');
        Flight::auth_middleware()->verifyToken($token);
        Flight::auth_middleware()->authorizeRole(Roles::ADMIN);

        (new OrderService())->updateOrderStatus(
            $id,
            Flight::request()->data->getData()['status']
        );

        Flight::json(['success' => true, 'message' => 'Order updated']);
    });

});
