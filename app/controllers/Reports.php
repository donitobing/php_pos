<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Reports extends Controller {
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
        $startDate = isset($_GET['start_date']) ? trim($_GET['start_date']) : date('Y-m-01');
        $endDate = isset($_GET['end_date']) ? trim($_GET['end_date']) : date('Y-m-d');

        $data = [
            'title' => 'Sales Report',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'summary' => $this->orderModel->getSummaryReport($startDate, $endDate),
            'topProducts' => $this->orderModel->getTopProducts($startDate, $endDate),
            'orders' => $this->orderModel->getReportData($startDate, $endDate)
        ];

        $this->view('reports/index', $data);
    }

    public function export() {
        $startDate = isset($_GET['start_date']) ? trim($_GET['start_date']) : date('Y-m-01');
        $endDate = isset($_GET['end_date']) ? trim($_GET['end_date']) : date('Y-m-d');

        $orders = $this->orderModel->getReportData($startDate, $endDate);
        $summary = $this->orderModel->getSummaryReport($startDate, $endDate);

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        
        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('POS System')
            ->setLastModifiedBy('POS System')
            ->setTitle('Sales Report')
            ->setSubject('Sales Report ' . $startDate . ' to ' . $endDate)
            ->setDescription('Sales report generated by POS System');

        // Summary Sheet
        $summarySheet = $spreadsheet->getActiveSheet();
        $summarySheet->setTitle('Summary');

        // Add summary data
        $summarySheet->setCellValue('A1', 'Sales Report Summary');
        $summarySheet->setCellValue('A2', 'Period: ' . date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)));
        
        $summarySheet->setCellValue('A4', 'Total Orders');
        $summarySheet->setCellValue('B4', $summary->total_orders);
        
        $summarySheet->setCellValue('A5', 'Total Customers');
        $summarySheet->setCellValue('B5', $summary->total_customers);
        
        $summarySheet->setCellValue('A6', 'Total Sales');
        $summarySheet->setCellValue('B6', $summary->total_sales);
        
        $summarySheet->setCellValue('A7', 'Total Tax');
        $summarySheet->setCellValue('B7', $summary->total_tax);
        
        $summarySheet->setCellValue('A8', 'Total Revenue');
        $summarySheet->setCellValue('B8', $summary->total_revenue);
        
        $summarySheet->setCellValue('A9', 'Average Order Value');
        $summarySheet->setCellValue('B9', $summary->average_order_value);

        // Format currency
        $summarySheet->getStyle('B6:B9')
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        // Create Details sheet
        $detailsSheet = $spreadsheet->createSheet();
        $detailsSheet->setTitle('Order Details');

        // Add headers
        $headers = ['Order Number', 'Date', 'Customer', 'Cashier', 'Items', 'Subtotal', 'Tax', 'Total'];
        $col = 'A';
        foreach ($headers as $header) {
            $detailsSheet->setCellValue($col . '1', $header);
            $detailsSheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        // Add order data
        $row = 2;
        foreach ($orders as $order) {
            $detailsSheet->setCellValue('A' . $row, $order->order_number);
            $detailsSheet->setCellValue('B' . $row, date('d/m/Y H:i', strtotime($order->created_at)));
            $detailsSheet->setCellValue('C' . $row, $order->customer_name);
            $detailsSheet->setCellValue('D' . $row, $order->cashier_name);
            $detailsSheet->setCellValue('E' . $row, $order->items);
            $detailsSheet->setCellValue('F' . $row, $order->subtotal);
            $detailsSheet->setCellValue('G' . $row, $order->tax_amount);
            $detailsSheet->setCellValue('H' . $row, $order->total_amount);
            $row++;
        }

        // Format currency columns
        $detailsSheet->getStyle('F2:H' . ($row-1))
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2E8F0']
            ]
        ];
        $detailsSheet->getStyle('A1:H1')->applyFromArray($headerStyle);
        $summarySheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 14]]);
        $summarySheet->getStyle('A4:A9')->applyFromArray(['font' => ['bold' => true]]);

        // Create Excel file
        $writer = new Xlsx($spreadsheet);
        
        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="sales_report_' . $startDate . '_' . $endDate . '.xlsx"');
        header('Cache-Control: max-age=0');

        // Save file to PHP output
        $writer->save('php://output');
        exit;
    }
}
