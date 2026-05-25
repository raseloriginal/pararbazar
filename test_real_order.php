<?php
$_SERVER['HTTP_HOST']='localhost'; 
require 'c:\xampp\htdocs\pararbazar\config\database.php'; 
$pdo = getDB();
$stmt = $pdo->query("SELECT * FROM orders ORDER BY id DESC LIMIT 1");
$order = $stmt->fetch(PDO::FETCH_ASSOC);
if ($order) {
    echo "Found order: " . $order['id'] . "\n";
    $_SERVER['REQUEST_URI'] = '/pararbazar/api/order_details?id=' . $order['id'];
    $_GET['id'] = $order['id'];
    $_SESSION['user_id'] = $order['user_id'];
    ob_start();
    require 'c:\xampp\htdocs\pararbazar\index.php';
    $output = ob_get_clean();
    echo "\nOutput:\n" . substr($output, 0, 500) . "...\n";
} else {
    echo "No orders found.\n";
}
