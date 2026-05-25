<?php
$pdo = getDB();
$stmt = $pdo->query("SELECT * FROM delivery_slots ORDER BY start_time ASC");
$slots = $stmt->fetchAll();
?>
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Delivery Slots</h2>
            <p class="text-sm text-gray-500 mt-1">Manage delivery windows and order capacity</p>
        </div>
        <button onclick="$('#addSlotModal').removeClass('hidden')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition flex items-center gap-2 shadow-sm">
            <i class="fa-solid fa-plus"></i> Add Slot
        </button>
    </div>
    
    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600">ID</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600">Time Window</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600">Max Orders</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <?php foreach($slots as $s): ?>
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-600">#<?= $s['id'] ?></td>
                        <td class="px-4 py-3 font-semibold text-gray-800">
                            <?= date('h:i A', strtotime($s['start_time'])) ?> - <?= date('h:i A', strtotime($s['end_time'])) ?>
                        </td>
                        <td class="px-4 py-3 text-gray-600"><?= $s['max_orders'] ?> orders</td>
                        <td class="px-4 py-3">
                            <?php if($s['status']): ?>
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-semibold">Active</span>
                            <?php else: ?>
                                <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-semibold">Disabled</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button onclick="deleteSlot(<?= $s['id'] ?>)" class="text-red-600 hover:text-red-800 mx-1"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if(empty($slots)): ?>
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">No delivery slots configured.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div id="addSlotModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-lg text-gray-800">Add Delivery Slot</h3>
            <button onclick="$('#addSlotModal').addClass('hidden')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="addSlotForm" class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Start Time</label>
                    <input type="time" name="start_time" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">End Time</label>
                    <input type="time" name="end_time" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 outline-none" required>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Max Orders (Capacity)</label>
                <input type="number" name="max_orders" value="50" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 outline-none" required>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="$('#addSlotModal').addClass('hidden')" class="px-4 py-2 text-gray-600 font-medium hover:bg-gray-50 rounded-lg transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition shadow-sm">Save Slot</button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#addSlotForm').submit(function(e) {
        e.preventDefault();
        $.post('<?= BASE_URL ?>admin/api', $(this).serialize() + '&action=add_slot', function(res) {
            if(res.status === 'success') {
                window.location.reload();
            } else {
                alert(res.message);
            }
        });
    });
});

function deleteSlot(id) {
    if(confirm('Are you sure you want to delete this delivery slot?')) {
        $.post('<?= BASE_URL ?>admin/api', {action: 'delete_slot', id: id}, function(res) {
            if(res.status === 'success') {
                window.location.reload();
            } else {
                alert(res.message);
            }
        });
    }
}
</script>
