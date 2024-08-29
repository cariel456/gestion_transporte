<?php
session_start();
$projectRoot = dirname(__FILE__, 3);
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once $projectRoot . '/includes/functions.php';

// Verificar si el usuario está autenticado
requireLogin();

// Obtener la lista de distribuciones de turnos
$distribuciones = getTurnosDistribucion();

// Manejar la búsqueda si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $tipo_servicio = $_POST['tipo_servicio'] ?? 0;
    
    $distribuciones = searchTurnosDistribucion($nombre, $descripcion, $tipo_servicio);
}

include ROOT_PATH . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Distribuciones de Turnos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Lista de Distribuciones de Turnos</h1>
        
        <!-- Formulario de búsqueda -->
        <form method="POST" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" name="nombre" class="form-control" placeholder="Nombre">
                </div>
                <div class="col-md-3">
                    <input type="text" name="descripcion" class="form-control" placeholder="Descripción">
                </div>
                <div class="col-md-3">
                    <select name="tipo_servicio" class="form-control">
                        <option value="">Todos los tipos de servicio</option>
                        <?php foreach (getTurnosTiposServicios() as $tipo): ?>
                            <option value="<?php echo $tipo['id']; ?>"><?php echo $tipo['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
            </div>
        </form>

        <table class="table table-striped">
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
                <?php foreach ($distribuciones as $distribucion): ?>
                    <tr>
                        <td><?php echo $distribucion['id']; ?></td>
                        <td><?php echo $distribucion['nombre']; ?></td>
                        <td><?php echo $distribucion['descripcion']; ?></td>
                        <td><?php echo getTurnosTipoServicioById($distribucion['tipo_servicio'])['nombre']; ?></td>
                        <td>
                            <a href="view.php?id=<?php echo $distribucion['id']; ?>" class="btn btn-primary btn-sm">Ver</a>
                            <a href="edit.php?id=<?php echo $distribucion['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="delete.php?id=<?php echo $distribucion['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que quieres eliminar esta distribución?')">Eliminar</a>
                            <a href="exportar_pdf.php?id=<?php echo $distribucion['id']; ?>" class="btn btn-info btn-sm" target="_blank">Exportar a PDF</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <a href="create.php" class="btn btn-success">Crear Nueva Distribución de Turnos</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>