<?php
class Category extends BaseModel {
    protected $table = 'categories';

    public function __construct() {
        parent::__construct();
    }

    public function getCategories() {
        $this->db->query("SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY name ASC");
        return $this->db->resultSet();
    }

    public function createCategory($data) {
        $this->db->query("INSERT INTO {$this->table} (id, name, description) VALUES (:id, :name, :description)");
        
        $this->db->bind(':id', $this->generateGuid());
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);

        return $this->db->execute();
    }

    public function updateCategory($id, $data) {
        $this->db->query("UPDATE {$this->table} SET name = :name, description = :description WHERE id = :id");
        
        $this->db->bind(':id', $id);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);

        return $this->db->execute();
    }

    public function deleteCategory($id) {
        $this->db->query("UPDATE {$this->table} SET deleted_at = CURRENT_TIMESTAMP WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
