<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-6"><?php echo $data['title']; ?></h1>

        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="<?php echo BASE_URL; ?>settings" method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>">
                <input type="hidden" name="id" value="<?php echo $data['id']; ?>">

                <!-- Store Name -->
                <div>
                    <label for="store_name" class="block text-sm font-medium text-gray-700">Store Name *</label>
                    <input type="text" id="store_name" name="store_name" value="<?php echo $data['store_name']; ?>" required
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <?php if (isset($data['errors']['store_name'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo $data['errors']['store_name']; ?></p>
                    <?php endif; ?>
                </div>

                <!-- Store Address -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Store Address</label>
                    <textarea id="address" name="address" rows="3"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"><?php echo $data['address']; ?></textarea>
                </div>

                <!-- Store Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <input type="text" id="phone" name="phone" value="<?php echo $data['phone']; ?>"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Tax Percentage -->
                <div>
                    <label for="tax_percentage" class="block text-sm font-medium text-gray-700">Tax Percentage (%)</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input type="number" id="tax_percentage" name="tax_percentage" 
                               value="<?php echo $data['tax_percentage']; ?>"
                               min="0" max="100" step="0.01" required
                               class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">%</span>
                        </div>
                    </div>
                    <?php if (isset($data['errors']['tax_percentage'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo $data['errors']['tax_percentage']; ?></p>
                    <?php endif; ?>
                    <p class="mt-1 text-sm text-gray-500">Enter a value between 0 and 100</p>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
