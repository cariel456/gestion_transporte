<?php
session_start();
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';

requireLogin();

$userPermissions = getUserPermissions();

if (!checkPermission('turnos_parejas_choferes', 'leer')) {
   // header("Location: " . BASE_URL . "/views/dashboard.php?error=permission_denied");
   // exit();
}

$turnos_parejas_choferes = getAllUnidades();

include ROOT_PATH . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unidades</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Unidades</h1>

        <?php //if (checkPermission('turnos_parejas_choferes', 'crear')): ?>
            <a href="create.php" class="btn btn-success mb-3">Crear Unidad</a>
        <?php //endif; ?>

        <a href="../dashboard.php" class="btn btn-secondary mb-3">Volver</a>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Código Interno</th>
                    <th>Descripción</th>
                    <th>Número Unidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($turnos_parejas_choferes as $turno) : ?>
                    <tr>
                        <td><?php echo $turno['codigo_interno']; ?></td>
                        <td><?php echo $turno['descripcion']; ?></td>
                        <td><?php echo $turno['numero_unidad']; ?></td>
                        <td>
                            <?php //if (checkPermission('turnos_parejas_choferes', 'actualizar')): ?>
                                <a href="update.php?id=<?php echo $turno['id']; ?>" class="btn btn-warning btn-sm">Actualizar</a>
                            <?php //endif; ?>
                            <?php //if (checkPermission('turnos_parejas_choferes', 'eliminar')): ?>
                                <a href="delete.php?id=<?php echo $turno['id']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
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