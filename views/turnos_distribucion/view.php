<?php
session_start();
$projectRoot = dirname(__FILE__, 3);
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once $projectRoot . '/includes/functions.php';

// Verificar si el usuario está autenticado
requireLogin();

// Obtener el ID de la distribución de turnos
$id = $_GET['id'] ?? 0;
if ($id == 0) {
    header("Location: read.php");
    exit();
}

// Obtener los datos de la distribución de turnos
$distribucion = getTurnosDistribucionById($id);
if (!$distribucion) {
    header("Location: read.php");
    exit();
}

// Obtener los detalles de la distribución de turnos
$detalles = getTurnosDistribucionDetalles($id);
include ROOT_PATH . '/includes/header.php'; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Distribución de Turnos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Ver Distribución de Turnos</h1>
        <div class="card mb-4">
            <div class="card-header">
                <h2>Información General</h2>
            </div>
            <div class="card-body">
                <p><strong>ID:</strong> <?php echo $distribucion['id']; ?></p>
                <p><strong>Nombre:</strong> <?php echo $distribucion['nombre']; ?></p>
                <p><strong>Descripción:</strong> <?php echo $distribucion['descripcion']; ?></p>
                <p><strong>Tipo de Servicio:</strong> <?php echo getTurnosTipoServicioById($distribucion['tipo_servicio'])['nombre']; ?></p>
            </div>
        </div>

        <h2>Detalles de la Distribución</h2>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Turno</th>
                    <th>Servicio</th>
                    <th>Personal</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detalles as $detalle) : ?>
                    <tr>
                        <td><?php echo getTurnosById($detalle['turno'])['nombre']; ?></td>
                        <td><?php echo getTurnosServiciosById($detalle['turnos_servicios'])['nombre']; ?></td>
                        <td><?php echo getPersonalById($detalle['personal'])['nombre_personal']; ?></td>
                        <td><?php echo $detalle['fecha']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="read.php" class="btn btn-primary">Volver a la lista</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>