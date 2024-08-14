<?php
session_start();
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';

requireLogin();

if (!checkPermission('turnos_parejas_choferes', 'eliminar')) {
   // header("Location: " . BASE_URL . "/views/dashboard.php?error=permission_denied");
   // exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: read.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (deleteTurnoParejasChoferes($id)) {
        header("Location: read.php");
        exit();
    } else {
        $error = "Error al eliminar el turno de pareja de choferes";
    }
}

$turno = getTurnoParejasChoferesById($id);

include ROOT_PATH . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Turno de Pareja de Choferes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Eliminar Turno de Pareja de Choferes</h1>

        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <p>¿Está seguro de que desea eliminar este turno de pareja de choferes?</p>
        
        <p><strong>Fecha:</strong> <?php echo $turno['fecha']; ?></p>
        <p><strong>Descripción:</strong> <?php echo $turno['descripcion']; ?></p>

        <form method="POST">
            <button type="submit" class="btn btn-danger">Eliminar</button>
            <a href="read.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>