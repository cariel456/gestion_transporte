<?php
$projectRoot = dirname(__FILE__, 3);
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once $projectRoot . '/includes/functions.php';
include ROOT_PATH . '/includes/header.php';

$unidades = getAllUnidades();

requireLogin();
    $userPermissions = getUserPermissions();

    // Verifica el permiso necesario para la página actual
    $requiredPermission = 'leer'; // 
    if (!checkPermission($requiredPermission)) {
        header("Location: " . BASE_URL . "/views/dashboard.php?error=permission_denied");
        exit();
    }

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
        <?php if (checkPermission('crear')): ?>
            <a type="submit" href="create.php" class="btn btn-primary mb-3">Ingresar nueva unidad</a>
        <?php endif; ?>
        <a href="../../index.php" class="btn btn-secondary mb-3">Volver</a>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Código Interno</th>
                    <th>Descripción</th>
                    <th>Habilitado</th>
                    <th>Número de Unidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($unidades as $unidad) : ?>
                    <tr>
                        <td><?php echo $unidad['id']; ?></td>
                        <td><?php echo $unidad['codigo_interno']; ?></td>
                        <td><?php echo $unidad['descripcion']; ?></td>
                        <td><?php echo $unidad['habilitado'] ? 'Sí' : 'No'; ?></td>
                        <td><?php echo $unidad['numero_unidad']; ?></td>
                        <td>
                            <?php if ($_SESSION['user_permissions']['actualizar']): ?>
                            <a href="update.php?id=<?php echo $unidad['id']; ?>" class="btn btn-warning btn-sm">Actualizar</a>
                            <?php endif; ?>
                            <?php if ($_SESSION['user_permissions']['eliminar']): ?>
                            <a href="delete.php?id=<?php echo $unidad['id']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>