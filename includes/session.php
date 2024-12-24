<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: " . BASE_URL . "/views/login.php");
        exit();
    }
}

function login($user_id, $username, $userPermissions) {
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
    $_SESSION['user_permissions'] = $userPermissions;
    $_SESSION['last_activity'] = time();
}

function logout() {
    session_unset();
    session_destroy();
}



//function checkPermission($item, $accion, $subitem = null) {
//    if (!isset($_SESSION['user_permissions'])) {
//        return false;
//    }
//    $permissions = $_SESSION['user_permissions'];
    
//    if (!isset($permissions[$item])) {
//        return false;
//    }
    
//    if ($subitem !== null) {
//        return isset($permissions[$item]['subitems'][$subitem]) && $permissions[$item]['subitems'][$subitem] == 1;
//    }
    
//    return isset($permissions[$item][$accion]) && $permissions[$item][$accion] == 1;
//}