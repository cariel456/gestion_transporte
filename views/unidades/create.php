<?php
session_start();
$projectRoot = dirname(__FILE__, 3);
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once $projectRoot . '/includes/functions.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo_interno = $_POST['codigo_interno'];
    $descripcion = $_POST['descripcion'];
    $numero_unidad = $_POST['numero_unidad'];
    
    if (createUnidad($codigo_interno, $descripcion, $numero_unidad)) {
        header("Location: read.php");
        exit();
    } else {
        $error = "Error al crear la unidad";
    }
}
include ROOT_PATH . '/includes/header.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Unidad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Crear Unidad</h1>
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="codigo_interno" class="form-label">Código Interno</label>
                <input type="text" class="form-control" id="codigo_interno" name="codigo_interno" required>
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <input type="text" class="form-control" id="descripcion" name="descripcion" >
            </div>
            <div class="mb-3">
                <label for="numero_unidad" class="form-label">Número de Unidad</label>
                <input type="number" class="form-control" id="numero_unidad" name="numero_unidad" >
            </div>
            <button type="submit" class="btn btn-primary">Crear</button>
            <a href="read.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>