<?php
require_once __DIR__ . '/config/database.php';

try {
    $pdo = getDB();
    
    // Add dsr_id if not exists
    try {
        $pdo->exec("ALTER TABLE orders ADD COLUMN dsr_id INT NULL DEFAULT NULL AFTER user_id");
        $pdo->exec("ALTER TABLE orders ADD FOREIGN KEY (dsr_id) REFERENCES users(id) ON DELETE SET NULL");
        echo "Added dsr_id.\n";
    } catch(Exception $e) { echo "dsr_id probably exists.\n"; }

    // Rename delivery_slot_id to slot_id
    try {
        // Drop foreign key first if needed, but MySQL allows RENAME COLUMN in 8.0+
        // For older MariaDB, CHANGE is safer
        $pdo->exec("ALTER TABLE orders CHANGE delivery_slot_id slot_id INT NOT NULL");
        echo "Renamed delivery_slot_id to slot_id.\n";
    } catch(Exception $e) { echo "slot_id probably already renamed.\n"; }

    // Fix status enum to support 'processing'
    try {
        $pdo->exec("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'processing', 'collected', 'out_for_delivery', 'delivered', 'cancelled') DEFAULT 'pending'");
        echo "Updated status ENUM.\n";
    } catch(Exception $e) { echo "Status enum probably fine.\n"; }

    echo "Patch complete.";
} catch(Exception $e) {
    echo "Fatal: " . $e->getMessage();
}
