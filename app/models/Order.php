<?php
class Order extends BaseModel {
    protected $table = 'orders';

    public function __construct() {
        parent::__construct();
    }

    public function getOrders($page = 1, $limit = 10, $search = '', $startDate = '', $endDate = '') {
        $offset = ($page - 1) * $limit;
        $where = "1=1";
        $params = [];
        
        if (!empty($search)) {
            $where .= " AND (o.order_number LIKE :search OR o.customer_name LIKE :search)";
            $params[':search'] = "%{$search}%";
        }

        if (!empty($startDate)) {
            $where .= " AND DATE(o.created_at) >= :start_date";
            $params[':start_date'] = $startDate;
        }

        if (!empty($endDate)) {
            $where .= " AND DATE(o.created_at) <= :end_date";
            $params[':end_date'] = $endDate;
        }

        $sql = "SELECT o.*, u.name as cashier_name 
                FROM {$this->table} o 
                LEFT JOIN users u ON o.created_by = u.id 
                WHERE {$where} 
                ORDER BY o.created_at DESC 
                LIMIT :limit OFFSET :offset";

        $this->db->query($sql);
        
        // Bind search parameters
        foreach ($params as $param => $value) {
            $this->db->bind($param, $value);
        }
        
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);

        return $this->db->resultSet();
    }

    public function getReportData($startDate, $endDate) {
        $sql = "SELECT o.*, u.name as cashier_name,
                GROUP_CONCAT(CONCAT(p.name, ' (', oi.quantity, ')') SEPARATOR ', ') as items
                FROM orders o
                LEFT JOIN users u ON o.created_by = u.id
                LEFT JOIN order_items oi ON o.id = oi.order_id
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE DATE(o.created_at) BETWEEN :start_date AND :end_date
                GROUP BY o.id
                ORDER BY o.created_at ASC";

        $this->db->query($sql);
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':end_date', $endDate);

        return $this->db->resultSet();
    }

    public function getSummaryReport($startDate, $endDate) {
        $sql = "SELECT 
                COUNT(DISTINCT o.id) as total_orders,
                COUNT(DISTINCT o.customer_name) as total_customers,
                SUM(o.subtotal) as total_sales,
                SUM(o.tax_amount) as total_tax,
                SUM(o.total_amount) as total_revenue,
                AVG(o.total_amount) as average_order_value
                FROM orders o
                WHERE DATE(o.created_at) BETWEEN :start_date AND :end_date";

        $this->db->query($sql);
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':end_date', $endDate);

        return $this->db->single();
    }

    public function getTopProducts($startDate, $endDate, $limit = 5) {
        $sql = "SELECT 
                p.name as product_name,
                SUM(oi.quantity) as total_quantity,
                SUM(oi.subtotal) as total_sales
                FROM order_items oi
                JOIN orders o ON oi.order_id = o.id
                JOIN products p ON oi.product_id = p.id
                WHERE DATE(o.created_at) BETWEEN :start_date AND :end_date
                GROUP BY p.id, p.name
                ORDER BY total_quantity DESC
                LIMIT :limit";

        $this->db->query($sql);
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':end_date', $endDate);
        $this->db->bind(':limit', $limit);

        return $this->db->resultSet();
    }

    public function getTotalOrders($search = '', $startDate = '', $endDate = '') {
        $where = "1=1";
        $params = [];
        
        if (!empty($search)) {
            $where .= " AND (order_number LIKE :search OR customer_name LIKE :search)";
            $params[':search'] = "%{$search}%";
        }

        if (!empty($startDate)) {
            $where .= " AND DATE(created_at) >= :start_date";
            $params[':start_date'] = $startDate;
        }

        if (!empty($endDate)) {
            $where .= " AND DATE(created_at) <= :end_date";
            $params[':end_date'] = $endDate;
        }

        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE {$where}";
        $this->db->query($sql);

        // Bind search parameters
        foreach ($params as $param => $value) {
            $this->db->bind($param, $value);
        }

        $result = $this->db->single();
        return $result->total;
    }

    // Create new order
    public function createOrder($data) {
        $this->db->beginTransaction();

        try {
            // Generate order number (format: INV/YYYYMMDD/XXXX)
            $orderNumber = $this->generateOrderNumber();

            // Insert order
            $this->db->query('INSERT INTO orders (id, order_number, customer_name, subtotal, tax_amount, total_amount, created_by, created_at) 
                             VALUES (:id, :order_number, :customer_name, :subtotal, :tax_amount, :total_amount, :created_by, NOW())');

            $orderId = $this->generateUuid();
            $this->db->bind(':id', $orderId);
            $this->db->bind(':order_number', $orderNumber);
            $this->db->bind(':customer_name', $data['customer_name']);
            $this->db->bind(':subtotal', $data['subtotal']);
            $this->db->bind(':tax_amount', $data['tax_amount']);
            $this->db->bind(':total_amount', $data['total_amount']);
            $this->db->bind(':created_by', $_SESSION['user_id']);

            $this->db->execute();

            // Insert order items and update stock
            foreach ($data['items'] as $item) {
                $this->db->query('INSERT INTO order_items (id, order_id, product_id, quantity, price, subtotal) 
                                 VALUES (:id, :order_id, :product_id, :quantity, :price, :subtotal)');

                $this->db->bind(':id', $this->generateUuid());
                $this->db->bind(':order_id', $orderId);
                $this->db->bind(':product_id', $item['product_id']);
                $this->db->bind(':quantity', $item['quantity']);
                $this->db->bind(':price', $item['price']);
                $this->db->bind(':subtotal', $item['total']);

                $this->db->execute();

                // Update product stock
                $this->db->query('UPDATE products SET stock = stock - :quantity WHERE id = :product_id');
                $this->db->bind(':quantity', $item['quantity']);
                $this->db->bind(':product_id', $item['product_id']);
                $this->db->execute();

                // Record stock movement
                $this->db->query('INSERT INTO product_movements (id, product_id, type, quantity, notes, created_by) 
                                 VALUES (:id, :product_id, :type, :quantity, :notes, :created_by)');

                $this->db->bind(':id', $this->generateUuid());
                $this->db->bind(':product_id', $item['product_id']);
                $this->db->bind(':type', 'out');
                $this->db->bind(':quantity', $item['quantity']);
                $this->db->bind(':notes', 'Sale: ' . $orderNumber);
                $this->db->bind(':created_by', $_SESSION['user_id']);

                $this->db->execute();
            }

            $this->db->commit();
            return ['success' => true, 'order_id' => $orderId];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // Get order by ID with items
    public function getOrderById($id) {
        $this->db->query('SELECT o.*, u.name as cashier_name 
                         FROM orders o 
                         LEFT JOIN users u ON o.created_by = u.id 
                         WHERE o.id = :id');
        $this->db->bind(':id', $id);
        $order = $this->db->single();

        if ($order) {
            $this->db->query('SELECT 
                                oi.id,
                                oi.order_id,
                                oi.product_id,
                                oi.quantity,
                                oi.price,
                                oi.subtotal,
                                p.name as product_name,
                                p.code as product_code
                             FROM order_items oi 
                             LEFT JOIN products p ON oi.product_id = p.id 
                             WHERE oi.order_id = :order_id');
            $this->db->bind(':order_id', $id);
            $order->items = $this->db->resultSet();
        }

        return $order;
    }

    // Generate order number
    private function generateOrderNumber() {
        $date = date('Ymd');
        $this->db->query("SELECT COUNT(*) as count FROM orders WHERE DATE(created_at) = CURDATE()");
        $result = $this->db->single();
        $count = str_pad($result->count + 1, 4, '0', STR_PAD_LEFT);
        return "INV/{$date}/{$count}";
    }

    // Generate UUID
    private function generateUuid() {
        if (function_exists('random_bytes')) {
            $data = random_bytes(16);
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        }
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
