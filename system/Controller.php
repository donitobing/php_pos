<?php
class Controller {
    public function __construct() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Load model
    public function model($model) {
        $modelFile = 'app/models/' . $model . '.php';
        if (!file_exists($modelFile)) {
            die('Model file not found: ' . $modelFile);
        }
        require_once $modelFile;
        if (!class_exists($model)) {
            die('Model class not found: ' . $model);
        }
        return new $model();
    }

    // Load view
    public function view($view, $data = [], $layout = 'default') {
        $viewFile = 'app/views/' . $view . '.php';
        if(!file_exists($viewFile)) {
            die('View file not found: ' . $viewFile);
        }

        // Extract data variables to make them available in the view
        extract($data);

        // Start output buffering
        ob_start();

        // Load the view
        require $viewFile;

        // Get the view content
        $content = ob_get_clean();

        // Load the layout if needed
        if (!isset($data['layout']) || $data['layout'] !== false) {
            $layoutFile = 'app/views/layouts/' . $layout . '.php';
            if (!file_exists($layoutFile)) {
                die('Layout file not found: ' . $layoutFile);
            }
            require $layoutFile;
        } else {
            echo $content;
        }
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

    // Audit trail
    protected function logAudit($action, $details) {
        if (!ENABLE_AUDIT_TRAIL) return;
        
        $audit = $this->model('AuditTrail');
        $audit->log([
            'user_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null,
            'action' => $action,
            'details' => $details,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT']
        ]);
    }

    // Check if user is logged in
    protected function isLoggedIn() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['last_activity'])) {
            return false;
        }

        // Check session timeout
        if (time() - $_SESSION['last_activity'] > SESSION_LIFETIME) {
            // Session expired, destroy it
            session_destroy();
            return false;
        }

        // Update last activity time
        $_SESSION['last_activity'] = time();
        return true;
    }

    // Require login
    protected function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('location: ' . BASE_URL . 'auth/login');
            exit;
        }
    }

    // CSRF protection
    protected function generateCsrfToken() {
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }

    protected function validateCsrfToken($token) {
        return isset($_SESSION[CSRF_TOKEN_NAME]) && 
               hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    }
}
