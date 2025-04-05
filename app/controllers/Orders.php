<?php
class Orders extends Controller {
    private $orderModel;

    public function __construct() {
        parent::__construct();
        
        if (!$this->isLoggedIn()) {
            header('location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $this->orderModel = $this->model('Order');
    }

    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $startDate = isset($_GET['start_date']) ? trim($_GET['start_date']) : '';
        $endDate = isset($_GET['end_date']) ? trim($_GET['end_date']) : '';
        $limit = 10;

        $orders = $this->orderModel->getOrders($page, $limit, $search, $startDate, $endDate);
        $total = $this->orderModel->getTotalOrders($search, $startDate, $endDate);
        $totalPages = ceil($total / $limit);

        $data = [
            'title' => 'Orders',
            'orders' => $orders,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'search' => $search,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        $this->view('orders/index', $data);
    }

    public function viewOrder($id = '') {
        if (empty($id)) {
            header('location: ' . BASE_URL . 'orders');
            exit;
        }

        $order = $this->orderModel->getOrderById($id);
        if (!$order) {
            $_SESSION['flash_message'] = 'Order not found';
            $_SESSION['flash_type'] = 'error';
            header('location: ' . BASE_URL . 'orders');
            exit;
        }

        $data = [
            'title' => 'Order Details',
            'order' => $order
        ];

        $this->view('orders/view', $data);
    }

    public function print($id = '') {
        if (empty($id)) {
            header('location: ' . BASE_URL . 'orders');
            exit;
        }

        $order = $this->orderModel->getOrderById($id);
        if (!$order) {
            $_SESSION['flash_message'] = 'Order not found';
            $_SESSION['flash_type'] = 'error';
            header('location: ' . BASE_URL . 'orders');
            exit;
        }

        $settings = $this->model('Setting')->getSettings();
        
        $data = [
            'title' => 'Receipt',
            'order' => $order,
            'settings' => $settings
        ];

        $this->view('pos/receipt', $data, 'blank');
    }
}
