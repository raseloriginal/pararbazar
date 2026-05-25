<?php
// admin/api.php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

// Handle Login
if ($action === 'login') {
    $phone = sanitize($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($phone) || empty($password)) {
        jsonResponse('error', 'All fields are required');
    }

    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT id, role, password FROM users WHERE phone = ? AND role = 'admin' LIMIT 1");
    $stmt->execute([$phone]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['user_role'] = 'admin';
        jsonResponse('success', 'Logged in');
    } else {
        jsonResponse('error', 'Invalid admin credentials');
    }
}

// Security Check for other actions
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    jsonResponse('error', 'Unauthorized access');
}

// Handle Logout
if ($action === 'logout') {
    session_destroy();
    jsonResponse('success', 'Logged out');
}

// Handle Categories
if ($action === 'add_category') {
    $name = sanitize($_POST['name'] ?? '');
    $icon = sanitize($_POST['icon'] ?? 'fa-solid fa-folder');
    if (empty($name)) {
        jsonResponse('error', 'Category name is required');
    }
    $pdo = getDB();
    $stmt = $pdo->prepare("INSERT INTO categories (name, icon) VALUES (?, ?)");
    $stmt->execute([$name, $icon]);
    jsonResponse('success', 'Category added');
}

if ($action === 'delete_category') {
    $id = (int)($_POST['id'] ?? 0);
    $pdo = getDB();
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    jsonResponse('success', 'Category deleted');
}

// Handle Products
if ($action === 'add_product') {
    $name = sanitize($_POST['name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $description = sanitize($_POST['description'] ?? '');
    
    // For simplicity, we are using a direct URL for testing instead of file upload logic
    // But we'll save it to the image column if provided
    $image_url = sanitize($_POST['image_url'] ?? '');
    // Strip off the base path if we were actually uploading, but for now we store the URL or leave it blank
    
    if (empty($name) || !$category_id || $price <= 0) {
        jsonResponse('error', 'Valid name, category, and price are required');
    }

    $pdo = getDB();
    $stmt = $pdo->prepare("INSERT INTO products (category_id, name, description, price, stock, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$category_id, $name, $description, $price, $stock, $image_url]);
    
    jsonResponse('success', 'Product added');
}

if ($action === 'delete_product') {
    $id = (int)($_POST['id'] ?? 0);
    $pdo = getDB();
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    jsonResponse('success', 'Product deleted');
}

// Handle Delivery Slots
if ($action === 'add_slot') {
    $start_time = $_POST['start_time'] ?? '';
    $end_time = $_POST['end_time'] ?? '';
    $max_orders = (int)($_POST['max_orders'] ?? 50);
    
    if (empty($start_time) || empty($end_time)) {
        jsonResponse('error', 'Start and End times are required');
    }

    $pdo = getDB();
    $stmt = $pdo->prepare("INSERT INTO delivery_slots (start_time, end_time, max_orders) VALUES (?, ?, ?)");
    $stmt->execute([$start_time, $end_time, $max_orders]);
    
    jsonResponse('success', 'Delivery slot added');
}

if ($action === 'delete_slot') {
    $id = (int)($_POST['id'] ?? 0);
    $pdo = getDB();
    $stmt = $pdo->prepare("DELETE FROM delivery_slots WHERE id = ?");
    $stmt->execute([$id]);
    jsonResponse('success', 'Delivery slot deleted');
}

// Handle DSR Management
if ($action === 'add_dsr') {
    $name = sanitize($_POST['name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    
    if (empty($name) || empty($phone)) {
        jsonResponse('error', 'Name and Phone are required');
    }

    $pdo = getDB();
    // Check if phone exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE phone = ?");
    $stmt->execute([$phone]);
    if ($stmt->fetch()) {
        jsonResponse('error', 'Phone number already registered');
    }

    $hashed_password = password_hash($phone, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (phone, password, role, name) VALUES (?, ?, 'dsr', ?)");
    $stmt->execute([$phone, $hashed_password, $name]);
    
    jsonResponse('success', 'DSR added successfully');
}

if ($action === 'delete_dsr') {
    $id = (int)($_POST['id'] ?? 0);
    $pdo = getDB();
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'dsr'");
    $stmt->execute([$id]);
    jsonResponse('success', 'DSR deleted successfully');
}

// Handle Order Assignment
if ($action === 'assign_dsr') {
    $order_id = (int)($_POST['order_id'] ?? 0);
    $dsr_id = (int)($_POST['dsr_id'] ?? 0);
    
    // dsr_id can be 0 if unassigned
    $dsr_val = $dsr_id > 0 ? $dsr_id : null;
    
    $pdo = getDB();
    $stmt = $pdo->prepare("UPDATE orders SET dsr_id = ?, status = 'processing' WHERE id = ?");
    $stmt->execute([$dsr_val, $order_id]);
    
    jsonResponse('success', 'Order assignment updated');
}

// Handle generic responses for unbuilt endpoints
jsonResponse('error', 'Action not implemented yet');
