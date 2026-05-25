<?php
$pdo = getDB();

// Fetch total sales (from delivered orders)
$salesStmt = $pdo->query("SELECT SUM(grand_total) as total FROM orders WHERE status = 'delivered'");
$totalSales = $salesStmt->fetch()['total'] ?? 0;

// Fetch active orders (pending or processing)
$activeStmt = $pdo->query("SELECT COUNT(id) as total FROM orders WHERE status IN ('pending', 'processing')");
$activeOrders = $activeStmt->fetch()['total'] ?? 0;

// Fetch total customers
$custStmt = $pdo->query("SELECT COUNT(id) as total FROM users WHERE role = 'customer'");
$totalCustomers = $custStmt->fetch()['total'] ?? 0;

// Fetch total products
$prodStmt = $pdo->query("SELECT COUNT(id) as total FROM products");
$totalProducts = $prodStmt->fetch()['total'] ?? 0;

// Fetch recent orders
$recentStmt = $pdo->query("
    SELECT o.id, o.grand_total, o.status, o.created_at, u.name 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.id DESC LIMIT 5
");
$recentOrders = $recentStmt->fetchAll();

// Fetch top selling products
$topStmt = $pdo->query("
    SELECT p.name, p.image, p.price, SUM(oi.quantity) as total_sold, SUM(oi.price * oi.quantity) as total_revenue
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    JOIN orders o ON oi.order_id = o.id
    WHERE o.status != 'cancelled'
    GROUP BY p.id
    ORDER BY total_sold DESC LIMIT 5
");
$topProducts = $topStmt->fetchAll();
?>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Stat Card 1 -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500 mb-1">Total Sales</p>
            <h3 class="text-2xl font-bold text-gray-800">৳<?= number_format($totalSales, 0) ?></h3>
        </div>
        <div class="w-12 h-12 bg-green-50 text-green-600 rounded-full flex items-center justify-center text-xl">
            <i class="fa-solid fa-bangladeshi-taka-sign"></i>
        </div>
    </div>
    
    <!-- Stat Card 2 -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500 mb-1">Active Orders</p>
            <h3 class="text-2xl font-bold text-gray-800"><?= number_format($activeOrders) ?></h3>
        </div>
        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-xl">
            <i class="fa-solid fa-cart-shopping"></i>
        </div>
    </div>
    
    <!-- Stat Card 3 -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500 mb-1">Total Customers</p>
            <h3 class="text-2xl font-bold text-gray-800"><?= number_format($totalCustomers) ?></h3>
        </div>
        <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-full flex items-center justify-center text-xl">
            <i class="fa-solid fa-users"></i>
        </div>
    </div>
    
    <!-- Stat Card 4 -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500 mb-1">Total Products</p>
            <h3 class="text-2xl font-bold text-gray-800"><?= number_format($totalProducts) ?></h3>
        </div>
        <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-full flex items-center justify-center text-xl">
            <i class="fa-solid fa-box-open"></i>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-bold text-gray-800 text-lg">Recent Orders</h3>
            <a href="<?= BASE_URL ?>admin/orders" class="text-sm text-green-600 font-medium hover:underline">View All</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="pb-3 text-sm font-semibold text-gray-600">Order ID</th>
                        <th class="pb-3 text-sm font-semibold text-gray-600">Customer</th>
                        <th class="pb-3 text-sm font-semibold text-gray-600">Amount</th>
                        <th class="pb-3 text-sm font-semibold text-gray-600">Status</th>
                        <th class="pb-3 text-sm font-semibold text-gray-600">Date</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <?php foreach($recentOrders as $ro): 
                        $bg = 'bg-gray-100 text-gray-700';
                        if($ro['status'] === 'pending') $bg = 'bg-orange-100 text-orange-700';
                        if($ro['status'] === 'processing') $bg = 'bg-blue-100 text-blue-700';
                        if($ro['status'] === 'delivered') $bg = 'bg-green-100 text-green-700';
                        if($ro['status'] === 'cancelled') $bg = 'bg-red-100 text-red-700';
                    ?>
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="py-4 font-bold text-gray-800">#<?= str_pad($ro['id'], 5, '0', STR_PAD_LEFT) ?></td>
                        <td class="py-4 font-semibold text-gray-600"><?= htmlspecialchars($ro['name']) ?></td>
                        <td class="py-4 font-bold text-gray-800">৳<?= number_format($ro['grand_total'], 2) ?></td>
                        <td class="py-4"><span class="<?= $bg ?> px-2 py-1 rounded text-xs font-bold uppercase"><?= $ro['status'] ?></span></td>
                        <td class="py-4 text-gray-500"><?= date('M d, Y', strtotime($ro['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($recentOrders)): ?>
                        <tr><td colspan="5" class="py-8 text-center text-gray-500">No recent orders.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-bold text-gray-800 text-lg mb-6">Top Selling Products</h3>
        <div class="space-y-4">
            <?php foreach($topProducts as $tp): ?>
            <div class="flex items-center justify-between border-b border-gray-50 pb-3 last:border-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center flex-shrink-0">
                        <?php if($tp['image']): ?>
                            <img src="<?= htmlspecialchars($tp['image']) ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <i class="fa-solid fa-box text-gray-400"></i>
                        <?php endif; ?>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-800"><?= htmlspecialchars($tp['name']) ?></p>
                        <p class="text-xs font-semibold text-gray-500"><?= $tp['total_sold'] ?> Sold</p>
                    </div>
                </div>
                <span class="font-bold text-green-600">৳<?= number_format($tp['total_revenue'], 0) ?></span>
            </div>
            <?php endforeach; ?>
            <?php if(empty($topProducts)): ?>
                <div class="text-center text-gray-500 py-4">No sales data yet.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
