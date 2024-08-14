<?php
session_start();
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';

requireLogin();

//$userPermissions = getUserPermissions();

//$requiredPermission = 'crear';
//if (!checkPermission($requiredPermission)) {
//    header("Location: " . BASE_URL . "/views/dashboard.php?error=permission_denied");
//    exit();
//}

$categorias = getAllCategoriasPersona();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = $_POST['codigo'];
    $categoria = $_POST['categoria'];
    $nombre_personal = $_POST['nombre_personal'];
    $legajo = $_POST['legajo'];
    $tarjeta = $_POST['tarjeta'];
    $vencimiento_licencia = $_POST['vencimiento_licencia'];
    $habilitado = isset($_POST['habilitado']) ? 1 : 0;

    if (createPersonal($codigo, $categoria, $nombre_personal, $legajo, $tarjeta, $vencimiento_licencia, $habilitado)) {
        header("Location: read.php");
        exit();
    } else {
        $error = "Error al crear el personal";
    }
}
include ROOT_PATH . '/includes/header.php'; 

?>

<div class="container mt-5">
    <h2>Crear Personal</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="codigo" class="form-label">Código</label>
            <input type="number" class="form-control" id="codigo" name="codigo">
        </div>
        <div class="mb-3">
            <label for="categoria" class="form-label">Categoría</label>
            <select class="form-control" id="categoria" name="categoria" required>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?php echo $categoria['id']; ?>"><?php echo $categoria['nombre_categoria']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="nombre_personal" class="form-label">Nombre Personal</label>
            <input type="text" class="form-control" id="nombre_personal" name="nombre_personal" >
        </div>
        <div class="mb-3">
            <label for="legajo" class="form-label">Legajo</label>
            <input type="text" class="form-control" id="legajo" name="legajo" >
        </div>
        <div class="mb-3">
            <label for="tarjeta" class="form-label">Tarjeta</label>
            <input type="text" class="form-control" id="tarjeta" name="tarjeta" >
        </div>
        <div class="mb-3">
            <label for="vencimiento_licencia" class="form-label">Vencimiento Licencia</label>
            <input type="date" class="form-control" id="vencimiento_licencia" name="vencimiento_licencia" >
        </div>
        <button type="submit" class="btn btn-primary">Crear</button>
        <a href="read.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>