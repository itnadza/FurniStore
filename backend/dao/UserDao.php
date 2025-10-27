<?php
require_once __DIR__ . '/../config.php';

class UserDao {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllUsers() {
        $stmt = $this->pdo->query("SELECT * FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createUser($first_name, $last_name, $email, $password, $age) {
        $stmt = $this->pdo->prepare("INSERT INTO users (first_name, last_name, email, password, age)
                                     VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$first_name, $last_name, $email, $password, $age]);
        return $this->pdo->lastInsertId();
    }

    public function updateUser($id, $first_name, $last_name, $email, $password, $age) {
        $stmt = $this->pdo->prepare("UPDATE users SET first_name=?, last_name=?, email=?, password=?, age=? WHERE id=?");
        return $stmt->execute([$first_name, $last_name, $email, $password, $age, $id]);
    }

    public function deleteUser($id) {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
