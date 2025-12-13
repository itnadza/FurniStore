<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Get all categories (accessible to all authenticated users)
Flight::route('GET /categories', function(){
    $categoryService = new CategoryService();
    $categories = $categoryService->getAll();
    Flight::json(['success' => true, 'data' => $categories]);
});

/**
 * @OA\Get(
 *     path="/categories/with-counts",
 *     tags={"categories"},
 *     summary="Get categories with product counts",
 *     description="Accessible to all users",
 *     @OA\Response(
 *         response=200,
 *         description="Categories with product counts"
 *     )
 * )
 */
Flight::route('GET /categories/with-counts', function(){
    $categoryService = new CategoryService();
    try {
        $categories = $categoryService->getCategoriesWithProductCounts();
        Flight::json(['success' => true, 'data' => $categories]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

// Get category by ID (accessible to all authenticated users)
Flight::route('GET /categories/@id', function($id){
    $categoryService = new CategoryService();
    $category = $categoryService->getById($id);
    if ($category) {
        Flight::json(['success' => true, 'data' => $category]);
    } else {
        Flight::json(['success' => false, 'message' => 'Category not found'], 404);
    }
});

// Create a new category (admin only)
Flight::route('POST /categories', function(){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);

    $data = Flight::request()->data->getData();
    $categoryService = new CategoryService();
    try {
        $result = $categoryService->create($data);
        Flight::json(['success' => true, 'data' => $result, 'message' => 'Category created successfully']);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Put(
 *     path="/categories/{id}",
 *     tags={"categories"},
 *     summary="Update category by ID",
 *     description="Admin-only route",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Category ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="Updated Category Name"),
 *             @OA\Property(property="description", type="string", example="Updated description")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Category updated successfully"),
 *     @OA\Response(response=403, description="Forbidden: Admin only")
 * )
 */
Flight::route('PUT /categories/@id', function($id){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);

    $data = Flight::request()->data->getData();
    $categoryService = new CategoryService();
    try {
        $categoryService->update($id, $data);
        Flight::json(['success' => true, 'message' => 'Category updated successfully']);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});

// Delete category (admin only)
Flight::route('DELETE /categories/@id', function($id){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);

    $categoryService = new CategoryService();
    try {
        $categoryService->delete($id);
        Flight::json(['success' => true, 'message' => 'Category deleted successfully']);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});

// Get category by name (accessible to all authenticated users)
Flight::route('GET /categories/name/@name', function($name){
    $categoryService = new CategoryService();
    $category = $categoryService->getByName($name);
    if ($category) {
        Flight::json(['success' => true, 'data' => $category]);
    } else {
        Flight::json(['success' => false, 'message' => 'Category not found'], 404);
    }
});
?>
