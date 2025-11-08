<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/CategoryDao.php';

class CategoryService extends BaseService {
    public function __construct() {
        parent::__construct(new CategoryDao());
    }

    // Business Logic: Validate category data
    public function create($categoryData) {
        $errors = $this->validateCategoryData($categoryData);
        if (!empty($errors)) {
            throw new Exception(implode(', ', $errors));
        }

        // Check for duplicate category name
        $existing = $this->dao->getCategoryByName($categoryData['name']);
        if ($existing) {
            throw new Exception("Category name already exists");
        }

        return $this->dao->create($categoryData);
    }

    // Business Logic: Update category with validation
    public function update($id, $categoryData) {
        $errors = $this->validateCategoryData($categoryData, false);
        if (!empty($errors)) {
            throw new Exception(implode(', ', $errors));
        }

        // Check for duplicate category name (excluding current category)
        if (isset($categoryData['name'])) {
            $existing = $this->dao->getCategoryByName($categoryData['name']);
            if ($existing && $existing['id'] != $id) {
                throw new Exception("Category name already exists");
            }
        }

        return $this->dao->update($id, $categoryData);
    }

    private function validateCategoryData($data, $isCreate = true) {
        $errors = [];

        if ($isCreate && empty($data['name'])) {
            $errors[] = "Category name is required";
        }

        if (isset($data['name']) && strlen($data['name']) > 100) {
            $errors[] = "Category name too long (max 100 characters)";
        }

        if (isset($data['name']) && strlen($data['name']) < 2) {
            $errors[] = "Category name too short (min 2 characters)";
        }

        return $errors;
    }

    // Business Logic: Get categories with product counts
    public function getCategoriesWithProductCounts() {
        return $this->dao->getCategoriesWithProductCounts();
    }

    // Business Logic: Check if category can be deleted (no products associated)
    public function canDeleteCategory($categoryId) {
        $products = $this->dao->getProductsByCategory($categoryId);
        return empty($products);
    }

    // Business Logic: Delete category only if no products are associated
    public function delete($id) {
        if (!$this->canDeleteCategory($id)) {
            throw new Exception("Cannot delete category: There are products associated with this category");
        }

        return $this->dao->delete($id);
    }
}
?>