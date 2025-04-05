<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Orders</h1>
        <a href="<?php echo BASE_URL; ?>pos" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
            New Order
        </a>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" action="<?php echo BASE_URL; ?>orders" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="<?php echo htmlspecialchars($data['search']); ?>" 
                       placeholder="Search order number or customer..." 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="w-48">
                <input type="date" name="start_date" value="<?php echo $data['startDate']; ?>" 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       title="Start Date">
            </div>
            <div class="w-48">
                <input type="date" name="end_date" value="<?php echo $data['endDate']; ?>" 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       title="End Date">
            </div>
            <div>
                <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    Filter
                </button>
            </div>
            <?php if (!empty($data['search']) || !empty($data['startDate']) || !empty($data['endDate'])): ?>
            <div>
                <a href="<?php echo BASE_URL; ?>orders" class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-600 px-4 py-2 rounded-lg">
                    Clear
                </a>
            </div>
            <?php endif; ?>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Number</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cashier</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($data['orders'])): ?>
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        No orders found
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($data['orders'] as $order): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        <?php echo htmlspecialchars($order->order_number); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php echo htmlspecialchars($order->customer_name ?: '-'); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php echo date('d/m/Y H:i', strtotime($order->created_at)); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php echo htmlspecialchars($order->cashier_name); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                        <?php echo number_format($order->total_amount, 0, ',', '.'); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="<?php echo BASE_URL; ?>orders/viewOrder/<?php echo $order->id; ?>" 
                           class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                        <a href="<?php echo BASE_URL; ?>orders/print/<?php echo $order->id; ?>" 
                           target="_blank"
                           class="text-green-600 hover:text-green-900">Print</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($data['totalPages'] > 1): ?>
    <div class="mt-6 flex justify-center">
        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
            <?php if ($data['currentPage'] > 1): ?>
            <a href="<?php echo BASE_URL; ?>orders?page=<?php echo ($data['currentPage'] - 1); ?>&search=<?php echo urlencode($data['search']); ?>&start_date=<?php echo $data['startDate']; ?>&end_date=<?php echo $data['endDate']; ?>" 
               class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                Previous
            </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $data['totalPages']; $i++): ?>
            <a href="<?php echo BASE_URL; ?>orders?page=<?php echo $i; ?>&search=<?php echo urlencode($data['search']); ?>&start_date=<?php echo $data['startDate']; ?>&end_date=<?php echo $data['endDate']; ?>" 
               class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium <?php echo $i === $data['currentPage'] ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-50'; ?>">
                <?php echo $i; ?>
            </a>
            <?php endfor; ?>

            <?php if ($data['currentPage'] < $data['totalPages']): ?>
            <a href="<?php echo BASE_URL; ?>orders?page=<?php echo ($data['currentPage'] + 1); ?>&search=<?php echo urlencode($data['search']); ?>&start_date=<?php echo $data['startDate']; ?>&end_date=<?php echo $data['endDate']; ?>" 
               class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                Next
            </a>
            <?php endif; ?>
        </nav>
    </div>
    <?php endif; ?>
</div>
