<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';
include ROOT_PATH . '/includes/header.php';

requireLogin();

$userPermissions = getUserPermissions();

// Verifica el permiso necesario para la página actual
$requiredPermission = 'leer';
if (!checkPermission($requiredPermission)) {
    header("Location: " . BASE_URL . "/views/dashboard.php?error=permission_denied");
    exit();
}

$horarios = getAllHorariosInterurbanos();
?>

<div class="container mt-5">
    <h2>Horarios Interurbanos</h2>
    <?php if (checkPermission('crear')): ?>
        <a href="create.php" class="btn btn-primary mb-3">Crear Nuevo Horario</a>
    <?php endif; ?>
    <a href="../../index.php" class="btn btn-secondary mb-3">Volver</a>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Terminal Salida</th>
                <th>Hora Salida</th>
                <th>Terminal Llegada</th>
                <th>Hora Llegada</th>
                <th>Habilitado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($horarios as $horario): ?>
            <tr>
                <td><?php echo $horario['id']; ?></td>
                <td><?php echo $horario['terminal_salida_nombre']; ?></td>
                <td><?php echo $horario['hora_salida']; ?></td>
                <td><?php echo $horario['terminal_llegada_nombre']; ?></td>
                <td><?php echo $horario['hora_llegada']; ?></td>
                <td><?php echo $horario['habilitado'] ? 'Sí' : 'No'; ?></td>
                <td>
                    <?php if (checkPermission('actualizar')): ?>
                    <a href="update.php?id=<?php echo $horario['id']; ?>" class="btn btn-warning btn-sm">Actualizar</a>
                    <?php endif; ?>
                    <?php if (checkPermission('eliminar')): ?>
                    <a href="delete.php?id=<?php echo $horario['id']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>