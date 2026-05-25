<?php
// Session, database config and functions are already loaded by index.php

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse('error', 'Invalid request method');
}

$cart = $_SESSION['cart'] ?? null;
if (!$cart || $cart['total_items'] === 0) {
    jsonResponse('error', 'Your cart is empty');
}

$slot_id = (int)($_POST['slot_id'] ?? 0);
$name = sanitize($_POST['name'] ?? '');
$phone = sanitize($_POST['phone'] ?? '');
$address = sanitize($_POST['address'] ?? '');

if (!$slot_id || !$name || !$phone || !$address) {
    jsonResponse('error', 'All fields are required');
}

try {
    $pdo = getDB();
    $pdo->beginTransaction();

    // 1. Handle User / Authentication
    $stmt = $pdo->prepare("SELECT id, password FROM users WHERE phone = ? LIMIT 1");
    $stmt->execute([$phone]);
    $user = $stmt->fetch();

    $user_id = null;

    if ($user) {
        $user_id = $user['id'];
        // Update name and address if provided
        $updateStmt = $pdo->prepare("UPDATE users SET name = ?, default_address = ? WHERE id = ?");
        $updateStmt->execute([$name, $address, $user_id]);
    } else {
        // Create new user (password = phone number as requested)
        $hashed_password = password_hash($phone, PASSWORD_DEFAULT);
        $insertUser = $pdo->prepare("INSERT INTO users (phone, password, role, name, default_address) VALUES (?, ?, 'customer', ?, ?)");
        $insertUser->execute([$phone, $hashed_password, $name, $address]);
        $user_id = $pdo->lastInsertId();
    }

    // Auto-login the user
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_phone'] = $phone;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_role'] = 'customer';

    // 2. Create Order
    $subtotal = $cart['total_amount'];
    $delivery_fee = 30; // mock delivery fee
    $grand_total = $subtotal + $delivery_fee;

    $insertOrder = $pdo->prepare("INSERT INTO orders (user_id, slot_id, subtotal, delivery_fee, grand_total, delivery_address) VALUES (?, ?, ?, ?, ?, ?)");
    $insertOrder->execute([$user_id, $slot_id, $subtotal, $delivery_fee, $grand_total, $address]);
    $order_id = $pdo->lastInsertId();

    // 3. Create Order Items
    $insertItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_time) VALUES (?, ?, ?, ?)");
    foreach ($cart['items'] as $item) {
        $insertItem->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);
    }

    $pdo->commit();

    // 4. Clear Cart
    unset($_SESSION['cart']);

    jsonResponse('success', 'Order placed successfully', ['order_id' => $order_id]);

} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // We log the error, but send a generic message to user
    error_log($e->getMessage());
    jsonResponse('error', 'Database error: ' . $e->getMessage());
}
