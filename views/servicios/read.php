<?php
session_start();
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';

requireLogin();

if (!checkPermission('paises', 'leer')) {
    //header("Location: " . BASE_URL . "/views/dashboard.php?error=permission_denied");
    //exit();
}

$paises = getAllServicios();

include ROOT_PATH . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Servicios</h1>

        <?php //if (checkPermission('paises', 'crear')): ?>
            <a href="create.php" class="btn btn-success mb-3">Crear Servicio</a>
            <a href="../../index.php" class="btn btn-secondary mb-3">Volver</a>
        <?php //endif; ?>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($paises as $pais): ?>
                    <tr>
                        <td><?php echo $pais['id']; ?></td>
                        <td><?php echo $pais['nombre']; ?></td>
                        <td><?php echo $pais['descripcion']; ?></td>
                        <td>
                            <?php //if (checkPermission('paises', 'actualizar')): ?>
                                <a href="update.php?id=<?php echo $pais['id']; ?>" class="btn btn-warning btn-sm">Actualizar</a>
                            <?php //endif; ?>
                            <?php //if (checkPermission('paises', 'eliminar')): ?>
                                <a href="delete.php?id=<?php echo $pais['id']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
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