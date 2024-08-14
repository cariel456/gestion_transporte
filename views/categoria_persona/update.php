<?php
session_start();
$projectRoot = dirname(__FILE__, 3);
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once $projectRoot . '/includes/functions.php';

requireLogin();

$userPermissions = getUserPermissions();

//$requiredPermission = 'leer';
//if (!checkPermission('personal', $requiredPermission)) {
//    header("Location: " . BASE_URL . "/views/dashboard.php?error=permission_denied");
//    exit();
//}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre_categoria'];
    $descripcion = $_POST['descripcion_categoria'];
    
    if (updateCategoriaPersona($id, $nombre, $descripcion)) {
        header("Location: read.php");
        exit();
    } else {
        $error = "Error al actualizar la categoría de persona";
    }
}
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: read.php");
    exit();
}

$categoria = getCategoriaPersonaById($id);
if (!$categoria) {
    header("Location: read.php");
    exit();
}
include ROOT_PATH . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Categoría de Persona</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Actualizar Categoría de Persona</h1>
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $categoria['id']; ?>">
            <div class="mb-3">
                <label for="nombre_categoria" class="form-label">Nombre de la Categoría</label>
                <input type="text" class="form-control" id="nombre_categoria" name="nombre_categoria" value="<?php echo $categoria['nombre_categoria']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="descripcion_categoria" class="form-label">Descripción de la Categoría</label>
                <textarea class="form-control" id="descripcion_categoria" name="descripcion_categoria" rows="3"><?php echo $categoria['descripcion_categoria']; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="read.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>