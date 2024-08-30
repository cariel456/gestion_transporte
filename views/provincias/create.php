<?php
$projectRoot = dirname(__FILE__, 3); 
require_once dirname(__DIR__, 2) . '/config/config.php'; 
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';   
require_once ROOT_PATH . '/sec/auth_check.php';       
require_once $projectRoot . '/includes/functions.php'; 

requireLogin();

$paises = getAllPaises();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nombre_provincia' => $_POST['nombre_provincia'],
        'descripcion_provincia' => $_POST['descripcion_provincia'],
        'pais' => $_POST['pais']
    ];
    
    if (createProvincia($data)) {
        header("Location: read.php");
        exit();
    } else {
        $error = "Error al crear la provincia";
    }
}

include ROOT_PATH . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Provincia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Crear Provincia</h1>
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="nombre_provincia" class="form-label">Nombre de la Provincia</label>
                <input type="text" class="form-control" id="nombre_provincia" name="nombre_provincia" required>
            </div>
            <div class="mb-3">
                <label for="descripcion_provincia" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion_provincia" name="descripcion_provincia" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label for="pais" class="form-label">País</label>
                <select class="form-select" id="pais" name="pais" required>
                    <option value="">Seleccione un país</option>
                    <?php foreach ($paises as $pais) : ?>
                        <option value="<?php echo $pais['id']; ?>"><?php echo $pais['nombre_pais']; ?></option>
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