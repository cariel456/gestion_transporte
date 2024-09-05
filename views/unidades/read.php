<?php
$projectRoot = dirname(__FILE__, 3); 
require_once dirname(__DIR__, 2) . '/config/config.php'; 
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';   
require_once ROOT_PATH . '/sec/auth_check.php';       
require_once $projectRoot . '/includes/functions.php'; 

requireLogin();

// Inicializar la variable de búsqueda
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Obtener las unidades basadas en la búsqueda
if (!empty($search)) {
    $unidades = getUnidadById($search);
} else {
    $unidades = getAllUnidades();
}

$rol_id = $_SESSION['rol_id'];
include ROOT_PATH . '/includes/header.php';
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

        <div class="row mb-3">
            <div class="col-md-6">
            <?php if ($rol_id == 1): ?>
                <a href="create.php" class="btn btn-success mb-3">Crear Unidad</a>
            <?php //endif?>
            <?php elseif ($rol_id == 2): ?>
                <a href="create.php" class="btn btn-success mb-3">Crear Unidad</a>
            <?php endif; ?>
                <a href="<?php echo BASE_URL; ?>/includes/header.php" class="btn btn-secondary">Volver</a>
            </div>
            <!--<div class="col-md-6">
                <form action="" method="GET" class="d-flex">
                    <input type="text" name="search" class="form-control me-2" placeholder="Buscar por código interno" value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </form>
            </div>-->
        </div>

        <?php if (empty($unidades)): ?>
            <p>No se encontraron unidades.</p>
        <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Código Interno</th>
                        <th>Descripción</th>
                        <th>Número Unidad</th>
                        <th>Habilitado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($unidades as $unidad) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($unidad['codigo_interno']); ?></td>
                            <td><?php echo htmlspecialchars($unidad['descripcion']); ?></td>
                            <td><?php echo htmlspecialchars($unidad['numero_unidad']); ?></td>
                            <td><?php echo $unidad['habilitado'] ? 'Sí' : 'No'; ?></td>
                            <td>
                            <?php if ($rol_id == 1):?>
                                <a href="update.php?id=<?php echo $solicitud['id']; ?>" class="btn btn-warning btn-sm">Actualizar</a>
                            <?php endif;?>
                            <?php if ($rol_id == 1): ?>
                                <a href="delete.php?id=<?php echo $solicitud['id']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                            <?php endif;?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>