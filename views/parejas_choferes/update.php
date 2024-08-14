<?php
session_start();
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';

requireLogin();

if (!checkPermission('parejas_choferes', 'actualizar')) {
    //header("Location: " . BASE_URL . "/views/dashboard.php?error=permission_denied");
    //exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: read.php");
    exit();
}

$pareja = getParejaChoferesById($id);
if (!$pareja) {
    header("Location: read.php");
    exit();
}

$personal = getAllPersonal();
$unidades = getAllUnidades(); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'chofer_id' => $_POST['chofer_id'],
        'chofer2_id' => $_POST['chofer2_id'],
        'pareja' => $_POST['pareja'],
        'unidad' => $_POST['unidad'] 
    ];
    
    if (updateParejaChoferes($id, $data)) {
        header("Location: read.php");
        exit();
    } else {
        $error = "Error al actualizar la pareja de choferes";
    }
}

include ROOT_PATH . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Pareja de Choferes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Actualizar Pareja de Choferes</h1>

        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="pareja" class="form-label">Numero de Pareja</label>
                <input type="text" class="form-control" id="pareja" name="pareja" required>
            </div>  
            <div class="mb-3">
                <label for="chofer_id" class="form-label">Chofer 1</label>
                <select class="form-select" id="chofer_id" name="chofer_id" required>
                    <?php foreach ($personal as $persona) : ?>
                        <option value="<?php echo $persona['id']; ?>" <?php echo ($persona['id'] == $pareja['id_chofer']) ? 'selected' : ''; ?>><?php echo $persona['nombre_personal']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="chofer2_id" class="form-label">Chofer 2</label>
                <select class="form-select" id="chofer2_id" name="chofer2_id" required>
                    <?php foreach ($personal as $persona) : ?>
                        <option value="<?php echo $persona['id']; ?>" <?php echo ($persona['id'] == $pareja['id_chofer2']) ? 'selected' : ''; ?>><?php echo $persona['nombre_personal']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="unidad" class="form-label">Unidad</label>
                <select class="form-select" id="unidad" name="unidad" required>
                    <?php foreach ($unidades as $unidad) : ?>
                        <option value="<?php echo $unidad['id']; ?>" <?php echo ($unidad['id'] == $pareja['unidad']) ? 'selected' : ''; ?>><?php echo $unidad['codigo_interno']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="read.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>