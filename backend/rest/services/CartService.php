<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/CartDao.php';
require_once __DIR__ . '/../dao/ProductDao.php'; // You'll need this

class CartService extends BaseService {
    private $productDao;

    public function __construct() {
        parent::__construct(new CartDao());
        $this->productDao = new ProductDao(); // Add this
    }

    public function addToCart($userId, $productId, $quantity = 1) {
        // Validate quantity
        if ($quantity <= 0) {
            throw new Exception("Quantity must be positive");
        }

        // Check if product exists (you'll need ProductDao)
        $product = $this->productDao->getById($productId);
        if (!$product) {
            throw new Exception("Product not found");
        }

        // Check stock availability
        if ($product['stock_quantity'] < $quantity) {
            throw new Exception("Not enough stock available");
        }

        return $this->dao->addToCart($userId, $productId, $quantity);
    }

    public function getCartWithDetails($userId) {
        $cartItems = $this->dao->getCartWithDetails($userId);
        
        // Add any business logic here (calculations, validations, etc.)
        foreach ($cartItems as &$item) {
            $item['subtotal'] = $item['price'] * $item['quantity'];
        }
        
        return $cartItems;
    }

    public function updateCartItem($userId, $productId, $quantity) {
        if ($quantity <= 0) {
            return $this->removeFromCart($userId, $productId);
        }

        // Check product stock if needed
        $product = $this->productDao->getById($productId);
        if ($product['stock_quantity'] < $quantity) {
            throw new Exception("Not enough stock available");
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

    public function getCartItemCount($userId) {
        $cartItems = $this->dao->getByUserId($userId);
        return count($cartItems);
    }
}
?>