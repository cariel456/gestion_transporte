<?php
session_start();
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';

requireLogin();

//$userPermissions = getUserPermissions();

//$requiredPermission = 'actualizar';
//if (!checkPermission($requiredPermission)) {
//    header("Location: " . BASE_URL . "/views/dashboard.php?error=permission_denied");
//    exit();
//}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: read.php");
    exit();
}

$persona = getPersonalById($id);
$categorias = getAllCategoriasPersona();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = $_POST['codigo'];
    $categoria = $_POST['categoria'];
    $nombre_personal = $_POST['nombre_personal'];
    $legajo = $_POST['legajo'];
    $tarjeta = $_POST['tarjeta'];
    $vencimiento_licencia = $_POST['vencimiento_licencia'];
    $habilitado = isset($_POST['habilitado']) ? 1 : 0;

    if (updatePersonal($id, $codigo, $categoria, $nombre_personal, $legajo, $tarjeta, $vencimiento_licencia, $habilitado)) {
        header("Location: read.php");
        exit();
    } else {
        $error = "Error al actualizar el personal";
    }
}
include ROOT_PATH . '/includes/header.php'; 
?>

<div class="container mt-5">
    <h2>Actualizar Personal</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="codigo" class="form-label">Código</label>
            <input type="number" class="form-control" id="codigo" name="codigo" value="<?php echo $persona['codigo']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="categoria" class="form-label">Categoría</label>
            <select class="form-control" id="categoria" name="categoria" required>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?php echo $categoria['id']; ?>" <?php echo $categoria['id'] == $persona['categoria'] ? 'selected' : ''; ?>><?php echo $categoria['nombre_categoria']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="nombre_personal" class="form-label">Nombre Personal</label>
            <input type="text" class="form-control" id="nombre_personal" name="nombre_personal" value="<?php echo $persona['nombre_personal']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="legajo" class="form-label">Legajo</label>
            <input type="text" class="form-control" id="legajo" name="legajo" value="<?php echo $persona['legajo']; ?>" >
        </div>
        <div class="mb-3">
            <label for="tarjeta" class="form-label">Tarjeta</label>
            <input type="text" class="form-control" id="tarjeta" name="tarjeta" value="<?php echo $persona['tarjeta']; ?>" >
        </div>
        <div class="mb-3">
            <label for="vencimiento_licencia" class="form-label">Vencimiento Licencia</label>
            <input type="date" class="form-control" id="vencimiento_licencia" name="vencimiento_licencia" value="<?php echo $persona['vencimiento_licencia']; ?>" >
        </div>
        
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="habilitado" name="habilitado"  checked <?php echo $persona['habilitado'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="habilitado">Habilitado</label>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="read.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>