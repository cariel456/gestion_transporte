<?php
session_start();
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';

requireLogin();

$localidades = getAllLocalidades();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nombre_terminal' => $_POST['nombre_terminal'],
        'descripcion_terminal' => $_POST['descripcion_terminal'],
        'localidad' => $_POST['localidad'],
        'telefono' => $_POST['telefono'],
        'telefono2' => $_POST['telefono2'],
        'correo' => $_POST['correo'],
        'correo2' => $_POST['correo2'],
        'web' => $_POST['web']
    ];
    
    if (createTerminal($data)) {
        header("Location: read.php");
        exit();
    } else {
        $error = "Error al crear la terminal";
    }
}

include ROOT_PATH . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Terminal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Crear Terminal</h1>
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="nombre_terminal" class="form-label">Nombre de la Terminal</label>
                <input type="text" class="form-control" id="nombre_terminal" name="nombre_terminal" required>
            </div>
            <div class="mb-3">
                <label for="descripcion_terminal" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion_terminal" name="descripcion_terminal" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label for="localidad" class="form-label">Localidad</label>
                <select class="form-control" id="localidad" name="localidad" required>
                    <?php foreach ($localidades as $localidad): ?>
                        <option value="<?php echo $localidad['id']; ?>"><?php echo $localidad['nombre_localidad']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="text" class="form-control" id="telefono" name="telefono">
            </div>
            <div class="mb-3">
                <label for="telefono2" class="form-label">Teléfono 2</label>
                <input type="text" class="form-control" id="telefono2" name="telefono2">
            </div>
            <div class="mb-3">
                <label for="correo" class="form-label">Correo</label>
                <input type="email" class="form-control" id="correo" name="correo">
            </div>
            <div class="mb-3">
                <label for="correo2" class="form-label">Correo 2</label>
                <input type="email" class="form-control" id="correo2" name="correo2">
            </div>
            <div class="mb-3">
                <label for="web" class="form-label">Sitio Web</label>
                <input type="url" class="form-control" id="web" name="web">
            </div>
            <button type="submit" class="btn btn-primary">Crear</button>
            <a href="read.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>