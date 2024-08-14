<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';

requireLogin();

$userPermissions = getUserPermissions();

$requiredPermission = 'borrar';
if (!checkPermission('personal', $requiredPermission)) {
    header("Location: " . BASE_URL . "/views/dashboard.php?error=permission_denied");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: read.php");
    exit();
}

$id = $_GET['id'];

if (deleteHorarioInterurbano($id)) {
    header("Location: read.php?success=1");
} else {
    header("Location: read.php?error=1");
}
exit();