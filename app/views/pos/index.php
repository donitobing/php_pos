<div class="container-fluid px-4 py-4">
    <div class="flex flex-col md:flex-row gap-4">
        <!-- Products Section (Left) -->
        <div class="w-full md:w-8/12">
            <div class="bg-white rounded-lg shadow-md p-4">
                <!-- Search -->
                <div class="mb-4">
                    <input type="text" id="searchProduct" placeholder="Search products by name or code..." 
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Products Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4" id="productsGrid">
                    <?php foreach ($data['products'] as $product): ?>
                    <div class="product-item border rounded-lg p-2 cursor-pointer hover:bg-gray-50" 
                         data-id="<?php echo $product->id; ?>"
                         data-name="<?php echo htmlspecialchars($product->name); ?>"
                         data-price="<?php echo $product->price; ?>"
                         data-stock="<?php echo $product->stock; ?>">
                        <div class="aspect-w-1 aspect-h-1 bg-gray-200 rounded-lg overflow-hidden">
                            <?php if ($product->image_path): ?>
                                <img src="<?php echo BASE_URL . $product->image_path; ?>" alt="<?php echo htmlspecialchars($product->name); ?>" 
                                     class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="mt-2">
                            <h3 class="text-sm font-medium text-gray-900 truncate"><?php echo htmlspecialchars($product->name); ?></h3>
                            <p class="text-sm text-gray-500"><?php echo number_format($product->price, 0, ',', '.'); ?></p>
                            <p class="text-xs text-gray-400">Stock: <?php echo $product->stock; ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Cart Section (Right) -->
        <div class="w-full md:w-4/12">
            <div class="bg-white rounded-lg shadow-md p-4">
                <form id="orderForm" action="<?php echo BASE_URL; ?>pos/create" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>">
                    <input type="hidden" name="items" id="orderItems" value="">
                    
                    <!-- Customer Name -->
                    <div class="mb-4">
                        <label for="customerName" class="block text-sm font-medium text-gray-700">Customer Name</label>
                        <input type="text" id="customerName" name="customer_name" 
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Cart Items -->
                    <div class="border rounded-lg mb-4">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Qty</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                                    <th class="px-3 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="cartItems">
                                <!-- Cart items will be inserted here -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary -->
                    <div class="space-y-2">
                        <input type="hidden" name="subtotal" id="subtotalInput">
                        <input type="hidden" name="tax_amount" id="taxAmountInput">
                        <input type="hidden" name="total_amount" id="totalAmountInput">
                        
                        <div class="flex justify-between text-sm">
                            <span>Subtotal:</span>
                            <span id="subtotal">0</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span>Tax (<?php echo $data['tax_percentage']; ?>%):</span>
                            <span id="tax">0</span>
                        </div>
                        <div class="flex justify-between font-bold text-lg border-t pt-2">
                            <span>Total:</span>
                            <span id="total">0</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-4 space-y-2">
                        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg">
                            Process Payment
                        </button>
                        <button type="button" id="clearCart" class="w-full bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg">
                            Clear Cart
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Quantity Modal -->
<div id="quantityModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg p-6 w-80">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Enter Quantity</h3>
        <div class="space-y-4">
            <div>
                <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                <input type="number" id="quantity" min="1" value="1" 
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <p class="mt-1 text-sm text-gray-500">Available stock: <span id="availableStock">0</span></p>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" id="cancelQuantity" 
                        class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg">
                    Cancel
                </button>
                <button type="button" id="confirmQuantity" 
                        class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg">
                    Add to Cart
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let cart = [];
const taxPercentage = <?php echo $data['tax_percentage']; ?>;
let selectedProduct = null;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Product click event
    document.querySelectorAll('.product-item').forEach(item => {
        item.addEventListener('click', function() {
            selectedProduct = {
                id: this.dataset.id,
                name: this.dataset.name,
                price: parseFloat(this.dataset.price),
                stock: parseInt(this.dataset.stock)
            };
            showQuantityModal();
        });
    });

    // Search functionality
    document.getElementById('searchProduct').addEventListener('input', debounce(searchProducts, 300));

    // Form submission
    document.getElementById('orderForm').addEventListener('submit', function(e) {
        e.preventDefault();
        if (cart.length === 0) {
            alert('Please add items to cart first');
            return;
        }
        processOrder();
    });

    // Clear cart
    document.getElementById('clearCart').addEventListener('click', clearCart);

    // Quantity modal events
    document.getElementById('confirmQuantity').addEventListener('click', addToCart);
    document.getElementById('cancelQuantity').addEventListener('click', hideQuantityModal);
    document.getElementById('quantity').addEventListener('input', validateQuantity);
});

// Show quantity modal
function showQuantityModal() {
    const modal = document.getElementById('quantityModal');
    const quantityInput = document.getElementById('quantity');
    const availableStock = document.getElementById('availableStock');

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    quantityInput.value = 1;
    availableStock.textContent = selectedProduct.stock;
    quantityInput.max = selectedProduct.stock;
}

