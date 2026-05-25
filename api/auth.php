<?php
// Session and functions loaded by index.php
header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';

if ($action === 'logout') {
    session_destroy();
    header("Location: /pararbazar/home");
    exit;
}

jsonResponse('error', 'Invalid action');
