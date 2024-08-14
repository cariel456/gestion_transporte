<?php
require_once '../../includes/functions.php';
requireLogin();

if (!checkPermission('admin', 'leer')) {
    header("Location: " . BASE_URL . "/views/dashboard.php");
    exit();
}

// Obtener todos los usuarios
$usuarios = getAllUsers();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Administración de Usuarios</h1>
        <a href="usuarios_create.php" class="btn btn-primary mb-3">Crear Nuevo Usuario</a>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de Usuario</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?php echo $usuario['id']; ?></td>
                        <td><?php echo htmlspecialchars($usuario['nombre_usuario']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['nombre_rol']); ?></td>
                        <td>
                            <a href="usuarios_update.php?id=<?php echo $usuario['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                            <a href="usuarios_delete.php?id=<?php echo $usuario['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar este usuario?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>