<?php
$pdo = getDB();
$dsr_id = $_SESSION['user_id'];

// Fetch slots that have orders assigned to this DSR
$stmt = $pdo->prepare("
    SELECT ds.*, 
           COUNT(o.id) as total_orders,
           SUM(o.grand_total) as total_value,
           SUM(CASE WHEN o.status = 'delivered' THEN 1 ELSE 0 END) as completed_orders
    FROM delivery_slots ds
    JOIN orders o ON o.slot_id = ds.id
    WHERE o.dsr_id = ?
    GROUP BY ds.id
    ORDER BY ds.start_time ASC
");
$stmt->execute([$dsr_id]);
$slots = $stmt->fetchAll();

$total_slots = count($slots);
$pending_orders = 0;
foreach($slots as $s) {
    $pending_orders += ($s['total_orders'] - $s['completed_orders']);
}
?>
<div class="bg-blue-600 text-white pb-16 pt-6 px-4 rounded-b-[2rem] shadow-md relative z-0">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Slot Dashboard</h1>
            <p class="text-blue-100 text-sm mt-1"><?= date('l, M d, Y') ?></p>
        </div>
        <button id="logoutBtn" class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center hover:bg-blue-700 transition">
            <i class="fa-solid fa-right-from-bracket"></i>
        </button>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-blue-500/50 rounded-2xl p-4 border border-blue-400/30">
            <div class="text-blue-100 text-xs font-semibold mb-1 uppercase tracking-wider">Total Slots</div>
            <div class="text-2xl font-bold"><?= $total_slots ?></div>
        </div>
        <div class="bg-blue-500/50 rounded-2xl p-4 border border-blue-400/30">
            <div class="text-blue-100 text-xs font-semibold mb-1 uppercase tracking-wider">Pending Orders</div>
            <div class="text-2xl font-bold"><?= $pending_orders ?></div>
        </div>
    </div>
</div>

<div class="px-4 -mt-8 relative z-10 space-y-4">
    <?php if(empty($slots)): ?>
        <div class="premium-card p-8 text-center text-slate-500">
            <i class="fa-solid fa-box-open text-4xl mb-3 text-slate-300"></i>
            <p>No orders assigned to you today.</p>
        </div>
    <?php endif; ?>

    <?php foreach($slots as $slot): 
        $is_completed = ($slot['total_orders'] > 0 && $slot['total_orders'] == $slot['completed_orders']);
    ?>
    <div class="premium-card p-4 border-l-4 <?= $is_completed ? 'border-green-500 opacity-75' : 'border-blue-500' ?>">
        <div class="flex justify-between items-start mb-3">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider mb-1 block <?= $is_completed ? 'text-green-600' : 'text-blue-600' ?>">
                    <?= $is_completed ? 'Completed' : 'Pending Slot' ?>
                </span>
                <h3 class="text-lg font-bold text-slate-800">
                    <?= date('h:i A', strtotime($slot['start_time'])) ?> - <?= date('h:i A', strtotime($slot['end_time'])) ?>
                </h3>
            </div>
            <?php if($is_completed): ?>
                <span class="bg-green-50 text-green-600 text-xs font-bold px-2 py-1 rounded-md">Done</span>
            <?php else: ?>
                <span class="bg-blue-50 text-blue-600 text-xs font-bold px-2 py-1 rounded-md">Pending</span>
            <?php endif; ?>
        </div>
        
        <div class="grid grid-cols-3 gap-2 <?= !$is_completed ? 'mb-4' : '' ?>">
            <div class="bg-slate-50 p-2 rounded-lg text-center">
                <div class="text-slate-400 text-[10px] uppercase font-bold">Orders</div>
                <div class="font-semibold text-slate-700"><?= $slot['total_orders'] ?></div>
            </div>
            <div class="bg-slate-50 p-2 rounded-lg text-center">
                <div class="text-slate-400 text-[10px] uppercase font-bold">Done</div>
                <div class="font-semibold text-slate-700"><?= $slot['completed_orders'] ?></div>
            </div>
            <div class="bg-slate-50 p-2 rounded-lg text-center">
                <div class="text-slate-400 text-[10px] uppercase font-bold">Value</div>
                <div class="font-semibold text-slate-700">৳<?= number_format($slot['total_value'], 0) ?></div>
            </div>
        </div>

        <?php if(!$is_completed): ?>
        <div class="flex gap-3">
            <a href="/pararbazar/dsr/collection?slot_id=<?= $slot['id'] ?>" class="flex-1 bg-slate-800 text-white text-center py-2.5 rounded-xl font-semibold text-sm hover:bg-slate-700 transition shadow-sm">
                <i class="fa-solid fa-box-open mr-1"></i> Collect
            </a>
            <a href="/pararbazar/dsr/delivery?slot_id=<?= $slot['id'] ?>" class="flex-1 bg-blue-600 text-white text-center py-2.5 rounded-xl font-semibold text-sm hover:bg-blue-700 transition shadow-sm">
                <i class="fa-solid fa-motorcycle mr-1"></i> Deliver
            </a>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>

<script>
$(document).ready(function() {
    $('#logoutBtn').click(function() {
        if(confirm('Are you sure you want to log out?')) {
            $.post('/pararbazar/dsr/api', { action: 'logout' }, function() {
                window.location.href = '/pararbazar/dsr/login';
            });
        }
    });
});
</script>
