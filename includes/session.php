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

function checkPermission($item, $accion, $subitem = null) {
    if (!isset($_SESSION['user_permissions'])) {
        return false;
    }
    $permissions = $_SESSION['user_permissions'];
    
    if (!isset($permissions[$item])) {
        return false;
    }
    
    if ($subitem !== null) {
        return isset($permissions[$item]['subitems'][$subitem]) && $permissions[$item]['subitems'][$subitem] == 1;
    }
    
    return isset($permissions[$item][$accion]) && $permissions[$item][$accion] == 1;
}

function getUserPermissions($user_id) {
    global $conn;
    
    $sql = "SELECT ru.permisos
            FROM usuarios u
            JOIN roles_usuarios ru ON u.rol_id = ru.id
            WHERE u.id = ? AND u.habilitado = 1 AND ru.habilitado = 1";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return json_decode($row['permisos'], true);
    }
    
    return [];
}