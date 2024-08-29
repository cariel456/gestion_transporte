<?php
session_start();
$projectRoot = dirname(__FILE__, 3);
require_once dirname(__DIR__, 2) . '/../config/config.php';
require_once ROOT_PATH . '/includes/functions.php';
require_once ROOT_PATH . '/includes/auth.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: read.php");
    exit();
}

if (deleteRangosHorarios($id)) {
    header("Location: read.php");
    exit();
} else {
    echo "Error al eliminar la categoría de persona";
}
$persona=getAllRangosHorarios();
?>

<div class="container mt-5">
    <h2>Eliminar Zona Horario Urbano</h2>
    <p>¿Está seguro de que desea eliminar el rango horario urbano: <?php echo $persona['nombre']; ?>?</p>
    <form method="POST">
        <button type="submit" class="btn btn-danger">Eliminar</button>
        <a href="read.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>