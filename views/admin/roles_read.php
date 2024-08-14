<?php
require_once '../../includes/functions.php';
requireLogin();

if (!checkPermission('admin', 'leer')) {
    header("Location: " . BASE_URL . "/views/dashboard.php");
    exit();
}

// Obtener todos los roles
$roles = getAllRoles();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Roles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Administración de Roles</h1>
        <a href="roles_create.php" class="btn btn-primary mb-3">Crear Nuevo Rol</a>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre del Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($roles as $role): ?>
                    <tr>
                        <td><?php echo $role['id']; ?></td>
                        <td><?php echo htmlspecialchars($role['nombre_rol']); ?></td>
                        <td>
                            <a href="roles_update.php?id=<?php echo $role['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                            <a href="roles_delete.php?id=<?php echo $role['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar este rol?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>