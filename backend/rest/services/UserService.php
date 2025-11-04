<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/UserDao.php';

class UserService extends BaseService {
    public function __construct() {
        parent::__construct(new UserDao());
    }

    // Business Logic: User registration with validation
    public function register($userData) {
        // Validation
        if (empty($userData['email']) || empty($userData['password'])) {
            throw new Exception("Email and password are required");
        }

        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        // Check if user exists
        $existingUser = $this->dao->getUserByEmail($userData['email']);
        if ($existingUser) {
            throw new Exception("Email already registered");
        }

        // Hash password
        $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);

        return $this->dao->create($userData);
    }

    // Business Logic: User login
    public function login($email, $password) {
        $user = $this->dao->getUserByEmail($email);
        
        if (!$user || !password_verify($password, $user['password'])) {
            throw new Exception("Invalid email or password");
        }

        // Remove password from response
        unset($user['password']);
        return $user;
    }
}
?>