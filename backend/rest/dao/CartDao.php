<?php
require_once __DIR__ . '/BaseDao.php';

class CartDao extends BaseDao {
    public function __construct() {
        parent::__construct("cart");
    }

    public function getAll() { return parent::getAll(); }
    public function getById($id) { return parent::getById($id); }
    public function insert($data) { return parent::insert($data); }
    public function update($id, $data) { return parent::update($id, $data); }
    public function delete($id) { return parent::delete($id); }

    // CUSTOM METHODS
    public function getByUserId($user_id) {
        $stmt = $this->connection->prepare("SELECT * FROM cart WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function clearCartByUserId($user_id) {
        $stmt = $this->connection->prepare("DELETE FROM cart WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        return $stmt->execute();
    }

    // 🚨 ADD THESE MISSING METHODS:

    public function getCartWithDetails($userId) {
        $sql = "SELECT c.*, p.name, p.price, p.image_url 
                FROM cart c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.user_id = :user_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCartItem($userId, $productId) {
        $sql = "SELECT * FROM cart WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateCartItem($userId, $productId, $quantity) {
        $sql = "UPDATE cart SET quantity = :quantity 
                WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':quantity', $quantity);
        return $stmt->execute();
    }

    public function addToCart($userId, $productId, $quantity) {
        $sql = "INSERT INTO cart (user_id, product_id, quantity) 
                VALUES (:user_id, :product_id, :quantity)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':quantity', $quantity);
        return $stmt->execute();
    }

    public function removeFromCart($userId, $productId) {
        $sql = "DELETE FROM cart WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        return $stmt->execute();
    }

    public function clearCart($userId) {
        $stmt = $this->connection->prepare("DELETE FROM cart WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userId);
        return $stmt->execute();
    }
}
?>
