<?php
$slot_id = (int)($_GET['slot_id'] ?? 0);
$dsr_id = $_SESSION['user_id'];

$pdo = getDB();

// Fetch slot details
$stmt = $pdo->prepare("SELECT * FROM delivery_slots WHERE id = ?");
$stmt->execute([$slot_id]);
$slot = $stmt->fetch();

// Fetch all items that need to be collected for this DSR in this slot
// Group by product to get total quantity required
$stmt = $pdo->prepare("
    SELECT p.id as product_id, p.name, p.image, c.name as category_name, SUM(oi.quantity) as total_qty
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    JOIN products p ON oi.product_id = p.id
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE o.slot_id = ? AND o.dsr_id = ? AND o.status != 'cancelled'
    GROUP BY p.id, c.id
    ORDER BY c.name ASC, p.name ASC
");
$stmt->execute([$slot_id, $dsr_id]);
$items = $stmt->fetchAll();

// Group items by category for UI
$grouped_items = [];
$total_distinct_items = 0;
$total_item_count = 0;

foreach($items as $item) {
    $cat = $item['category_name'] ?? 'Other';
    if(!isset($grouped_items[$cat])) {
        $grouped_items[$cat] = [];
    }
    $grouped_items[$cat][] = $item;
    $total_distinct_items++;
    $total_item_count += $item['total_qty'];
}
?>
<div class="bg-slate-900 text-white px-4 py-4 sticky top-0 z-30 flex items-center justify-between shadow-md">
    <div class="flex items-center gap-3">
        <a href="<?= BASE_URL ?>dsr/dashboard" class="text-slate-300 hover:text-white transition"><i class="fa-solid fa-arrow-left text-lg"></i></a>
        <div>
            <h2 class="text-lg font-bold leading-tight">Collection</h2>
            <p class="text-slate-400 text-xs">Slot <?= $slot ? date('h:i A', strtotime($slot['start_time'])) : '' ?></p>
        </div>
    </div>
    <div class="bg-blue-600 px-3 py-1 rounded-lg text-xs font-bold">
        <span id="collectedCount">0</span> / <?= $total_distinct_items ?>
    </div>
</div>

<div class="px-4 py-6 space-y-4 pb-24">
    <div class="bg-blue-50 border border-blue-100 p-3 rounded-xl flex gap-3 text-blue-800 text-sm mb-6">
        <i class="fa-solid fa-circle-info mt-0.5"></i>
        <p>Pick items from the inventory and mark them as collected. Tap the circle when done.</p>
    </div>

    <?php if(empty($items)): ?>
        <div class="text-center text-gray-500 py-10">No items to collect.</div>
    <?php endif; ?>

    <?php foreach($grouped_items as $category => $category_items): ?>
    <div>
        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2"><?= htmlspecialchars($category) ?></h3>
        <div class="space-y-2">
            <?php foreach($category_items as $item): ?>
            <!-- Item -->
            <div class="premium-card p-3 flex items-center gap-3">
                <div class="w-12 h-12 bg-slate-100 rounded-lg overflow-hidden flex-shrink-0 flex items-center justify-center">
                    <?php if($item['image']): ?>
                        <img src="<?= htmlspecialchars($item['image']) ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <i class="fa-solid fa-box text-gray-300 text-xl"></i>
                    <?php endif; ?>
                </div>
                <div class="flex-grow">
                    <h4 class="text-sm font-bold text-slate-800"><?= htmlspecialchars($item['name']) ?></h4>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-lg font-black text-slate-800">x <?= $item['total_qty'] ?></span>
                    <button class="collect-btn w-8 h-8 rounded-full border-2 border-slate-300 flex items-center justify-center text-transparent transition-colors">
                        <i class="fa-solid fa-check text-sm"></i>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Floating Action Button -->
<?php if(!empty($items)): ?>
<div class="fixed bottom-4 left-4 right-4 z-40">
    <button id="completeCollectionBtn" class="w-full bg-slate-300 text-slate-500 font-bold py-3.5 rounded-xl shadow-lg transition-colors" disabled>
        Complete Collection
    </button>
</div>
<?php endif; ?>

<script>
$(document).ready(function() {
    let totalItems = <?= $total_distinct_items ?>;
    let collectedCount = 0;

    $('.collect-btn').click(function() {
        $(this).toggleClass('bg-blue-600 border-blue-600 text-white');
        $(this).toggleClass('border-slate-300 text-transparent');
        
        if($(this).hasClass('bg-blue-600')) {
            $(this).closest('.premium-card').addClass('opacity-50');
            collectedCount++;
        } else {
            $(this).closest('.premium-card').removeClass('opacity-50');
            collectedCount--;
        }
        
        $('#collectedCount').text(collectedCount);
        
        if (collectedCount === totalItems && totalItems > 0) {
            $('#completeCollectionBtn').removeClass('bg-slate-300 text-slate-500').addClass('bg-blue-600 text-white').prop('disabled', false);
        } else {
            $('#completeCollectionBtn').addClass('bg-slate-300 text-slate-500').removeClass('bg-blue-600 text-white').prop('disabled', true);
        }
    });

    $('#completeCollectionBtn').click(function() {
        $(this).html('<i class="fa-solid fa-spinner fa-spin"></i> Processing...');
        setTimeout(() => {
            alert('Collection marked as completed! You can now start deliveries.');
            window.location.href = '<?= BASE_URL ?>dsr/dashboard';
        }, 800);
    });
});
</script>
