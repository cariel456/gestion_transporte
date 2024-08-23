<?php
session_start();
    $projectRoot = dirname(__FILE__, 3);
    require_once dirname(__DIR__, 2) . '/config/config.php';
    require_once ROOT_PATH . '/includes/auth.php';
    require_once $projectRoot . '/includes/functions.php';

    $categorias = getAllTurnos();
    requireLogin();

    $userPermissions = getUserPermissions();
    
    //$requiredPermission = 'leer';
    //if (!checkPermission('personal', $requiredPermission)) {
    //    header("Location: " . BASE_URL . "/views/dashboard.php?error=permission_denied");
    //    exit();
    //}
    include ROOT_PATH . '/includes/header.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turnos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">

    <h1>Ingresar nuevo turno</h1>
        <?php //if ($_SESSION['user_permissions']['crear']): ?>
        <a href="create.php" class="btn btn-success mb-3">Crear</a>
        <?php //endif; ?>
        <a href="../../index.php" class="btn btn-secondary mb-3">Volver</a>

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
                <?php foreach ($categorias as $categoria) : ?>
                    <tr>
                        <td><?php echo $categoria['id']; ?></td>
                        <td><?php echo $categoria['nombre']; ?></td>
                        <td><?php echo $categoria['descripcion']; ?></td>
                        <td>
                            <?php //if ($_SESSION['user_permissions']['actualizar']): ?>
                            <a href="update.php?id=<?php echo $categoria['id']; ?>" class="btn btn-warning btn-sm">Actualizar</a>
                            <?php //endif; ?>
                            <?php //if ($_SESSION['user_permissions']['eliminar']): ?>
                            <a href="delete.php?id=<?php echo $categoria['id']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
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