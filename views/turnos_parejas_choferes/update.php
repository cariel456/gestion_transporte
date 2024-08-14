<?php
session_start();
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';

requireLogin();

if (!checkPermission('turnos_parejas_choferes', 'actualizar')) {
    //header("Location: " . BASE_URL . "/views/dashboard.php?error=permission_denied");
    //exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: read.php");
    exit();
}

$turno = getTurnoParejasChoferesById($id);
if (!$turno) {
    header("Location: read.php");
    exit();
}

$parejas = getAllParejasChoferes();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pareja = getParejaChoferesById($_POST['pareja_id']);
    $data = [
        'chofer_id' => $pareja['chofer_id'],
        'chofer2_id' => $pareja['chofer2_id'],
        'descripcion' => $_POST['descripcion'],
        'pareja_id' => $_POST['pareja_id'],
        'fecha' => $_POST['fecha']
    ];
    
    if (updateTurnoParejasChoferes($id, $data)) {
        header("Location: read.php");
        exit();
    } else {
        $error = "Error al actualizar el turno";
    }
}

include ROOT_PATH . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Turno de Pareja de Choferes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Actualizar Turno de Pareja de Choferes</h1>

        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
        <div class="mb-3">
            <label for="pareja_id" class="form-label">Pareja de Choferes</label>
            <select class="form-select" id="pareja_id" name="pareja_id" required>
                <?php foreach ($parejas as $pareja) : ?>
                    <option value="<?php echo $pareja['id']; ?>" <?php echo ($pareja['id'] == $turno['pareja_id']) ? 'selected' : ''; ?>>
                        <?php echo $pareja['chofer1_nombre'] . ' - ' . $pareja['chofer2_nombre']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <input type="text" class="form-control" id="descripcion" name="descripcion" value="<?php echo $turno['descripcion']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="fecha" class="form-label">Fecha</label>
                <input type="date" class="form-control" id="fecha" name="fecha" value="<?php echo $turno['fecha']; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="read.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>