<?php
// config/database.php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pararbazar_db');

function getDB() {
    static $pdo;
    if ($pdo === null) {
        try {
            $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // If DB doesn't exist yet, we don't want to break the setup script
            if (strpos($e->getMessage(), 'Unknown database') !== false) {
                try {
                    $pdo = new PDO("mysql:host=" . DB_HOST . ";charset=utf8mb4", DB_USER, DB_PASS);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e2) {
                    die("Connection failed: " . $e2->getMessage());
                }
            } else {
                die("Connection failed: " . $e->getMessage());
            }
        }
    }
    return $pdo;
}
