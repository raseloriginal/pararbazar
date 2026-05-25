<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<?php
if (!isset($_SESSION['user_id'])) {
    echo "<div class='text-center p-8 mt-10'>
            <i class='fa-solid fa-user-lock text-4xl text-gray-300 mb-4'></i>
            <p class='text-gray-500'>Please login to view your profile.</p>
          </div>";
    require_once __DIR__ . '/../../includes/footer.php';
    exit;
}

$pdo = getDB();
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Fetch last 3 orders
$orderStmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC LIMIT 3");
$orderStmt->execute([$_SESSION['user_id']]);
$recentOrders = $orderStmt->fetchAll();
?>

<div class="px-4 py-6 bg-white border-b">
    <div class="flex items-center gap-4">
        <div class="w-20 h-20 bg-green-100 rounded-full flex justify-center items-center text-green-600 text-3xl font-bold shadow-sm">
            <i class="fa-solid fa-user"></i>
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-800"><?= htmlspecialchars($user['name'] ?? 'Customer') ?></h2>
            <p class="text-gray-500 text-sm mt-1"><i class="fa-solid fa-phone mr-1"></i> <?= htmlspecialchars($user['phone']) ?></p>
        </div>
    </div>
</div>

<div class="px-4 py-6 space-y-6">
    <!-- Saved Address -->
    <div>
        <h3 class="text-sm font-semibold text-gray-800 mb-3">Saved Address</h3>
        <div class="premium-card p-4 flex items-start gap-3">
            <i class="fa-solid fa-location-dot text-green-600 mt-1"></i>
            <p class="text-sm text-gray-600"><?= nl2br(htmlspecialchars($user['default_address'] ?? 'No address saved.')) ?></p>
        </div>
    </div>

    <!-- Recent Orders -->
    <div>
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-sm font-semibold text-gray-800">Recent Orders</h3>
            <a href="/pararbazar/orders" class="text-xs font-medium text-green-600">View All</a>
        </div>
        
        <div class="space-y-3">
            <?php if (empty($recentOrders)): ?>
                <div class="text-center p-4 text-gray-500 text-sm premium-card">No recent orders.</div>
            <?php else: ?>
                <?php foreach ($recentOrders as $order): 
                    $statusColor = 'text-orange-500 bg-orange-50';
                    if ($order['status'] === 'delivered') $statusColor = 'text-green-600 bg-green-50';
                    if ($order['status'] === 'cancelled') $statusColor = 'text-red-500 bg-red-50';
                ?>
                    <div class="premium-card p-3 flex justify-between items-center">
                        <div>
                            <span class="text-xs text-gray-500">Order #<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></span>
                            <p class="font-bold text-gray-800">৳<?= number_format($order['grand_total'], 2) ?></p>
                        </div>
                        <div class="flex flex-col items-end gap-1">
                            <span class="text-[10px] font-semibold px-2 py-1 rounded-md uppercase <?= $statusColor ?>">
                                <?= htmlspecialchars($order['status']) ?>
                            </span>
                            <span class="text-xs text-gray-500"><?= date('M d', strtotime($order['created_at'])) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Actions -->
    <div class="pt-4">
        <a href="/pararbazar/api/auth?action=logout" class="block w-full text-center premium-card py-3 text-red-500 font-bold hover:bg-red-50 transition-colors">
            Logout
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
