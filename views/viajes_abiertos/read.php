<?php
session_start();
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';

requireLogin();
$viajes = getAllViajesAbiertos();

include ROOT_PATH . '/includes/header.php';
?>

<div class="container mt-5">
    <h2>Viajes Abiertos</h2>
    <a href="create.php" class="btn btn-primary mb-3">Crear Viaje Abierto</a>
    <a href="../index.php" class="btn btn-secondary mb-3">Volver</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Chofer 1</th>
                <th>Chofer 2</th>
                <th>Unidad</th>
                <th>Estado</th>
                <th>Chofer Actual</th>
                <th>Observaciones</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($viajes as $viaje): ?>
            <tr>
                <td><?php echo $viaje['id']; ?></td>
                <td><?php echo $viaje['nombre_chofer1']; ?></td>
                <td><?php echo $viaje['nombre_chofer2']; ?></td>
                <td><?php echo $viaje['codigo_unidad']; ?></td>
                <td><?php echo $viaje['estado_viaje_abierto']; ?></td>
                <td><?php echo $viaje['nombre_chofer_actual']; ?></td>
                <td><?php echo $viaje['observaciones']; ?></td>
                <td>
                    <a href="update.php?id=<?php echo $viaje['id']; ?>" class="btn btn-sm btn-primary">Actualizar</a>
                    <a href="delete.php?id=<?php echo $viaje['id']; ?>" class="btn btn-sm btn-danger">Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>