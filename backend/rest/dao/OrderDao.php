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

    //CUSTOM METHODS
    public function getByUserId($user_id) {
        $stmt = $this->connection->prepare("SELECT * FROM orders WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
