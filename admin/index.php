<?php
// admin/index.php
$route = $_GET['route'] ?? 'dashboard';
$action = $route;

$is_admin = isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

if (!$is_admin && $action !== 'login') {
    redirect('/admin/login');
}
if ($is_admin && $action === 'login') {
    redirect('/admin/dashboard');
}

// Basic API handler inside admin
if ($action === 'api') {
    require_once __DIR__ . '/api.php';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Parar Bazar</title>
    <link rel="icon" href="<?= BASE_URL ?>images/icon.png" type="image/png">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

<?php if ($is_admin): ?>
    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col transition-all">
        <div class="h-16 flex items-center px-6 bg-slate-950 font-bold text-white text-lg tracking-wider">
            <img src="<?= BASE_URL ?>images/icon.png" alt="Logo" class="w-8 h-8 mr-2 rounded object-contain"> PararBazar
        </div>
        <nav class="flex-1 overflow-y-auto py-4 space-y-1">
            <a href="<?= BASE_URL ?>admin/dashboard" class="flex items-center px-6 py-3 hover:bg-slate-800 hover:text-white <?= $action === 'dashboard' ? 'bg-slate-800 text-white border-l-4 border-green-500' : 'border-l-4 border-transparent' ?>">
                <i class="fa-solid fa-chart-line w-6"></i> Dashboard
            </a>
            <a href="<?= BASE_URL ?>admin/orders" class="flex items-center px-6 py-3 hover:bg-slate-800 hover:text-white <?= $action === 'orders' ? 'bg-slate-800 text-white border-l-4 border-green-500' : 'border-l-4 border-transparent' ?>">
                <i class="fa-solid fa-cart-shopping w-6"></i> Orders
            </a>
            <a href="<?= BASE_URL ?>admin/products" class="flex items-center px-6 py-3 hover:bg-slate-800 hover:text-white <?= $action === 'products' ? 'bg-slate-800 text-white border-l-4 border-green-500' : 'border-l-4 border-transparent' ?>">
                <i class="fa-solid fa-box w-6"></i> Products
            </a>
            <a href="<?= BASE_URL ?>admin/categories" class="flex items-center px-6 py-3 hover:bg-slate-800 hover:text-white <?= $action === 'categories' ? 'bg-slate-800 text-white border-l-4 border-green-500' : 'border-l-4 border-transparent' ?>">
                <i class="fa-solid fa-tags w-6"></i> Categories
            </a>
            <a href="<?= BASE_URL ?>admin/dsr" class="flex items-center px-6 py-3 hover:bg-slate-800 hover:text-white <?= $action === 'dsr' ? 'bg-slate-800 text-white border-l-4 border-green-500' : 'border-l-4 border-transparent' ?>">
                <i class="fa-solid fa-motorcycle w-6"></i> DSR Staff
            </a>
            <a href="<?= BASE_URL ?>admin/slots" class="flex items-center px-6 py-3 hover:bg-slate-800 hover:text-white <?= $action === 'slots' ? 'bg-slate-800 text-white border-l-4 border-green-500' : 'border-l-4 border-transparent' ?>">
                <i class="fa-solid fa-clock w-6"></i> Delivery Slots
            </a>
        </nav>
        <div class="p-4 bg-slate-950">
            <button id="adminLogoutBtn" class="w-full flex items-center justify-center gap-2 bg-red-600/20 text-red-500 hover:bg-red-600 hover:text-white py-2 rounded transition">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <!-- Topbar -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 shadow-sm">
            <h1 class="text-xl font-semibold text-gray-800 capitalize"><?= htmlspecialchars($action) ?></h1>
            <div class="flex items-center gap-4">
                <div class="text-sm text-gray-500">Welcome, <strong>Admin</strong></div>
                <div class="w-9 h-9 bg-green-100 rounded-full flex items-center justify-center text-green-700 font-bold">A</div>
            </div>
        </header>
        
        <!-- Page Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-8">
            <?php
            $allowed_pages = ['dashboard', 'products', 'categories', 'orders', 'dsr', 'slots'];
            if (in_array($action, $allowed_pages)) {
                require_once __DIR__ . "/views/{$action}.php";
            } else {
                echo "<div class='bg-white p-8 rounded-lg shadow-sm text-center text-gray-500'>Page not found or under construction.</div>";
            }
            ?>
        </main>
    </div>
<?php else: ?>
    <?php require_once __DIR__ . "/views/login.php"; ?>
<?php endif; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#adminLogoutBtn').click(function() {
                if(confirm('Logout from admin?')) {
                    $.post('<?= BASE_URL ?>admin/api', { action: 'logout' }, function() {
                        window.location.href = '<?= BASE_URL ?>admin/login';
                    });
                }
            });
        });
    </script>
</body>
</html>
