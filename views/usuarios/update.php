<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';

requireLogin();

$userPermissions = getUserPermissions();

// Verifica el permiso necesario para la página actual
$requiredPermission = 'actualizar';
if (!checkPermission($requiredPermission)) {
    header("Location: " . BASE_URL . "/views/dashboard.php?error=permission_denied");
    exit();
}

include ROOT_PATH . '/includes/header.php'; 

$roles = getAllRoles(); // Asumiendo que tienes una función para obtener todos los roles

if (!isset($_GET['id'])) {
    header("Location: read.php");
    exit();
}

$id = $_GET['id'];
$user = getUserById($id);

if (!$user) {
    header("Location: read.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_usuario = $_POST['nombre_usuario'];
    $descripcion_usuario = $_POST['descripcion_usuario'];
    $habilitado = isset($_POST['habilitado']) ? 1 : 0;
    $role_id = $_POST['role_id'];
    $password = !empty($_POST['password']) ? $_POST['password'] : null;

    if (updateUser($id, $nombre_usuario, $descripcion_usuario, $habilitado, $role_id, $password)) {
        header("Location: read.php");
        exit();
    } else {
        $error = "Error al actualizar el usuario";
    }
}
?>
<div class="container mt-5">
    <h2>Actualizar Usuario</h2>

    <form method="POST">
        <div class="mb-3">
            <label for="nombre_usuario" class="form-label">Nombre de Usuario</label>
            <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" value="<?php echo htmlspecialchars($user['nombre_usuario']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="descripcion_usuario" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion_usuario" name="descripcion_usuario"><?php echo htmlspecialchars($user['descripcion_usuario']); ?></textarea>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="habilitado" name="habilitado" <?php echo $user['habilitado'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="habilitado">Habilitado</label>
        </div>
        <div class="mb-3">
            <label for="role_id" class="form-label">Rol</label>
            <select class="form-control" id="role_id" name="role_id" required>
                <?php foreach ($roles as $role): ?>
                    <option value="<?php echo $role['id']; ?>" <?php echo $role['id'] == $user['rol_id'] ? 'selected' : ''; ?>><?php echo $role['nombre_rol']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Contraseña (dejar en blanco para no cambiar)</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
        <a href="read.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>