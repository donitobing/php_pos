<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800"><?php echo $data['title']; ?></h1>
            <a href="<?php echo BASE_URL; ?>users" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                Back to List
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="<?php echo BASE_URL; ?>users/add" method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>">

                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Username *</label>
                    <input type="text" id="username" name="username" value="<?php echo $data['username']; ?>" required
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <?php if (isset($data['errors']['username'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo $data['errors']['username']; ?></p>
                    <?php endif; ?>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password *</label>
                    <input type="password" id="password" name="password" required minlength="6"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <?php if (isset($data['errors']['password'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo $data['errors']['password']; ?></p>
                    <?php endif; ?>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password *</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="6"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <?php if (isset($data['errors']['confirm_password'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo $data['errors']['confirm_password']; ?></p>
                    <?php endif; ?>
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                    <input type="text" id="name" name="name" value="<?php echo $data['name']; ?>" required
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <?php if (isset($data['errors']['name'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo $data['errors']['name']; ?></p>
                    <?php endif; ?>
                </div>

                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">Role *</label>
                    <select id="role" name="role" required
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-md">
                        <option value="">Select Role</option>
                        <option value="admin" <?php echo $data['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                        <option value="cashier" <?php echo $data['role'] === 'cashier' ? 'selected' : ''; ?>>Cashier</option>
                    </select>
                    <?php if (isset($data['errors']['role'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo $data['errors']['role']; ?></p>
                    <?php endif; ?>
                </div>

                <div class="flex justify-end">
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg">
                        Save User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
