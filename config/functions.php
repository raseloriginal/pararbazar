<?php
// config/functions.php

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirect($path) {
    header("Location: /pararbazar/" . ltrim($path, '/'));
    exit;
}

function jsonResponse($status, $message, $data = []) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

function generateUploadName($originalName) {
    $ext = pathinfo($originalName, PATHINFO_EXTENSION);
    return uniqid() . '_' . time() . '.' . $ext;
}
