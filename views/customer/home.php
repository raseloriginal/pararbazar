<?php 
require_once __DIR__ . '/../../includes/header.php'; 

$pdo = getDB();

// Fetch Categories
$catStmt = $pdo->query("SELECT * FROM categories WHERE status = 1 ORDER BY sort_order ASC, name ASC");
$categories = $catStmt->fetchAll();

// Fetch Products
$prodStmt = $pdo->query("SELECT * FROM products WHERE status = 1 ORDER BY id DESC");
$products = $prodStmt->fetchAll();
?>

<!-- Categories Wrapper -->
<div class="bg-white pb-3 pt-2">
    <!-- Horizontal Sliding Categories -->
    <div class="flex overflow-x-auto no-scrollbar px-4 gap-3 pb-2" id="categoriesContainer">
        <?php foreach($categories as $index => $cat): 
            // Cycle through some nice background colors for categories
            $colors = ['green', 'orange', 'blue', 'yellow', 'red', 'purple'];
            $color = $colors[$index % count($colors)];
        ?>
        <div class="flex flex-col items-center gap-1 min-w-[72px] cursor-pointer category-btn" data-category-id="<?= $cat['id'] ?>">
            <div class="w-14 h-14 rounded-2xl bg-<?= $color ?>-100 flex items-center justify-center text-<?= $color ?>-600 shadow-sm border border-<?= $color ?>-200">
                <i class="<?= htmlspecialchars($cat['icon'] ?: 'fa-solid fa-layer-group') ?> text-xl"></i>
            </div>
            <span class="text-[11px] font-medium text-center leading-tight truncate w-14"><?= htmlspecialchars($cat['name']) ?></span>
        </div>
        <?php endforeach; ?>
        
        <?php if(empty($categories)): ?>
            <div class="text-sm text-gray-500 py-2">No categories found</div>
        <?php endif; ?>
    </div>
</div>

<!-- Products Grid -->
<div class="px-4 py-6">
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3" id="productsContainer">
        <?php foreach($products as $p): ?>
        <div class="premium-card p-3 flex flex-col product-card" data-id="<?= $p['id'] ?>" data-category-id="<?= $p['category_id'] ?>">
            <div class="relative w-full aspect-square mb-2 bg-gray-50 rounded-lg overflow-hidden flex items-center justify-center">
                <?php if($p['image']): ?>
                    <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="w-full h-full object-cover product-image">
                <?php else: ?>
                    <i class="fa-solid fa-box text-gray-300 text-4xl"></i>
                <?php endif; ?>
            </div>
            <h3 class="text-sm font-semibold text-gray-800 line-clamp-2 mb-1 product-name"><?= htmlspecialchars($p['name']) ?></h3>
            <p class="text-xs text-gray-500 mb-2"><?= htmlspecialchars($p['description'] ?: '1 unit') ?></p>
            <div class="mt-auto flex justify-between items-center">
                <span class="font-bold text-gray-900 product-price" data-price="<?= $p['price'] ?>">৳<?= number_format($p['price'], 0) ?></span>
                
                <!-- Add Button -->
                <button class="add-to-cart-btn bg-green-100 text-green-600 w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xl hover:bg-green-600 hover:text-white transition-colors" data-id="<?= $p['id'] ?>">
                    +
                </button>
                
                <!-- Quantity Controls (Hidden initially) -->
                <div class="qty-controls hidden items-center gap-2 bg-green-50 rounded-lg p-1 border border-green-100">
                    <button class="qty-decrease w-6 h-6 flex items-center justify-center text-green-700 bg-white rounded shadow-sm" data-id="<?= $p['id'] ?>"><i class="fa-solid fa-minus text-[10px]"></i></button>
                    <span class="qty-value text-sm font-semibold text-green-800 w-4 text-center">1</span>
                    <button class="qty-increase w-6 h-6 flex items-center justify-center text-green-700 bg-white rounded shadow-sm" data-id="<?= $p['id'] ?>"><i class="fa-solid fa-plus text-[10px]"></i></button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        
        <?php if(empty($products)): ?>
            <div class="col-span-full text-center text-gray-500 py-10">No products available.</div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const catBtns = document.querySelectorAll('.category-btn');
        const prodCards = document.querySelectorAll('.product-card');

        // Allow 'All' filtering by clicking an active category to toggle it off
        let activeCatId = null;

        catBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const catId = this.getAttribute('data-category-id');
                
                if (activeCatId === catId) {
                    // Reset filter
                    activeCatId = null;
                    this.querySelector('div').classList.remove('ring-4', 'ring-opacity-50');
                    prodCards.forEach(card => card.style.display = 'flex');
                } else {
                    // Apply filter
                    activeCatId = catId;
                    
                    // Reset all rings
                    catBtns.forEach(b => b.querySelector('div').classList.remove('ring-4', 'ring-opacity-50', 'ring-' + b.querySelector('div').className.match(/bg-([a-z]+)-100/)[1] + '-500'));
                    
                    // Add ring to selected
                    const colorMatch = this.querySelector('div').className.match(/bg-([a-z]+)-100/);
                    if(colorMatch) {
                        this.querySelector('div').classList.add('ring-4', 'ring-opacity-50', 'ring-' + colorMatch[1] + '-500');
                    }

                    prodCards.forEach(card => {
                        if (card.getAttribute('data-category-id') === catId) {
                            card.style.display = 'flex';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                }
            });
        });
    });
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
