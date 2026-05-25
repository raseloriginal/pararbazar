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
                        <a href="#" class="text-green-600 font-semibold text-xs">View Details</a>
                    </div>
                </div>
                <?php
            }
        }
    }
    ?>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
