<?php
$projectRoot = dirname(__FILE__, 3);
require_once $projectRoot . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre_especialidad'];
    $descripcion = $_POST['descripcion_especialidad'];
    
    if (updateEspecialidad($id, $nombre, $descripcion)) {
        header("Location: read.php");
        exit();
    } else {
        $error = "Error al actualizar la especialidad";
    }
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: read.php");
    exit();
}

$especialidad = getEspecialidadById($id);
if (!$especialidad) {
    header("Location: read.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Especialidad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Actualizar Especialidad</h1>
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $especialidad['id']; ?>">
            <div class="mb-3">
                <label for="nombre_especialidad" class="form-label">Nombre de la Especialidad</label>
                <input type="text" class="form-control" id="nombre_especialidad" name="nombre_especialidad" value="<?php echo $especialidad['nombre_especialidad']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="descripcion_especialidad" class="form-label">Descripción de la Especialidad</label>
                <textarea class="form-control" id="descripcion_especialidad" name="descripcion_especialidad" rows="3"><?php echo $especialidad['descripcion_especialidad']; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>