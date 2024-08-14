<?php
session_start();
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';

requireLogin();

if (!checkPermission('turnos_parejas_choferes', 'crear')) {
   // header("Location: " . BASE_URL . "/views/dashboard.php?error=permission_denied");
   // exit();
}

$parejas_choferes = getAllParejasChoferes();
$turnos = getAllTurnos();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'pareja_id' => $_POST['pareja_id'],
        'chofer_id' => $_POST['chofer_id'],
        'chofer2_id' => $_POST['chofer2_id'],
        'turno' => $_POST['turno'],
        'turno2' => $_POST['turno2'],
        'descripcion' => $_POST['descripcion'],
        'fecha' => $_POST['fecha']
    ];
    
    if (createTurnoParejasChoferes($data)) {
        header("Location: read.php");
        exit();
    } else {
        $error = "Error al crear el turno de pareja de choferes";
    }
}

include ROOT_PATH . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Turno de Pareja de Choferes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Crear Turno de Pareja de Choferes</h1>

        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="pareja_id" class="form-label">Pareja de Choferes</label>
                <select class="form-select" id="pareja_id" name="pareja_id" required>
                    <option value="">Seleccione una pareja</option>
                    <?php foreach ($parejas_choferes as $pareja) : ?>
                        <option value="<?php echo $pareja['id']; ?>"><?php echo $pareja['pareja']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="chofer_id" class="form-label">Chofer 1</label>
                <select class="form-select" id="chofer_id" name="chofer_id" required>
                    <option value="">Seleccione el chofer 1</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="turno" class="form-label">Turno Chofer 1</label>
                <select class="form-select" id="turno" name="turno" required>
                    <option value="">Seleccione un turno</option>
                    <?php foreach ($turnos as $turno) : ?>
                        <option value="<?php echo $turno['id']; ?>"><?php echo $turno['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="chofer2_id" class="form-label">Chofer 2</label>
                <select class="form-select" id="chofer2_id" name="chofer2_id" required>
                    <option value="">Seleccione el chofer 2</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="turno2" class="form-label">Turno Chofer 2</label>
                <select class="form-select" id="turno2" name="turno2" required>
                    <option value="">Seleccione un turno</option>
                    <?php foreach ($turnos as $turno) : ?>
                        <option value="<?php echo $turno['id']; ?>"><?php echo $turno['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <input type="text" class="form-control" id="descripcion" name="descripcion">
            </div>
            <div class="mb-3">
                <label for="fecha" class="form-label">Fecha</label>
                <input type="date" class="form-control" id="fecha" name="fecha" required>
            </div>
            <button type="submit" class="btn btn-primary">Crear</button>
            <a href="read.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('pareja_id').addEventListener('change', function() {
        var pareja_id = this.value;
        if(pareja_id) {
            fetch('get_choferes.php?pareja_id=' + pareja_id)
                .then(response => response.json())
                .then(data => {
                    var chofer1Select = document.getElementById('chofer_id');
                    var chofer2Select = document.getElementById('chofer2_id');
                    chofer1Select.innerHTML = '<option value="">Seleccione el chofer 1</option>';
                    chofer2Select.innerHTML = '<option value="">Seleccione el chofer 2</option>';
                    chofer1Select.innerHTML += '<option value="' + data.chofer1.id + '">' + data.chofer1.nombre + '</option>';
                    chofer2Select.innerHTML += '<option value="' + data.chofer2.id + '">' + data.chofer2.nombre + '</option>';
                });
        }
    });
    </script>
</body>
</html>