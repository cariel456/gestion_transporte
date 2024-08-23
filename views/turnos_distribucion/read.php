<?php
// Incluir los archivos necesarios
session_start();
$projectRoot = dirname(__FILE__, 3);
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once $projectRoot . '/includes/functions.php';

// Verificar si el usuario está autenticado
requireLogin();

// Manejar la eliminación de registros
//if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
//    $id = $_POST['delete_id'];
//    if (deleteTurnosDistribucion($id)) {
//        $message = "Distribución de turnos eliminada correctamente.";
//    } else {
//        $error = "Error al eliminar la distribución de turnos.";
//    }
//}

// Obtener la lista de distribuciones de turnos
$turnosDistribucion = getTurnosDistribucion();
include ROOT_PATH . '/includes/header.php'; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Distribuciones de Turnos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Distribuciones de Turnos</h1>
        <?php if (isset($message)) : ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php elseif (isset($error)) : ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <div class="d-flex justify-content-between mb-3">
            <a href="create.php" class="btn btn-success">Crear Distribución de Turnos</a>
            <div>
                <a href="search.php" class="btn btn-primary">Buscar Distribución de Turnos</a>
                <a href="pdf.php" class="btn btn-danger">Exportar a PDF</a>
            </div>
        </div>
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
                        <form method="GET" action="view.php" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $distribucion['id']; ?>">
                            <button type="submit" class="btn btn-info btn-sm">Ver</button>
                        </form>
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