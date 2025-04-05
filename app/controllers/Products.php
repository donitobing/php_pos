<?php
class Products extends Controller {
    private $productModel;
    private $categoryModel;

    public function __construct() {
        parent::__construct();
        
        if (!$this->isLoggedIn()) {
            header('location: ' . BASE_URL . 'auth/login');
            exit;
        }
        $this->productModel = $this->model('Product');
        $this->categoryModel = $this->model('Category');
    }

    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $limit = 10;

        $products = $this->productModel->getProducts($page, $limit, $search);
        $totalProducts = $this->productModel->getTotalProducts($search);
        $totalPages = ceil($totalProducts / $limit);

        $data = [
            'title' => 'Product Management',
            'products' => $products,
            'categories' => $this->categoryModel->getCategories(),
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'search' => $search,
            'csrf_token' => $this->generateCsrfToken()
        ];

        $this->view('products/index', $data);
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
                'category_id' => trim($_POST['category_id']),
                'code' => trim($_POST['code']),
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description']),
                'price' => floatval($_POST['price']),
                'stock' => intval($_POST['stock']),
                'min_stock' => intval($_POST['min_stock']),
                'errors' => []
            ];

            // Validate data
            if (empty($data['code'])) {
                $data['errors']['code'] = 'Product code is required';
            } elseif ($this->productModel->findByCode($data['code'])) {
                $data['errors']['code'] = 'Product code already exists';
            }

            if (empty($data['name'])) {
                $data['errors']['name'] = 'Product name is required';
            }

            if ($data['price'] < 0) {
                $data['errors']['price'] = 'Price cannot be negative';
            }

            if ($data['stock'] < 0) {
                $data['errors']['stock'] = 'Stock cannot be negative';
            }

            if ($data['min_stock'] < 0) {
                $data['errors']['min_stock'] = 'Minimum stock cannot be negative';
            }

            // If no errors, save product
            if (empty($data['errors'])) {
                if ($this->productModel->createProduct($data)) {
                    $this->logAudit('product_create', 'Created product: ' . $data['name']);
                    $_SESSION['flash_message'] = 'Product added successfully';
                    $_SESSION['flash_type'] = 'success';
                    header('location: ' . BASE_URL . 'products');
                    exit;
                } else {
                    $_SESSION['flash_message'] = 'Something went wrong';
                    $_SESSION['flash_type'] = 'error';
                }
            }

            // Load view with errors
            $data['categories'] = $this->categoryModel->getCategories();
            $data['csrf_token'] = $this->generateCsrfToken();
            $this->view('products/add', $data);
        } else {
            $data = [
                'title' => 'Add Product',
                'categories' => $this->categoryModel->getCategories(),
                'category_id' => '',
                'code' => '',
                'name' => '',
                'description' => '',
                'price' => '',
                'stock' => '',
                'min_stock' => '',
                'errors' => [],
                'csrf_token' => $this->generateCsrfToken()
            ];

            $this->view('products/add', $data);
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
                'category_id' => trim($_POST['category_id']),
                'code' => trim($_POST['code']),
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description']),
                'price' => floatval($_POST['price']),
                'stock' => intval($_POST['stock']),
                'min_stock' => intval($_POST['min_stock']),
                'errors' => []
            ];

            // Validate data
            if (empty($data['code'])) {
                $data['errors']['code'] = 'Product code is required';
            } else {
                $existingProduct = $this->productModel->findByCode($data['code']);
                if ($existingProduct && $existingProduct->id !== $id) {
                    $data['errors']['code'] = 'Product code already exists';
                }
            }

            if (empty($data['name'])) {
                $data['errors']['name'] = 'Product name is required';
            }

            if ($data['price'] < 0) {
                $data['errors']['price'] = 'Price cannot be negative';
            }

            if ($data['stock'] < 0) {
                $data['errors']['stock'] = 'Stock cannot be negative';
            }

            if ($data['min_stock'] < 0) {
                $data['errors']['min_stock'] = 'Minimum stock cannot be negative';
            }

            // If no errors, update product
            if (empty($data['errors'])) {
                if ($this->productModel->updateProduct($id, $data)) {
                    $this->logAudit('product_update', 'Updated product: ' . $data['name']);
                    $_SESSION['flash_message'] = 'Product updated successfully';
                    $_SESSION['flash_type'] = 'success';
                    header('location: ' . BASE_URL . 'products');
                    exit;
                } else {
                    $_SESSION['flash_message'] = 'Something went wrong';
                    $_SESSION['flash_type'] = 'error';
                }
            }

            // Load view with errors
            $data['categories'] = $this->categoryModel->getCategories();
            $data['csrf_token'] = $this->generateCsrfToken();
            $this->view('products/edit', $data);
        } else {
            // Get product by ID
            $product = $this->productModel->findById($id);
            if (!$product) {
                $_SESSION['flash_message'] = 'Product not found';
                $_SESSION['flash_type'] = 'error';
                header('location: ' . BASE_URL . 'products');
                exit;
            }

            $data = [
                'title' => 'Edit Product',
                'categories' => $this->categoryModel->getCategories(),
                'id' => $product->id,
                'category_id' => $product->category_id,
                'code' => $product->code,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'stock' => $product->stock,
                'min_stock' => $product->min_stock,
                'errors' => [],
                'csrf_token' => $this->generateCsrfToken()
            ];

            $this->view('products/edit', $data);
        }
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validate CSRF token
            if (!$this->validateCsrfToken($_POST['csrf_token'])) {
                die('Invalid CSRF token');
            }

            $id = $_POST['id'];
            $product = $this->productModel->findById($id);

            if ($product && $this->productModel->deleteProduct($id)) {
                $this->logAudit('product_delete', 'Deleted product: ' . $product->name);
                $_SESSION['flash_message'] = 'Product deleted successfully';
                $_SESSION['flash_type'] = 'success';
            } else {
                $_SESSION['flash_message'] = 'Something went wrong';
                $_SESSION['flash_type'] = 'error';
            }
        }

        header('location: ' . BASE_URL . 'products');
        exit;
    }
}
