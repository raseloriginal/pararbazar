<?php
$pdo = getDB();
$stmt = $pdo->query("
    SELECT o.*, u.name as customer_name, u.phone as customer_phone, 
           dsr.name as dsr_name, 
           ds.start_time, ds.end_time
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    LEFT JOIN users dsr ON o.dsr_id = dsr.id
    LEFT JOIN delivery_slots ds ON o.slot_id = ds.id
    ORDER BY o.id DESC
");
$orders = $stmt->fetchAll();

// Get DSRs for assignment dropdown
$dsrStmt = $pdo->query("SELECT id, name FROM users WHERE role = 'dsr'");
$dsrs = $dsrStmt->fetchAll();
?>
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Order Tracking</h2>
            <p class="text-sm text-gray-500 mt-1">Manage and assign customer orders</p>
        </div>
    </div>
    
    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600">Order ID</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600">Customer</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600">Slot</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600">Total</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600">Assign DSR</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <?php foreach($orders as $o): ?>
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="px-4 py-4 font-bold text-gray-800">#<?= str_pad($o['id'], 5, '0', STR_PAD_LEFT) ?></td>
                        <td class="px-4 py-4">
                            <p class="font-semibold text-gray-800"><?= htmlspecialchars($o['customer_name']) ?></p>
                            <p class="text-xs text-gray-500"><?= htmlspecialchars($o['customer_phone']) ?></p>
                        </td>
                        <td class="px-4 py-4 text-gray-600 text-xs font-semibold">
                            <?php if($o['start_time']): ?>
                                <?= date('h:i A', strtotime($o['start_time'])) ?> - <?= date('h:i A', strtotime($o['end_time'])) ?>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-4 font-bold text-gray-800">৳<?= number_format($o['grand_total'], 2) ?></td>
                        <td class="px-4 py-4">
                            <?php 
                            $bg = 'bg-gray-100 text-gray-700';
                            if($o['status'] === 'pending') $bg = 'bg-orange-100 text-orange-700';
                            if($o['status'] === 'processing') $bg = 'bg-blue-100 text-blue-700';
                            if($o['status'] === 'delivered') $bg = 'bg-green-100 text-green-700';
                            if($o['status'] === 'cancelled') $bg = 'bg-red-100 text-red-700';
                            ?>
                            <span class="<?= $bg ?> px-2 py-1 rounded text-xs font-bold uppercase"><?= $o['status'] ?></span>
                        </td>
                        <td class="px-4 py-4">
                            <select onchange="assignDSR(<?= $o['id'] ?>, this.value)" class="border border-gray-300 rounded px-2 py-1 text-xs focus:ring-2 focus:ring-green-500 outline-none w-32">
                                <option value="">Unassigned</option>
                                <?php foreach($dsrs as $d): ?>
                                    <option value="<?= $d['id'] ?>" <?= $o['dsr_id'] == $d['id'] ? 'selected' : '' ?>><?= htmlspecialchars($d['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if(empty($orders)): ?>
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">No orders found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function assignDSR(orderId, dsrId) {
    $.post('<?= BASE_URL ?>admin/api', {
        action: 'assign_dsr',
        order_id: orderId,
        dsr_id: dsrId
    }, function(res) {
        if(res.status === 'success') {
            // Optional visual feedback
        } else {
            alert(res.message);
        }
    });
}
</script>
