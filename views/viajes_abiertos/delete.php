<?php
session_start();
require_once '../config/config.php';
require_once ROOT_PATH . '/includes/functions.php';

if (!isset($_GET['id'])) {
    header("Location: read.php");
    exit();
}

$id = $_GET['id'];
$viaje = getViajeAbiertoById($id);

if (!$viaje) {
    header("Location: read.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (deleteViajeAbierto($id)) {
        header("Location: read.php");
        exit();
    } else {
        $error = "Error al eliminar el viaje abierto";
    }
}

include ROOT_PATH . '/includes/header.php';
?>

<div class="container mt-5">
    <h2>Eliminar Viaje Abierto</h2>
    <p>¿Está seguro de que desea eliminar el viaje abierto con ID "<?php echo $viaje['id']; ?>"?</p>
    <form action="" method="POST">
        <button type="submit" class="btn btn-danger">Eliminar</button>
        <a href="read.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>