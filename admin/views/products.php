<?php
$pdo = getDB();
// Get Products
$stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC");
$products = $stmt->fetchAll();

// Get Categories for the Add Form
$catStmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $catStmt->fetchAll();
?>
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Products Management</h2>
            <p class="text-sm text-gray-500 mt-1">Manage your inventory and pricing</p>
        </div>
        <button onclick="$('#addProductModal').removeClass('hidden')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition flex items-center gap-2 shadow-sm">
            <i class="fa-solid fa-plus"></i> Add Product
        </button>
    </div>
    
    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600">Product</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600">Category</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600">Price</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600">Stock</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <?php foreach($products as $p): ?>
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="px-4 py-3 flex items-center gap-3">
                            <div class="w-10 h-10 rounded object-cover bg-gray-100 overflow-hidden flex items-center justify-center">
                                <?php if($p['image']): ?>
                                    <img src="/pararbazar/assets/uploads/<?= htmlspecialchars($p['image']) ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <i class="fa-solid fa-image text-gray-400"></i>
                                <?php endif; ?>
                            </div>
                            <div>
                                <span class="font-medium text-gray-800 block"><?= htmlspecialchars($p['name']) ?></span>
                                <span class="text-xs text-gray-500 block truncate w-32"><?= htmlspecialchars($p['description']) ?></span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600"><?= htmlspecialchars($p['category_name'] ?? 'Uncategorized') ?></td>
                        <td class="px-4 py-3 font-semibold text-gray-800">৳<?= $p['price'] ?></td>
                        <td class="px-4 py-3 text-gray-600"><?= $p['stock'] ?></td>
                        <td class="px-4 py-3 text-right">
                            <button onclick="deleteProduct(<?= $p['id'] ?>)" class="text-red-600 hover:text-red-800 mx-1"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if(empty($products)): ?>
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">No products found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div id="addProductModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 overflow-y-auto pt-10 pb-10">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md overflow-hidden relative">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center sticky top-0 bg-white z-10">
            <h3 class="font-bold text-lg text-gray-800">Add Product</h3>
            <button onclick="$('#addProductModal').addClass('hidden')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="addProductForm" class="p-6 space-y-4" enctype="multipart/form-data">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Product Name</label>
                <input type="text" name="name" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 outline-none" required>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Category</label>
                <select name="category_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 outline-none" required>
                    <option value="">Select a Category</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Price (৳)</label>
                    <input type="number" step="0.01" name="price" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Stock</label>
                    <input type="number" name="stock" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 outline-none" value="100" required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Description / Unit Size</label>
                <input type="text" name="description" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 outline-none" placeholder="e.g. 1 kg or 500g">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Product Image URL (Optional Mockup)</label>
                <input type="url" name="image_url" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 outline-none" placeholder="https://placehold.co/200x200">
                <p class="text-xs text-gray-400 mt-1">Use a URL for testing instead of file upload</p>
            </div>

            <div class="pt-4 flex justify-end gap-3 sticky bottom-0 bg-white">
                <button type="button" onclick="$('#addProductModal').addClass('hidden')" class="px-4 py-2 text-gray-600 font-medium hover:bg-gray-50 rounded-lg transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition shadow-sm">Save Product</button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#addProductForm').submit(function(e) {
        e.preventDefault();
        
        let formData = new FormData(this);
        formData.append('action', 'add_product');

        $.ajax({
            url: '/pararbazar/admin/api',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                if(res.status === 'success') {
                    window.location.reload();
                } else {
                    alert(res.message);
                }
            }
        });
    });
});

function deleteProduct(id) {
    if(confirm('Are you sure you want to delete this product?')) {
        $.post('/pararbazar/admin/api', {action: 'delete_product', id: id}, function(res) {
            if(res.status === 'success') {
                window.location.reload();
            } else {
                alert(res.message);
            }
        });
    }
}
</script>
