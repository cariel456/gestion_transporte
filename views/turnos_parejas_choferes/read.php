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

$turnos_parejas_choferes = getAllTurnosParejasChoferes();

include ROOT_PATH . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turnos de Parejas de Choferes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Turnos de Parejas de Choferes</h1>

        <?php //if (checkPermission('turnos_parejas_choferes', 'crear')): ?>
            <a href="create.php" class="btn btn-success mb-3">Crear Turno de Pareja de Choferes</a>
        <?php //endif; ?>

        <a href="../dashboard.php" class="btn btn-secondary mb-3">Volver</a>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Pareja</th>
                    <th>Chofer 1</th>
                    <th>Turno 1</th>
                    <th>Chofer 2</th>
                    <th>Turno 2</th>
                    <th>Descripción</th>
                    <th>Habilitado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($turnos_parejas_choferes as $turno) : ?>
                    <tr>
                        <td><?php echo $turno['fecha']; ?></td>
                        <td><?php echo $turno['chofer1_nombre'] . ' - ' . $turno['chofer2_nombre']; ?></td>
                        <td><?php echo $turno['chofer1_nombre']; ?></td>
                        <td><?php echo $turno['turno1_nombre']; ?></td>
                        <td><?php echo $turno['chofer2_nombre']; ?></td>
                        <td><?php echo $turno['turno2_nombre']; ?></td>
                        <td><?php echo $turno['descripcion']; ?></td>
                        <td><?php echo $turno['habilitado'] ? 'Sí' : 'No'; ?></td>
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