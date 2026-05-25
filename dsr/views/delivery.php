<?php
$slot_id = (int)($_GET['slot_id'] ?? 0);
$dsr_id = $_SESSION['user_id'];

$pdo = getDB();

// Fetch slot details
$stmt = $pdo->prepare("SELECT * FROM delivery_slots WHERE id = ?");
$stmt->execute([$slot_id]);
$slot = $stmt->fetch();

// Fetch orders for this DSR in this slot
$stmt = $pdo->prepare("
    SELECT o.*, u.name as customer_name, u.phone as customer_phone, u.default_address 
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.slot_id = ? AND o.dsr_id = ?
    ORDER BY o.id ASC
");
$stmt->execute([$slot_id, $dsr_id]);
$orders = $stmt->fetchAll();
?>
<div class="bg-blue-600 text-white px-4 py-4 sticky top-0 z-30 shadow-md">
    <div class="flex items-center gap-3">
        <a href="<?= BASE_URL ?>dsr/dashboard" class="text-blue-200 hover:text-white transition"><i class="fa-solid fa-arrow-left text-lg"></i></a>
        <div>
            <h2 class="text-lg font-bold leading-tight">Deliveries</h2>
            <p class="text-blue-200 text-xs">
                <?= count($orders) ?> Orders in Slot <?= $slot ? date('h:i A', strtotime($slot['start_time'])) : '' ?>
            </p>
        </div>
    </div>
</div>

<div class="px-4 py-6 space-y-4 pb-12">
    <?php if(empty($orders)): ?>
        <div class="text-center text-gray-500 py-10">No orders to deliver in this slot.</div>
    <?php endif; ?>

    <?php foreach($orders as $o): 
        $is_delivered = $o['status'] === 'delivered';
        $is_cancelled = $o['status'] === 'cancelled';
        $done = $is_delivered || $is_cancelled;
    ?>
    <!-- Order Card -->
    <div class="premium-card overflow-hidden <?= $done ? 'opacity-70' : '' ?>" id="order_card_<?= $o['id'] ?>">
        <div class="p-4 border-b border-slate-100 flex justify-between items-start">
            <div class="flex gap-3">
                <div class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center text-slate-500 font-bold flex-shrink-0">
                    <?= strtoupper(substr($o['customer_name'], 0, 2)) ?>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800"><?= htmlspecialchars($o['customer_name']) ?></h3>
                    <p class="text-xs text-slate-500 mt-0.5"><i class="fa-solid fa-phone mr-1"></i> <a href="tel:<?= htmlspecialchars($o['customer_phone']) ?>" class="text-blue-600"><?= htmlspecialchars($o['customer_phone']) ?></a></p>
                    <p class="text-xs text-slate-500 mt-1"><i class="fa-solid fa-location-dot mr-1"></i> <?= htmlspecialchars($o['default_address'] ?? 'No address') ?></p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-xs text-slate-400">#<?= str_pad($o['id'], 5, '0', STR_PAD_LEFT) ?></p>
                <p class="font-bold text-blue-600 mt-1">৳<?= number_format($o['grand_total'], 2) ?></p>
            </div>
        </div>
        
        <?php if(!$done): ?>
        <div class="bg-slate-50 p-3 flex gap-2" id="order_actions_<?= $o['id'] ?>">
            <button class="flex-1 bg-green-500 text-white font-bold py-2 rounded-lg text-sm hover:bg-green-600 transition" onclick="updateOrderStatus(<?= $o['id'] ?>, 'delivered')">
                <i class="fa-solid fa-check mr-1"></i> Done
            </button>
            <button class="flex-1 bg-slate-200 text-slate-600 font-bold py-2 rounded-lg text-sm hover:bg-slate-300 transition" onclick="updateOrderStatus(<?= $o['id'] ?>, 'cancelled')">
                Cancel
            </button>
        </div>
        <?php else: ?>
        <div class="bg-slate-100 p-2 text-center text-sm font-bold <?= $is_delivered ? 'text-green-600' : 'text-red-500' ?>">
            <?= strtoupper($o['status']) ?>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>

<script>
function updateOrderStatus(id, status) {
    if(confirm('Are you sure you want to mark this order as ' + status + '?')) {
        $.post('<?= BASE_URL ?>dsr/api', {
            action: 'update_order_status',
            order_id: id,
            status: status
        }, function(res) {
            if(res.status === 'success') {
                window.location.reload();
            } else {
                alert(res.message);
            }
        });
    }
}
</script>
