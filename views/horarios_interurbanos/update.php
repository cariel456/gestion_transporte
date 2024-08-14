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

$terminales = getAllTerminales();

if (!isset($_GET['id'])) {
    header("Location: read.php");
    exit();
}

$id = $_GET['id'];
$horario = getHorarioInterurbanoById($id);

if (!$horario) {
    header("Location: read.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $terminal_salida = $_POST['terminal_salida'];
    $hora_salida = $_POST['hora_salida'];
    $terminal_llegada = $_POST['terminal_llegada'];
    $hora_llegada = $_POST['hora_llegada'];
    $habilitado = isset($_POST['habilitado']) ? 1 : 0;

    if (updateHorarioInterurbano($id, $terminal_salida, $hora_salida, $terminal_llegada, $hora_llegada, $habilitado)) {
        header("Location: read.php");
        exit();
    } else {
        $error = "Error al actualizar el horario interurbano";
    }
}
include ROOT_PATH . '/includes/header.php';
?>

<div class="container mt-5">
    <h2>Actualizar Horario Interurbano</h2>

    <form method="POST">
        <div class="mb-3">
            <label for="terminal_salida" class="form-label">Terminal de Salida</label>
            <select class="form-control" id="terminal_salida" name="terminal_salida" required>
                <?php foreach ($terminales as $terminal): ?>
                    <option value="<?php echo $terminal['id']; ?>" <?php echo ($terminal['id'] == $horario['terminal_salida']) ? 'selected' : ''; ?>><?php echo $terminal['nombre_terminal']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="hora_salida" class="form-label">Hora de Salida</label>
            <input type="time" class="form-control" id="hora_salida" name="hora_salida" value="<?php echo $horario['hora_salida']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="terminal_llegada" class="form-label">Terminal de Llegada</label>
            <select class="form-control" id="terminal_llegada" name="terminal_llegada" required>
                <?php foreach ($terminales as $terminal): ?>
                    <option value="<?php echo $terminal['id']; ?>" <?php echo ($terminal['id'] == $horario['terminal_llegada']) ? 'selected' : ''; ?>><?php echo $terminal['nombre_terminal']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="hora_llegada" class="form-label">Hora de Llegada</label>
            <input type="time" class="form-control" id="hora_llegada" name="hora_llegada" value="<?php echo $horario['hora_llegada']; ?>" required>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="habilitado" name="habilitado" <?php echo $horario['habilitado'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="habilitado">Habilitado</label>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Horario</button>
        <a href="read.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>