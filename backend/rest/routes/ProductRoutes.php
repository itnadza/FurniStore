<?php
/**
 * @OA\Get(
 *     path="/products",
 *     tags={"products"},
 *     summary="Get all products",
 *     @OA\Response(
 *         response=200,
 *         description="Array of all products"
 *     )
 * )
 */
Flight::route('GET /products', function(){
    $productService = new ProductService();
    $products = $productService->getAll();
    Flight::json(['success' => true, 'data' => $products]);
});

/**
 * @OA\Get(
 *     path="/products/{id}",
 *     tags={"products"},
 *     summary="Get product by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Product ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product data"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product not found"
 *     )
 * )
 */
Flight::route('GET /products/@id', function($id){
    $productService = new ProductService();
    $product = $productService->getById($id);
    if ($product) {
        Flight::json(['success' => true, 'data' => $product]);
    } else {
        Flight::json(['success' => false, 'message' => 'Product not found'], 404);
    }
});

/**
 * @OA\Post(
 *     path="/products",
 *     tags={"products"},
 *     summary="Create a new product",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "price", "category_id"},
 *             @OA\Property(property="name", type="string", example="Modern Chair"),
 *             @OA\Property(property="description", type="string", example="Comfortable modern chair"),
 *             @OA\Property(property="price", type="number", format="float", example=199.99),
 *             @OA\Property(property="category_id", type="integer", example=1),
 *             @OA\Property(property="stock_quantity", type="integer", example=50),
 *             @OA\Property(property="image_url", type="string", example="chair.jpg")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product created successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error"
 *     )
 * )
 */
Flight::route('POST /products', function(){
    $data = Flight::request()->data->getData();
    $productService = new ProductService();
    try {
        $result = $productService->create($data);
        Flight::json(['success' => true, 'data' => $result, 'message' => 'Product created successfully']);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Put(
 *     path="/products/{id}",
 *     tags={"products"},
 *     summary="Update product by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Product ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="Updated Chair"),
 *             @OA\Property(property="description", type="string", example="Updated description"),
 *             @OA\Property(property="price", type="number", format="float", example=249.99),
 *             @OA\Property(property="stock_quantity", type="integer", example=25)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product updated successfully"
 *     )
 * )
 */
Flight::route('PUT /products/@id', function($id){
    $data = Flight::request()->data->getData();
    $productService = new ProductService();
    try {
        $result = $productService->update($id, $data);
        Flight::json(['success' => true, 'message' => 'Product updated successfully']);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Delete(
 *     path="/products/{id}",
 *     tags={"products"},
 *     summary="Delete product by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Product ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product deleted successfully"
 *     )
 * )
 */
Flight::route('DELETE /products/@id', function($id){
    $productService = new ProductService();
    $result = $productService->delete($id);
    if ($result) {
        Flight::json(['success' => true, 'message' => 'Product deleted successfully']);
    } else {
        Flight::json(['success' => false, 'message' => 'Product not found'], 404);
    }
});

/**
 * @OA\Get(
 *     path="/products/category/{category_id}",
 *     tags={"products"},
 *     summary="Get products by category",
 *     @OA\Parameter(
 *         name="category_id",
 *         in="path",
 *         required=true,
 *         description="Category ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Products in category"
 *     )
 * )
 */
Flight::route('GET /products/category/@category_id', function($category_id){
    $productService = new ProductService();
    $products = $productService->getProductsByCategory($category_id);
    Flight::json(['success' => true, 'data' => $products]);
});
?>