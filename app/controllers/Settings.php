<?php
class Settings extends Controller {
    private $settingModel;

    public function __construct() {
        parent::__construct();
        
        if (!$this->isLoggedIn()) {
            header('location: ' . BASE_URL . 'auth/login');
            exit;
        }

        // Only admin can access settings
        if ($_SESSION['user_role'] !== 'admin') {
            $_SESSION['flash_message'] = 'Access denied. Admin only.';
            $_SESSION['flash_type'] = 'error';
            header('location: ' . BASE_URL . 'dashboard');
            exit;
        }

        $this->settingModel = $this->model('Setting');
        // Initialize settings if not exists
        $this->settingModel->initializeSettings();
    }

    public function index() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validate CSRF token
            if (!$this->validateCsrfToken($_POST['csrf_token'])) {
                die('Invalid CSRF token');
            }

            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'id' => trim($_POST['id']),
                'store_name' => trim($_POST['store_name']),
                'address' => trim($_POST['address']),
                'phone' => trim($_POST['phone']),
                'tax_percentage' => floatval(trim($_POST['tax_percentage'])),
                'errors' => []
            ];

            // Validate data
            if (empty($data['store_name'])) {
                $data['errors']['store_name'] = 'Store name is required';
            }

            if ($data['tax_percentage'] < 0 || $data['tax_percentage'] > 100) {
                $data['errors']['tax_percentage'] = 'Tax percentage must be between 0 and 100';
            }

            // If no errors, update settings
            if (empty($data['errors'])) {
                if ($this->settingModel->updateSettings($data)) {
                    $this->logAudit('settings_update', 'Updated store settings');
                    $_SESSION['flash_message'] = 'Settings updated successfully';
                    $_SESSION['flash_type'] = 'success';
                    header('location: ' . BASE_URL . 'settings');
                    exit;
                } else {
                    $_SESSION['flash_message'] = 'Something went wrong';
                    $_SESSION['flash_type'] = 'error';
                }
            }

            $data['title'] = 'Store Settings';
            $data['csrf_token'] = $this->generateCsrfToken();
            $this->view('settings/index', $data);
        } else {
            $settings = $this->settingModel->getSettings();
            
            $data = [
                'title' => 'Store Settings',
                'id' => $settings->id,
                'store_name' => $settings->store_name,
                'address' => $settings->address,
                'phone' => $settings->phone,
                'tax_percentage' => $settings->tax_percentage,
                'errors' => [],
                'csrf_token' => $this->generateCsrfToken()
            ];

            $this->view('settings/index', $data);
        }
    }
}
