<?php
require_once __DIR__ . '/config/database.php';

$pdo = getDB();

// Insert Categories
$categories = [
    ['Fresh Veg', 'fa-solid fa-leaf'],
    ['Fruits', 'fa-solid fa-apple-whole'],
    ['Beverages', 'fa-solid fa-bottle-water'],
    ['Dairy', 'fa-solid fa-cheese'],
    ['Meat', 'fa-solid fa-drumstick-bite'],
    ['Snacks', 'fa-solid fa-cookie'],
];

$pdo->query("SET FOREIGN_KEY_CHECKS = 0; TRUNCATE TABLE products; TRUNCATE TABLE categories; TRUNCATE TABLE delivery_slots; SET FOREIGN_KEY_CHECKS = 1;");
$stmt = $pdo->prepare("INSERT INTO categories (name, icon, sort_order) VALUES (?, ?, ?)");
foreach ($categories as $index => $cat) {
    $stmt->execute([$cat[0], $cat[1], $index + 1]);
}

// Get Category IDs
$catRows = $pdo->query("SELECT id, name FROM categories")->fetchAll(PDO::FETCH_KEY_PAIR);
$veg_id = array_search('Fresh Veg', $catRows);
$fruit_id = array_search('Fruits', $catRows);
$bev_id = array_search('Beverages', $catRows);
$dairy_id = array_search('Dairy', $catRows);
$meat_id = array_search('Meat', $catRows);
$snack_id = array_search('Snacks', $catRows);

// Insert Products
$products = [
    [$veg_id, 'Local Tomato (Deshi)', '1 kg', 45, 'https://placehold.co/200x200?text=Tomato'],
    [$veg_id, 'Local Onion (Premium)', '1 kg', 95, 'https://placehold.co/200x200?text=Onion'],
    [$veg_id, 'Fresh Potato', '1 kg', 40, 'https://placehold.co/200x200?text=Potato'],
    [$veg_id, 'Green Chili', '250 g', 30, 'https://placehold.co/200x200?text=Chili'],
    
    [$fruit_id, 'Sagar Banana', '1 Dozen', 120, 'https://placehold.co/200x200?text=Banana'],
    [$fruit_id, 'Green Apple', '1 kg', 250, 'https://placehold.co/200x200?text=Apple'],
    
    [$dairy_id, 'Aarong Pasteurized Milk', '1 Liter', 90, 'https://placehold.co/200x200?text=Milk'],
    [$dairy_id, 'Farm Fresh Eggs (Brown)', '1 Dozen', 140, 'https://placehold.co/200x200?text=Eggs'],
    
    [$meat_id, 'Broiler Chicken (Dressed)', '1 kg', 220, 'https://placehold.co/200x200?text=Chicken'],
    [$meat_id, 'Beef (Bone in)', '1 kg', 750, 'https://placehold.co/200x200?text=Beef'],
    
    [$bev_id, 'Coca Cola', '1.25 Liter', 75, 'https://placehold.co/200x200?text=Coke'],
    [$snack_id, 'Bombay Sweets Potato Crackers', '25 g', 15, 'https://placehold.co/200x200?text=Chips'],
];

$stmt = $pdo->prepare("INSERT INTO products (category_id, name, description, price, stock, image) VALUES (?, ?, ?, ?, 100, ?)");
foreach ($products as $p) {
    $stmt->execute([$p[0], $p[1], $p[2], $p[3], $p[4]]);
}

// Add Delivery Slots
$slots = [
    ['09:00:00', '11:30:00', 50],
    ['14:30:00', '17:00:00', 50],
    ['18:00:00', '20:30:00', 50]
];
$stmt = $pdo->prepare("INSERT INTO delivery_slots (start_time, end_time, max_orders) VALUES (?, ?, ?)");
foreach ($slots as $s) {
    $stmt->execute([$s[0], $s[1], $s[2]]);
}

echo "Database successfully seeded with dummy data!";
