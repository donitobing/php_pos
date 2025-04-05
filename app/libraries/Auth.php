<?php
class Auth {
    private $db;
    
    public function __construct() {
        $this->db = new Database;
    }
    
    // Check if user is logged in
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    // Check user role
    public function hasRole($role) {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
    }
    
    // Require specific role
    public function requireRole($role) {
        if (!$this->hasRole($role)) {
            header('location: ' . BASE_URL . 'auth/login');
            exit;
        }
    }
    
    // Get current user
    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['user_name'],
                'role' => $_SESSION['user_role']
            ];
        }
        return null;
    }
}
