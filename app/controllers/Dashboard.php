<?php
class Dashboard extends Controller {
    private $orderModel;
    private $productModel;

    public function __construct() {
        parent::__construct();
        
        if (!$this->isLoggedIn()) {
            header('location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $this->orderModel = $this->model('Order');
        $this->productModel = $this->model('Product');
    }

    public function index() {
        // Get today's date
        $today = date('Y-m-d');
        $firstDayOfMonth = date('Y-m-01');
        $lastDayOfMonth = date('Y-m-t');

        // Get summary data
        $todaySummary = $this->orderModel->getSummaryReport($today, $today);
        $monthSummary = $this->orderModel->getSummaryReport($firstDayOfMonth, $lastDayOfMonth);
        $topProducts = $this->orderModel->getTopProducts($firstDayOfMonth, $lastDayOfMonth, 5);
        $lowStockProducts = $this->productModel->getLowStockProducts();

        // Calculate growth
        $lastMonth = date('Y-m-d', strtotime('-1 month'));
        $lastMonthFirstDay = date('Y-m-01', strtotime('-1 month'));
        $lastMonthLastDay = date('Y-m-t', strtotime('-1 month'));
        $lastMonthSummary = $this->orderModel->getSummaryReport($lastMonthFirstDay, $lastMonthLastDay);

        $growth = 0;
        if ($lastMonthSummary->total_revenue > 0) {
            $growth = (($monthSummary->total_revenue - $lastMonthSummary->total_revenue) / $lastMonthSummary->total_revenue) * 100;
        }

        $data = [
            'title' => 'Dashboard',
            'user' => [
                'name' => $_SESSION['user_name'],
                'role' => $_SESSION['user_role']
            ],
            'today' => [
                'orders' => $todaySummary->total_orders,
                'revenue' => $todaySummary->total_revenue,
                'average_order' => $todaySummary->average_order_value
            ],
            'month' => [
                'orders' => $monthSummary->total_orders,
                'revenue' => $monthSummary->total_revenue,
                'growth' => $growth
            ],
            'topProducts' => $topProducts,
            'lowStockProducts' => $lowStockProducts
        ];

        $this->view('dashboard/index', $data);
    }
}
