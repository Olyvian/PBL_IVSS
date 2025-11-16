<?php
session_start();

require_once __DIR__ . '/../config/database.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserRole() {
    return $_SESSION['role'] ?? 'guest';
}

function redirectIfNotLoggedIn($allowedRoles = []) {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
    if (!empty($allowedRoles) && !in_array(getUserRole(), $allowedRoles)) {
        die("Akses ditolak.");
    }
}
?>
