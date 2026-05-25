    </main>

    <!-- Includes -->
    <?php require_once __DIR__ . '/cart-drawer.php'; ?>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Search toggle
            $('#searchBtn').click(function() {
                $('#searchContainer').slideToggle(200);
                $('#searchInput').focus();
            });

            // Handle add to cart click
            $(document).on('click', '.add-to-cart-btn', function() {
                let productId = $(this).data('id');
                let productCard = $(this).closest('.product-card');
                let price = productCard.find('.product-price').data('price');
                let name = productCard.find('.product-name').text();
                let image = productCard.find('.product-image').attr('src');
                
                // Show quantity controls
                $(this).addClass('hidden');
                productCard.find('.qty-controls').removeClass('hidden').addClass('flex');
                
                updateCart(productId, 1, name, price, image);
            });

            // Handle qty increase
            $(document).on('click', '.qty-increase', function() {
                let productId = $(this).data('id');
                let qtyEl = $(this).siblings('.qty-value');
                let newQty = parseInt(qtyEl.text()) + 1;
                qtyEl.text(newQty);
                updateCart(productId, newQty);
            });

            // Handle qty decrease
            $(document).on('click', '.qty-decrease', function() {
                let productId = $(this).data('id');
                let qtyEl = $(this).siblings('.qty-value');
                let newQty = parseInt(qtyEl.text()) - 1;
                
                if (newQty <= 0) {
                    let productCard = $(this).closest('.product-card');
                    productCard.find('.qty-controls').addClass('hidden').removeClass('flex');
                    productCard.find('.add-to-cart-btn').removeClass('hidden');
                    updateCart(productId, 0); // 0 means remove
                } else {
                    qtyEl.text(newQty);
                    updateCart(productId, newQty);
                }
            });

            function updateCart(productId, quantity, name = null, price = null, image = null) {
                $.ajax({
                    url: '<?= BASE_URL ?>api/cart',
                    type: 'POST',
                    data: {
                        action: 'update',
                        product_id: productId,
                        quantity: quantity,
                        name: name,
                        price: price,
                        image: image
                    },
                    success: function(response) {
                        window.updateFloatingCart(response.data);
                    }
                });
            }

            window.updateFloatingCart = function(cartData) {
                if (cartData.total_items > 0) {
                    $('#floatingCart').removeClass('translate-y-full').addClass('translate-y-0');
                    $('#cartItemCount').text(cartData.total_items + ' item' + (cartData.total_items > 1 ? 's' : ''));
                    $('#cartTotalAmount').text('৳' + cartData.total_amount);
                } else {
                    $('#floatingCart').addClass('translate-y-full').removeClass('translate-y-0');
                    // Also auto-close drawer if it is open
                    const drawer = document.getElementById('cartDrawer');
                    if (drawer && !drawer.classList.contains('translate-y-full')) {
                        if (typeof toggleCartDrawer === 'function') {
                            toggleCartDrawer();
                        }
                    }
                }
            };
            
            // Initial cart fetch
            $.get('<?= BASE_URL ?>api/cart?action=get', function(response) {
                if(response.status === 'success') {
                    window.updateFloatingCart(response.data);
                    // Also update any quantity controls on the page to match current cart state
                    for(let id in response.data.items) {
                        let qty = response.data.items[id].quantity;
                        let productCard = $('.product-card[data-id="'+id+'"]');
                        if(productCard.length > 0) {
                            productCard.find('.add-to-cart-btn').addClass('hidden');
                            let qc = productCard.find('.qty-controls');
                            qc.removeClass('hidden').addClass('flex');
                            qc.find('.qty-value').text(qty);
                        }
                    }
                }
            });

            // Register Service Worker for PWA
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('<?= BASE_URL ?>service-worker.js')
                .then(function(registration) {
                    console.log('ServiceWorker registration successful');
                }).catch(function(err) {
                    console.log('ServiceWorker registration failed: ', err);
                });
            }
        });
    </script>
</body>
</html>
