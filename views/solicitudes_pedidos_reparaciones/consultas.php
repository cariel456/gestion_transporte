<?php
$projectRoot = dirname(__FILE__, 3); 
require_once dirname(__DIR__, 2) . '/config/config.php'; 
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';   
require_once ROOT_PATH . '/sec/auth_check.php';       
require_once $projectRoot . '/includes/functions.php'; 

requireLogin();

function escape($value) {
    global $conn;
    return $conn->real_escape_string($value);
}

$personal = getAllPersonal();
$unidades = getAllUnidades();
$localidades = getAllLocalidades();
$especialidades = getAllEspecialidadesTalleres();
$niveles_urgencias = getAllNivelesUrgencias();
$grupos_funciones = getAllGruposFunciones();

// Construir la consulta SQL base
$sql = "SELECT s.*, 
               p1.nombre_personal AS solicitante_nombre, 
               p2.nombre_personal AS conductor_nombre,
               p3.nombre_personal AS mantenimiento_nombre,
               u.codigo_interno AS unidad_codigo,
               l.nombre_localidad,
               es.nombre AS estado_nombre
        FROM solicitudes_pedidos_reparaciones s
        LEFT JOIN personal p1 ON s.solicitante = p1.id
        LEFT JOIN personal p2 ON s.nombre_completo_conductor = p2.id
        LEFT JOIN personal p3 ON s.nombre_completo_mantenimiento = p3.id
        LEFT JOIN unidades u ON s.numero_unidad = u.id
        LEFT JOIN localidades l ON s.ubicacion = l.id
        LEFT JOIN estados_solicitud es ON s.estado_actual_id = es.id
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
        $whereClause[] = "s.grupo_funcion = " . escape($_GET['grupo_funcion']);
    }
    if (!empty($_GET['conductor'])) {
        $whereClause[] = "s.nombre_completo_conductor = " . escape($_GET['conductor']);
    }
    if (!empty($_GET['mantenimiento'])) {
        $whereClause[] = "s.nombre_completo_mantenimiento = " . escape($_GET['mantenimiento']);
    }
    if (!empty($_GET['estado'])) {
        $whereClause[] = "s.estado_actual_id = " . escape($_GET['estado']);
    }
    if (isset($_GET['habilitado']) && $_GET['habilitado'] !== '') {
        $whereClause[] = "s.habilitado = " . (int)$_GET['habilitado'];
    }
}

if (!empty($whereClause)) {
    $sql .= " AND " . implode(" AND ", $whereClause);
}

$sql .= " ORDER BY s.fecha_solicitud DESC, s.id DESC";

$result = $conn->query($sql);

