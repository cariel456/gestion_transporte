<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';
include ROOT_PATH . '/includes/header.php';

$terminales = getAllTerminales();
$lineas = getAllLineas(); // Agregamos esta línea
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $terminal_salida = $_POST['terminal_salida'];
    $terminal_llegada = $_POST['terminal_llegada'];
    $horarios = [];

    foreach ($_POST['linea_id'] as $key => $linea_id) {
        if (!empty($_POST['hora_salida'][$key]) && !empty($_POST['hora_llegada'][$key])) {
            $horarios[] = [
                'linea_id' => $linea_id,
                'terminal_salida' => $terminal_salida,
                'terminal_llegada' => $terminal_llegada,
                'hora_salida' => $_POST['hora_salida'][$key],
                'hora_llegada' => $_POST['hora_llegada'][$key]
            ];
        }
    }

    if (createHorariosInterurbanos($horarios)) {
        header("Location: read.php?success=1");
        exit();
    } else {
        $error = "Error al crear los horarios interurbanos";
    }
}
?>

<div class="container mt-5">
    <h2>Crear Nuevos Horarios Interurbanos</h2>

    <form method="POST" id="horarioForm">
        <div class="mb-3">
            <label for="terminal_salida" class="form-label">Terminal de Salida</label>
            <select class="form-control" id="terminal_salida" name="terminal_salida" required>
                <option value="">Seleccione una terminal</option>
                <?php foreach ($terminales as $terminal): ?>
                    <option value="<?php echo $terminal['id']; ?>"><?php echo $terminal['nombre_terminal']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="terminal_llegada" class="form-label">Terminal de Llegada</label>
            <select class="form-control" id="terminal_llegada" name="terminal_llegada" required>
                <option value="">Seleccione una terminal</option>
                <?php foreach ($terminales as $terminal): ?>
                    <option value="<?php echo $terminal['id']; ?>"><?php echo $terminal['nombre_terminal']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div id="horarios"></div>
        <button type="button" class="btn btn-secondary mb-3" onclick="agregarHorario()">Agregar otro horario</button>
        <button type="submit" class="btn btn-primary">Crear Horarios</button>
        <a href="read.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
function agregarHorario() {
    const horariosDiv = document.getElementById('horarios');
    const nuevaFila = document.createElement('div');
    nuevaFila.className = 'row mb-3';
    nuevaFila.innerHTML = `
        <div class="col">
            <select class="form-control linea-select" name="linea_id[]" required>
                <option value="">Seleccione una línea</option>
                <?php foreach ($lineas as $linea): ?>
                    <option value="<?php echo $linea['id']; ?>"><?php echo $linea['numero'] . ' - ' . $linea['descripcion']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col">
            <input type="time" class="form-control" name="hora_salida[]" required>
        </div>
        <div class="col">
            <input type="time" class="form-control" name="hora_llegada[]" required>
        </div>
    `;
    horariosDiv.appendChild(nuevaFila);
}

// Agregar el primer horario al cargar la página
document.addEventListener('DOMContentLoaded', agregarHorario);
</script>