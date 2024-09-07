<?php
$projectRoot = dirname(__FILE__, 3); 
require_once dirname(__DIR__, 2) . '/config/config.php'; 
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';   
require_once ROOT_PATH . '/sec/auth_check.php';       
require_once $projectRoot . '/includes/functions.php'; 

requireLogin();

// Obtener la lista de distribuciones de turnos
$distribuciones = getTurnosDistribucion();

// Manejar la búsqueda si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $tipo_servicio = $_POST['tipo_servicio'] ?? 0;
    $fecha_inicio = $_POST['fecha_inicio'] ?? '';
    $fecha_fin = $_POST['fecha_fin'] ?? '';
    $personal = $_POST['personal'] ?? '';
    
    $distribuciones = searchTurnosDistribucion($nombre, $descripcion, $tipo_servicio, $fecha_inicio, $fecha_fin, $personal);
}

// Obtener la lista de personal para el selector
$personal_list = getAllPersonal();

include ROOT_PATH . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Distribuciones de Turnos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .search-form {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Lista de Distribuciones de Turnos</h1>
        
        <form class="search-form" method="POST">
            <div class="row g-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="nombre" placeholder="Nombre" value="<?php echo $_POST['nombre'] ?? ''; ?>">
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" name="descripcion" placeholder="Descripción" value="<?php echo $_POST['descripcion'] ?? ''; ?>">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="tipo_servicio">
                        <option value="">Tipo de Servicio</option>
                        <?php
                        $tipos_servicio = getTurnosServicios();
                        foreach ($tipos_servicio as $tipo) {
                            $selected = ($_POST['tipo_servicio'] ?? '') == $tipo['id'] ? 'selected' : '';
                            echo "<option value='{$tipo['id']}' {$selected}>{$tipo['nombre']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="personal">
                        <option value="">Personal</option>
                        <?php
                        foreach ($personal_list as $persona) {
                            $selected = ($_POST['personal'] ?? '') == $persona['id'] ? 'selected' : '';
                            echo "<option value='{$persona['id']}' {$selected}>{$persona['nombre_personal']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control datepicker" name="fecha_inicio" placeholder="Fecha Inicio" value="<?php echo $_POST['fecha_inicio'] ?? ''; ?>">
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control datepicker" name="fecha_fin" placeholder="Fecha Fin" value="<?php echo $_POST['fecha_fin'] ?? ''; ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Buscar</button>
                </div>
            </div>
        </form>
        
        <table class="table table-striped table-hover">
            <thead class="table-dark">
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
                            <div class="btn-group" role="group">
                                <a href="view.php?id=<?php echo $distribucion['id']; ?>" class="btn btn-primary btn-sm">Ver</a>
                                <?php if (in_array('escritura', $_SESSION['permissions']) || in_array('total', $_SESSION['permissions'])): ?>
                                    <a href="update.php?id=<?php echo $item['id']; ?>" class="btn btn-warning btn-sm">Actualizar</a>
                                    <a href="delete.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                                 <?php endif; ?>
                                <a href="exportar_pdf.php?id=<?php echo $distribucion['id']; ?>" class="btn btn-info btn-sm" target="_blank">Exportar a PDF</a>
                                <a href="consultas.php?id=<?php echo $distribucion['id']; ?>" class="btn btn-secondary btn-sm">Consultas</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php if (in_array('escritura', $_SESSION['permissions']) || in_array('total', $_SESSION['permissions'])): ?>
            <a href="create.php" class="btn btn-primary">Crear Nuevo</a>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr(".datepicker", {
                dateFormat: "Y-m-d",
            });
        });
    </script>
</body>
</html>