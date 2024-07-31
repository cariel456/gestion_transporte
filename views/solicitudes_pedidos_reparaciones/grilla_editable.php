<?php
$projectRoot = dirname(__FILE__, 3);
require_once $projectRoot . '/includes/functions.php';

// Obtener los filtros de la URL
$filtros = [
    'solicitante' => $_GET['solicitante'] ?? '',
    'conductor' => $_GET['conductor'] ?? '',
    'mantenimiento' => $_GET['mantenimiento'] ?? '',
    'fecha_desde' => $_GET['fecha_desde'] ?? '',
    'fecha_hasta' => $_GET['fecha_hasta'] ?? '',
    'unidad' => $_GET['unidad'] ?? '',
];

// Modificar la función getAllSolicitudesPedidosReparaciones para que acepte filtros
$solicitudes = getAllSolicitudesPedidosReparaciones($filtros);

$personal = getAllPersonal();
$unidades = getAllUnidades();

// Manejar la exportación a Excel
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    exportToExcel($solicitudes);
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grilla de Solicitudes de Pedidos de Reparación</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid mt-5">
        <h1>Grilla de Solicitudes de Pedidos de Reparación</h1>
        
        <!-- Formulario de filtros -->
        <form method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-2">
                    <input type="text" class="form-control" name="solicitante" placeholder="Solicitante" value="<?php echo $filtros['solicitante']; ?>">
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control" name="conductor" placeholder="Conductor" value="<?php echo $filtros['conductor']; ?>">
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control" name="mantenimiento" placeholder="Mantenimiento" value="<?php echo $filtros['mantenimiento']; ?>">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="fecha_desde" placeholder="Fecha desde" value="<?php echo $filtros['fecha_desde']; ?>">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="fecha_hasta" placeholder="Fecha hasta" value="<?php echo $filtros['fecha_hasta']; ?>">
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control" name="unidad" placeholder="Unidad" value="<?php echo $filtros['unidad']; ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['export' => 'excel'])); ?>" class="btn btn-success">Exportar a Excel</a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Solicitante</th>
                        <th>Conductor</th>
                        <th>Mantenimiento</th>
                        <th>Fecha Solicitud</th>
                        <th>Unidad</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($solicitudes as $solicitud) : ?>
                        <tr>
                            <td><?php echo $solicitud['solicitante']; ?></td>
                            <td><?php echo $solicitud['conductor']; ?></td>
                            <td><?php echo $solicitud['mantenimiento']; ?></td>
                            <td><?php echo $solicitud['fecha_solicitud']; ?></td>
                            <td><?php echo $solicitud['unidad']; ?></td>
                            <td><?php echo $solicitud['observaciones']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