// Hide quantity modal
function hideQuantityModal() {
    const modal = document.getElementById('quantityModal');
    modal.classList.remove('flex');
    modal.classList.add('hidden');
    selectedProduct = null;
}

// Validate quantity
function validateQuantity() {
    const quantity = parseInt(this.value);
    const max = parseInt(this.max);
    if (quantity > max) {
        this.value = max;
    }
    if (quantity < 1) {
        this.value = 1;
    }
}

// Add to cart
function addToCart() {
    const quantity = parseInt(document.getElementById('quantity').value);
    if (quantity < 1 || quantity > selectedProduct.stock) {
        alert('Invalid quantity');
        return;
    }

    const existingItem = cart.find(item => item.product_id === selectedProduct.id);
    if (existingItem) {
        if (existingItem.quantity + quantity > selectedProduct.stock) {
            alert('Not enough stock');
            return;
        }
        existingItem.quantity += quantity;
        existingItem.total = existingItem.quantity * existingItem.price;
    } else {
        cart.push({
            product_id: selectedProduct.id,
            name: selectedProduct.name,
            price: selectedProduct.price,
            quantity: quantity,
            total: selectedProduct.price * quantity
        });
    }

    updateCartDisplay();
    hideQuantityModal();
}

// Update cart display
function updateCartDisplay() {
    const cartItems = document.getElementById('cartItems');
    cartItems.innerHTML = '';

    let subtotal = 0;
    cart.forEach((item, index) => {
        subtotal += item.total;
        cartItems.innerHTML += `
            <tr>
                <td class="px-3 py-2">
                    <div class="text-sm font-medium text-gray-900">${item.name}</div>
                    <div class="text-sm text-gray-500">${formatCurrency(item.price)}</div>
                </td>
                <td class="px-3 py-2 text-center text-sm">${item.quantity}</td>
                <td class="px-3 py-2 text-right text-sm">${formatCurrency(item.total)}</td>
                <td class="px-3 py-2 text-right">
                    <button type="button" onclick="removeFromCart(${index})" class="text-red-600 hover:text-red-900">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </td>
            </tr>
        `;
    });

    const tax = subtotal * (taxPercentage / 100);
    const total = subtotal + tax;

    document.getElementById('subtotal').textContent = formatCurrency(subtotal);
    document.getElementById('tax').textContent = formatCurrency(tax);
    document.getElementById('total').textContent = formatCurrency(total);

    document.getElementById('subtotalInput').value = subtotal;
    document.getElementById('taxAmountInput').value = tax;
    document.getElementById('totalAmountInput').value = total;
    document.getElementById('orderItems').value = JSON.stringify(cart);
}

// Remove from cart
function removeFromCart(index) {
    cart.splice(index, 1);
    updateCartDisplay();
}

// Clear cart
function clearCart() {
    if (confirm('Are you sure you want to clear the cart?')) {
        cart = [];
        updateCartDisplay();
    }
}

// Process order
function processOrder() {
    const form = document.getElementById('orderForm');
    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Open receipt in new tab
            window.open(data.receipt_url, '_blank');
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while processing the order');
    });
}

// Search products
function searchProducts() {
    const search = this.value.trim();
    fetch('<?php echo BASE_URL; ?>pos/search?q=' + encodeURIComponent(search))
        .then(response => response.json())
        .then(products => {
            const grid = document.getElementById('productsGrid');
            grid.innerHTML = products.map(product => `
                <div class="product-item border rounded-lg p-2 cursor-pointer hover:bg-gray-50" 
                     data-id="${product.id}"
                     data-name="${escapeHtml(product.name)}"
                     data-price="${product.price}"
                     data-stock="${product.stock}">
                    <div class="aspect-w-1 aspect-h-1 bg-gray-200 rounded-lg overflow-hidden">
                        ${product.image_path 
                            ? `<img src="<?php echo BASE_URL; ?>${product.image_path}" alt="${escapeHtml(product.name)}" class="w-full h-full object-cover">`
                            : `<div class="w-full h-full flex items-center justify-center bg-gray-100">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                               </div>`
                        }
                    </div>
                    <div class="mt-2">
                        <h3 class="text-sm font-medium text-gray-900 truncate">${escapeHtml(product.name)}</h3>
                        <p class="text-sm text-gray-500">${formatCurrency(product.price)}</p>
                        <p class="text-xs text-gray-400">Stock: ${product.stock}</p>
                    </div>
                </div>
            `).join('');

            // Reattach click events
            document.querySelectorAll('.product-item').forEach(item => {
                item.addEventListener('click', function() {
                    selectedProduct = {
                        id: this.dataset.id,
                        name: this.dataset.name,
                        price: parseFloat(this.dataset.price),
                        stock: parseInt(this.dataset.stock)
                    };
                    showQuantityModal();
                });
            });
        })
        .catch(error => console.error('Error:', error));
}

// Helper functions
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID').format(amount);
}

function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func.apply(this, args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
</script>
