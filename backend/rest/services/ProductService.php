<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/ProductDao.php';

class ProductService extends BaseService {
    public function __construct() {
        parent::__construct(new ProductDao());
    }

    public function create($productData) {
        $errors = $this->validateProductData($productData);
        if (!empty($errors)) {
            throw new Exception(implode(', ', $errors));
        }

        // Set default values
        $productData['stock_quantity'] = $productData['stock_quantity'] ?? 0;
        $productData['featured'] = $productData['featured'] ?? 0;

        return $this->dao->insert($productData);
    }

    public function update($id, $productData) {
        $errors = $this->validateProductData($productData, false);
        if (!empty($errors)) {
            throw new Exception(implode(', ', $errors));
        }

        return $this->dao->update($id, $productData);
    }

    private function validateProductData($data, $isCreate = true) {
        $errors = [];

        if ($isCreate && empty($data['name'])) {
            $errors[] = "Product name is required";
        }

        if ($isCreate && empty($data['price'])) {
            $errors[] = "Product price is required";
        }

        if (isset($data['price']) && $data['price'] < 0) {
            $errors[] = "Price cannot be negative";
        }

        if (isset($data['stock_quantity']) && $data['stock_quantity'] < 0) {
            $errors[] = "Stock quantity cannot be negative";
        }

        if (isset($data['name']) && strlen($data['name']) > 255) {
            $errors[] = "Product name too long (max 255 characters)";
        }

        return $errors;
    }

    public function isProductAvailable($productId, $quantity = 1) {
        $product = $this->dao->getById($productId);
        return $product && $product['stock_quantity'] >= $quantity;
    }

    public function updateStock($productId, $quantityChange) {
        if ($quantityChange == 0) {
            return true; 
        }

        $product = $this->dao->getById($productId);
        if (!$product) {
            throw new Exception("Product not found");
        }

        $newStock = $product['stock_quantity'] + $quantityChange;
        if ($newStock < 0) {
            throw new Exception("Insufficient stock");
        }

        return $this->dao->updateStock($productId, $quantityChange);
    }

    public function getProductsByCategory($categoryId) {
        return $this->dao->getByCategoryId($categoryId);
    }

    public function getFeaturedProducts($limit = 10) {
        return $this->dao->getFeaturedProducts($limit);
    }

    public function searchProducts($searchTerm) {
        if (empty($searchTerm)) {
            return $this->getAll();
        }
        return $this->dao->searchProducts($searchTerm);
    }

    public function getLowStockProducts($threshold = 10) {
        $products = $this->getAll();
        $lowStock = [];
        
        foreach ($products as $product) {
            if ($product['stock_quantity'] <= $threshold) {
                $lowStock[] = $product;
            }
        }
        
        return $lowStock;
    }
}
?>