<?php
$projectRoot = dirname(__FILE__, 3); 
require_once dirname(__DIR__, 2) . '/config/config.php'; 
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';   
require_once ROOT_PATH . '/sec/auth_check.php';       
require_once $projectRoot . '/includes/functions.php'; 

// Actualizar la última actividad
$_SESSION['last_activity'] = time();

requireLogin();

$personal = getAllPersonal();
$unidades = getAllUnidades();
$localidades = getAllLocalidades();
$niveles_urgencias = getAllNivelesUrgencias();
$grupos_funciones = getAllGruposFunciones();
$especialidades = getAllEspecialidadesTalleres();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $especialidades = isset($_POST['especialidades']) ? $_POST['especialidades'] : [];
    $data = [
        'especialidades' => $especialidades,
        'grupo_funcion' => $_POST['grupo_funcion'],
        'nivel_urgencia' => $_POST['nivel_urgencia'],
        'nombre_completo_conductor' => $_POST['nombre_completo_conductor'],
        'nombre_completo_mantenimiento' => $_POST['nombre_completo_mantenimiento'],
        'numero_solicitud' => $_POST['numero_solicitud'],
        'numero_unidad' => $_POST['numero_unidad'],
        'solicitante' => $_POST['solicitante'],
        'ubicacion' => $_POST['ubicacion'],
        'fecha_solicitud' => $_POST['fecha_solicitud'],
        'observaciones' => $_POST['observaciones']

    ];
    
    if (createSolicitudPedidoReparacion($data)) {
        header("Location: read.php");
        exit();
    } else {
        $error = "Error al crear la solicitud de pedido de reparación";
    }
}
include ROOT_PATH . '/includes/header.php'; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Solicitud de Pedido de Reparación</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body>
    <div class="container mt-5">

        <h1>Crear Solicitud de Pedido de Reparación</h1>

        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="solicitante" class="form-label">Solicitante</label>
                <select class="form-select select2" id="solicitante" name="solicitante">
                    <option value="">Seleccione un solicitante</option>
                    <?php foreach ($personal as $solicitante) : ?>
                        <option value="<?php echo $solicitante['id']; ?>"><?php echo $solicitante['nombre_personal']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="fecha_solicitud">Fecha de Solicitud</label>
                <input type="datetime-local" class="form-control" id="fecha_solicitud" name="fecha_solicitud" value="<?php echo isset($solicitud['fecha_solicitud']) ? date('Y-m-d', strtotime($solicitud['fecha_solicitud'])) : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="nombre_completo_conductor" class="form-label">Conductor</label>
                <select class="form-select select2" id="nombre_completo_conductor" name="nombre_completo_conductor">
                    <option value="">Seleccione un Conductor</option>
                    <?php foreach ($personal as $persona) : ?>
                        <option value="<?php echo $persona['id']; ?>"><?php echo $persona['nombre_personal']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
         
            <div class="mb-3">
                <label for="especialidades" class="form-label">Especialidades</label>
                <select multiple class="form-select" id="especialidades" name="especialidades[]" required>
                    <?php foreach ($especialidades as $especialidad) : ?>
                        <option value="<?php echo $especialidad['id']; ?>"><?php echo $especialidad['nombre_especialidad']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>


            <div class="mb-3">
                <label for="ubicacion" class="form-label">Ubicación</label>
                <select class="form-select select2" id="ubicacion" name="ubicacion" required>
                    <option value="">Seleccione una ubicación</option>
                    <?php foreach ($localidades as $localidad) : ?>
                        <option value="<?php echo $localidad['id']; ?>"><?php echo $localidad['nombre_localidad']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="grupo_funcion" class="form-label">Grupo Función</label>
                <select class="form-select" id="grupo_funcion" name="grupo_funcion" required>
                    <option value="">Seleccione un grupo función</option>
                    <?php foreach ($grupos_funciones as $grupo) : ?>
                        <option value="<?php echo $grupo['id']; ?>"><?php echo $grupo['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

        <div class="mb-3">
            <label for="nivel_urgencia" class="form-label">Nivel de Urgencia</label>
            <select class="form-select" id="nivel_urgencia" name="nivel_urgencia" required>
                <option value="">Seleccione un nivel de urgencia</option>
                <?php foreach ($niveles_urgencias as $nivel) : ?>
                    <option value="<?php echo $nivel['id']; ?>"><?php echo $nivel['nombre_urgencia']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

            <div class="mb-3">
            <label for="nombre_completo_mantenimiento" class="form-label">Mantenimiento</label>
            <select class="form-select select2" id="nombre_completo_mantenimiento" name="nombre_completo_mantenimiento">
                <option value="">Persona de Mantenimiento</option>
                <?php foreach ($personal as $persona) : ?>
                    <option value="<?php echo $persona['id']; ?>"><?php echo $persona['nombre_personal']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
            <div class="mb-3">
                <label for="numero_solicitud" class="form-label">Número de Solicitud</label>
                <input type="number" class="form-control" id="numero_solicitud" name="numero_solicitud">
            </div>
            <div class="mb-3">
            <label for="numero_unidad" class="form-label">Número de Unidad</label>
            <select class="form-select select2" id="numero_unidad" name="numero_unidad">
                <option value="">Seleccione una unidad</option>
                <?php foreach ($unidades as $unidad) : ?>
                    <option value="<?php echo $unidad['id']; ?>"><?php echo $unidad['codigo_interno']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
            <div class="mb-3">
                <label for="observaciones" class="form-label">Detalle Pedidos</label>
                <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
            </div>
            <!--<div class="mb-3">
                <label for="numero_ot" class="form-label">Número OT</label>
                <input type="number" class="form-control" id="numero_ot" name="numero_ot">
            </div>-->
            <button type="submit" class="btn btn-primary">Crear</button>
            <a href="read.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Seleccione una opción",
            allowClear: true
        });
    });
</script>
</body>
</html>