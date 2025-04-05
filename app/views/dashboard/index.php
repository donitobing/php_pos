<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Welcome, <?php echo htmlspecialchars($data['user']['name']); ?></h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Quick Stats -->
            <div class="bg-blue-100 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-blue-800">Today's Sales</h3>
                <p class="text-2xl font-bold text-blue-900">Rp <?php echo number_format($data['today']['revenue'], 0, ',', '.'); ?></p>
            </div>
            
            <div class="bg-green-100 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-green-800">Average Order</h3>
                <p class="text-2xl font-bold text-green-900">Rp <?php echo number_format($data['today']['average_order'], 0, ',', '.'); ?></p>
            </div>
            
            <div class="bg-yellow-100 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-yellow-800">Monthly Orders</h3>
                <p class="text-2xl font-bold text-yellow-900"><?php echo number_format($data['month']['orders']); ?></p>
            </div>
            
            <div class="bg-purple-100 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-purple-800">Monthly Growth</h3>
                <p class="text-2xl font-bold <?php echo $data['month']['growth'] >= 0 ? 'text-green-600' : 'text-red-600'; ?>">
                    <?php echo ($data['month']['growth'] >= 0 ? '+' : '') . number_format($data['month']['growth'], 1); ?>%
                </p>
            </div>
        </div>

        <div class="mt-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Quick Actions</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <a href="<?php echo BASE_URL; ?>pos" class="flex flex-col items-center p-4 bg-indigo-100 rounded-lg hover:bg-indigo-200">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <span class="mt-2 text-indigo-600 font-medium">New Sale</span>
                </a>
                
                <a href="<?php echo BASE_URL; ?>products" class="flex flex-col items-center p-4 bg-green-100 rounded-lg hover:bg-green-200">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span class="mt-2 text-green-600 font-medium">Products</span>
                </a>
                
                <a href="<?php echo BASE_URL; ?>reports" class="flex flex-col items-center p-4 bg-yellow-100 rounded-lg hover:bg-yellow-200">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="mt-2 text-yellow-600 font-medium">Reports</span>
                </a>
                
                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <a href="<?php echo BASE_URL; ?>users" class="flex flex-col items-center p-4 bg-purple-100 rounded-lg hover:bg-purple-200">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span class="mt-2 text-purple-600 font-medium">Users</span>
                </a>
                <?php endif; ?>
                
                <a href="<?php echo BASE_URL; ?>settings" class="flex flex-col items-center p-4 bg-gray-100 rounded-lg hover:bg-gray-200">
                    <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="mt-2 text-gray-600 font-medium">Settings</span>
                </a>
            </div>
        </div>
    </div>
</div>
