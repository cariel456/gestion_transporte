<?php
session_start();
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';

requireLogin();

$provincias = getAllProvincias();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nombre_localidad' => $_POST['nombre_localidad'],
        'descripcion_localidad' => $_POST['descripcion_localidad'],
        'provincia' => $_POST['provincia']
    ];
    
    if (createLocalidad($data)) {
        header("Location: read.php");
        exit();
    } else {
        $error = "Error al crear la localidad";
    }
}

include ROOT_PATH . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Localidad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Crear Localidad</h1>
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="nombre_localidad" class="form-label">Nombre de la Localidad</label>
                <input type="text" class="form-control" id="nombre_localidad" name="nombre_localidad" required>
            </div>
            <div class="mb-3">
                <label for="descripcion_localidad" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion_localidad" name="descripcion_localidad" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label for="provincia" class="form-label">Provincia</label>
                <select class="form-select" id="provincia" name="provincia" required>
                    <option value="">Seleccione una provincia</option>
                    <?php foreach ($provincias as $provincia) : ?>
                        <option value="<?php echo $provincia['id']; ?>"><?php echo $provincia['nombre_provincia']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Crear</button>
            <a href="read.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>