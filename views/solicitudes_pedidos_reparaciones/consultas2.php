<?php
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';

function escape($value) {
    global $conn;
    return $conn->real_escape_string($value);
}

// Obtener las opciones para los selects
$unidadesOptions =  getAllUnidades();

// Construir la consulta SQL base
$sql = "SELECT s.*, p1.nombre_personal AS solicitante_nombre, 
               p2.nombre_personal AS conductor_nombre,
               p3.nombre_personal AS mantenimiento_nombre,
               u.codigo_interno AS unidad_codigo,
               l.nombre_localidad
        FROM solicitudes_pedidos_reparaciones s
        LEFT JOIN personal p1 ON s.solicitante = p1.id
        LEFT JOIN personal p2 ON s.nombre_completo_conductor = p2.id
        LEFT JOIN personal p3 ON s.nombre_completo_mantenimiento = p3.id
        LEFT JOIN unidades u ON s.numero_unidad = u.id
        LEFT JOIN localidades l ON s.ubicacion = l.id
        WHERE 1=1";

$whereClause = [];

// Aplicar filtros si se han enviado
if ($_SERVER["REQUEST_METHOD"] == "GET" && !empty($_GET)) {
    if (!empty($_GET['numero_solicitud'])) {
        $whereClause[] = "s.numero_solicitud = " . escape($_GET['numero_solicitud']);
    }
    if (!empty($_GET['fecha_inicio']) && !empty($_GET['fecha_fin'])) {
        $whereClause[] = "s.fecha_solicitud BETWEEN '" . escape($_GET['fecha_inicio']) . "' AND '" . escape($_GET['fecha_fin']) . "'";
    }
    if (!empty($_GET['solicitante'])) {
        $whereClause[] = "s.solicitante = " . escape($_GET['solicitante']);
    }
    if (!empty($_GET['ubicacion'])) {
        $whereClause[] = "s.ubicacion = " . escape($_GET['ubicacion']);
    }
    if (!empty($_GET['numero_unidad'])) {
        $whereClause[] = "s.numero_unidad = " . escape($_GET['numero_unidad']);
    }
    if (!empty($_GET['nivel_urgencia'])) {
        $whereClause[] = "s.nivel_urgencia = " . escape($_GET['nivel_urgencia']);
    }
    if (!empty($_GET['grupo_funcion'])) {
        $whereClause[] = "s.grupo_funcion LIKE '%" . escape($_GET['grupo_funcion']) . "%'";
    }
    if (!empty($_GET['especialidades'])) {
        $whereClause[] = "s.especialidades LIKE '%" . escape($_GET['especialidades']) . "%'";
    }
    if (!empty($_GET['conductor'])) {
        $whereClause[] = "s.nombre_completo_conductor = " . escape($_GET['conductor']);
    }
    if (!empty($_GET['mantenimiento'])) {
        $whereClause[] = "s.nombre_completo_mantenimiento = " . escape($_GET['mantenimiento']);
    }
    if (isset($_GET['habilitado']) && $_GET['habilitado'] !== '') {
        $whereClause[] = "s.habilitado = " . (int)$_GET['habilitado'];
    }
}

