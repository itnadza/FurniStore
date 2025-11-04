<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/ProductDao.php';

class ProductService extends BaseService {
    public function __construct() {
        parent::__construct(new ProductDao());
    }

    // Business Logic: Validate product data
    public function create($productData) {
        if (empty($productData['name']) || empty($productData['price'])) {
            throw new Exception("Name and price are required");
        }

        if ($productData['price'] < 0) {
            throw new Exception("Price cannot be negative");
        }

        return $this->dao->create($productData);
    }

    // Business Logic: Check stock availability
    public function isProductAvailable($productId, $quantity = 1) {
        $product = $this->dao->getById($productId);
        return $product && $product['stock_quantity'] >= $quantity;
    }
}
?>