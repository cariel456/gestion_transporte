<?php
$projectRoot = dirname(__FILE__, 3); 
require_once dirname(__DIR__, 2) . '/config/config.php'; 
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';   
require_once ROOT_PATH . '/sec/auth_check.php';       
require_once $projectRoot . '/includes/functions.php'; 

// Obtener los servicios y terminales para los dropdowns
$servicios = getAllServicios();
$terminales = getAllTerminales();

// Verificar si se ha proporcionado un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    //$_SESSION['error'] = "ID de horario interurbano no válido.";
    //header("Location: read.php");
    //exit();
}

$id_horario = $_GET['id'];

// Obtener los datos del horario interurbano
$horario = getHorarioInterurbanoById($id_horario);
if (!$horario) {
    $_SESSION['error'] = "Horario interurbano no encontrado.";
    header("Location: read.php");
    exit();
}

// Obtener los detalles del horario
$detalles = getHorarioInterurbanoDetalles($id_horario);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Procesar el formulario
    $servicio1 = $_POST['servicio1'];
    $servicio2 = $_POST['servicio2'];
    $servicio3 = $_POST['servicio3'];
    $terminal_salida = $_POST['terminal_salida'];
    $terminal_llegada = $_POST['terminal_llegada'];
    $descripcion = $_POST['descripcion'];
    
    // Actualizar en la tabla maestro
    $actualizado = updateHorarioInterurbano($id_horario, $servicio1, $servicio2, $servicio3, $terminal_salida, $terminal_llegada, $descripcion);
    
    if ($actualizado) {
        // Eliminar detalles antiguos
       // deleteHorarioInterurbanoDetalles($id_horario);
        
        // Insertar nuevos detalles
        $horas1 = $_POST['hora1'];
        $horas2 = $_POST['hora2'];
        
        for ($i = 0; $i < count($horas1); $i++) {
           // insertHorarioInterurbanoDetalle($id_horario, $horas1[$i], $horas2[$i]);
        }
        
       // $_SESSION['message'] = "Horario interurbano actualizado exitosamente.";
       // header("Location: read.php");
        exit();
    } else {
        //$error = "Error al actualizar el horario interurbano.";
    }
}

include ROOT_PATH . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Horario Interurbano</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Actualizar Horario Interurbano</h1>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="servicio1" class="form-label">Servicio 1</label>
                <select class="form-select" id="servicio1" name="servicio1" required>
                    <option value="">Seleccione un servicio</option>
                    <?php foreach ($servicios as $servicio): ?>
                        <option value="<?php echo $servicio['id']; ?>" <?php echo ($servicio['id'] == $horario['servicio1']) ? 'selected' : ''; ?>><?php echo $servicio['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="servicio2" class="form-label">Servicio 2</label>
                <select class="form-select" id="servicio2" name="servicio2">
                    <option value="">Seleccione un servicio</option>
                    <?php foreach ($servicios as $servicio): ?>
                        <option value="<?php echo $servicio['id']; ?>" <?php echo ($servicio['id'] == $horario['servicio2']) ? 'selected' : ''; ?>><?php echo $servicio['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="servicio3" class="form-label">Servicio 3</label>
                <select class="form-select" id="servicio3" name="servicio3">
                    <option value="">Seleccione un servicio</option>
                    <?php foreach ($servicios as $servicio): ?>
                        <option value="<?php echo $servicio['id']; ?>" <?php echo ($servicio['id'] == $horario['servicio3']) ? 'selected' : ''; ?>><?php echo $servicio['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="terminal_salida" class="form-label">Terminal de Salida</label>
                <select class="form-select" id="terminal_salida" name="terminal_salida" required>
                    <option value="">Seleccione una terminal</option>
                    <?php foreach ($terminales as $terminal): ?>
                        <option value="<?php echo $terminal['id']; ?>" <?php echo ($terminal['id'] == $horario['terminal_salida']) ? 'selected' : ''; ?>><?php echo $terminal['nombre_terminal']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="terminal_llegada" class="form-label">Terminal de Llegada</label>
                <select class="form-select" id="terminal_llegada" name="terminal_llegada" required>
                    <option value="">Seleccione una terminal</option>
                    <?php foreach ($terminales as $terminal): ?>
                        <option value="<?php echo $terminal['id']; ?>" <?php echo ($terminal['id'] == $horario['terminal_llegada']) ? 'selected' : ''; ?>><?php echo $terminal['nombre_terminal']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripcion</label>
                <input type="text" class="form-control" id="descripcion" name="descripcion" value="<?php echo htmlspecialchars($horario['descripcion']); ?>" required>
            </div>

            <h2>Detalles de Horarios</h2>
            <table class="table" id="detalles-table">
                <thead>
                    <tr>
                        <th>Hora Salida</th>
                        <th>Hora Llegada</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($detalles as $detalle): ?>
                    <tr>
                        <td><input type="time" class="form-control" name="hora1[]" value="<?php echo $detalle['hora1']; ?>" required></td>
                        <td><input type="time" class="form-control" name="hora2[]" value="<?php echo $detalle['hora2']; ?>" required></td>
                        <td><button type="button" class="btn btn-danger btn-sm remove-row">Eliminar</button></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button type="button" class="btn btn-secondary mb-3" id="add-row">Agregar Fila</button>
            
            <div>
                <button type="submit" class="btn btn-primary">Actualizar Horario Interurbano</button>
                <a href="read.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('add-row').addEventListener('click', function() {
            var table = document.getElementById('detalles-table');
            var row = table.insertRow(-1);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            
            cell1.innerHTML = '<input type="time" class="form-control" name="hora1[]" required>';
            cell2.innerHTML = '<input type="time" class="form-control" name="hora2[]" required>';
            cell3.innerHTML = '<button type="button" class="btn btn-danger btn-sm remove-row">Eliminar</button>';
        });

        document.getElementById('detalles-table').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-row')) {
                e.target.closest('tr').remove();
            }
        });
    </script>
</body>
</html>