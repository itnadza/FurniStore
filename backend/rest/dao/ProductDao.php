<?php
require_once __DIR__ . '/BaseDao.php';

class ProductDao extends BaseDao {
    public function __construct() {
        parent::__construct("products");
    }

    public function getAll() { return parent::getAll(); }
    public function getById($id) { return parent::getById($id); }
    public function insert($data) { return parent::insert($data); }
    public function update($id, $data) { return parent::update($id, $data); }
    public function delete($id) { return parent::delete($id); }

    // CUSTOM METHODS 
    public function getByCategoryId($category_id) {
        $stmt = $this->connection->prepare("SELECT * FROM products WHERE category_id = :category_id");
        $stmt->bindParam(':category_id', $category_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByUserId($user_id) {
        $stmt = $this->connection->prepare("SELECT * FROM products WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

   
    public function create($data) {
        return $this->insert($data);
    }

    public function updateStock($productId, $quantityChange) {
        $sql = "UPDATE products SET stock_quantity = stock_quantity + :quantity_change WHERE id = :product_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':quantity_change', $quantityChange);
        $stmt->bindParam(':product_id', $productId);
        return $stmt->execute();
    }

    public function getFeaturedProducts($limit = 10) {
        $sql = "SELECT * FROM products WHERE featured = 1 ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchProducts($searchTerm) {
        $sql = "SELECT * FROM products WHERE name LIKE :search OR description LIKE :search";
        $stmt = $this->connection->prepare($sql);
        $searchParam = "%$searchTerm%";
        $stmt->bindParam(':search', $searchParam);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>