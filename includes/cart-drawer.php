<!-- Floating Cart Button -->
<div id="floatingCart" class="fixed bottom-4 left-4 right-4 transform translate-y-full transition-transform duration-300 z-50">
    <div class="bg-green-600 rounded-xl shadow-lg p-3 text-white flex justify-between items-center cursor-pointer" onclick="toggleCartDrawer()">
        <div class="flex items-center gap-3">
            <div class="bg-green-700 p-2 rounded-lg">
                <i class="fa-solid fa-basket-shopping text-xl"></i>
            </div>
            <div>
                <p id="cartItemCount" class="font-semibold text-sm">0 items</p>
                <p class="text-xs text-green-100">Review your cart</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span id="cartTotalAmount" class="font-bold text-lg">৳0.00</span>
            <i class="fa-solid fa-chevron-right text-sm"></i>
        </div>
    </div>
</div>

<!-- Cart Drawer (Bottom Sheet) -->
<div id="cartDrawerOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden transition-opacity opacity-0" onclick="toggleCartDrawer()"></div>

<div id="cartDrawer" class="fixed bottom-0 left-0 right-0 bg-white rounded-t-2xl z-50 transform translate-y-full transition-transform duration-300 max-h-[90vh] flex flex-col">
    <!-- Handle for sliding -->
    <div class="w-full flex justify-center pt-3 pb-1" onclick="toggleCartDrawer()">
        <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
    </div>
    
    <div class="px-4 py-3 flex justify-between items-center border-b">
        <h2 class="text-xl font-bold text-gray-800">Your Cart</h2>
        <button onclick="toggleCartDrawer()" class="text-gray-400 hover:text-gray-600">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>
    </div>
    
    <!-- Cart Items Container -->
    <div id="cartItemsContainer" class="p-4 overflow-y-auto flex-grow space-y-4">
        <!-- Items will be injected here via AJAX -->
        <div class="text-center text-gray-500 py-10">Your cart is empty</div>
    </div>
    
    <!-- Checkout Area -->
    <div class="p-4 border-t bg-gray-50 rounded-t-xl mt-auto">
        <div class="flex justify-between mb-2 text-sm text-gray-600">
            <span>Subtotal</span>
            <span id="drawerSubtotal">৳0.00</span>
        </div>
        <div class="flex justify-between mb-4 text-sm text-gray-600">
            <span>Delivery Fee</span>
            <span id="drawerDelivery">৳30.00</span>
        </div>
        <div class="flex justify-between items-center mb-4">
            <span class="font-bold text-gray-800">Grand Total</span>
            <span id="drawerGrandTotal" class="font-bold text-xl text-green-600">৳0.00</span>
        </div>
        
        <a href="/pararbazar/checkout" class="block w-full bg-green-600 text-white text-center font-bold py-3 rounded-xl shadow-lg hover:bg-green-700 transition-colors">
            Proceed to Checkout
        </a>
    </div>
</div>

<script>
    function toggleCartDrawer() {
        const overlay = document.getElementById('cartDrawerOverlay');
        const drawer = document.getElementById('cartDrawer');
        
        if (drawer.classList.contains('translate-y-full')) {
            // Open
            overlay.classList.remove('hidden');
            // Small delay to allow display:block to apply before transition
            setTimeout(() => {
                overlay.classList.remove('opacity-0');
                drawer.classList.remove('translate-y-full');
            }, 10);
            loadCartItems();
        } else {
            // Close
            overlay.classList.add('opacity-0');
            drawer.classList.add('translate-y-full');
            setTimeout(() => {
                overlay.classList.add('hidden');
            }, 300);
        }
    }

    function loadCartItems() {
        $.get('/pararbazar/api/cart?action=get', function(response) {
            if(response.status === 'success') {
                const cart = response.data;
                const container = $('#cartItemsContainer');
                
                if (cart.total_items === 0) {
                    container.html('<div class="text-center text-gray-500 py-10"><i class="fa-solid fa-cart-shopping text-4xl mb-3 text-gray-300"></i><p>Your cart is empty</p></div>');
                    return;
                }
                
                let html = '';
                for(let id in cart.items) {
                    let item = cart.items[id];
                    let imgPath = item.image ? item.image : 'https://placehold.co/100x100?text=No+Image';
                    html += `
                        <div class="flex gap-4 items-center premium-card p-3" data-id="${id}">
                            <img src="${imgPath}" alt="${item.name}" class="w-16 h-16 object-cover rounded-lg">
                            <div class="flex-grow">
                                <h3 class="font-semibold text-gray-800">${item.name}</h3>
                                <p class="text-green-600 font-bold">৳${item.price}</p>
                            </div>
                            <div class="flex items-center gap-3 bg-gray-100 rounded-lg p-1">
                                <button class="drawer-qty-btn decrease w-8 h-8 flex items-center justify-center text-gray-600 rounded-md bg-white shadow-sm" data-id="${id}">
                                    <i class="fa-solid ${item.quantity === 1 ? 'fa-trash text-red-500' : 'fa-minus'}"></i>
                                </button>
                                <span class="font-semibold w-4 text-center">${item.quantity}</span>
                                <button class="drawer-qty-btn increase w-8 h-8 flex items-center justify-center text-gray-600 rounded-md bg-white shadow-sm" data-id="${id}">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    `;
                }
                container.html(html);
                
                // Update totals
                $('#drawerSubtotal').text('৳' + cart.total_amount);
                // Assuming fixed delivery fee of 30 for now
                let delivery = cart.total_amount > 0 ? 30 : 0;
                $('#drawerDelivery').text('৳' + delivery);
                $('#drawerGrandTotal').text('৳' + (parseFloat(cart.total_amount) + delivery));
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Handle drawer quantity buttons
        $(document).on('click', '.drawer-qty-btn.increase', function() {
            let id = $(this).data('id');
            let currentQty = parseInt($(this).siblings('span').text());
            updateCartFromDrawer(id, currentQty + 1);
        });

        $(document).on('click', '.drawer-qty-btn.decrease', function() {
            let id = $(this).data('id');
            let currentQty = parseInt($(this).siblings('span').text());
            updateCartFromDrawer(id, currentQty - 1);
        });
    });

    function updateCartFromDrawer(productId, quantity) {
        $.ajax({
            url: '/pararbazar/api/cart',
            type: 'POST',
            data: {
                action: 'update',
                product_id: productId,
                quantity: quantity
            },
            success: function(response) {
                // Update the floating cart summary too
                if(typeof window.updateFloatingCart === 'function') {
                    window.updateFloatingCart(response.data);
                }
                // Update the main page quantity controls to stay in sync
                let productCard = $('.product-card[data-id="'+productId+'"]');
                if(productCard.length > 0) {
                    if (quantity <= 0) {
                        productCard.find('.qty-controls').addClass('hidden').removeClass('flex');
                        productCard.find('.add-to-cart-btn').removeClass('hidden');
                    } else {
                        productCard.find('.qty-value').text(quantity);
                    }
                }
                // Reload drawer items
                loadCartItems();
            }
        });
    }
</script>
