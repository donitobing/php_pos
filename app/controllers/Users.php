<?php
class Users extends Controller {
    private $userModel;

    public function __construct() {
        parent::__construct();
        
        if (!$this->isLoggedIn()) {
            header('location: ' . BASE_URL . 'auth/login');
            exit;
        }

        // Only admin can access user management
        if ($_SESSION['user_role'] !== 'admin') {
            $_SESSION['flash_message'] = 'Access denied. Admin only.';
            $_SESSION['flash_type'] = 'error';
            header('location: ' . BASE_URL . 'dashboard');
            exit;
        }

        $this->userModel = $this->model('User');
    }

    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $limit = 10;

        $users = $this->userModel->getUsers($page, $limit, $search);
        $totalUsers = $this->userModel->getTotalUsers($search);
        $totalPages = ceil($totalUsers / $limit);

        $data = [
            'title' => 'User Management',
            'users' => $users,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'search' => $search,
            'csrf_token' => $this->generateCsrfToken()
        ];

        $this->view('users/index', $data);
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validate CSRF token
            if (!$this->validateCsrfToken($_POST['csrf_token'])) {
                die('Invalid CSRF token');
            }

            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'title' => 'Add User',
                'username' => trim($_POST['username']),
                'password' => trim($_POST['password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'name' => trim($_POST['name']),
                'role' => trim($_POST['role']),
                'errors' => []
            ];

            // Validate data
            if (empty($data['username'])) {
                $data['errors']['username'] = 'Username is required';
            } elseif ($this->userModel->findUserByUsername($data['username'])) {
                $data['errors']['username'] = 'Username already exists';
            }

            if (empty($data['password'])) {
                $data['errors']['password'] = 'Password is required';
            } elseif (strlen($data['password']) < 6) {
                $data['errors']['password'] = 'Password must be at least 6 characters';
            }

            if ($data['password'] !== $data['confirm_password']) {
                $data['errors']['confirm_password'] = 'Passwords do not match';
            }

            if (empty($data['name'])) {
                $data['errors']['name'] = 'Name is required';
            }

            if (empty($data['role']) || !in_array($data['role'], ['admin', 'cashier'])) {
                $data['errors']['role'] = 'Invalid role selected';
            }

            // If no errors, save user
            if (empty($data['errors'])) {
                // Hash password
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

                if ($this->userModel->createUser($data)) {
                    $this->logAudit('user_create', 'Created user: ' . $data['username']);
                    $_SESSION['flash_message'] = 'User added successfully';
                    $_SESSION['flash_type'] = 'success';
                    header('location: ' . BASE_URL . 'users');
                    exit;
                } else {
                    $_SESSION['flash_message'] = 'Something went wrong';
                    $_SESSION['flash_type'] = 'error';
                }
            }

            // Load view with errors
            $data['csrf_token'] = $this->generateCsrfToken();
            $this->view('users/add', $data);
        } else {
            $data = [
                'title' => 'Add User',
                'username' => '',
                'password' => '',
                'confirm_password' => '',
                'name' => '',
                'role' => '',
                'errors' => [],
                'csrf_token' => $this->generateCsrfToken()
            ];

            $this->view('users/add', $data);
        }
    }

    public function edit($id = '') {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validate CSRF token
            if (!$this->validateCsrfToken($_POST['csrf_token'])) {
                die('Invalid CSRF token');
            }

            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'id' => $id,
                'username' => trim($_POST['username']),
                'password' => trim($_POST['password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'name' => trim($_POST['name']),
                'role' => trim($_POST['role']),
                'errors' => []
            ];

            // Validate data
            if (empty($data['username'])) {
                $data['errors']['username'] = 'Username is required';
            } else {
                $existingUser = $this->userModel->findUserByUsername($data['username']);
                if ($existingUser && $existingUser->id !== $id) {
                    $data['errors']['username'] = 'Username already exists';
                }
            }

            if (!empty($data['password'])) {
                if (strlen($data['password']) < 6) {
                    $data['errors']['password'] = 'Password must be at least 6 characters';
                }
                if ($data['password'] !== $data['confirm_password']) {
                    $data['errors']['confirm_password'] = 'Passwords do not match';
                }
            }

            if (empty($data['name'])) {
                $data['errors']['name'] = 'Name is required';
            }

            if (empty($data['role']) || !in_array($data['role'], ['admin', 'cashier'])) {
                $data['errors']['role'] = 'Invalid role selected';
            }

            // If no errors, update user
            if (empty($data['errors'])) {
                // Hash password if provided
                if (!empty($data['password'])) {
                    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                }

                if ($this->userModel->updateUser($id, $data)) {
                    $this->logAudit('user_update', 'Updated user: ' . $data['username']);
                    $_SESSION['flash_message'] = 'User updated successfully';
                    $_SESSION['flash_type'] = 'success';
                    header('location: ' . BASE_URL . 'users');
                    exit;
                } else {
                    $_SESSION['flash_message'] = 'Something went wrong';
                    $_SESSION['flash_type'] = 'error';
                }
            }

            // Load view with errors
            $data['csrf_token'] = $this->generateCsrfToken();
            $this->view('users/edit', $data);
        } else {
            // Get user by ID
            $user = $this->userModel->findById($id);
            if (!$user) {
                $_SESSION['flash_message'] = 'User not found';
                $_SESSION['flash_type'] = 'error';
                header('location: ' . BASE_URL . 'users');
                exit;
            }

            $data = [
                'title' => 'Edit User',
                'id' => $user->id,
                'username' => $user->username,
                'password' => '',
                'confirm_password' => '',
                'name' => $user->name,
                'role' => $user->role,
                'errors' => [],
                'csrf_token' => $this->generateCsrfToken()
            ];

            $this->view('users/edit', $data);
        }
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validate CSRF token
            if (!$this->validateCsrfToken($_POST['csrf_token'])) {
                die('Invalid CSRF token');
            }

            $id = $_POST['id'];

            // Prevent deleting own account
            if ($id === $_SESSION['user_id']) {
                $_SESSION['flash_message'] = 'Cannot delete your own account';
                $_SESSION['flash_type'] = 'error';
                header('location: ' . BASE_URL . 'users');
                exit;
            }

            $user = $this->userModel->findById($id);

            if ($user && $this->userModel->deleteUser($id)) {
                $this->logAudit('user_delete', 'Deleted user: ' . $user->username);
                $_SESSION['flash_message'] = 'User deleted successfully';
                $_SESSION['flash_type'] = 'success';
            } else {
                $_SESSION['flash_message'] = 'Something went wrong';
                $_SESSION['flash_type'] = 'error';
            }
        }

        header('location: ' . BASE_URL . 'users');
        exit;
    }
}
