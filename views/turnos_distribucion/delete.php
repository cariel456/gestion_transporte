<?php
$projectRoot = dirname(__FILE__, 3); 
require_once dirname(__DIR__, 2) . '/config/config.php'; 
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';   
require_once ROOT_PATH . '/sec/auth_check.php';       
require_once $projectRoot . '/includes/functions.php'; 

requireLogin();

// Verificar si se proporcionó un ID
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Se requiere un ID para eliminar la distribución de turnos.";
    header("Location: read.php");
    exit();
}

$id = $_GET['id'];

// Obtener la información de la distribución de turnos
$distribucion = getTurnosDistribucionById($id);

if (!$distribucion) {
    $_SESSION['error'] = "No se encontró la distribución de turnos.";
    header("Location: read.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Primero, eliminar los detalles
    if (deleteTurnosDistribucionDetalles($id)) {
        // Luego, eliminar el registro maestro
        if (deleteTurnosDistribucion($id)) {
            $_SESSION['message'] = "Distribución de turnos eliminada exitosamente.";
            header("Location: read.php");
            exit();
        } else {
            $error = "Error al eliminar la distribución de turnos.";
        }
    } else {
        $error = "Error al eliminar los detalles de la distribución de turnos.";
    }
}
include ROOT_PATH . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Distribución de Turnos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Eliminar Distribución de Turnos</h1>
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">¿Está seguro que desea eliminar esta distribución de turnos?</h5>
                <p class="card-text"><strong>Nombre:</strong> <?php echo htmlspecialchars($distribucion['nombre']); ?></p>
                <p class="card-text"><strong>Descripción:</strong> <?php echo htmlspecialchars($distribucion['descripcion']); ?></p>
                <p class="card-text"><strong>Tipo de Servicio:</strong> <?php echo htmlspecialchars(getTurnosTipoServicioById($distribucion['tipo_servicio'])['nombre']); ?></p>
                <form method="POST">
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                    <a href="read.php" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>