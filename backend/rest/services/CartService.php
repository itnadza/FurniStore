<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/CartDao.php';

class CartService extends BaseService {
    public function __construct() {
        parent::__construct(new CartDao());
    }

    public function addToCart($userId, $productId, $quantity = 1) {
        if ($quantity <= 0) {
            throw new Exception("Quantity must be positive");
        }

        return $this->dao->addToCart($userId, $productId, $quantity);
    }


    public function getCartWithDetails($userId) {
        return $this->dao->getCartWithDetails($userId);
    }

    

    public function updateCartItem($userId, $productId, $quantity) {
        if ($quantity <= 0) {
            return $this->removeFromCart($userId, $productId);
        }

        return $this->dao->updateCartItem($userId, $productId, $quantity);
    }

   
    public function removeFromCart($userId, $productId) {
        return $this->dao->removeFromCart($userId, $productId);
    }

    public function clearCart($userId) {
        return $this->dao->clearCart($userId);
    }


    public function calculateCartTotal($userId) {
        $cartItems = $this->getCartWithDetails($userId);
        $total = 0;

        foreach ($cartItems as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return $total;
    }
}
?>