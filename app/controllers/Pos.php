<?php
class Pos extends Controller {
    private $productModel;
    private $orderModel;
    private $settingModel;

    public function __construct() {
        parent::__construct();
        
        if (!$this->isLoggedIn()) {
            header('location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $this->productModel = $this->model('Product');
        $this->orderModel = $this->model('Order');
        $this->settingModel = $this->model('Setting');
    }

    public function index() {
        $products = $this->productModel->getActiveProducts();
        $settings = $this->settingModel->getSettings();
        
        $data = [
            'title' => 'Point of Sale',
            'products' => $products,
            'tax_percentage' => $settings->tax_percentage,
            'csrf_token' => $this->generateCsrfToken()
        ];

        $this->view('pos/index', $data);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit('Method not allowed');
        }

        // Validate CSRF token
        if (!$this->validateCsrfToken($_POST['csrf_token'])) {
            header('HTTP/1.1 403 Forbidden');
            exit('Invalid CSRF token');
        }

        // Validate and sanitize input
        $items = json_decode($_POST['items'], true);
        if (empty($items)) {
            header('HTTP/1.1 400 Bad Request');
            exit('No items in cart');
        }

        $data = [
            'customer_name' => trim($_POST['customer_name']),
            'subtotal' => floatval($_POST['subtotal']),
            'tax_amount' => floatval($_POST['tax_amount']),
            'total_amount' => floatval($_POST['total_amount']),
            'items' => $items
        ];

        // Create order
        $result = $this->orderModel->createOrder($data);
        
        if ($result['success']) {
            $this->logAudit('create_order', 'Created new order');
            
            // Return order ID for receipt printing
            // Return success with receipt URL
            $receiptUrl = BASE_URL . 'pos/receipt/' . $result['order_id'];
            echo json_encode([
                'success' => true,
                'message' => 'Order created successfully',
                'order_id' => $result['order_id'],
                'receipt_url' => $receiptUrl
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to create order: ' . $result['error']
            ]);
        }
    }

    public function receipt($id = '') {
        if (empty($id)) {
            header('location: ' . BASE_URL . 'pos');
            exit;
        }

        $order = $this->orderModel->getOrderById($id);
        if (!$order) {
            $_SESSION['flash_message'] = 'Order not found';
            $_SESSION['flash_type'] = 'error';
            header('location: ' . BASE_URL . 'pos');
            exit;
        }

        $settings = $this->settingModel->getSettings();
        
        $data = [
            'title' => 'Receipt',
            'order' => $order,
            'settings' => $settings
        ];

        // Use blank layout for receipt
        $this->view('pos/receipt', $data, 'blank');
    }

    public function search() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit('Method not allowed');
        }

        $search = isset($_GET['q']) ? trim($_GET['q']) : '';
        $products = $this->productModel->searchProducts($search);

        header('Content-Type: application/json');
        echo json_encode($products);
    }
}
