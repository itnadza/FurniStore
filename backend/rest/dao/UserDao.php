<?php
require_once __DIR__ . '/BaseDao.php';

class UserDao extends BaseDao {
    protected $table_name;

    public function __construct() {
        $this->table_name = "users";
        parent::__construct($this->table_name);
    }

    public function getAll() {
        return parent::getAll();
    }

    public function getById($id) {
        return parent::getById($id);
    }

    public function insert($data) {
        return parent::insert($data);
    }

    public function update($id, $data) {
        return parent::update($id, $data);
    }

    public function delete($id) {
        return parent::delete($id);
    }

    // CUSTOM METHODS 
    public function getByEmail($email) {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getUserByEmail($email) {
        return $this->getByEmail($email);
    }

    public function createUser($first_name, $last_name, $email, $password, $age) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        return $this->insert([
            'first_name' => $first_name,
            'last_name'  => $last_name,
            'email'      => $email,
            'password'   => $hashedPassword,
            'age'        => $age
        ]);
    }

    // Alias for insert to match service calls
    public function create($data) {
        return $this->insert($data);
    }
}
?>