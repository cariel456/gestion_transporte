<?php
$projectRoot = dirname(__FILE__, 3); 
require_once dirname(__DIR__, 2) . '/config/config.php'; 
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';   
require_once ROOT_PATH . '/sec/auth_check.php';       
require_once $projectRoot . '/includes/functions.php'; 
requireLogin();

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