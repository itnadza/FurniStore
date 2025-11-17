<?php
/**
 * @OA\Get(
 *     path="/users",
 *     tags={"users"},
 *     summary="Get all users",
 *     @OA\Response(
 *         response=200,
 *         description="Array of all users"
 *     )
 * )
 */
Flight::route('GET /users', function(){
    $userService = new UserService();
    $users = $userService->getAll();
    foreach ($users as &$user) {
        unset($user['password']);
    }
    Flight::json(['success' => true, 'data' => $users]);
});

/**
 * @OA\Get(
 *     path="/users/{id}",
 *     tags={"users"},
 *     summary="Get user by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User data"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found"
 *     )
 * )
 */
Flight::route('GET /users/@id', function($id){
    $userService = new UserService();
    $user = $userService->getById($id);
    if ($user) {
        unset($user['password']);
        Flight::json(['success' => true, 'data' => $user]);
    } else {
        Flight::json(['success' => false, 'message' => 'User not found'], 404);
    }
});

/**
 * @OA\Post(
 *     path="/users/register",
 *     tags={"users"},
 *     summary="Register a new user",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email", "password", "first_name", "last_name"},
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *             @OA\Property(property="password", type="string", example="securepassword123"),
 *             @OA\Property(property="first_name", type="string", example="John"),
 *             @OA\Property(property="last_name", type="string", example="Doe")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User registered successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error"
 *     )
 * )
 */
Flight::route('POST /users/register', function(){
    $data = Flight::request()->data->getData();
    $userService = new UserService();
    try {
        $result = $userService->register($data);
        unset($result['password']);
        Flight::json(['success' => true, 'data' => $result, 'message' => 'User registered successfully']);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Post(
 *     path="/users/login",
 *     tags={"users"},
 *     summary="User login",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email", "password"},
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *             @OA\Property(property="password", type="string", example="password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Invalid credentials"
 *     )
 * )
 */
Flight::route('POST /users/login', function(){
    $data = Flight::request()->data->getData();
    $userService = new UserService();
    try {
        $user = $userService->login($data['email'], $data['password']);
        Flight::json(['success' => true, 'data' => $user, 'message' => 'Login successful']);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 401);
    }
});

/**
 * @OA\Put(
 *     path="/users/{id}",
 *     tags={"users"},
 *     summary="Update user by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="first_name", type="string", example="John"),
 *             @OA\Property(property="last_name", type="string", example="Smith"),
 *             @OA\Property(property="email", type="string", format="email", example="johnsmith@example.com")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User updated successfully"
 *     )
 * )
 */
Flight::route('PUT /users/@id', function($id){
    $data = Flight::request()->data->getData();
    $userService = new UserService();
    try {
        $result = $userService->update($id, $data);
        Flight::json(['success' => true, 'message' => 'User updated successfully']);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Patch(
 *     path="/users/{id}",
 *     tags={"users"},
 *     summary="Partially update user",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="first_name", type="string", example="John"),
 *             @OA\Property(property="last_name", type="string", example="Smith")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User partially updated successfully"
 *     )
 * )
 */
Flight::route('PATCH /users/@id', function($id){
    $data = Flight::request()->data->getData();
    $userService = new UserService();
    try {
        $result = $userService->update($id, $data);
        Flight::json(['success' => true, 'message' => 'User partially updated successfully']);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Delete(
 *     path="/users/{id}",
 *     tags={"users"},
 *     summary="Delete user by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User deleted successfully"
 *     )
 * )
 */
Flight::route('DELETE /users/@id', function($id){
    $userService = new UserService();
    $result = $userService->delete($id);
    if ($result) {
        Flight::json(['success' => true, 'message' => 'User deleted successfully']);
    } else {
        Flight::json(['success' => false, 'message' => 'User not found'], 404);
    }
});

/**
 * @OA\Put(
 *     path="/users/{id}/profile",
 *     tags={"users"},
 *     summary="Update user profile",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="first_name", type="string", example="John"),
 *             @OA\Property(property="last_name", type="string", example="Smith"),
 *             @OA\Property(property="email", type="string", format="email", example="newemail@example.com"),
 *             @OA\Property(property="password", type="string", example="newpassword123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Profile updated successfully"
 *     )
 * )
 */
Flight::route('PUT /users/@id/profile', function($id){
    $data = Flight::request()->data->getData();
    $userService = new UserService();
    try {
        $result = $userService->updateProfile($id, $data);
        Flight::json(['success' => true, 'message' => 'Profile updated successfully']);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});
?>