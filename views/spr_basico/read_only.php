<?php
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';
requireLogin();

include ROOT_PATH . '/includes/header.php'; 

$solicitudes = getAllSolicitudesPedidosReparaciones();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitudes de Pedidos de Reparación</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Solicitudes de Pedidos de Reparación</h1>
        <a href="../../index.php" class="btn btn-secondary mb-3">Volver</a>
        <table class="table table-striped">
        <thead>
    <tr>
        <th>OT</th>
        <th>Solicitante</th>
        <th>Fecha Solicitud</th>
        <th>Conductor</th>
        <th>Mantenimiento</th>
        <th>Especialidad</th>
        <th>Ubicación</th>
        <th>Grupo Función</th>
        <th>Nivel Urgencia</th>
        <th>Número Solicitud</th>
        <th>Unidad</th>
    </tr>
</thead>
<tbody>
    <?php foreach ($solicitudes as $solicitud) : ?>
        <tr>
            <td><?php echo $solicitud['id']; ?></td>
            <td><?php echo $solicitud['solicitante']; ?></td>
            <td><?php echo $solicitud['fecha_solicitud']; ?></td>
            <td><?php echo $solicitud['conductor']; ?></td>
            <td><?php echo $solicitud['mantenimiento']; ?></td>
            <td><?php echo $solicitud['especialidades']; ?></td>
            <td><?php echo $solicitud['ubicacion']; ?></td>
            <td><?php echo $solicitud['grupo_funcion']; ?></td>
            <td><?php echo $solicitud['nivel_urgencia']; ?></td>            
            <td><?php echo $solicitud['numero_solicitud']; ?></td>
            <td><?php echo $solicitud['numero_unidad']; ?></td>
        </tr>
    <?php endforeach; ?>
</tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <?include ROOT_PATH . '/includes/footer.php'; ?>
</body>
</html>