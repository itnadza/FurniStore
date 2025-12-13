<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Group all cart routes under /cart
Flight::group('/cart', function() {

    /**
     * @OA\Get(
     *     path="/cart",
     *     tags={"cart"},
     *     summary="Get user's cart with details",
     *     @OA\Response(
     *         response=200,
     *         description="User's cart items with product details"
     *     )
     * )
     */
    Flight::route('GET /', function() {
        $auth = new AuthMiddleware();
        $token = str_replace('Bearer ', '', Flight::request()->headers['Authorization'] ?? '');
        $auth->verifyToken($token);
        $auth->authorizeRole(Roles::USER);

        $userId = Flight::get('user')->id;
        $cartService = new CartService();
        try {
            $cart = $cartService->getCartWithDetails($userId);
            Flight::json(['success' => true, 'data' => $cart]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    });

    /**
     * @OA\Post(
     *     path="/cart/add",
     *     tags={"cart"},
     *     summary="Add item to cart",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id"},
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="quantity", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item added to cart"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error"
     *     )
     * )
     */
    Flight::route('POST /add', function() {
        $auth = new AuthMiddleware();
        $token = str_replace('Bearer ', '', Flight::request()->headers['Authorization'] ?? '');
        $auth->verifyToken($token);
        $auth->authorizeRole(Roles::USER);

        $userId = Flight::get('user')->id;
        $data = Flight::request()->data->getData();

        if (!isset($data['product_id'])) {
            Flight::json(['success' => false, 'message' => 'Product ID is required'], 400);
            return;
        }

        $cartService = new CartService();
        try {
            $quantity = $data['quantity'] ?? 1;
            $cartService->addToCart($userId, $data['product_id'], $quantity);
            Flight::json(['success' => true, 'message' => 'Item added to cart']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    });

    /**
     * @OA\Put(
     *     path="/cart/update",
     *     tags={"cart"},
     *     summary="Update cart item quantity",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id", "quantity"},
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="quantity", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cart item updated"
     *     )
     * )
     */
    Flight::route('PUT /update', function() {
        $auth = new AuthMiddleware();
        $token = str_replace('Bearer ', '', Flight::request()->headers['Authorization'] ?? '');
        $auth->verifyToken($token);
        $auth->authorizeRole(Roles::USER);

        $userId = Flight::get('user')->id;
        $data = Flight::request()->data->getData();

        if (!isset($data['product_id']) || !isset($data['quantity'])) {
            Flight::json(['success' => false, 'message' => 'Product ID and quantity are required'], 400);
            return;
        }

        $cartService = new CartService();
        try {
            $cartService->updateCartItem($userId, $data['product_id'], $data['quantity']);
            Flight::json(['success' => true, 'message' => 'Cart item updated']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    });

    /**
     * @OA\Delete(
     *     path="/cart/remove/{product_id}",
     *     tags={"cart"},
     *     summary="Remove item from cart",
     *     @OA\Parameter(
     *         name="product_id",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item removed from cart"
     *     )
     * )
     */
    Flight::route('DELETE /remove/@product_id', function($product_id) {
        $auth = new AuthMiddleware();
        $token = str_replace('Bearer ', '', Flight::request()->headers['Authorization'] ?? '');
        $auth->verifyToken($token);
        $auth->authorizeRole(Roles::USER);

        $userId = Flight::get('user')->id;
        $cartService = new CartService();
        try {
            $cartService->removeFromCart($userId, $product_id);
            Flight::json(['success' => true, 'message' => 'Item removed from cart']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    });

    /**
     * @OA\Delete(
     *     path="/cart/clear",
     *     tags={"cart"},
     *     summary="Clear user's cart",
     *     @OA\Response(
     *         response=200,
     *         description="Cart cleared"
     *     )
     * )
     */
    Flight::route('DELETE /clear', function() {
        $auth = new AuthMiddleware();
        $token = str_replace('Bearer ', '', Flight::request()->headers['Authorization'] ?? '');
        $auth->verifyToken($token);
        $auth->authorizeRole(Roles::USER);

        $userId = Flight::get('user')->id;
        $cartService = new CartService();
        try {
            $cartService->clearCart($userId);
            Flight::json(['success' => true, 'message' => 'Cart cleared']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    });

    /**
     * @OA\Get(
     *     path="/cart/total",
     *     tags={"cart"},
     *     summary="Calculate cart total",
     *     @OA\Response(
     *         response=200,
     *         description="Cart total amount"
     *     )
     * )
     */
    Flight::route('GET /total', function() {
        $auth = new AuthMiddleware();
        $token = str_replace('Bearer ', '', Flight::request()->headers['Authorization'] ?? '');
        $auth->verifyToken($token);
        $auth->authorizeRole(Roles::USER);

        $userId = Flight::get('user')->id;
        $cartService = new CartService();
        try {
            $total = $cartService->calculateCartTotal($userId);
            Flight::json(['success' => true, 'data' => ['total' => $total]]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    });

    /**
     * @OA\Get(
     *     path="/cart/count",
     *     tags={"cart"},
     *     summary="Get cart item count",
     *     @OA\Response(
     *         response=200,
     *         description="Number of items in cart"
     *     )
     * )
     */
    Flight::route('GET /count', function() {
        $auth = new AuthMiddleware();
        $token = str_replace('Bearer ', '', Flight::request()->headers['Authorization'] ?? '');
        $auth->verifyToken($token);
        $auth->authorizeRole(Roles::USER);

        $userId = Flight::get('user')->id;
        $cartService = new CartService();
        try {
            $count = $cartService->getCartItemCount($userId);
            Flight::json(['success' => true, 'data' => ['count' => $count]]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    });

});
?>
