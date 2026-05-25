<?php
// dsr/api.php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

if ($action === 'login') {
    $phone = sanitize($_POST['phone'] ?? '');
    
    if (empty($phone)) {
        jsonResponse('error', 'Phone number is required');
    }

    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT id, role, password FROM users WHERE phone = ? AND role = 'dsr' LIMIT 1");
    $stmt->execute([$phone]);
    $dsr = $stmt->fetch();

    if ($dsr) {
        // For simplicity, DSR login is just their assigned phone number right now
        // In real world, verify password hash
        if (password_verify($phone, $dsr['password'])) {
            $_SESSION['user_id'] = $dsr['id'];
            $_SESSION['user_role'] = 'dsr';
            jsonResponse('success', 'Logged in');
        } else {
            jsonResponse('error', 'Invalid credentials');
        }
    } else {
        jsonResponse('error', 'DSR account not found');
    }
}

// Security check for other actions
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'dsr') {
    jsonResponse('error', 'Unauthorized');
}

if ($action === 'logout') {
    session_destroy();
    jsonResponse('success', 'Logged out');
}

if ($action === 'update_order_status') {
    $order_id = (int)($_POST['order_id'] ?? 0);
    $status = $_POST['status'] ?? '';
    
    if (!in_array($status, ['delivered', 'cancelled'])) {
        jsonResponse('error', 'Invalid status');
    }
    
    $dsr_id = $_SESSION['user_id'];
    
    $pdo = getDB();
    // Only allow updating orders assigned to this DSR
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ? AND dsr_id = ?");
    $stmt->execute([$status, $order_id, $dsr_id]);
    
    if ($stmt->rowCount() > 0) {
        jsonResponse('success', 'Order status updated');
    } else {
        jsonResponse('error', 'Order not found or not assigned to you');
    }
}

jsonResponse('error', 'Invalid action');
