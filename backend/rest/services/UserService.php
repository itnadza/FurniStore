<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/UserDao.php';

class UserService extends BaseService {
    public function __construct() {
        parent::__construct(new UserDao());
    }
    
    public function register($userData) {
        // Validation
        if (empty($userData['email']) || empty($userData['password'])) {
            throw new Exception("Email and password are required");
        }

        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        // Check if email already exists
        $existingUser = $this->dao->getByEmail($userData['email']);
        if ($existingUser) {
            throw new Exception("Email already registered");
        }

        // Hash password
        $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);

        // Create user
        return $this->dao->insert($userData);
    }
   
    public function login($email, $password) {
        $user = $this->dao->getByEmail($email);
        
        if (!$user || !password_verify($password, $user['password'])) {
            throw new Exception("Invalid email or password");
        }

        // Remove password from returned data
        unset($user['password']);
        return $user;
    }

    public function updateProfile($id, $data) {
        // If password is being updated, hash it
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        return $this->update($id, $data);
    }
}
?>