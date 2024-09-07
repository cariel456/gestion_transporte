<?php
    require_once '../../config/config.php';
    require_once ROOT_PATH . '/includes/auth.php';
    require_once ROOT_PATH . '/includes/functions.php';

    $roles = getAllRoles();
    requireLogin();

    $userPermissions = getUserPermissions();

    // Verifica el permiso necesario para la página actual
    $requiredPermission = 'leer'; // 
    if (!checkPermission($requiredPermission)) {
        header("Location: " . BASE_URL . "/views/dashboard.php?error=permission_denied");
        exit();
    }
    
    include ROOT_PATH . '/includes/header.php'; 
?>

<div class="container mt-5">

    <h2>Roles</h2>
    <?php if (in_array('escritura', $_SESSION['permissions']) || in_array('total', $_SESSION['permissions'])): ?>
            <a href="create.php" class="btn btn-primary">Crear Nuevo</a>
        <?php endif; ?>
    <a href="../../index.php" class="btn btn-secondary mb-3">Volver</a>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Leer</th>
            <th>Crear</th>
            <th>Actualizar</th>
            <th>Eliminar</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($roles as $role): ?>
        <tr>
            <td><?php echo $role['id']; ?></td>
            <td><?php echo $role['nombre_rol']; ?></td>
            <td><?php echo $role['leer'] ? 'Sí' : 'No'; ?></td>
            <td><?php echo $role['crear'] ? 'Sí' : 'No'; ?></td>
            <td><?php echo $role['actualizar'] ? 'Sí' : 'No'; ?></td>
            <td><?php echo $role['eliminar'] ? 'Sí' : 'No'; ?></td>
            <td>
                            <?php if (in_array('modificar', $_SESSION['permissions']) || in_array('total', $_SESSION['permissions'])): ?>
                                <a href="update.php?id=<?php echo $item['id']; ?>" class="btn btn-warning btn-sm">Actualizar</a>
                            <?php endif; ?>
                            <?php if (in_array('eliminar', $_SESSION['permissions']) || in_array('total', $_SESSION['permissions'])): ?>
                                <a href="delete.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                            <?php endif; ?>
                        </td>
            </tr>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>