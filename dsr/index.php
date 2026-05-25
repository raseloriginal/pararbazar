<?php
// dsr/index.php
// Main router already handled DB connection and functions
$route = $_GET['route'] ?? 'dashboard';
$action = $route; // simple routing

// Ensure dsr is logged in unless on login page
$is_dsr = isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'dsr';

if (!$is_dsr && $action !== 'login') {
    redirect('/dsr/login');
}
if ($is_dsr && $action === 'login') {
    redirect('/dsr/dashboard');
}

// Basic API handler inside dsr
if ($action === 'api') {
    require_once __DIR__ . '/api.php';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>DSR Panel | Parar Bazar</title>
    <link rel="icon" href="<?= BASE_URL ?>images/icon.png" type="image/png">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- PWA Manifest -->
    <link rel="manifest" href="<?= BASE_URL ?>dsr/manifest.json">
    <meta name="theme-color" content="#1e293b">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            -webkit-tap-highlight-color: transparent;
        }
        .premium-card {
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body class="pb-6">

<?php
    $allowed_pages = ['login', 'dashboard', 'collection', 'delivery'];
    if (in_array($action, $allowed_pages)) {
        require_once __DIR__ . "/views/{$action}.php";
    } else {
        echo "<div class='p-8 text-center'>404 Not Found</div>";
    }
?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('<?= BASE_URL ?>dsr/sw.js');
        }
    </script>
</body>
</html>
