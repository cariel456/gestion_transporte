<?php
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';

requireLogin();

// Para read.php
checkPermission('leer');

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
        <h1>Solicitudes de pedidos de reparaciones</h1>
        <?php if ($_SESSION['user_permissions']['crear']): ?>
        <a href="create.php" class="btn btn-success mb-3">Crear Solicitud</a>
        <?php endif; ?>
        <a href="../../index.php" class="btn btn-secondary mb-3">Volver</a>
        <table class="table table-striped">
        <thead>
    <tr>
        <th>N°Solicitud</th>
        <th>Solicitante</th>
        <th>Fecha Solicitud</th>
        <th>Conductor</th>
        <th>Especialidad</th>
        <th>Ubicación</th>
        <th>Grupo Función</th>
        <th>Nivel Urgencia</th>
        <th>Mantenimiento</th>
        <th>Unidad</th>
        <th>Pedido Detalle</th>
        <th>Acciones</th>
    </tr>
</thead>
<tbody>
    <?php foreach ($solicitudes as $solicitud) : ?>
        <tr>
            <td><?php echo $solicitud['id']; ?></td>
            <td><?php echo $solicitud['solicitante']; ?></td>
            <td><?php echo $solicitud['fecha_solicitud']; ?></td>
            <td><?php echo $solicitud['conductor']; ?></td>
            <td><?php echo $solicitud['especialidades']; ?></td>
            <td><?php echo $solicitud['ubicacion']; ?></td>
            <td><?php echo $solicitud['grupo_funcion']; ?></td>
            <td><?php echo $solicitud['nivel_urgencia']; ?></td>
            <td><?php echo $solicitud['mantenimiento']; ?></td>
            <td><?php echo $solicitud['numero_unidad']; ?></td>
            <td><?php echo $solicitud['observaciones']; ?></td>
            <td>
                    <?php if ($_SESSION['user_permissions']['actualizar']): ?>
                    <a href="update.php?id=<?php echo $solicitud['id']; ?>" class="btn btn-warning btn-sm">Actualizar</a>
                    <?php endif; ?>
                    <?php if ($_SESSION['user_permissions']['eliminar']): ?>
                    <a href="delete.php?id=<?php echo $solicitud['id']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                    <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
</body>
</html>