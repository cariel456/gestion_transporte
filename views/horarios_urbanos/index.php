<?php
session_start();
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';
$horarios = obtenerHorariosUrbanos();
$localidades = getAllLocalidades();
include ROOT_PATH . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horarios Urbanos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Horarios Urbanos</h1>
        <a href="create.php" class="btn btn-primary mb-3">Crear Nuevo Horario</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Localidad</th>
                    <th>Rango Horario</th>
                    <th>Zona Horaria</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($horarios as $horario): ?>
                <tr>
                    <td><?php echo $horario['id']; ?></td>
                    <td>
                        <?php
                        $localidad = array_filter($localidades, function($loc) use ($horario) {
                            return $loc['id'] == $horario['localidad'];
                        });
                        $localidad = reset($localidad);
                        echo $localidad ? $localidad['nombre_localidad'] : 'N/A';
                        ?>
                    </td>
                    <td><?php //echo obtenerRangosHorarios($horario['nombre']); ?></td>
                    <td><?php //echo obtenerZonasHorarios($horario['nombre']); ?></td>
                    <td><?php echo $horario['descripcion']; ?></td>
                    <td>
                        <a href="view.php?id=<?php echo $horario['id']; ?>" class="btn btn-info btn-sm">Ver</a>
                        <a href="edit.php?id=<?php echo $horario['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="delete.php?id=<?php echo $horario['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>