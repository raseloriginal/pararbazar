<?php
session_start();
$_SESSION['user_id'] = 1; // fake login
$_GET['id'] = 1; // fake order id
$_SERVER['REQUEST_URI'] = '/pararbazar/api/order_details?id=1';
$_SERVER['HTTP_HOST'] = 'localhost';

require_once __DIR__ . '/index.php';
