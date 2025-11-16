<?php
require_once __DIR__ . '/BaseDao.php';

class CategoryDao extends BaseDao {
    public function __construct() {
        parent::__construct("categories");
    }

    public function getAll() { return parent::getAll(); }
    public function getById($id) { return parent::getById($id); }
    public function insert($data) { return parent::insert($data); }
    public function update($id, $data) { return parent::update($id, $data); }
    public function delete($id) { return parent::delete($id); }

    // CUSTOM METHODS
    public function getByName($name) {
        $stmt = $this->connection->prepare("SELECT * FROM categories WHERE name = :name");
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Alias for getByName to match service calls
    public function getCategoryByName($name) {
        return $this->getByName($name);
    }

    public function getCategoriesWithProductCounts() {
        $sql = "SELECT c.*, COUNT(p.id) as product_count 
                FROM categories c 
                LEFT JOIN products p ON c.id = p.category_id 
                GROUP BY c.id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductsByCategory($categoryId) {
        $sql = "SELECT * FROM products WHERE category_id = :category_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':category_id', $categoryId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Alias for insert to match service calls
    public function create($data) {
        return $this->insert($data);
    }
}
?>