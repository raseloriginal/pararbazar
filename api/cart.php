<?php
// Session and functions are already loaded by index.php

header('Content-Type: application/json');

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [
        'items' => [],
        'total_items' => 0,
        'total_amount' => 0
    ];
}

$action = $_REQUEST['action'] ?? '';

if ($action === 'get') {
    jsonResponse('success', 'Cart fetched', $_SESSION['cart']);
}

if ($action === 'update') {
    $product_id = $_POST['product_id'] ?? 0;
    $quantity = (int)($_POST['quantity'] ?? 0);
    $name = $_POST['name'] ?? null;
    $price = $_POST['price'] ?? null;
    $image = $_POST['image'] ?? null;
    
    if (!$product_id) {
        jsonResponse('error', 'Invalid product ID');
    }

    if ($quantity <= 0) {
        // Remove item
        if (isset($_SESSION['cart']['items'][$product_id])) {
            unset($_SESSION['cart']['items'][$product_id]);
        }
    } else {
        // Update or add
        if (isset($_SESSION['cart']['items'][$product_id])) {
            $_SESSION['cart']['items'][$product_id]['quantity'] = $quantity;
            // Also update price in case it changed but for simple implementation we keep it
        } else {
            if ($name && $price !== null) {
                $_SESSION['cart']['items'][$product_id] = [
                    'id' => $product_id,
                    'name' => $name,
                    'price' => $price,
                    'quantity' => $quantity,
                    'image' => $image
                ];
            } else {
                // If it's a new item we need name and price
                jsonResponse('error', 'Missing product details');
            }
        }
    }

    // Recalculate totals
    $total_items = 0;
    $total_amount = 0;
    foreach ($_SESSION['cart']['items'] as $item) {
        $total_items += $item['quantity'];
        $total_amount += ($item['quantity'] * $item['price']);
    }

    $_SESSION['cart']['total_items'] = $total_items;
    $_SESSION['cart']['total_amount'] = $total_amount;

    jsonResponse('success', 'Cart updated', $_SESSION['cart']);
}

jsonResponse('error', 'Invalid action');
