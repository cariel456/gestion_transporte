<?php
$projectRoot = dirname(__FILE__, 3);
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once $projectRoot . '/includes/functions.php';
require_once ROOT_PATH . '/includes/auth.php';

requireLogin();

// Para create.php
checkPermission('crear');

include ROOT_PATH . '/includes/header.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre_categoria'];
    $descripcion = $_POST['descripcion_categoria'];
    
    if (createCategoriaPersona($nombre, $descripcion)) {
        header("Location: read.php");
        exit();
    } else {
        $error = "Error al crear la categoría de persona";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Categoría de Persona</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Crear Categoría de Persona</h1>
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="nombre_categoria" class="form-label">Nombre de la Categoría</label>
                <input type="text" class="form-control" id="nombre_categoria" name="nombre_categoria" required>
            </div>
            <div class="mb-3">
                <label for="descripcion_categoria" class="form-label">Descripción de la Categoría</label>
                <textarea class="form-control" id="descripcion_categoria" name="descripcion_categoria" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Crear</button>
            <a href="read.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>