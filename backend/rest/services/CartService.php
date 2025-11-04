<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/CartDao.php';

class CartService extends BaseService {
    public function __construct() {
        parent::__construct(new CartDao());
    }

    // Business Logic: Add to cart with validation
    public function addToCart($userId, $productId, $quantity = 1) {
        if ($quantity <= 0) {
            throw new Exception("Quantity must be positive");
        }

        return $this->dao->addToCart($userId, $productId, $quantity);
    }

    // Business Logic: Get cart with details
    public function getCartWithDetails($userId) {
        return $this->dao->getCartWithDetails($userId);
    }
}
?>