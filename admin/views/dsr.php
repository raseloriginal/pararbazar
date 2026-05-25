<?php
$pdo = getDB();
$stmt = $pdo->query("SELECT * FROM users WHERE role = 'dsr' ORDER BY id DESC");
$dsrs = $stmt->fetchAll();
?>
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-800">DSR Management</h2>
            <p class="text-sm text-gray-500 mt-1">Manage your Delivery Staff Riders</p>
        </div>
        <button onclick="$('#addDSRModal').removeClass('hidden')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition flex items-center gap-2 shadow-sm">
            <i class="fa-solid fa-plus"></i> Add DSR
        </button>
    </div>
    
    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600">ID</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600">Name</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600">Phone (Login ID)</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600">Registered</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <?php foreach($dsrs as $d): ?>
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-600">#<?= $d['id'] ?></td>
                        <td class="px-4 py-3 font-semibold text-gray-800"><?= htmlspecialchars($d['name']) ?></td>
                        <td class="px-4 py-3 text-gray-600"><i class="fa-solid fa-phone text-xs mr-1 text-gray-400"></i> <?= htmlspecialchars($d['phone']) ?></td>
                        <td class="px-4 py-3 text-gray-500"><?= date('M d, Y', strtotime($d['created_at'])) ?></td>
                        <td class="px-4 py-3 text-right">
                            <button onclick="deleteDSR(<?= $d['id'] ?>)" class="text-red-600 hover:text-red-800 mx-1"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if(empty($dsrs)): ?>
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">No DSR staff found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div id="addDSRModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-lg text-gray-800">Add DSR Staff</h3>
            <button onclick="$('#addDSRModal').addClass('hidden')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="addDSRForm" class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Full Name</label>
                <input type="text" name="name" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 outline-none" required>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Phone Number</label>
                <input type="tel" name="phone" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 outline-none" placeholder="01XXXXXXXXX" required>
                <p class="text-xs text-gray-500 mt-1">This will be used as their login ID and PIN.</p>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="$('#addDSRModal').addClass('hidden')" class="px-4 py-2 text-gray-600 font-medium hover:bg-gray-50 rounded-lg transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition shadow-sm">Save DSR</button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#addDSRForm').submit(function(e) {
        e.preventDefault();
        $.post('<?= BASE_URL ?>admin/api', $(this).serialize() + '&action=add_dsr', function(res) {
            if(res.status === 'success') {
                window.location.reload();
            } else {
                alert(res.message);
            }
        });
    });
});

function deleteDSR(id) {
    if(confirm('Are you sure you want to delete this DSR?')) {
        $.post('<?= BASE_URL ?>admin/api', {action: 'delete_dsr', id: id}, function(res) {
            if(res.status === 'success') {
                window.location.reload();
            } else {
                alert(res.message);
            }
        });
    }
}
</script>
