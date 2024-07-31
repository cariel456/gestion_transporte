<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: " . BASE_URL . "/views/login.php");
        exit();
    }
}

function checkPermission($permission) {
    if (!isset($_SESSION['user_permissions'][$permission]) || !$_SESSION['user_permissions'][$permission]) {
        return false;
    }
    return true;
}

function getUserPermissions() {
    return $_SESSION['user_permissions'] ?? [];
}