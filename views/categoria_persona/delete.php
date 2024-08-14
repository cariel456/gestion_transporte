<?php
$projectRoot = dirname(__FILE__, 3);
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once $projectRoot . '/includes/functions.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: read.php");
    exit();
}

if (deleteCategoriaPersona($id)) {
    header("Location: read.php");
    exit();
} else {
    echo "Error al eliminar la categoría de persona";
}
$persona=getAllCategoriasPersona();
?>

<div class="container mt-5">
    <h2>Eliminar Personal</h2>
    <p>¿Está seguro de que desea eliminar la categoria: <?php echo $persona['nombre_categoria']; ?>?</p>
    <form method="POST">
        <button type="submit" class="btn btn-danger">Eliminar</button>
        <a href="read.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>