<?php
class User extends BaseModel {
    protected $table = 'users';
    public function __construct() {
        parent::__construct();
    }

    // Find user by username
    public function findUserByUsername($username) {
        $this->db->query('SELECT * FROM users WHERE username = :username AND deleted_at IS NULL');
        $this->db->bind(':username', $username);

        $row = $this->db->single();
        return $row;
    }

    // Get user by ID
    public function getUserById($id) {
        $this->db->query('SELECT * FROM users WHERE id = :id');
        $this->db->bind(':id', $id);

        $row = $this->db->single();
        return $row;
    }

    // Get users with pagination and search
    public function getUsers($page = 1, $limit = 10, $search = '') {
        $offset = ($page - 1) * $limit;
        $query = 'SELECT id, username, name, role, created_at FROM users WHERE deleted_at IS NULL';
        $params = [];
        
        if (!empty($search)) {
            $query .= ' AND (username LIKE :search OR name LIKE :search)';
            $params[':search'] = "%{$search}%";
        }
        
        $query .= ' ORDER BY created_at DESC LIMIT :limit OFFSET :offset';
        $params[':limit'] = $limit;
        $params[':offset'] = $offset;
        
        $this->db->query($query);
        foreach ($params as $param => $value) {
            $this->db->bind($param, $value);
        }
        
        return $this->db->resultSet();
    }

    // Get total number of users
    public function getTotalUsers($search = '') {
        $query = 'SELECT COUNT(*) as total FROM users WHERE deleted_at IS NULL';
        $params = [];
        
        if (!empty($search)) {
            $query .= ' AND (username LIKE :search OR name LIKE :search)';
            $params[':search'] = "%{$search}%";
        }
        
        $this->db->query($query);
        foreach ($params as $param => $value) {
            $this->db->bind($param, $value);
        }
        
        $result = $this->db->single();
        return $result->total;
    }

    // Create new user
    public function createUser($data) {
        // Generate UUID for the user
        $uuid = $this->generateUuid();
        
        $this->db->query('INSERT INTO users (id, username, password, name, role, created_at) 
                          VALUES (:id, :username, :password, :name, :role, NOW())');
        
        $this->db->bind(':id', $uuid);
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':role', $data['role']);

        return $this->db->execute();
    }

    // Generate UUID
    private function generateUuid() {
        if (function_exists('random_bytes')) {
            $data = random_bytes(16);
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        } else {
            // Fallback for older PHP versions
            return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );
        }
    }

    // Update user
    public function updateUser($id, $data) {
        // If password is provided, update it too
        if (!empty($data['password'])) {
            $this->db->query('UPDATE users SET username = :username, password = :password, name = :name, role = :role, updated_at = NOW() WHERE id = :id');
            $this->db->bind(':password', $data['password']);
        } else {
            $this->db->query('UPDATE users SET username = :username, name = :name, role = :role, updated_at = NOW() WHERE id = :id');
        }

        $this->db->bind(':id', $id);
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':role', $data['role']);

        return $this->db->execute();
    }

    // Delete user
    public function deleteUser($id) {
        $this->db->query('DELETE FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
