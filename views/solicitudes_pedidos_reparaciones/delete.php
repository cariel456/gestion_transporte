
<?php
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';

requireLogin();

// Para read.php
checkPermission('leer');

// Para create.php
checkPermission('crear');

// Para update.php
checkPermission('actualizar');

// Para delete.php
checkPermission('eliminar');

include ROOT_PATH . '/includes/header.php'; 

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: read.php");
    exit();
}

if (deleteSolicitudPedidoReparacion($id)) {
    header("Location: read.php");
    exit();
} else {
    echo "Error al eliminar la solicitud de pedido de reparación";
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

    <?include ROOT_PATH . '/includes/footer.php'; ?>