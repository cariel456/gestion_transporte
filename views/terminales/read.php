<?php
$projectRoot = dirname(__FILE__, 3); 
require_once dirname(__DIR__, 2) . '/config/config.php'; 
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';   
require_once ROOT_PATH . '/sec/auth_check.php';       
require_once $projectRoot . '/includes/functions.php'; 
requireLogin();

$terminales = getAllTerminales();

include ROOT_PATH . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terminales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Terminales</h1>
        
        <?php //if (checkPermission('terminales', 'crear')): ?>
            <a href="create.php" class="btn btn-success mb-3">Crear Terminal</a>
            <a href="../../index.php" class="btn btn-secondary mb-3">Volver</a>
        <?php //endif; ?>
        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Localidad</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Web</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($terminales as $terminal): ?>
                    <tr>
                        <td><?php echo $terminal['id']; ?></td>
                        <td><?php echo $terminal['nombre_terminal']; ?></td>
                        <td><?php echo $terminal['descripcion_terminal']; ?></td>
                        <td><?php echo $terminal['nombre_localidad']; ?></td>
                        <td><?php echo $terminal['telefono']; ?></td>
                        <td><?php echo $terminal['correo']; ?></td>
                        <td><?php echo $terminal['web']; ?></td>
                        <td>
                            <?php //if (checkPermission('terminales', 'actualizar')): ?>
                                <a href="update.php?id=<?php echo $terminal['id']; ?>" class="btn btn-warning btn-sm">Actualizar</a>
                            <?php //endif; ?>
                            <?php //if (checkPermission('terminales', 'eliminar')): ?>
                                <a href="delete.php?id=<?php echo $terminal['id']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                            <?php //endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>