<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800"><?php echo $data['title']; ?></h1>
        <a href="<?php echo BASE_URL; ?>products/add" 
           class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
            Add New Product
        </a>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form action="<?php echo BASE_URL; ?>products" method="GET" class="flex gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="<?php echo $data['search']; ?>" 
                       placeholder="Search products..." 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" 
                    class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg">
                Search
            </button>
        </form>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($data['products'] as $product): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <?php echo htmlspecialchars($product->code); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <?php echo htmlspecialchars($product->name); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <?php echo htmlspecialchars($product->category_name ?? 'Uncategorized'); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <?php echo number_format($product->price, 2); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            <?php echo $product->stock <= $product->min_stock ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'; ?>">
                            <?php echo $product->stock; ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="<?php echo BASE_URL . 'products/edit/' . $product->id; ?>" 
                           class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                        <form action="<?php echo BASE_URL; ?>products/delete" method="POST" class="inline">
                            <input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>">
                            <input type="hidden" name="id" value="<?php echo $product->id; ?>">
                            <button type="submit" class="text-red-600 hover:text-red-900" 
                                    onclick="return confirm('Are you sure you want to delete this product?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($data['products'])): ?>
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        No products found
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($data['totalPages'] > 1): ?>
    <div class="flex justify-center mt-6">
        <div class="flex rounded-md">
            <?php if ($data['currentPage'] > 1): ?>
            <a href="<?php echo BASE_URL . 'products?page=' . ($data['currentPage'] - 1) . '&search=' . urlencode($data['search']); ?>" 
               class="px-3 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                Previous
            </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $data['totalPages']; $i++): ?>
            <a href="<?php echo BASE_URL . 'products?page=' . $i . '&search=' . urlencode($data['search']); ?>" 
               class="px-3 py-2 border border-gray-300 bg-white text-sm font-medium 
                      <?php echo $i === $data['currentPage'] ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-50'; ?>">
                <?php echo $i; ?>
            </a>
            <?php endfor; ?>

            <?php if ($data['currentPage'] < $data['totalPages']): ?>
            <a href="<?php echo BASE_URL . 'products?page=' . ($data['currentPage'] + 1) . '&search=' . urlencode($data['search']); ?>" 
               class="px-3 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                Next
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
