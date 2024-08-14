<?php
session_start();
$projectRoot = dirname(__FILE__, 3);
require_once $projectRoot . '/includes/functions.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: read.php");
    exit();
}

if (deleteUnidad($id)) {
    header("Location: read.php");
    exit();
} else {
    echo "Error al eliminar la unidad";
}

$persona = getAllUnidades($id);

?>


<div class="container mt-5">
    <h2>Eliminar Unidad</h2>
    <p>¿Está seguro de que desea eliminar a la unidad<?php echo $persona['codigo_interno']; ?>?</p>
    <form method="POST">
        <button type="submit" class="btn btn-danger">Eliminar</button>
        <a href="read.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>