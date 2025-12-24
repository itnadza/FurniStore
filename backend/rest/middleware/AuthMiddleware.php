<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware {

    public function verifyToken() {
        
        $headers = getallheaders();

        if (!isset($headers['Authentication'])) {
            Flight::halt(401, 'Missing authentication header');
        }

        $authHeader = $headers['Authentication'];

        if (!str_starts_with($authHeader, 'Bearer ')) {
            Flight::halt(401, 'Invalid authentication header format');
        }

        $token = str_replace('Bearer ', '', $authHeader);

        try {
            $decoded = JWT::decode(
                $token,
                new Key(Config::JWT_SECRET(), 'HS256')
            );
        } catch (Exception $e) {
            Flight::halt(401, 'Invalid or expired token');
        }

        
        Flight::set('user', $decoded->user);
        Flight::set('jwt_token', $token);

        return true;
    }

    public function authorizeRole($requiredRole) {
        $user = Flight::get('user');

        if (!isset($user->role) || $user->role !== $requiredRole) {
            Flight::halt(403, 'Access denied: insufficient privileges');
        }
    }

    public function authorizeRoles(array $roles) {
        $user = Flight::get('user');

        if (!isset($user->role) || !in_array($user->role, $roles)) {
            Flight::halt(403, 'Forbidden: role not allowed');
        }
    }

    public function authorizePermission($permission) {
        $user = Flight::get('user');

        if (!isset($user->permissions) || !is_array($user->permissions)) {
            Flight::halt(403, 'Access denied: no permissions assigned');
        }

        if (!in_array($permission, $user->permissions)) {
            Flight::halt(403, 'Access denied: permission missing');
        }
    }
}
