<?php
session_start();
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';

requireLogin();

$parejas_choferes = getAllUnidades();

include ROOT_PATH . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parejas de Choferes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Parejas de Choferes</h1>

        <?php if (in_array('escritura', $_SESSION['permissions']) || in_array('total', $_SESSION['permissions'])): ?>
            <a href="create.php" class="btn btn-primary">Crear Nuevo</a>
        <?php endif; ?>

        <a href="../dashboard.php" class="btn btn-secondary mb-3">Volver</a>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Pareja</th>
                    <th>Chofer 1</th>
                    <th>Chofer 2</th>
                    <th>Unidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($parejas_choferes as $pareja) : ?>
                    <tr>
                        <td><?php echo $pareja['pareja']; ?></td>
                        <td><?php echo $pareja['chofer1_nombre']; ?></td>
                        <td><?php echo $pareja['chofer2_nombre']; ?></td>
                        <td><?php echo $pareja['unidad_codigo']; ?></td>
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