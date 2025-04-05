<?php
class Product extends BaseModel {
    protected $table = 'products';

    public function __construct() {
        parent::__construct();
    }

    public function getLowStockProducts($limit = 5) {
        $sql = "SELECT * FROM products 
                WHERE deleted_at IS NULL 
                AND stock <= min_stock 
                ORDER BY (stock - min_stock) ASC 
                LIMIT :limit";

        $this->db->query($sql);
        $this->db->bind(':limit', $limit);

        return $this->db->resultSet();
    }

    public function getActiveProducts() {
        $this->db->query("SELECT p.*, c.name as category_name 
                         FROM {$this->table} p 
                         LEFT JOIN categories c ON p.category_id = c.id 
                         WHERE p.deleted_at IS NULL AND p.stock > 0 
                         ORDER BY p.name ASC");
        return $this->db->resultSet();
    }

    public function searchProducts($search = '') {
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.deleted_at IS NULL AND p.stock > 0";
        
        if (!empty($search)) {
            $sql .= " AND (p.code LIKE :search OR p.name LIKE :search)";
        }
        
        $sql .= " ORDER BY p.name ASC";
        
        $this->db->query($sql);
        
        if (!empty($search)) {
            $this->db->bind(':search', "%{$search}%");
        }
        
        return $this->db->resultSet();
    }

    public function getProducts($page = 1, $limit = 10, $search = '') {
        $offset = ($page - 1) * $limit;
        $where = "p.deleted_at IS NULL";
        
        if (!empty($search)) {
            $where .= " AND (p.code LIKE :search OR p.name LIKE :search)";
        }

        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE {$where} 
                ORDER BY p.created_at DESC 
                LIMIT :limit OFFSET :offset";

        $this->db->query($sql);
        
        if (!empty($search)) {
            $this->db->bind(':search', "%{$search}%");
        }
        
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);

        return $this->db->resultSet();
    }

    public function getTotalProducts($search = '') {
        $where = "deleted_at IS NULL";
        
        if (!empty($search)) {
            $where .= " AND (code LIKE :search OR name LIKE :search)";
        }

        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE {$where}";
        $this->db->query($sql);

        if (!empty($search)) {
            $this->db->bind(':search', "%{$search}%");
        }

        $result = $this->db->single();
        return $result->total;
    }

    public function createProduct($data) {
        $this->db->query("INSERT INTO {$this->table} (id, category_id, code, name, description, price, stock, min_stock) 
                         VALUES (:id, :category_id, :code, :name, :description, :price, :stock, :min_stock)");

        $this->db->bind(':id', $this->generateGuid());
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':code', $data['code']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':stock', $data['stock']);
        $this->db->bind(':min_stock', $data['min_stock']);

        return $this->db->execute();
    }

    public function updateProduct($id, $data) {
        $this->db->query("UPDATE {$this->table} 
                         SET category_id = :category_id, 
                             code = :code,
                             name = :name, 
                             description = :description,
                             price = :price,
                             stock = :stock,
                             min_stock = :min_stock,
                             updated_at = CURRENT_TIMESTAMP
                         WHERE id = :id");

        $this->db->bind(':id', $id);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':code', $data['code']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':stock', $data['stock']);
        $this->db->bind(':min_stock', $data['min_stock']);

        return $this->db->execute();
    }

    public function deleteProduct($id) {
        $this->db->query("UPDATE {$this->table} SET deleted_at = CURRENT_TIMESTAMP WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function findByCode($code) {
        $this->db->query("SELECT * FROM {$this->table} WHERE code = :code AND deleted_at IS NULL");
        $this->db->bind(':code', $code);
        return $this->db->single();
    }
}
