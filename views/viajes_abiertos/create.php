<?php
session_start();
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';

$choferes = getAllChoferes();
$unidades = getAllUnidadesForViajes();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'chofer1' => $_POST['chofer1'],
        'chofer2' => $_POST['chofer2'],
        'unidad' => $_POST['unidad'],
        'estado_viaje_abierto' => $_POST['estado_viaje_abierto'],
        'turno_registro_sistema' => $_POST['turno_registro_sistema'],
        'turno_registro_planilla' => $_POST['turno_registro_planilla'],
        'chofer_actual' => $_POST['chofer_actual'],
        'observaciones' => $_POST['observaciones'],
        'habilitado' => isset($_POST['habilitado']) ? 1 : 0
    ];

    if (createViajeAbierto($data['chofer1'], $data['chofer2'], $data['unidad'], $data['estado_viaje_abierto'], 
                           $data['turno_registro_sistema'], $data['turno_registro_planilla'], $data['chofer_actual'], 
                           $data['observaciones'], $data['habilitado'])) {
        header("Location: read.php");
        exit();
    } else {
        $error = "Error al crear el viaje abierto";
    }
}

include ROOT_PATH . '/includes/header.php';
?>

<div class="container mt-5">
    <h2>Crear Viaje Abierto</h2>
    <form action="" method="POST">
        <div class="form-group">
            <label for="chofer1">Chofer 1</label>
            <select name="chofer1" id="chofer1" class="form-control" required>
                <?php foreach ($choferes as $chofer): ?>
                    <option value="<?php echo $chofer['id']; ?>"><?php echo $chofer['nombre_personal']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="chofer2">Chofer 2</label>
            <select name="chofer2" id="chofer2" class="form-control" required>
                <?php foreach ($choferes as $chofer): ?>
                    <option value="<?php echo $chofer['id']; ?>"><?php echo $chofer['nombre_personal']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="unidad">Unidad</label>
            <select name="unidad" id="unidad" class="form-control" required>
                <?php foreach ($unidades as $unidad): ?>
                    <option value="<?php echo $unidad['id']; ?>"><?php echo $unidad['codigo_interno']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="estado_viaje_abierto">Estado del Viaje</label>
            <input type="number" name="estado_viaje_abierto" id="estado_viaje_abierto" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="turno_registro_sistema">Turno Registro Sistema</label>
            <input type="number" name="turno_registro_sistema" id="turno_registro_sistema" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="turno_registro_planilla">Turno Registro Planilla</label>
            <input type="number" name="turno_registro_planilla" id="turno_registro_planilla" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="chofer_actual">Chofer Actual</label>
            <select name="chofer_actual" id="chofer_actual" class="form-control" required>
                <?php foreach ($choferes as $chofer): ?>
                    <option value="<?php echo $chofer['id']; ?>"><?php echo $chofer['nombre_personal']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="observaciones">Observaciones</label>
            <textarea name="observaciones" id="observaciones" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Crear</button>
        <a href="read.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>