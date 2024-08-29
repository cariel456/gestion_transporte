<?php
$projectRoot = dirname(__FILE__, 3); 
require_once dirname(__DIR__, 2) . '/config/config.php'; 
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';   
require_once ROOT_PATH . '/sec/auth_check.php';       
require_once $projectRoot . '/includes/functions.php'; 

// Actualizar la última actividad
$_SESSION['last_activity'] = time();

requireLogin();


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

include ROOT_PATH . '/includes/header.php'; 

?>

    <div class="container mt-5">
        <h1>Eliminar [Nombre de la Entidad]</h1>
        <p>¿Está seguro de que desea eliminar este registro?</p>
        <form method="POST">
            <button type="submit" class="btn btn-danger">Eliminar</button>
            <a href="read.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>