<?php
session_start();
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nombre_urgencia' => $_POST['nombre_urgencia'],
        'descripcion_urgencia' => $_POST['descripcion_urgencia']
    ];
    
    if (createNivelUrgencia($data)) {
        header("Location: read.php");
        exit();
    } else {
        $error = "Error al crear el nivel de urgencia";
    }
}

include ROOT_PATH . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nivel de Urgencia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Crear Nivel de Urgencia</h1>
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="nombre_urgencia" class="form-label">Nombre del Nivel de Urgencia</label>
                <input type="text" class="form-control" id="nombre_urgencia" name="nombre_urgencia" required>
            </div>
            <div class="mb-3">
                <label for="descripcion_urgencia" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion_urgencia" name="descripcion_urgencia" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Crear</button>
            <a href="read.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>