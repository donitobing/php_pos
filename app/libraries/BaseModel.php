<?php
class BaseModel {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';

    public function __construct() {
        $this->db = new Database;
    }

    // Generate GUID
    protected function generateGuid() {
        if (function_exists('com_create_guid')) {
            return trim(com_create_guid(), '{}');
        }
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    // Find by ID
    public function findById($id) {
        $this->db->query("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Get all records
    public function getAll($orderBy = null) {
        $sql = "SELECT * FROM {$this->table}";
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    // Delete record
    public function delete($id) {
        $this->db->query("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Count records
    public function count($conditions = '') {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        if ($conditions) {
            $sql .= " WHERE {$conditions}";
        }
        $this->db->query($sql);
        $result = $this->db->single();
        return $result->count;
    }

    // Begin transaction
    public function beginTransaction() {
        return $this->db->beginTransaction();
    }

    // Commit transaction
    public function commit() {
        return $this->db->commit();
    }

    // Rollback transaction
    public function rollBack() {
        return $this->db->rollBack();
    }
}
