<?php
class Auth extends Controller {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = $this->model('User');
    }

    public function index() {
        if ($this->isLoggedIn()) {
            header('location: ' . BASE_URL . 'dashboard/index');
            exit;
        }
        header('location: ' . BASE_URL . 'auth/login');
        exit;
    }

    public function login() {
        if ($this->isLoggedIn()) {
            header('location: ' . BASE_URL . 'dashboard/index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validate CSRF token
            if (!$this->validateCsrfToken($_POST['csrf_token'])) {
                die('Invalid CSRF token');
            }

            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'username' => trim($_POST['username']),
                'password' => trim($_POST['password']),
                'username_err' => '',
                'password_err' => '',
                'csrf_token' => $this->generateCsrfToken()
            ];

            // Validate username
            if (empty($data['username'])) {
                $data['username_err'] = 'Please enter username';
            }

            // Validate password
            if (empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            }

            // Check for errors
            if (empty($data['username_err']) && empty($data['password_err'])) {
                // Check and verify user
                $user = $this->userModel->findUserByUsername($data['username']);

                if ($user && password_verify($data['password'], $user->password)) {
                    // Create session
                    $_SESSION['user_id'] = $user->id;
                    $_SESSION['user_name'] = $user->name;
                    $_SESSION['user_role'] = $user->role;
                    $_SESSION['last_activity'] = time();

                    // Log successful login
                    $this->logAudit('login', 'User logged in successfully');

                    header('location: ' . BASE_URL . 'dashboard/index');
                } else {
                    $data['password_err'] = 'Invalid username or password';
                    $this->logAudit('login_failed', 'Failed login attempt for username: ' . $data['username']);
                    $this->view('auth/login', $data);
                }
            } else {
                // Load view with errors
                $this->view('auth/login', $data);
            }
        } else {
            // Init data
            $data = [
                'username' => '',
                'password' => '',
                'username_err' => '',
                'password_err' => '',
                'csrf_token' => $this->generateCsrfToken()
            ];

            // Load view
            $this->view('auth/login', $data);
        }
    }

    public function logout() {
        if ($this->isLoggedIn()) {
            $this->logAudit('logout', 'User logged out');
        }
        
        // Unset all session values
        $_SESSION = array();

        // Destroy the session
        session_destroy();

        // Redirect to login
        header('location: ' . BASE_URL . 'auth/login');
        exit;
    }
}
