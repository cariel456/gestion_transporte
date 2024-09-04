<?php
session_start();
require_once 'error_handler.php';

function redirect($url) {
    header("Location: " . $url);
    exit();
}

// Tiempo de expiración en segundos (60 minutos)
$session_timeout = 3600;

try {
    // Verificar si la sesión está activa
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_timeout)) {
        session_unset();
        session_destroy();
        redirect("index.php/../..");
    }

    // Actualizar la última actividad
    $_SESSION['last_activity'] = time();
} catch (Exception $e) {
    handleError($e->getMessage());
}
?>