<?php
$projectRoot = dirname(__FILE__, 3); 
require_once dirname(__DIR__, 2) . '/config/config.php'; 
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';   
require_once ROOT_PATH . '/sec/auth_check.php';       
require_once $projectRoot . '/includes/functions.php'; 

requireLogin();

$categorias = getAllServicios();

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

<div class=" container mt-3">

<h1>Servicios</h1>
<?php if (in_array('escritura', $_SESSION['permissions']) || in_array('total', $_SESSION['permissions'])): ?>
            <a href="create.php" class="btn btn-primary">Crear Nuevo</a>
     <?php endif; ?>
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
                            <?php if (in_array('modificar', $_SESSION['permissions']) || in_array('total', $_SESSION['permissions'])): ?>
                                <a href="update.php?id=<?php echo $item['id']; ?>" class="btn btn-warning btn-sm">Actualizar</a>
                            <?php endif; ?>
                            <?php if (in_array('eliminar', $_SESSION['permissions']) || in_array('total', $_SESSION['permissions'])): ?>
                                <a href="delete.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
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
