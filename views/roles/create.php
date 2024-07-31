<?php
require_once '../../config/config.php';
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


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_rol = $_POST['nombre_rol'];
    $leer = isset($_POST['leer']) ? 1 : 0;
    $crear = isset($_POST['crear']) ? 1 : 0;
    $actualizar = isset($_POST['actualizar']) ? 1 : 0;
    $eliminar = isset($_POST['eliminar']) ? 1 : 0;

    if (createRole($nombre_rol, $leer, $crear, $actualizar, $eliminar)) {
        header("Location: read.php");
        exit();
    } else {
        $error = "Error al crear el rol";
    }
}
?>

<h2>Roles</h2>
<?php if (checkPermission('crear')): ?>
    <a href="create.php" class="btn btn-primary mb-3">Crear Nuevo Rol</a>
<?php endif; ?>

<form method="POST">
    <div class="mb-3">
        <label for="nombre_rol" class="form-label">Nombre del Rol</label>
        <input type="text" class="form-control" id="nombre_rol" name="nombre_rol" required>
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="leer" name="leer">
        <label class="form-check-label" for="leer">Leer</label>
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="crear" name="crear">
        <label class="form-check-label" for="crear">Crear</label>
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="actualizar" name="actualizar">
        <label class="form-check-label" for="actualizar">Actualizar</label>
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="eliminar" name="eliminar">
        <label class="form-check-label" for="eliminar">Eliminar</label>
    </div>
    <a href="read.php" class="btn btn-secondary">Cancelar</a>
</form>