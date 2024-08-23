<?php
// Incluir los archivos necesarios
session_start();
$projectRoot = dirname(__FILE__, 3);
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once $projectRoot . '/includes/functions.php';

// Verificar si el usuario está autenticado
requireLogin();

// Obtener los tipos de servicio
$tiposServicio = getTurnosTiposServicios();

// Manejar el envío del formulario de búsqueda
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $tipoServicio = $_POST['tipo_servicio'] ?? 0;

    $turnosDistribucion = searchTurnosDistribucion($nombre, $descripcion, $tipoServicio);
} else {
    $turnosDistribucion = getTurnosDistribucion();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Distribuciones de Turnos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Buscar Distribuciones de Turnos</h1>
        <form method="POST">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $nombre; ?>">
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <input type="text" class="form-control" id="descripcion" name="descripcion" value="<?php echo $descripcion; ?>">
            </div>
            <div class="mb-3">
                <label for="tipo_servicio" class="form-label">Tipo de Servicio</label>
                <select class="form-select" id="tipo_servicio" name="tipo_servicio">
                    <option value="0">Todos</option>
                    <?php foreach ($tiposServicio as $tipoServicio) : ?>
                        <option value="<?php echo $tipoServicio['id']; ?>" <?php echo $tipoServicio['id'] == $tipoServicio ? 'selected' : ''; ?>><?php echo $tipoServicio['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Buscar</button>
            <a href="pdf.php" class="btn btn-danger">Exportar a PDF</a>
        </form>
        <hr>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Tipo de Servicio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($turnosDistribucion as $distribucion) : ?>
                    <tr>
                        <td><?php echo $distribucion['id']; ?></td>
                        <td><?php echo $distribucion['nombre']; ?></td>
                        <td><?php echo $distribucion['descripcion']; ?></td>
                        <td><?php echo getTurnosTipoServicioById($distribucion['tipo_servicio'])['nombre']; ?></td>
                        <td>
                            <a href="update.php?id=<?php echo $distribucion['id']; ?>" class="btn btn-primary btn-sm">Editar</a>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="delete_id" value="<?php echo $distribucion['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro que desea eliminar esta distribución de turnos?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>