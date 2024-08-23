<?php
session_start();
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';

requireLogin();

$userPermissions = getUserPermissions();

$requiredPermission = 'leer';
if (!checkPermission('personal', $requiredPermission)) {
    //header("Location: " . BASE_URL . "/views/dashboard.php?error=permission_denied");
    //exit();
}

$usuarios = getAllUsers();

include ROOT_PATH . '/includes/header.php'; 

?>

<div class="container mt-5">
    <h2>Usuarios</h2>
    <?php //if (isset($_SESSION['user_permissions']['crear']) && $_SESSION['user_permissions']['crear']): ?>
        <a type="submit" href="create.php" class="btn btn-primary mb-3">Crear Nuevo Usuario</a>
    <?php //endif; ?>
    <a href="../../index.php" class="btn btn-secondary mb-3">Volver</a>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Descripcion</th>
                <th>Habilitado</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td><?php echo $usuario['id']; ?></td>
                <td><?php echo $usuario['nombre_usuario']; ?></td>
                <td><?php echo $usuario['descripcion_usuario'] ?></td>
                <td><?php echo $usuario['habilitado'] ? 'Sí' : 'No'; ?></td>
                <td><?php echo $usuario['rol_id'] ?></td>
                <td>
                    <?php //if (isset($_SESSION['user_permissions']['actualizar']) && $_SESSION['user_permissions']['actualizar']): ?>
                        <a href="update.php?id=<?php echo $usuario['id']; ?>" class="btn btn-warning btn-sm">Actualizar</a>
                    <?php //endif; ?>
                    <?php //if (isset($_SESSION['user_permissions']['eliminar']) && $_SESSION['user_permissions']['eliminar']): ?>
                        <a href="delete.php?id=<?php echo $usuario['id']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                    <?php // endif; ?>
            </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>