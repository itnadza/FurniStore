<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/OrderDao.php';
require_once __DIR__ . '/CartService.php';
require_once __DIR__ . '/ProductService.php';

class OrderService extends BaseService {
    private $cartService;
    private $productService;

    public function __construct() {
        parent::__construct(new OrderDao());
        $this->cartService = new CartService();
        $this->productService = new ProductService();
    }

    // Business Logic: Create order from cart
    public function createOrderFromCart($userId, $orderData) {
        // Validate required fields
        $required = ['shipping_address', 'payment_method'];
        foreach ($required as $field) {
            if (empty($orderData[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }

        // Get cart items
        $cartItems = $this->cartService->getCartWithDetails($userId);
        if (empty($cartItems)) {
            throw new Exception("Cart is empty");
        }

        // Validate stock availability for all items
        foreach ($cartItems as $item) {
            if (!$this->productService->isProductAvailable($item['product_id'], $item['quantity'])) {
                throw new Exception("Insufficient stock for product: " . $item['name']);
            }
        }

        // Calculate total amount
        $totalAmount = 0;
        foreach ($cartItems as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
        }

        // Create order data
        $orderData['user_id'] = $userId;
        $orderData['total_amount'] = $totalAmount;
        $orderData['status'] = 'pending';

        // Create the order
        $orderId = $this->dao->create($orderData);

        // Create order items and update stock
        foreach ($cartItems as $item) {
            $orderItemData = [
                'order_id' => $orderId,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['price'] * $item['quantity']
            ];
            
            // Add order item
            $this->dao->addOrderItem($orderItemData);

            // Update product stock
            $this->productService->updateStock($item['product_id'], -$item['quantity']);
        }

        // Clear the user's cart
        $this->cartService->clearCart($userId);

        return $orderId;
    }

    // Business Logic: Update order status with validation
    public function updateOrderStatus($orderId, $status) {
        $allowedStatuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
        
        if (!in_array($status, $allowedStatuses)) {
            throw new Exception("Invalid order status. Allowed: " . implode(', ', $allowedStatuses));
        }

        $order = $this->dao->getById($orderId);
        if (!$order) {
            throw new Exception("Order not found");
        }

        // If cancelling order, restore stock
        if ($status === 'cancelled' && $order['status'] !== 'cancelled') {
            $this->restoreOrderStock($orderId);
        }

        // If order was cancelled and now being reactivated, check stock again
        if ($order['status'] === 'cancelled' && $status !== 'cancelled') {
            $this->validateOrderStock($orderId);
        }

        return $this->dao->update($orderId, ['status' => $status]);
    }

    // Business Logic: Restore stock when order is cancelled
    private function restoreOrderStock($orderId) {
        $orderItems = $this->dao->getOrderItems($orderId);
        
        foreach ($orderItems as $item) {
            $this->productService->updateStock($item['product_id'], $item['quantity']);
        }
    }

    // Business Logic: Validate stock for order reactivation
    private function validateOrderStock($orderId) {
        $orderItems = $this->dao->getOrderItems($orderId);
        
        foreach ($orderItems as $item) {
            if (!$this->productService->isProductAvailable($item['product_id'], $item['quantity'])) {
                throw new Exception("Cannot reactivate order: Insufficient stock for product ID " . $item['product_id']);
            }
        }

        // Deduct stock again
        foreach ($orderItems as $item) {
            $this->productService->updateStock($item['product_id'], -$item['quantity']);
        }
    }

    // Business Logic: Get user's orders
    public function getUserOrders($userId) {
        return $this->dao->getUserOrders($userId);
    }

    // Business Logic: Get order with full details
    public function getOrderWithDetails($orderId) {
        $order = $this->dao->getById($orderId);
        if (!$order) {
            throw new Exception("Order not found");
        }

        $order['items'] = $this->dao->getOrderItemsWithDetails($orderId);
        return $order;
    }

    // Business Logic: Calculate order statistics
    public function getOrderStatistics($userId = null) {
        return $this->dao->getOrderStatistics($userId);
    }

    // Business Logic: Validate order data before creation
    public function validateOrderData($orderData) {
        $errors = [];

        if (empty($orderData['shipping_address'])) {
            $errors[] = "Shipping address is required";
        }

        if (empty($orderData['payment_method'])) {
            $errors[] = "Payment method is required";
        }

        $allowedPaymentMethods = ['credit_card', 'debit_card', 'paypal', 'cash_on_delivery'];
        if (isset($orderData['payment_method']) && !in_array($orderData['payment_method'], $allowedPaymentMethods)) {
            $errors[] = "Invalid payment method";
        }

        return $errors;
    }
}
?>