<?php
/**
 * @OA\Get(
 *     path="/categories",
 *     tags={"categories"},
 *     summary="Get all categories",
 *     @OA\Response(
 *         response=200,
 *         description="Array of all categories"
 *     )
 * )
 */
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
 *     @OA\Response(
 *         response=200,
 *         description="Categories with product counts"
 *     )
 * )
 */
Flight::route('GET /categories/with-counts', function(){
    $categoryService = new CategoryService();
    $categories = $categoryService->getCategoriesWithProductCounts();
    Flight::json(['success' => true, 'data' => $categories]);
});

/**
 * @OA\Get(
 *     path="/categories/{id}",
 *     tags={"categories"},
 *     summary="Get category by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Category ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Category data"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Category not found"
 *     )
 * )
 */
Flight::route('GET /categories/@id', function($id){
    $categoryService = new CategoryService();
    $category = $categoryService->getById($id);
    if ($category) {
        Flight::json(['success' => true, 'data' => $category]);
    } else {
        Flight::json(['success' => false, 'message' => 'Category not found'], 404);
    }
});

/**
 * @OA\Post(
 *     path="/categories",
 *     tags={"categories"},
 *     summary="Create a new category",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", example="Living Room"),
 *             @OA\Property(property="description", type="string", example="Furniture for living room")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Category created successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error"
 *     )
 * )
 */
Flight::route('POST /categories', function(){
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
 *     @OA\Response(
 *         response=200,
 *         description="Category updated successfully"
 *     )
 * )
 */
Flight::route('PUT /categories/@id', function($id){
    $data = Flight::request()->data->getData();
    $categoryService = new CategoryService();
    try {
        $result = $categoryService->update($id, $data);
        Flight::json(['success' => true, 'message' => 'Category updated successfully']);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Delete(
 *     path="/categories/{id}",
 *     tags={"categories"},
 *     summary="Delete category by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Category ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Category deleted successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Cannot delete category with associated products"
 *     )
 * )
 */
Flight::route('DELETE /categories/@id', function($id){
    $categoryService = new CategoryService();
    try {
        $result = $categoryService->delete($id);
        Flight::json(['success' => true, 'message' => 'Category deleted successfully']);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});
?>