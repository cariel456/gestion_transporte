<?php
session_start();
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';

requireLogin();

$niveles_urgencias = getAllNivelesUrgencias();

include ROOT_PATH . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Niveles de Urgencia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Niveles de Urgencia</h1>
        <a href="create.php" class="btn btn-success mb-3">Crear Nivel de Urgencia</a>
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
                <?php foreach ($niveles_urgencias as $nivel) : ?>
                    <tr>
                        <td><?php echo $nivel['id']; ?></td>
                        <td><?php echo $nivel['nombre_urgencia']; ?></td>
                        <td><?php echo $nivel['descripcion_urgencia']; ?></td>
                        <td>
                            <a href="update.php?id=<?php echo $nivel['id']; ?>" class="btn btn-warning btn-sm">Actualizar</a>
                            <a href="delete.php?id=<?php echo $nivel['id']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>