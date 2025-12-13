<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

Flight::group('/orders', function() {

    /**
     * @OA\Get(
     *     path="/orders",
     *     tags={"orders"},
     *     summary="Get user's orders",
     *     @OA\Response(
     *         response=200,
     *         description="Array of user's orders with details"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    Flight::route('GET /', function(){
        $token = Flight::request()->headers['Authorization'] ?? null;
        $token = str_replace('Bearer ', '', $token);
        Flight::auth_middleware()->verifyToken($token);

        $user = Flight::get('user');
        $orderService = new OrderService();
        try {
            $orders = $orderService->getUserOrders($user->id);
            Flight::json(['success' => true, 'data' => $orders]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    });

    /**
     * @OA\Get(
     *     path="/orders/{id}",
     *     tags={"orders"},
     *     summary="Get order by ID with details",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Order ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response=200, description="Order details"),
     *     @OA\Response(response=404, description="Order not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    Flight::route('GET /@id', function($id){
        $token = Flight::request()->headers['Authorization'] ?? null;
        $token = str_replace('Bearer ', '', $token);
        Flight::auth_middleware()->verifyToken($token);

        $orderService = new OrderService();
        try {
            $order = $orderService->getOrderWithDetails($id);
            Flight::json(['success' => true, 'data' => $order]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    });

    /**
     * @OA\Post(
     *     path="/orders",
     *     tags={"orders"},
     *     summary="Create a new order from cart",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"shipping_address", "payment_method"},
     *             @OA\Property(property="shipping_address", type="string", example="123 Main St, City, State 12345"),
     *             @OA\Property(property="payment_method", type="string", example="credit_card"),
     *             @OA\Property(property="notes", type="string", example="Leave at front door")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Order created successfully"),
     *     @OA\Response(response=400, description="Validation error or cart is empty"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    Flight::route('POST /', function(){
        $token = Flight::request()->headers['Authorization'] ?? null;
        $token = str_replace('Bearer ', '', $token);
        Flight::auth_middleware()->verifyToken($token);
        Flight::auth_middleware()->authorizeRole(Roles::USER);

        $user = Flight::get('user');
        $data = Flight::request()->data->getData();

        if (empty($data['shipping_address']) || empty($data['payment_method'])) {
            Flight::json(['success' => false, 'message' => 'Shipping address and payment method are required'], 400);
            return;
        }

        $orderService = new OrderService();
        try {
            $orderId = $orderService->createOrderFromCart($user->id, $data);
            Flight::json(['success' => true, 'data' => ['order_id' => $orderId], 'message' => 'Order created successfully']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    });

    /**
     * @OA\Put(
     *     path="/orders/{id}/status",
     *     tags={"orders"},
     *     summary="Update order status",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Order ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", example="shipped")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Order status updated successfully"),
     *     @OA\Response(response=400, description="Invalid status or order not found"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    Flight::route('PUT /@id/status', function($id){
        $token = Flight::request()->headers['Authorization'] ?? null;
        $token = str_replace('Bearer ', '', $token);
        Flight::auth_middleware()->verifyToken($token);
        Flight::auth_middleware()->authorizeRole(Roles::ADMIN);

        $data = Flight::request()->data->getData();
        if (empty($data['status'])) {
            Flight::json(['success' => false, 'message' => 'Status is required'], 400);
            return;
        }

        $orderService = new OrderService();
        try {
            $orderService->updateOrderStatus($id, $data['status']);
            Flight::json(['success' => true, 'message' => 'Order status updated successfully']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    });

    /**
     * @OA\Get(
     *     path="/orders/statistics",
     *     tags={"orders"},
     *     summary="Get order statistics",
     *     @OA\Response(response=200, description="Order statistics"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    Flight::route('GET /statistics', function(){
        $token = Flight::request()->headers['Authorization'] ?? null;
        $token = str_replace('Bearer ', '', $token);
        Flight::auth_middleware()->verifyToken($token);
        Flight::auth_middleware()->authorizeRole(Roles::ADMIN);

        $orderService = new OrderService();
        try {
            $statistics = $orderService->getOrderStatistics();
            Flight::json(['success' => true, 'data' => $statistics]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    });

    /**
     * @OA\Get(
     *     path="/orders/status/{status}",
     *     tags={"orders"},
     *     summary="Get orders by status",
     *     @OA\Parameter(
     *         name="status",
     *         in="path",
     *         required=true,
     *         description="Order status",
     *         @OA\Schema(type="string", example="pending")
     *     ),
     *     @OA\Response(response=200, description="Orders with specified status"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    Flight::route('GET /status/@status', function($status){
        $token = Flight::request()->headers['Authorization'] ?? null;
        $token = str_replace('Bearer ', '', $token);
        Flight::auth_middleware()->verifyToken($token);

        $orderService = new OrderService();
        try {
            $orders = $orderService->getOrdersByStatus($status);
            Flight::json(['success' => true, 'data' => $orders]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    });

});
?>