require_once ROOT_PATH . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultas - Solicitudes de Taller</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="<?php echo BASE_URL; ?>/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --bg-dark-primary: #1b1b1b;
            --bg-dark-secondary: #212121;
            --bg-card: #2d2d2d;
            --text-light: #f8f9fa;
            --text-muted: #adb5bd;
            --green-start: #28a745;
            --green-end: #1c7430;
            --accent-amber: #ffc107;
            --border-subtle: rgba(40, 167, 69, 0.3);
            --danger-red: #dc3545;
            --warning-orange: #fd7e14;
            --info-blue: #0dcaf0;
        }
        
        body {
            background-color: var(--bg-dark-primary);
            color: var(--text-light);
            font-family: 'Roboto', sans-serif;
            min-height: 100vh;
            padding-bottom: 2rem;
        }
        
        .main-container {
            max-width: 1600px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        /* Header de página */
        .page-header {
            background: linear-gradient(135deg, var(--bg-card), var(--bg-dark-secondary));
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border-subtle);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.4);
        }
        
        .page-title {
            font-family: 'Montserrat', sans-serif;
            font-size: clamp(1.5rem, 4vw, 2rem);
            font-weight: 700;
            background: linear-gradient(135deg, var(--green-start), var(--green-end));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0 0 0.5rem 0;
            letter-spacing: 1px;
        }
        
        .page-subtitle {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin: 0;
        }
        
        /* Card de filtros */
        .filters-card {
            background: linear-gradient(to bottom right, var(--bg-card), var(--bg-dark-secondary));
            border-radius: 15px;
            border: 1px solid var(--border-subtle);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
            margin-bottom: 2rem;
            overflow: hidden;
        }
        
        .filters-header {
            background: linear-gradient(135deg, var(--green-start), var(--green-end));
            color: white;
            padding: 1rem 1.5rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .filters-body {
            padding: 1.5rem;
        }
        
        .form-label {
            color: var(--text-light);
            font-weight: 500;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            letter-spacing: 0.3px;
        }
        
        .form-control, .form-select {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-light);
            border-radius: 8px;
            padding: 0.625rem 0.875rem;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            background-color: rgba(255, 255, 255, 0.08);
            border-color: var(--green-start);
            box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
            color: var(--text-light);
        }
        
        .form-control::placeholder {
            color: var(--text-muted);
            opacity: 0.7;
        }
        
        .form-select option {
            background-color: var(--bg-dark-secondary);
            color: var(--text-light);
        }
        
        /* Select2 dark theme */
        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--multiple {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            min-height: 38px;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: var(--text-light);
            line-height: 36px;
            padding-left: 12px;
        }
        
        .select2-container--default .select2-results__option {
            background-color: var(--bg-dark-secondary);
            color: var(--text-light);
        }
        
        .select2-container--default .select2-results__option--highlighted {
            background-color: var(--green-start);
        }
        
        .select2-dropdown {
            background-color: var(--bg-dark-secondary);
            border: 1px solid var(--border-subtle);
        }
        
        /* Botones */
        .btn-filter {
            background: linear-gradient(135deg, var(--green-start), var(--green-end));
            color: white;
            border: none;
            padding: 0.625rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        
        .btn-filter:hover {
            background: linear-gradient(135deg, var(--green-end), var(--green-start));
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
            color: white;
        }
        
        .btn-clear {
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--text-light);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 0.625rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-clear:hover {
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
            border-color: rgba(255, 255, 255, 0.3);
        }
        
        .btn-export {
            background: linear-gradient(135deg, var(--info-blue), #0891b2);
            color: white;
            border: none;
            padding: 0.625rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-export:hover {
            background: linear-gradient(135deg, #0891b2, var(--info-blue));
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(13, 202, 240, 0.4);
            color: white;
        }
        
        .btn-back {
            background-color: rgba(255, 255, 255, 0.05);
            color: var(--text-muted);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 0.5rem 1.25rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-back:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--text-light);
            border-color: rgba(255, 255, 255, 0.2);
        }
        
        /* Tabla de resultados */
        .table-container {
            background: linear-gradient(to bottom right, var(--bg-card), var(--bg-dark-secondary));
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
            border: 1px solid var(--border-subtle);
            overflow: hidden;
        }
        
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .results-count {
            color: var(--text-muted);
            font-size: 0.875rem;
        }
        
        .results-count strong {
            color: var(--accent-amber);
            font-size: 1.1rem;
        }
        
        .table-responsive {
            border-radius: 10px;
            overflow-x: auto;
        }
        
        .table-custom {
            margin-bottom: 0;
            font-size: 0.875rem;
        }
        
        .table-custom thead {
            background: linear-gradient(135deg, var(--green-start), var(--green-end));
            color: white;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .table-custom thead th {
            border: none;
            padding: 1rem 0.75rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-size: 0.75rem;
            white-space: nowrap;
            vertical-align: middle;
        }
        
        .table-custom tbody tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.2s ease;
        }
        
        .table-custom tbody tr:hover {
            background-color: rgba(40, 167, 69, 0.1);
            transform: scale(1.01);
        }
        
        .table-custom tbody td {
            padding: 0.875rem 0.75rem;
            color: var(--text-light);
            vertical-align: middle;
        }
        
        /* Badges */
        .badge-especialidad {
            background-color: rgba(40, 167, 69, 0.2);
            color: var(--green-start);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.7rem;
            display: inline-block;
            margin: 0.125rem;
            border: 1px solid var(--green-start);
            font-weight: 500;
        }
        
        .badge-urgencia {
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
        }
        
        .urgencia-baja {
            background: linear-gradient(135deg, #28a745, #1e7e34);
            color: white;
        }
        
        .urgencia-media {
            background: linear-gradient(135deg, #fd7e14, #e8590c);
            color: white;
        }
        
        .urgencia-alta {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
        }
        
        .badge-estado {
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.7rem;
            display: inline-block;
        }
        
        .estado-pendiente {
            background: linear-gradient(135deg, #ffc107, #ff9800);
            color: #000;
        }
        
        .estado-proceso {
            background: linear-gradient(135deg, #0dcaf0, #0891b2);
            color: white;
        }
        
        .estado-completado {
            background: linear-gradient(135deg, #28a745, #1e7e34);
            color: white;
        }
        
        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-muted);
        }
        
        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        /* Animaciones */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .page-header,
        .filters-card,
        .table-container {
            animation: fadeIn 0.5s ease-out;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .page-header {
                padding: 1.5rem;
            }
            
            .filters-body {
                padding: 1rem;
            }
            
            .table-container {
                padding: 1rem;
            }
            
            .btn-filter, .btn-clear, .btn-export {
                width: 100%;
                margin-bottom: 0.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="main-container">
        <!-- Card de Filtros -->
        <div class="filters-card">
            <div class="filters-header">
                <span>🎯 Filtros de Búsqueda</span>
            </div>
            <div class="filters-body">
                <form method="GET">
                    <div class="row g-3">
                        <!-- Fila 1 -->
                        <div class="col-md-3">
                            <label for="numero_solicitud" class="form-label">N° Solicitud</label>
                            <input type="number" class="form-control" id="numero_solicitud" name="numero_solicitud" 
                                   value="<?php echo isset($_GET['numero_solicitud']) ? htmlspecialchars($_GET['numero_solicitud']) : ''; ?>"
                                   placeholder="Ej: 123">
                        </div>
                        
                        <div class="col-md-3">
                            <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                                   value="<?php echo isset($_GET['fecha_inicio']) ? htmlspecialchars($_GET['fecha_inicio']) : ''; ?>">
                        </div>
                        
                        <div class="col-md-3">
                            <label for="fecha_fin" class="form-label">Fecha Fin</label>
                            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                                   value="<?php echo isset($_GET['fecha_fin']) ? htmlspecialchars($_GET['fecha_fin']) : ''; ?>">
                        </div>
                        
                        <div class="col-md-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-select" id="estado" name="estado">
                                <option value="">Todos los estados</option>
                                <?php 
                                $estados = getAllEstados();
                                foreach ($estados as $estado): 
                                ?>
                                    <option value="<?php echo $estado['id']; ?>" 
                                        <?php echo (isset($_GET['estado']) && $_GET['estado'] == $estado['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($estado['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Fila 2 -->
                        <div class="col-md-3">
                            <label for="numero_unidad" class="form-label">Unidad</label>
                            <select class="form-select select2" id="numero_unidad" name="numero_unidad">
                                <option value="">Todas las unidades</option>
                                <?php foreach ($unidades as $unidad): ?>
                                    <option value="<?php echo $unidad['id']; ?>" 
                                        <?php echo (isset($_GET['numero_unidad']) && $_GET['numero_unidad'] == $unidad['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($unidad['codigo_interno']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="nivel_urgencia" class="form-label">Nivel de Urgencia</label>
                            <select class="form-select" id="nivel_urgencia" name="nivel_urgencia">
                                <option value="">Todos los niveles</option>
                                <?php foreach ($niveles_urgencias as $nivel): ?>
                                    <option value="<?php echo $nivel['id']; ?>"
                                        <?php echo (isset($_GET['nivel_urgencia']) && $_GET['nivel_urgencia'] == $nivel['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($nivel['nombre_urgencia']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="grupo_funcion" class="form-label">Grupo Función</label>
                            <select class="form-select" id="grupo_funcion" name="grupo_funcion">
                                <option value="">Todos los grupos</option>
                                <?php foreach ($grupos_funciones as $grupo): ?>
                                    <option value="<?php echo $grupo['id']; ?>"
                                        <?php echo (isset($_GET['grupo_funcion']) && $_GET['grupo_funcion'] == $grupo['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($grupo['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="ubicacion" class="form-label">Ubicación</label>
                            <select class="form-select select2" id="ubicacion" name="ubicacion">
                                <option value="">Todas las ubicaciones</option>
                                <?php foreach ($localidades as $localidad): ?>
                                    <option value="<?php echo $localidad['id']; ?>" 
                                        <?php echo (isset($_GET['ubicacion']) && $_GET['ubicacion'] == $localidad['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($localidad['nombre_localidad']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Fila 3 -->
                        <div class="col-md-3">
                            <label for="solicitante" class="form-label">Solicitante</label>
                            <select class="form-select select2" id="solicitante" name="solicitante">
                                <option value="">Todos los solicitantes</option>
                                <?php foreach ($personal as $persona): ?>
                                    <option value="<?php echo $persona['id']; ?>" 
                                        <?php echo (isset($_GET['solicitante']) && $_GET['solicitante'] == $persona['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($persona['nombre_personal']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="conductor" class="form-label">Conductor</label>
                            <select class="form-select select2" id="conductor" name="conductor">
                                <option value="">Todos los conductores</option>
                                <?php foreach ($personal as $persona): ?>
                                    <option value="<?php echo $persona['id']; ?>" 
                                        <?php echo (isset($_GET['conductor']) && $_GET['conductor'] == $persona['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($persona['nombre_personal']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="mantenimiento" class="form-label">Mecánico</label>
                            <select class="form-select select2" id="mantenimiento" name="mantenimiento">
                                <option value="">Todos los mecánicos</option>
                                <?php foreach ($personal as $persona): ?>
                                    <option value="<?php echo $persona['id']; ?>" 
                                        <?php echo (isset($_GET['mantenimiento']) && $_GET['mantenimiento'] == $persona['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($persona['nombre_personal']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="habilitado" class="form-label">Estado Registro</label>
                            <select class="form-select" id="habilitado" name="habilitado">
                                <option value="">Todos</option>
                                <option value="1" <?php echo (isset($_GET['habilitado']) && $_GET['habilitado'] === '1') ? 'selected' : ''; ?>>Activos</option>
                                <option value="0" <?php echo (isset($_GET['habilitado']) && $_GET['habilitado'] === '0') ? 'selected' : ''; ?>>Inactivos</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12 d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn-filter">
                                🔍 Buscar
                            </button>
                            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn-clear">
                                🔄 Limpiar Filtros
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Tabla de Resultados -->
        <div class="table-container">
            <div class="table-header">
                <div class="results-count">
                    📊 Resultados encontrados: 
                    <strong><?php echo $result ? $result->num_rows : 0; ?></strong>
                </div>
                <?php if ($result && $result->num_rows > 0): ?>
                    <a href="exportar_pdf.php?<?php echo http_build_query($_GET); ?>" 
                       class="btn-export" target="_blank">
                        📄 Exportar a PDF
                    </a>
                <?php endif; ?>
            </div>
            
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>OT</th>
                            <th>N° Solicitud</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Unidad</th>
                            <th>Urgencia</th>
                            <th>Grupo</th>
                            <th>Especialidades</th>
                            <th>Ubicación</th>
                            <th>Solicitante</th>
                            <th>Conductor</th>
                            <th>Mecánico</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result && $result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                // Determinar clase de urgencia
                                $urgencia_nombre = getNivelUrgenciaNombre($row['nivel_urgencia']);
                                $urgencia_class = match(strtolower($urgencia_nombre)) {
                                    'alta', 'crítica' => 'urgencia-alta',
                                    'media', 'moderada' => 'urgencia-media',
                                    'media', 'moderada' => 'urgencia-media',
                                    default => 'urgencia-baja'
                                };
                                
                                // Determinar clase de estado
                                $estado_nombre = $row['estado_nombre'] ?? 'Pendiente';
                                $estado_class = match(strtolower($estado_nombre)) {
                                    'completado', 'finalizado', 'resuelto' => 'estado-completado',
                                    'en proceso', 'procesando', 'en progreso' => 'estado-proceso',
                                    default => 'estado-pendiente'
                                };
                                
                                echo "<tr>";
                                echo "<td><strong>#" . str_pad($row["id"], 4, '0', STR_PAD_LEFT) . "</strong></td>";
                                echo "<td>" . htmlspecialchars($row["numero_solicitud"]) . "</td>";
                                
                                // Fecha formateada
                                $fecha = new DateTime($row["fecha_solicitud"]);
                                echo "<td>" . $fecha->format('d/m/Y') . "</td>";
                                
                                // Badge de estado
                                echo "<td><span class='badge-estado {$estado_class}'>" . htmlspecialchars($estado_nombre) . "</span></td>";
                                
                                echo "<td><strong>" . htmlspecialchars($row["unidad_codigo"]) . "</strong></td>";
                                
                                // Badge de urgencia
                                echo "<td><span class='badge-urgencia {$urgencia_class}'>" . htmlspecialchars($urgencia_nombre) . "</span></td>";
                                
                                echo "<td>" . htmlspecialchars(getGrupoFuncionNombre($row['grupo_funcion'])) . "</td>";
                                
                                // Especialidades
                                echo "<td>";
                                $especialidades_solicitud = getEspecialidadesBySolicitudId($row['id']);
                                foreach ($especialidades_solicitud as $esp) {
                                    echo "<span class='badge-especialidad'>" . htmlspecialchars($esp) . "</span> ";
                                }
                                echo "</td>";
                                
                                echo "<td>" . htmlspecialchars($row["nombre_localidad"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["solicitante_nombre"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["conductor_nombre"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["mantenimiento_nombre"]) . "</td>";
                                
                                // Observaciones con truncado
                                $observaciones = htmlspecialchars($row["observaciones"]);
                                if (strlen($observaciones) > 50) {
                                    echo "<td title='{$observaciones}'>" . substr($observaciones, 0, 50) . "...</td>";
                                } else {
                                    echo "<td>{$observaciones}</td>";
                                }
                                
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='13'>";
                            echo "<div class='empty-state'>";
                            echo "<div class='empty-state-icon'>📋</div>";
                            echo "<h3>No se encontraron resultados</h3>";
                            echo "<p class='text-muted'>Intenta ajustar los filtros de búsqueda</p>";
                            echo "</div>";
                            echo "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($result && $result->num_rows > 0): ?>
                <div class="mt-3 text-center">
                    <small class="text-muted">
                        💡 Tip: Puedes ordenar los resultados combinando diferentes filtros
                    </small>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/vendor/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Inicializar Select2 con tema oscuro
            $('.select2').select2({
                theme: 'default',
                placeholder: 'Seleccionar...',
                allowClear: true,
                width: '100%'
            });
            
            // Animación de tooltips en observaciones
            $('[title]').each(function() {
                $(this).css('cursor', 'help');
            });
            
            // Resaltar filas con urgencia alta
            $('.urgencia-alta').closest('tr').css({
                'border-left': '4px solid #dc3545',
                'background-color': 'rgba(220, 53, 69, 0.05)'
            });
            
            // Efecto hover mejorado
            $('.table-custom tbody tr').hover(
                function() {
                    $(this).css('box-shadow', '0 4px 8px rgba(40, 167, 69, 0.2)');
                },
                function() {
                    $(this).css('box-shadow', 'none');
                }
            );
            
            // Auto-submit al cambiar filtros rápidos
            $('#estado, #nivel_urgencia').on('change', function() {
                if (confirm('¿Aplicar este filtro automáticamente?')) {
                    $(this).closest('form').submit();
                }
            });
            
            // Validación de fechas
            $('#fecha_inicio, #fecha_fin').on('change', function() {
                const fechaInicio = new Date($('#fecha_inicio').val());
                const fechaFin = new Date($('#fecha_fin').val());
                
                if (fechaInicio && fechaFin && fechaInicio > fechaFin) {
                    alert('⚠️ La fecha de inicio no puede ser mayor a la fecha fin');
                    $(this).val('');
                }
            });
            
            // Mensaje de confirmación al exportar
            $('.btn-export').on('click', function(e) {
                const numResultados = <?php echo $result ? $result->num_rows : 0; ?>;
                if (numResultados > 100) {
                    if (!confirm(`Se exportarán ${numResultados} registros. ¿Continuar?`)) {
                        e.preventDefault();
                    }
                }
            });
            
            // Shortcut de teclado para buscar (Ctrl + F)
            $(document).on('keydown', function(e) {
                if (e.ctrlKey && e.key === 'f') {
                    e.preventDefault();
                    $('#numero_solicitud').focus();
                }
            });
        });
    </script>
</body>
</html>