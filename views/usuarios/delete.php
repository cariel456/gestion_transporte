<?php
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';

requireLogin();
$userPermissions = getUserPermissions();

// Verifica el permiso necesario para la página actual
$requiredPermission = 'eliminar'; // 
if (!checkPermission($requiredPermission)) {
    header("Location: " . BASE_URL . "/views/dashboard.php?error=permission_denied");
    exit();
}

include ROOT_PATH . '/includes/header.php'; 

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: read.php");
    exit();
}

if (deleteUser($id)) {
    header("Location: read.php");
    exit();
} else {
    echo "Error al eliminar el usuario";
} 
?>

    <div class="container mt-5">
        <h1>Eliminar [Nombre de la Entidad]</h1>
        <p>¿Está seguro de que desea eliminar este registro?</p>
        <form method="POST">
            <button type="submit" class="btn btn-danger">Eliminar</button>
            <a href="read.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>