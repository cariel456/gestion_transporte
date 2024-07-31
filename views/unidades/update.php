<?php
$projectRoot = dirname(__FILE__, 3);
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once $projectRoot . '/includes/functions.php';
include ROOT_PATH . '/includes/header.php';


$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: read.php");
    exit();
}

$unidad = getUnidadById($id);
if (!$unidad) {
    header("Location: read.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo_interno = $_POST['codigo_interno'];
    $descripcion = $_POST['descripcion'];
    $habilitado = isset($_POST['habilitado']) ? 1 : 0;
    $numero_unidad = $_POST['numero_unidad'];
    
    if (updateUnidad($id, $codigo_interno, $descripcion, $habilitado, $numero_unidad)) {
        header("Location: read.php");
        exit();
    } else {
        $error = "Error al actualizar la unidad";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Unidad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Actualizar Unidad</h1>
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="codigo_interno" class="form-label">Código Interno</label>
                <input type="text" class="form-control" id="codigo_interno" name="codigo_interno" value="<?php echo $unidad['codigo_interno']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <input type="text" class="form-control" id="descripcion" name="descripcion" value="<?php echo $unidad['descripcion']; ?>" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="habilitado" name="habilitado" <?php echo $unidad['habilitado'] ? 'checked' : ''; ?>>
                <label class="form-check-label" for="habilitado">Habilitado</label>
            </div>
            <div class="mb-3">
                <label for="numero_unidad" class="form-label">Número de Unidad</label>
                <input type="number" class="form-control" id="numero_unidad" name="numero_unidad" value="<?php echo $unidad['numero_unidad']; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="read.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>