<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Order Details</h1>
            <div class="space-x-2">
                <a href="<?php echo BASE_URL; ?>orders" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    Back
                </a>
                <a href="<?php echo BASE_URL; ?>orders/print/<?php echo $data['order']->id; ?>" 
                   target="_blank"
                   class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                    Print Receipt
                </a>
            </div>
        </div>

        <!-- Order Info -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Order Information</h2>
                    <div class="space-y-2">
                        <div>
                            <span class="text-gray-600">Order Number:</span>
                            <span class="ml-2 font-medium"><?php echo htmlspecialchars($data['order']->order_number); ?></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Date:</span>
                            <span class="ml-2"><?php echo date('d/m/Y H:i', strtotime($data['order']->created_at)); ?></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Cashier:</span>
                            <span class="ml-2"><?php echo htmlspecialchars($data['order']->cashier_name); ?></span>
                        </div>
                    </div>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Customer Information</h2>
                    <div class="space-y-2">
                        <div>
                            <span class="text-gray-600">Name:</span>
                            <span class="ml-2"><?php echo htmlspecialchars($data['order']->customer_name ?: '-'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($data['order']->items as $item): ?>
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($item->product_name); ?></div>
                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($item->product_code); ?></div>
                        </td>
                        <td class="px-6 py-4 text-center text-sm text-gray-500">
                            <?php echo $item->quantity; ?>
                        </td>
                        <td class="px-6 py-4 text-right text-sm text-gray-500">
                            <?php echo number_format($item->price, 0, ',', '.'); ?>
                        </td>
                        <td class="px-6 py-4 text-right text-sm text-gray-900">
                            <?php echo number_format($item->subtotal, 0, ',', '.'); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-500">Subtotal</td>
                        <td class="px-6 py-3 text-right text-sm font-medium text-gray-900">
                            <?php echo number_format($data['order']->subtotal, 0, ',', '.'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-500">
                            Tax (<?php echo number_format($data['order']->tax_amount / $data['order']->subtotal * 100, 1); ?>%)
                        </td>
                        <td class="px-6 py-3 text-right text-sm font-medium text-gray-900">
                            <?php echo number_format($data['order']->tax_amount, 0, ',', '.'); ?>
                        </td>
                    </tr>
                    <tr class="bg-gray-100">
                        <td colspan="3" class="px-6 py-3 text-right text-sm font-bold text-gray-800">Total</td>
                        <td class="px-6 py-3 text-right text-sm font-bold text-gray-800">
                            <?php echo number_format($data['order']->total_amount, 0, ',', '.'); ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
