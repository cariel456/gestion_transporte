<?php
session_start();
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';

requireLogin();

//$userPermissions = getUserPermissions();

//$requiredPermission = 'eliminar';
//if (!checkPermission($requiredPermission)) {
//    header("Location: " . BASE_URL . "/views/dashboard.php?error=permission_denied");
//    exit();
//}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: read.php");
    exit();
}

$persona = getPersonalById($id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (deletePersonal($id)) {
        header("Location: read.php");
        exit();
    } else {
        $error = "Error al eliminar el personal";
    }
}
include ROOT_PATH . '/includes/header.php'; 

?>

<div class="container mt-5">
    <h2>Eliminar Personal</h2>
    <p>¿Está seguro de que desea eliminar a <?php echo $persona['nombre_personal']; ?>?</p>
    <form method="POST">
        <button type="submit" class="btn btn-danger">Eliminar</button>
        <a href="read.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>