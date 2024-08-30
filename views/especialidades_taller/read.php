<?php
$projectRoot = dirname(__FILE__, 3); 
require_once dirname(__DIR__, 2) . '/config/config.php'; 
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';   
require_once ROOT_PATH . '/sec/auth_check.php';       
require_once $projectRoot . '/includes/functions.php'; 
requireLogin();

$especialidades = getAllEspecialidades();
include ROOT_PATH . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Especialidades Taller</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Especialidades Taller</h1>
        <a href="create.php" class="btn btn-primary mb-3">Crear Nueva Especialidad</a>
        <a href="../../index.php" class="btn btn-secondary mb-3">Volver</a>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Habilitado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($especialidades as $especialidad) : ?>
                    <tr>
                        <td><?php echo $especialidad['id']; ?></td>
                        <td><?php echo $especialidad['nombre_especialidad']; ?></td>
                        <td><?php echo $especialidad['descripcion_especialidad']; ?></td>
                        <td><?php echo $especialidad['habilitado'] ? 'Sí' : 'No'; ?></td>
                        <td>
                            <a href="update.php?id=<?php echo $especialidad['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                            <a href="delete.php?id=<?php echo $especialidad['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar esta especialidad?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>