<?php
$pdo = getDB();
$stmt = $pdo->query("SELECT * FROM categories ORDER BY sort_order ASC, id DESC");
$categories = $stmt->fetchAll();
?>
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Categories</h2>
            <p class="text-sm text-gray-500 mt-1">Manage product categories</p>
        </div>
        <button onclick="$('#addCategoryModal').removeClass('hidden')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition flex items-center gap-2 shadow-sm">
            <i class="fa-solid fa-plus"></i> Add Category
        </button>
    </div>
    
    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600">ID</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600">Name</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600">Icon</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <?php foreach($categories as $cat): ?>
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-600">#<?= $cat['id'] ?></td>
                        <td class="px-4 py-3 font-semibold text-gray-800"><?= htmlspecialchars($cat['name']) ?></td>
                        <td class="px-4 py-3 text-gray-600"><i class="<?= htmlspecialchars($cat['icon']) ?>"></i></td>
                        <td class="px-4 py-3">
                            <?php if($cat['status']): ?>
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-semibold">Active</span>
                            <?php else: ?>
                                <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-semibold">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button onclick="deleteCategory(<?= $cat['id'] ?>)" class="text-red-600 hover:text-red-800 mx-1"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($categories)): ?>
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">No categories found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div id="addCategoryModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-lg text-gray-800">Add Category</h3>
            <button onclick="$('#addCategoryModal').addClass('hidden')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="addCategoryForm" class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Name</label>
                <input type="text" name="name" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">FontAwesome Icon Class</label>
                <input type="text" name="icon" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 outline-none" placeholder="fa-solid fa-leaf">
                <p class="text-xs text-gray-500 mt-1">Example: fa-solid fa-leaf</p>
            </div>
            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="$('#addCategoryModal').addClass('hidden')" class="px-4 py-2 text-gray-600 font-medium hover:bg-gray-50 rounded-lg transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition shadow-sm">Save Category</button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#addCategoryForm').submit(function(e) {
        e.preventDefault();
        $.post('<?= BASE_URL ?>admin/api', $(this).serialize() + '&action=add_category', function(res) {
            if(res.status === 'success') {
                window.location.reload();
            } else {
                alert(res.message);
            }
        });
    });
});

function deleteCategory(id) {
    if(confirm('Are you sure you want to delete this category?')) {
        $.post('<?= BASE_URL ?>admin/api', {action: 'delete_category', id: id}, function(res) {
            if(res.status === 'success') {
                window.location.reload();
            } else {
                alert(res.message);
            }
        });
    }
}
</script>
