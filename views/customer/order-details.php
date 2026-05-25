<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<?php
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo "<div class='text-center p-8 text-gray-500'>Please login to view order details.</div>";
    require_once __DIR__ . '/../../includes/footer.php';
    exit;
}

$order_id = $_GET['id'] ?? null;
if (!$order_id) {
    echo "<div class='text-center p-8 text-gray-500'>Invalid order.</div>";
    require_once __DIR__ . '/../../includes/footer.php';
    exit;
}

$pdo = getDB();
$stmt = $pdo->prepare("
    SELECT o.*, ds.start_time, ds.end_time 
    FROM orders o 
    LEFT JOIN delivery_slots ds ON o.delivery_slot_id = ds.id 
    WHERE o.id = ? AND o.user_id = ?
");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch();

if (!$order) {
    echo "<div class='text-center p-8 text-gray-500'>Order not found.</div>";
    require_once __DIR__ . '/../../includes/footer.php';
    exit;
}

// Fetch items
$stmtItems = $pdo->prepare("
    SELECT oi.*, p.name as product_name, p.image as product_image 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");
$stmtItems->execute([$order_id]);
$items = $stmtItems->fetchAll();

$statusColor = 'text-orange-500 bg-orange-50';
if ($order['status'] === 'delivered') $statusColor = 'text-green-600 bg-green-50';
if ($order['status'] === 'cancelled') $statusColor = 'text-red-500 bg-red-50';
?>

<div class="px-4 py-4 bg-white border-b sticky top-[56px] z-30">
    <div class="flex items-center gap-3">
        <a href="<?= BASE_URL ?>orders" class="text-gray-600"><i class="fa-solid fa-arrow-left"></i></a>
        <h2 class="text-lg font-bold text-gray-800">Order #<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></h2>
    </div>
</div>

<div class="p-4 space-y-4 pb-24">
    <!-- Status & Info Card -->
    <div class="premium-card p-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-gray-800">Order Status</h3>
            <span class="text-xs font-semibold px-3 py-1 rounded-full uppercase <?= $statusColor ?>">
                <?= htmlspecialchars($order['status']) ?>
            </span>
        </div>
        <div class="space-y-2 text-sm text-gray-600">
            <div class="flex items-start gap-2">
                <i class="fa-regular fa-calendar mt-1 w-4 text-center"></i>
                <div>
                    <p class="font-semibold text-gray-800">Date Ordered</p>
                    <p><?= date('M d, Y h:i A', strtotime($order['created_at'])) ?></p>
                </div>
            </div>
            <?php if($order['start_time']): ?>
            <div class="flex items-start gap-2">
                <i class="fa-regular fa-clock mt-1 w-4 text-center"></i>
                <div>
                    <p class="font-semibold text-gray-800">Delivery Slot</p>
                    <p><?= date('h:i A', strtotime($order['start_time'])) ?> - <?= date('h:i A', strtotime($order['end_time'])) ?></p>
                </div>
            </div>
            <?php endif; ?>
            <div class="flex items-start gap-2">
                <i class="fa-solid fa-location-dot mt-1 w-4 text-center"></i>
                <div>
                    <p class="font-semibold text-gray-800">Delivery Address</p>
                    <p><?= htmlspecialchars($order['delivery_address']) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Items List -->
    <div class="premium-card p-4">
        <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Order Items</h3>
        <div class="space-y-4">
            <?php foreach ($items as $item): ?>
            <div class="flex items-center gap-3">
                <div class="w-16 h-16 rounded-xl bg-gray-100 flex-shrink-0 flex items-center justify-center overflow-hidden">
                    <?php if($item['product_image']): ?>
                        <img src="<?= BASE_URL ?>images/products/<?= htmlspecialchars($item['product_image']) ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <i class="fa-solid fa-box text-gray-400 text-2xl"></i>
                    <?php endif; ?>
                </div>
                <div class="flex-1">
                    <p class="font-bold text-gray-800 text-sm"><?= htmlspecialchars($item['product_name']) ?></p>
                    <p class="text-xs text-gray-500 mt-1">Qty: <?= $item['quantity'] ?> × ৳<?= number_format($item['price_at_time'], 2) ?></p>
                </div>
                <div class="font-bold text-gray-800">
                    ৳<?= number_format($item['quantity'] * $item['price_at_time'], 2) ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Summary -->
    <div class="premium-card p-4 space-y-3">
        <div class="flex justify-between text-gray-600 text-sm">
            <span>Subtotal</span>
            <span>৳<?= number_format($order['subtotal'], 2) ?></span>
        </div>
        <div class="flex justify-between text-gray-600 text-sm">
            <span>Delivery Fee</span>
            <span>৳<?= number_format($order['delivery_fee'], 2) ?></span>
        </div>
        <div class="pt-3 border-t border-dashed flex justify-between font-bold text-lg text-gray-800">
            <span>Grand Total</span>
            <span class="text-green-600">৳<?= number_format($order['grand_total'], 2) ?></span>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
