<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/functions.php';

$request_uri = $_SERVER['REQUEST_URI'];
$base_path = '/pararbazar';
$path = str_replace($base_path, '', $request_uri);
$path = parse_url($path, PHP_URL_PATH);
$path = trim($path, '/');

if ($path === '') {
    $path = 'home';
}

$segments = explode('/', $path);

// Route to setup DB
if ($segments[0] === 'setup_db') {
    require_once __DIR__ . '/setup_db.php';
    exit;
}

// Route to Admin System
if ($segments[0] === 'admin') {
    array_shift($segments);
    $sub_path = empty($segments) ? 'dashboard' : implode('/', $segments);
    $_GET['route'] = $sub_path; // pass route to admin index
    require_once __DIR__ . '/admin/index.php';
    exit;
}

// Route to DSR System
if ($segments[0] === 'dsr') {
    array_shift($segments);
    $sub_path = empty($segments) ? 'login' : implode('/', $segments);
    $_GET['route'] = $sub_path; // pass route to dsr index
    require_once __DIR__ . '/dsr/index.php';
    exit;
}

// Route to API
if ($segments[0] === 'api') {
    array_shift($segments);
    $api_endpoint = $segments[0] ?? 'index';
    $file = __DIR__ . "/api/{$api_endpoint}.php";
    if (file_exists($file)) {
        require_once $file;
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'API Endpoint Not Found']);
    }
    exit;
}

// Frontend Customer Routes
$allowed_customer_pages = ['home', 'profile', 'orders', 'checkout'];
if (in_array($segments[0], $allowed_customer_pages)) {
    require_once __DIR__ . "/views/customer/{$segments[0]}.php";
} else {
    http_response_code(404);
    echo "404 Not Found";
}
