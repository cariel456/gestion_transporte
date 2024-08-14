<?php
require_once 'config/config.php'; 
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/sec/error_handler.php';
require_once ROOT_PATH . '/sec/auth_check.php';

// Actualizar la última actividad
$_SESSION['last_activity'] = time();

// Verificar si el usuario está autenticado
checkAuthentication();
?>