<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt</title>
    <style>
        @page {
            size: 80mm auto; /* Width: 80mm, Height: auto */
            margin: 0mm;
        }
        @media print {
            html, body {
                width: 80mm; /* Standard thermal receipt width */
                height: auto;
                margin: 0;
                padding: 0;
            }
        }
        body {
            font-family: 'Courier New', monospace;
            margin: 0;
            padding: 2mm;
            width: 76mm; /* 80mm - 2mm padding on each side */
            font-size: 10pt;
            line-height: 1.2;
            background: white;
        }
        /* Text alignments */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        /* Margins */
        .mb-1 { margin-bottom: 1mm; }
        .mb-2 { margin-bottom: 2mm; }

        /* Borders */
        .border-top { border-top: 1px dashed #000; padding-top: 1mm; }
        .border-bottom { border-bottom: 1px dashed #000; padding-bottom: 1mm; }

        /* Tables */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            font-size: 9pt;
        }
        th, td { 
            padding: 0.5mm 0; 
            vertical-align: top;
        }
        th { text-align: left; }
        .amount { text-align: right; }

        /* Store info */
        .store-info {
            font-size: 9pt;
            line-height: 1.1;
        }
        .store-name {
            font-size: 11pt;
            font-weight: bold;
        }

        /* Items */
        .item-name {
            font-size: 9pt;
        }
        .item-details {
            font-size: 8pt;
            color: #666;
        }

        /* Totals */
        .totals {
            font-size: 9pt;
        }
        .grand-total {
            font-size: 11pt;
            font-weight: bold;
        }

        /* Footer */
        .footer {
            font-size: 9pt;
            text-align: center;
            margin-top: 2mm;
        }

        /* Print button */
        .print-button {
            display: block;
            width: 100%;
            padding: 10px;
            background: #4CAF50;
            color: white;
            text-align: center;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 4mm;
        }
        @media print {
            .print-button { display: none; }
            @page {
                size: 80mm auto;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Store Info -->
    <div class="text-center mb-2 store-info">
        <div class="store-name"><?php echo htmlspecialchars($data['settings']->store_name); ?></div>
        <div><?php echo nl2br(htmlspecialchars($data['settings']->address)); ?></div>
        <div><?php echo htmlspecialchars($data['settings']->phone); ?></div>
    </div>

    <!-- Order Info -->
    <div class="mb-2">
        <div>No: <?php echo htmlspecialchars($data['order']->order_number); ?></div>
        <div>Date: <?php echo date('d/m/Y H:i', strtotime($data['order']->created_at)); ?></div>
        <div>Cashier: <?php echo htmlspecialchars($data['order']->cashier_name); ?></div>
        <?php if (!empty($data['order']->customer_name)): ?>
        <div>Customer: <?php echo htmlspecialchars($data['order']->customer_name); ?></div>
        <?php endif; ?>
    </div>

    <!-- Order Items -->
    <div class="border-top border-bottom">
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="amount">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['order']->items as $item): ?>
                <tr>
                    <td>
                        <div class="item-name"><?php echo htmlspecialchars($item->product_name); ?></div>
                        <div class="item-details"><?php echo $item->quantity; ?> x <?php echo number_format($item->price, 0, ',', '.'); ?></div>
                    </td>
                    <td class="amount"><?php echo number_format($item->subtotal, 0, ',', '.'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Order Summary -->
    <table class="mb-2 totals">
        <tr>
            <td>Subtotal</td>
            <td class="amount"><?php echo number_format($data['order']->subtotal, 0, ',', '.'); ?></td>
        </tr>
        <tr>
            <td>Tax (<?php echo $data['settings']->tax_percentage; ?>%)</td>
            <td class="amount"><?php echo number_format($data['order']->tax_amount, 0, ',', '.'); ?></td>
        </tr>
        <tr class="grand-total">
            <td>Total</td>
            <td class="amount"><?php echo number_format($data['order']->total_amount, 0, ',', '.'); ?></td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="footer">
        <div>Thank you for your purchase!</div>
        <div>Please come again</div>
    </div>

    <!-- Print Button -->
    <button class="print-button" onclick="window.print()">Print Receipt</button>

    <script>
        // Auto print on page load
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
