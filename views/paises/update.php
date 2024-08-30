<?php
$projectRoot = dirname(__FILE__, 3); 
require_once dirname(__DIR__, 2) . '/config/config.php'; 
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';   
require_once ROOT_PATH . '/sec/auth_check.php';       
require_once $projectRoot . '/includes/functions.php'; 

requireLogin();

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: read.php");
    exit();
}

$pais = getPaisById($id);
if (!$pais) {
    header("Location: read.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'id' => $id,
        'nombre_pais' => $_POST['nombre_pais'],
        'descripcion_pais' => $_POST['descripcion_pais']
    ];
    
    if (updatePais($data)) {
        echo "asdad";
        header("Location: read.php");
        exit();
    } else {
        $error = "Error al actualizar el país";
    }
}

include ROOT_PATH . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar País</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Actualizar País</h1>
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="nombre_pais" class="form-label">Nombre del País</label>
                <input type="text" class="form-control" id="nombre_pais" name="nombre_pais" value="<?php echo $pais['nombre_pais']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="descripcion_pais" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion_pais" name="descripcion_pais" rows="3"><?php echo $pais['descripcion_pais']; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="read.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>