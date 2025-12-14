<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use OpenApi\Annotations as OA;

require_once __DIR__ . '/../data/Roles.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../services/CartService.php';

/**
 * @OA\Tag(
 *     name="Cart",
 *     description="Cart management endpoints"
 * )
 */

Flight::group('/cart', function() {

    /**
     * @OA\Get(
     *     path="/cart",
     *     tags={"Cart"},
     *     summary="Get user's cart",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="User cart returned")
     * )
     */
    Flight::route('GET /', function() {
        $auth = new AuthMiddleware();
        $token = str_replace('Bearer ', '', Flight::request()->headers['Authorization'] ?? '');
        $auth->verifyToken($token);
        $auth->authorizeRole(Roles::USER);

        $userId = Flight::get('user')->id;
        $cartService = new CartService();
        Flight::json(['success' => true, 'data' => $cartService->getCartWithDetails($userId)]);
    });

    /**
     * @OA\Post(
     *     path="/cart/add",
     *     tags={"Cart"},
     *     summary="Add item to cart",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id"},
     *             @OA\Property(property="product_id", type="integer"),
     *             @OA\Property(property="quantity", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Item added")
     * )
     */
    Flight::route('POST /add', function() {
        $auth = new AuthMiddleware();
        $token = str_replace('Bearer ', '', Flight::request()->headers['Authorization'] ?? '');
        $auth->verifyToken($token);
        $auth->authorizeRole(Roles::USER);

        $data = Flight::request()->data->getData();
        $quantity = $data['quantity'] ?? 1;

        (new CartService())->addToCart(
            Flight::get('user')->id,
            $data['product_id'],
            $quantity
        );

        Flight::json(['success' => true, 'message' => 'Item added to cart']);
    });

    /**
     * @OA\Put(
     *     path="/cart/update",
     *     tags={"Cart"},
     *     summary="Update cart item quantity",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id","quantity"},
     *             @OA\Property(property="product_id", type="integer"),
     *             @OA\Property(property="quantity", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Item updated")
     * )
     */
    Flight::route('PUT /update', function() {
        $auth = new AuthMiddleware();
        $token = str_replace('Bearer ', '', Flight::request()->headers['Authorization'] ?? '');
        $auth->verifyToken($token);
        $auth->authorizeRole(Roles::USER);

        $data = Flight::request()->data->getData();

        (new CartService())->updateCartItem(
            Flight::get('user')->id,
            $data['product_id'],
            $data['quantity']
        );

        Flight::json(['success' => true, 'message' => 'Cart updated']);
    });

    /**
     * @OA\Delete(
     *     path="/cart/remove/{product_id}",
     *     tags={"Cart"},
     *     summary="Remove item from cart",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="product_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Item removed")
     * )
     */
    Flight::route('DELETE /remove/@product_id', function($product_id) {
        $auth = new AuthMiddleware();
        $token = str_replace('Bearer ', '', Flight::request()->headers['Authorization'] ?? '');
        $auth->verifyToken($token);
        $auth->authorizeRole(Roles::USER);

        (new CartService())->removeFromCart(Flight::get('user')->id, $product_id);
        Flight::json(['success' => true, 'message' => 'Item removed']);
    });

    /**
     * @OA\Delete(
     *     path="/cart/clear",
     *     tags={"Cart"},
     *     summary="Clear cart",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Cart cleared")
     * )
     */
    Flight::route('DELETE /clear', function() {
        $auth = new AuthMiddleware();
        $token = str_replace('Bearer ', '', Flight::request()->headers['Authorization'] ?? '');
        $auth->verifyToken($token);
        $auth->authorizeRole(Roles::USER);

        (new CartService())->clearCart(Flight::get('user')->id);
        Flight::json(['success' => true, 'message' => 'Cart cleared']);
    });

    /**
     * @OA\Get(
     *     path="/cart/total",
     *     tags={"Cart"},
     *     summary="Get cart total",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Cart total")
     * )
     */
    Flight::route('GET /total', function() {
        $auth = new AuthMiddleware();
        $token = str_replace('Bearer ', '', Flight::request()->headers['Authorization'] ?? '');
        $auth->verifyToken($token);
        $auth->authorizeRole(Roles::USER);

        $total = (new CartService())->calculateCartTotal(Flight::get('user')->id);
        Flight::json(['success' => true, 'data' => ['total' => $total]]);
    });

    /**
     * @OA\Get(
     *     path="/cart/count",
     *     tags={"Cart"},
     *     summary="Get cart item count",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Item count")
     * )
     */
    Flight::route('GET /count', function() {
        $auth = new AuthMiddleware();
        $token = str_replace('Bearer ', '', Flight::request()->headers['Authorization'] ?? '');
        $auth->verifyToken($token);
        $auth->authorizeRole(Roles::USER);

        $count = (new CartService())->getCartItemCount(Flight::get('user')->id);
        Flight::json(['success' => true, 'data' => ['count' => $count]]);
    });

});