if (!empty($whereClause)) {
    $sql .= " AND " . implode(" AND ", $whereClause);
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitudes de Pedidos y Reparaciones</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>
    <div class="container mt-5">
        <h2>Solicitudes de Pedidos y Reparaciones</h2>
        
        <!-- Formulario de filtro -->
        <form method="GET" class="mb-4">
            <div class="form-row">
                <div class="col-md-2 mb-3">
                    <label for="numero_solicitud">Número de Solicitud</label>
                    <input type="number" class="form-control" id="numero_solicitud" name="numero_solicitud" value="<?php echo isset($_GET['numero_solicitud']) ? htmlspecialchars($_GET['numero_solicitud']) : ''; ?>">
                </div>
                <div class="col-md-2 mb-3">
                    <label for="fecha_inicio">Fecha Inicio</label>
                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?php echo isset($_GET['fecha_inicio']) ? htmlspecialchars($_GET['fecha_inicio']) : ''; ?>">
                </div>
                <div class="col-md-2 mb-3">
                    <label for="fecha_fin">Fecha Fin</label>
                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?php echo isset($_GET['fecha_fin']) ? htmlspecialchars($_GET['fecha_fin']) : ''; ?>">
                </div>
                <div class="col-md-2 mb-3">
                    <label for="solicitante">Solicitante</label>
                    <select class="form-control select2" id="solicitante" name="solicitante">
                        <option value="">Seleccione...</option>
                        <?php foreach ($personalOptions as $id => $nombre): ?>
                            <option value="<?php echo $id; ?>" <?php echo (isset($_GET['id']) && $_GET['id'] == $id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($nombre); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="ubicacion">Ubicación</label>
                    <select class="form-control select2" id="ubicacion" name="ubicacion">
                        <option value="">Seleccione...</option>
                        <?php foreach ($localidadesOptions as $id => $nombre): ?>
                            <option value="<?php echo $id; ?>" <?php echo (isset($_GET['ubicacion']) && $_GET['ubicacion'] == $id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($nombre); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="numero_unidad">Número de Unidad</label>
                    <select class="form-control select2" id="numero_unidad" name="numero_unidad">
                        <option value="">Seleccione...</option>
                        <?php foreach ($unidadesOptions as $id => $codigo): ?>
                            <option value="<?php echo $id; ?>" <?php echo (isset($_GET['codigo_interno']) && $_GET['codigo_interno'] == $id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($codigo); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-2 mb-3">
                    <label for="nivel_urgencia">Nivel de Urgencia</label>
                    <input type="number" class="form-control" id="nivel_urgencia" name="nivel_urgencia" value="<?php echo isset($_GET['nivel_urgencia']) ? htmlspecialchars($_GET['nivel_urgencia']) : ''; ?>">
                </div>
                <div class="col-md-2 mb-3">
                    <label for="grupo_funcion">Grupo Función</label>
                    <input type="text" class="form-control" id="grupo_funcion" name="grupo_funcion" value="<?php echo isset($_GET['grupo_funcion']) ? htmlspecialchars($_GET['grupo_funcion']) : ''; ?>">
                </div>
                <div class="col-md-2 mb-3">
                    <label for="especialidades">Especialidades</label>
                    <input type="text" class="form-control" id="especialidades" name="especialidades" value="<?php echo isset($_GET['especialidades']) ? htmlspecialchars($_GET['especialidades']) : ''; ?>">
                </div>
                <div class="col-md-2 mb-3">
                    <label for="conductor">Conductor</label>
                    <select class="form-control select2" id="conductor" name="conductor">
                        <option value="">Seleccione...</option>
                        <?php foreach ($personalOptions as $id => $nombre): ?>
                            <option value="<?php echo $id; ?>" <?php echo (isset($_GET['conductor']) && $_GET['conductor'] == $id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($nombre); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="mantenimiento">Mantenimiento</label>
                    <select class="form-control select2" id="mantenimiento" name="mantenimiento">
                        <option value="">Seleccione...</option>
                        <?php foreach ($personalOptions as $id => $nombre): ?>
                            <option value="<?php echo $id; ?>" <?php echo (isset($_GET['mantenimiento']) && $_GET['mantenimiento'] == $id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($nombre); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="habilitado">Habilitado</label>
                    <select class="form-control" id="habilitado" name="habilitado">
                        <option value="">Todos</option>
                        <option value="1" <?php echo (isset($_GET['habilitado']) && $_GET['habilitado'] === '1') ? 'selected' : ''; ?>>Sí</option>
                        <option value="0" <?php echo (isset($_GET['habilitado']) && $_GET['habilitado'] === '0') ? 'selected' : ''; ?>>No</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-secondary">Limpiar filtros</a>
        </form>

        <!-- Tabla de resultados -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Número de Solicitud</th>
                    <th>Fecha de Solicitud</th>
                    <th>Solicitante</th>
                    <th>Ubicación</th>
                    <th>Unidad</th>
                    <th>Nivel de Urgencia</th>
                    <th>Grupo Función</th>
                    <th>Especialidades</th>
                    <th>Conductor</th>
                    <th>Mantenimiento</th>
                    <th>Habilitado</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["numero_solicitud"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["fecha_solicitud"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["solicitante_nombre"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["nombre_localidad"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["unidad_codigo"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["nivel_urgencia"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["grupo_funcion"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["especialidades"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["conductor_nombre"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["mantenimiento_nombre"]) . "</td>";
                        echo "<td>" . ($row["habilitado"] ? "Sí" : "No") . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='12'>No se encontraron resultados</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
</body>
</html>