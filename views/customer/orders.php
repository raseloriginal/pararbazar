<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<div class="px-4 py-4 bg-white border-b sticky top-[56px] z-30">
    <div class="flex items-center gap-3">
        <a href="<?= BASE_URL ?>home" class="text-gray-600"><i class="fa-solid fa-arrow-left"></i></a>
        <h2 class="text-lg font-bold text-gray-800">My Orders</h2>
    </div>
</div>

<div class="p-4 space-y-4">
    <?php
    $user_id = $_SESSION['user_id'] ?? null;
    if (!$user_id) {
        echo "<div class='text-center p-8 text-gray-500'>Please login to view orders.</div>";
    } else {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
        $stmt->execute([$user_id]);
        $orders = $stmt->fetchAll();

        if (empty($orders)) {
            echo "<div class='text-center p-8 text-gray-500'>You have no orders yet.</div>";
        } else {
            foreach ($orders as $order) {
                $statusColor = 'text-orange-500 bg-orange-50';
                if ($order['status'] === 'delivered') $statusColor = 'text-green-600 bg-green-50';
                if ($order['status'] === 'cancelled') $statusColor = 'text-red-500 bg-red-50';
                ?>
                <div class="premium-card p-4">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <span class="text-xs text-gray-500">Order ID: #<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></span>
                            <p class="font-bold text-gray-800 mt-1">৳<?= number_format($order['grand_total'], 2) ?></p>
                        </div>
                        <span class="text-xs font-semibold px-2 py-1 rounded-md uppercase <?= $statusColor ?>">
                            <?= htmlspecialchars($order['status']) ?>
                        </span>
                    </div>
                    <div class="text-sm text-gray-600 flex justify-between items-center">
                        <span><?= date('M d, Y', strtotime($order['created_at'])) ?></span>
                        <button onclick="viewOrderDetails(<?= $order['id'] ?>)" class="text-green-600 font-semibold text-xs">View Details</button>
                    </div>
                </div>
                <?php
            }
        }
    }
    ?>
</div>

<!-- Order Details Modal (Bottom Sheet style) -->
<div id="orderDetailsOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-[60] hidden transition-opacity opacity-0" onclick="closeOrderDetails()"></div>
<div id="orderDetailsModal" class="fixed bottom-0 left-0 right-0 bg-white rounded-t-2xl z-[60] transform translate-y-full transition-transform duration-300 max-h-[90vh] flex flex-col">
    <div class="w-full flex justify-center pt-3 pb-1 cursor-pointer" onclick="closeOrderDetails()">
        <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
    </div>
    <div class="px-4 py-3 flex justify-between items-center border-b">
        <h2 class="text-xl font-bold text-gray-800" id="modalOrderTitle">Order Details</h2>
        <button onclick="closeOrderDetails()" class="text-gray-400 hover:text-gray-600">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>
    </div>
    <div id="modalOrderContent" class="p-4 overflow-y-auto flex-grow space-y-4 pb-10">
        <!-- Content injected via JS -->
    </div>
</div>

<script>
function viewOrderDetails(orderId) {
    const overlay = document.getElementById('orderDetailsOverlay');
    const modal = document.getElementById('orderDetailsModal');
    
    $('#modalOrderTitle').text('Order #' + String(orderId).padStart(5, '0'));
    $('#modalOrderContent').html('<div class="text-center py-10"><i class="fa-solid fa-spinner fa-spin text-3xl text-green-600"></i></div>');
    
    // Open modal
    overlay.classList.remove('hidden');
    setTimeout(() => {
        overlay.classList.remove('opacity-0');
        modal.classList.remove('translate-y-full');
    }, 10);

    // Fetch data
    $.get('<?= BASE_URL ?>api/order_details?id=' + orderId, function(response) {
        if(response && response.status === 'success') {
            $('#modalOrderContent').html(response.html);
        } else {
            $('#modalOrderContent').html('<div class="text-center text-red-500 py-10">' + (response ? response.message : 'Failed to load data.') + '</div>');
        }
    }).fail(function() {
        $('#modalOrderContent').html('<div class="text-center text-red-500 py-10">Error connecting to server.</div>');
    });
}

function closeOrderDetails() {
    const overlay = document.getElementById('orderDetailsOverlay');
    const modal = document.getElementById('orderDetailsModal');
    
    overlay.classList.add('opacity-0');
    modal.classList.add('translate-y-full');
    setTimeout(() => {
        overlay.classList.add('hidden');
    }, 300);
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
