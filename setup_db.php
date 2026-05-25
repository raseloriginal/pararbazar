<?php
// setup_db.php
require_once __DIR__ . '/config/database.php';

try {
    $pdo = getDB();
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE " . DB_NAME);

    // Users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        phone VARCHAR(20) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('customer', 'dsr', 'admin') DEFAULT 'customer',
        name VARCHAR(100) NULL,
        default_address TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Categories table
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        icon VARCHAR(255) NULL,
        sort_order INT DEFAULT 0,
        status TINYINT(1) DEFAULT 1
    )");

    // Products table
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        description TEXT NULL,
        price DECIMAL(10,2) NOT NULL,
        image VARCHAR(255) NULL,
        stock INT DEFAULT 0,
        status TINYINT(1) DEFAULT 1,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
    )");

    // Delivery slots table
    $pdo->exec("CREATE TABLE IF NOT EXISTS delivery_slots (
        id INT AUTO_INCREMENT PRIMARY KEY,
        start_time TIME NOT NULL,
        end_time TIME NOT NULL,
        status TINYINT(1) DEFAULT 1,
        max_orders INT DEFAULT 50
    )");

    // Orders table
    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        delivery_slot_id INT NOT NULL,
        subtotal DECIMAL(10,2) NOT NULL,
        delivery_fee DECIMAL(10,2) NOT NULL,
        grand_total DECIMAL(10,2) NOT NULL,
        status ENUM('pending', 'collected', 'out_for_delivery', 'delivered', 'cancelled') DEFAULT 'pending',
        delivery_address TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (delivery_slot_id) REFERENCES delivery_slots(id)
    )");

    // Order items table
    $pdo->exec("CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        price_at_time DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id)
    )");

    // DSR assignments table
    $pdo->exec("CREATE TABLE IF NOT EXISTS dsr_assignments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        dsr_id INT NOT NULL,
        slot_id INT NOT NULL,
        assignment_date DATE NOT NULL,
        status ENUM('pending', 'collected', 'completed') DEFAULT 'pending',
        FOREIGN KEY (dsr_id) REFERENCES users(id),
        FOREIGN KEY (slot_id) REFERENCES delivery_slots(id)
    )");

    // Insert mock admin user
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE phone = 'admin'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $hash = password_hash('admin', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO users (phone, password, role, name) VALUES ('admin', '$hash', 'admin', 'Super Admin')");
    }

    echo "Database setup completed successfully!";
} catch (PDOException $e) {
    die("Setup failed: " . $e->getMessage());
}
