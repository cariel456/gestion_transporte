<?php
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';

requireLogin();

$userPermissions = getUserPermissions();

// Verifica el permiso necesario para la página actual
$requiredPermission = 'actualizar'; // 
if (!checkPermission($requiredPermission)) {
    header("Location: " . BASE_URL . "/views/dashboard.php?error=permission_denied");
    exit();
}
include ROOT_PATH . '/includes/header.php'; 

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: read.php");
    exit();
}

$rol = getRoleById($id);
if (!$rol) {
    header("Location: read.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_rol = $_POST['nombre_rol'];
    $leer = isset($_POST['leer']) ? 1 : 0;
    $crear = isset($_POST['crear']) ? 1 : 0;
    $actualizar = isset($_POST['actualizar']) ? 1 : 0;
    $eliminar = isset($_POST['eliminar']) ? 1 : 0;
    
    if (updateRole($id, $nombre_rol, $leer, $crear, $actualizar, $eliminar)) {
        header("Location: read.php");
        exit();
    } else {
        $error = "Error al actualizar el rol";
    } 
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Rol</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Actualizar Rol</h1>
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="nombre_rol" class="form-label">Nombre Rol</label>
                <input type="text" class="form-control" id="nombre_rol" name="nombre_rol" value="<?php echo $rol['nombre_rol']; ?>" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="leer" name="leer" <?php echo $rol['leer'] ? 'checked' : ''; ?>>
                <label class="form-check-label" for="leer">Leer</label>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="crear" name="crear" <?php echo $rol['crear'] ? 'checked' : ''; ?>>
                <label class="form-check-label" for="crear">Crear</label>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="actualizar" name="actualizar" <?php echo $rol['actualizar'] ? 'checked' : ''; ?>>
                <label class="form-check-label" for="actualizar">Actualizar</label>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="eliminar" name="eliminar" <?php echo $rol['eliminar'] ? 'checked' : ''; ?>>
                <label class="form-check-label" for="eliminar">Eliminar</label>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="read.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>