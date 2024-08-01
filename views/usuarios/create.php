<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';

include ROOT_PATH . '/includes/header.php'; 

$roles = getAllRoles(); // Asumiendo que tienes una función para obtener todos los roles

requireLogin();

$userPermissions = getUserPermissions();

// Verifica el permiso necesario para la página actual
$requiredPermission = 'leer'; // 
if (!checkPermission($requiredPermission)) {
    header("Location: " . BASE_URL . "/views/dashboard.php?error=permission_denied");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_usuario = $_POST['nombre_usuario'];
    $descripcion_usuario = $_POST['descripcion_usuario'];
    $habilitado = isset($_POST['habilitado']) ? 1 : 0;
    $role_id = $_POST['role_id'];
    $password = $_POST['password'];

    if (createUser($nombre_usuario, $descripcion_usuario, $habilitado, $role_id, $password)) {
        header("Location: read.php");
        exit();
    } else {
        $error = "Error al crear el usuario";
    }
}
?>

<div class="container mt-5">

<h2>Usuarios</h2>

<form method="POST">
    <div class="mb-3">
        <label for="nombre_usuario" class="form-label">Nombre de Usuario</label>
        <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" required>
    </div>
    <div class="mb-3">
        <label for="descripcion_usuario" class="form-label">Descripción</label>
        <textarea class="form-control" id="descripcion_usuario" name="descripcion_usuario"></textarea>
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="habilitado" name="habilitado" checked>
        <label class="form-check-label" for="habilitado">Habilitado</label>
    </div>
    <div class="mb-3">
        <label for="role_id" class="form-label">Rol</label>
        <select class="form-control" id="role_id" name="role_id" required>
            <?php foreach ($roles as $role): ?>
                <option value="<?php echo $role['id']; ?>"><?php echo $role['nombre_rol']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    
    <?php if (checkPermission('crear')): ?>
        <a href="create.php" class="btn btn-primary mb-3">Crear</a>
    <?php endif; ?>
    <a href="read.php" class="btn btn-secondary">Cancelar</a>
</form>
</div>