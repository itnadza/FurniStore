<?php
require_once __DIR__ . '/BaseDao.php';

class OrderDao extends BaseDao {
    public function __construct() {
        parent::__construct("orders");
    }

    public function getAll() { return parent::getAll(); }
    public function getById($id) { return parent::getById($id); }
    public function insert($data) { return parent::insert($data); }
    public function update($id, $data) { return parent::update($id, $data); }
    public function delete($id) { return parent::delete($id); }

    // CUSTOM METHODS
    public function getByUserId($user_id) {
        $stmt = $this->connection->prepare("SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Alias for insert to match service calls
    public function create($data) {
        return $this->insert($data);
    }

    public function addOrderItem($orderItemData) {
        $sql = "INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) 
                VALUES (:order_id, :product_id, :quantity, :price, :subtotal)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':order_id', $orderItemData['order_id']);
        $stmt->bindParam(':product_id', $orderItemData['product_id']);
        $stmt->bindParam(':quantity', $orderItemData['quantity']);
        $stmt->bindParam(':price', $orderItemData['price']);
        $stmt->bindParam(':subtotal', $orderItemData['subtotal']);
        return $stmt->execute();
    }

    public function getOrderItems($orderId) {
        $sql = "SELECT * FROM order_items WHERE order_id = :order_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':order_id', $orderId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderItemsWithDetails($orderId) {
        $sql = "SELECT oi.*, p.name, p.image_url 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = :order_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':order_id', $orderId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserOrders($userId) {
        return $this->getByUserId($userId);
    }

    public function getOrderStatistics($userId = null) {
        if ($userId) {
            // User-specific statistics
            $sql = "SELECT 
                    COUNT(*) as total_orders,
                    SUM(total_amount) as total_spent,
                    AVG(total_amount) as avg_order_value,
                    MIN(created_at) as first_order_date,
                    MAX(created_at) as last_order_date
                    FROM orders 
                    WHERE user_id = :user_id";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindParam(':user_id', $userId);
        } else {
            // Admin statistics (all orders)
            $sql = "SELECT 
                    COUNT(*) as total_orders,
                    SUM(total_amount) as total_revenue,
                    AVG(total_amount) as avg_order_value,
                    COUNT(DISTINCT user_id) as unique_customers,
                    status
                    FROM orders 
                    GROUP BY status";
            $stmt = $this->connection->prepare($sql);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrdersByStatus($status) {
        $sql = "SELECT * FROM orders WHERE status = :status ORDER BY created_at DESC";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>