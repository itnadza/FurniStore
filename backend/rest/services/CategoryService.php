<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/CategoryDao.php';

class CategoryService extends BaseService {
    public function __construct() {
        parent::__construct(new CategoryDao());
    }

    // Validate category data
    public function create($categoryData) {
        $errors = $this->validateCategoryData($categoryData);
        if (!empty($errors)) {
            throw new Exception(implode(', ', $errors));
        }

        // Check if category name already exists
        $existing = $this->dao->getByName($categoryData['name']);
        if ($existing) {
            throw new Exception("Category name already exists");
        }

        return $this->dao->insert($categoryData);
    }

    public function update($id, $categoryData) {
        $errors = $this->validateCategoryData($categoryData, false);
        if (!empty($errors)) {
            throw new Exception(implode(', ', $errors));
        }

        // Check if category name already exists (excluding current category)
        if (isset($categoryData['name'])) {
            $existing = $this->dao->getByName($categoryData['name']);
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

        if (isset($data['description']) && strlen($data['description']) > 500) {
            $errors[] = "Description too long (max 500 characters)";
        }

        return $errors;
    }

    public function getCategoriesWithProductCounts() {
        return $this->dao->getCategoriesWithProductCounts();
    }

    public function canDeleteCategory($categoryId) {
        $products = $this->dao->getProductsByCategory($categoryId);
        return empty($products);
    }

    // Delete category only if no products are associated
    public function delete($id) {
        if (!$this->canDeleteCategory($id)) {
            throw new Exception("Cannot delete category: There are products associated with this category");
        }

        return $this->dao->delete($id);
    }

    // Get category by name
    public function getByName($name) {
        return $this->dao->getByName($name);
    }
}
?>