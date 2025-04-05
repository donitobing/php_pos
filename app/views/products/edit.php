<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800"><?php echo $data['title']; ?></h1>
            <a href="<?php echo BASE_URL; ?>products" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                Back to List
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="<?php echo BASE_URL . 'products/edit/' . $data['id']; ?>" method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>">
                
                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                    <select id="category_id" name="category_id" 
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-md">
                        <option value="">Select Category</option>
                        <?php foreach ($data['categories'] as $category): ?>
                        <option value="<?php echo $category->id; ?>" <?php echo $data['category_id'] === $category->id ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category->name); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Product Code -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700">Product Code *</label>
                    <input type="text" id="code" name="code" value="<?php echo $data['code']; ?>" required
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <?php if (isset($data['errors']['code'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo $data['errors']['code']; ?></p>
                    <?php endif; ?>
                </div>

                <!-- Product Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Product Name *</label>
                    <input type="text" id="name" name="name" value="<?php echo $data['name']; ?>" required
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <?php if (isset($data['errors']['name'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo $data['errors']['name']; ?></p>
                    <?php endif; ?>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"><?php echo $data['description']; ?></textarea>
                </div>

                <!-- Price -->
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700">Price *</label>
                    <input type="number" id="price" name="price" value="<?php echo $data['price']; ?>" required step="0.01" min="0"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <?php if (isset($data['errors']['price'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo $data['errors']['price']; ?></p>
                    <?php endif; ?>
                </div>

                <!-- Stock -->
                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700">Stock *</label>
                    <input type="number" id="stock" name="stock" value="<?php echo $data['stock']; ?>" required min="0"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <?php if (isset($data['errors']['stock'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo $data['errors']['stock']; ?></p>
                    <?php endif; ?>
                </div>

                <!-- Minimum Stock -->
                <div>
                    <label for="min_stock" class="block text-sm font-medium text-gray-700">Minimum Stock *</label>
                    <input type="number" id="min_stock" name="min_stock" value="<?php echo $data['min_stock']; ?>" required min="0"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <?php if (isset($data['errors']['min_stock'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo $data['errors']['min_stock']; ?></p>
                    <?php endif; ?>
                </div>

                <div class="flex justify-end">
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg">
                        Update Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
