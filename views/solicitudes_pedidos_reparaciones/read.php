<?php
$projectRoot = dirname(__FILE__, 3); 
require_once dirname(__DIR__, 2) . '/config/config.php'; 
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';   
require_once ROOT_PATH . '/sec/auth_check.php';       
require_once $projectRoot . '/includes/functions.php'; 

requireLogin();
$_SESSION['last_activity'] = time();

// Obtener datos necesarios
$solicitudes = getAllSolicitudesPedidosReparaciones();
$niveles_urgencias = getAllNivelesUrgencias();
$grupos_funciones = getAllGruposFunciones();
$especialidades = getAllEspecialidadesTalleres();

$rol_id = $_SESSION['rol_id'];
$tienePermisoEscritura = in_array('escritura', $_SESSION['permissions']) || in_array('total', $_SESSION['permissions']);
$tienePermisoModificar = in_array('modificar', $_SESSION['permissions']) || in_array('total', $_SESSION['permissions']);
$tienePermisoEliminar = in_array('eliminar', $_SESSION['permissions']) || in_array('total', $_SESSION['permissions']);

include ROOT_PATH . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitudes de Reparación - EldoWay</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    
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
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
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
            margin: 0;
            letter-spacing: 1px;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-top: 1.5rem;
        }
        
        .btn-custom {
            padding: 0.625rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            border: none;
        }
        
        .btn-create {
            background: linear-gradient(135deg, var(--green-start), var(--green-end));
            color: white;
        }
        
        .btn-create:hover {
            background: linear-gradient(135deg, var(--green-end), var(--green-start));
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
            color: white;
        }
        
        .btn-back {
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--text-light);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .btn-back:hover {
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
            border-color: rgba(255, 255, 255, 0.3);
        }
        
        .table-container {
            background: linear-gradient(to bottom right, var(--bg-card), var(--bg-dark-secondary));
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
            border: 1px solid var(--border-subtle);
            overflow: hidden;
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
            font-size: 0.8rem;
            white-space: nowrap;
        }
        
        .table-custom tbody tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.2s ease;
        }
        
        .table-custom tbody tr:hover {
            background-color: rgba(40, 167, 69, 0.1);
        }
        
        .table-custom tbody td {
            padding: 0.875rem 0.75rem;
            color: var(--text-light);
            vertical-align: middle;
        }
        
        .badge-especialidad {
            background-color: rgba(40, 167, 69, 0.2);
            color: var(--green-start);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            display: inline-block;
            margin: 0.125rem;
            border: 1px solid var(--green-start);
        }
        
        .badge-urgencia {
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .urgencia-alta {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
        }
        
        .urgencia-media {
            background: linear-gradient(135deg, #fd7e14, #e8590c);
            color: white;
        }
        
        .urgencia-baja {
            background: linear-gradient(135deg, #28a745, #1e7e34);
            color: white;
        }
        
        .badge-estado {
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
            display: inline-block;
            margin-bottom: 0.5rem;
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
        
        .btn-action {
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.2s ease;
            border: none;
            margin: 0.125rem;
            white-space: nowrap;
        }
        
        .btn-update-state {
            background: linear-gradient(135deg, var(--info-blue), #0891b2);
            color: white;
        }
        
        .btn-update-state:hover {
            background: linear-gradient(135deg, #0891b2, var(--info-blue));
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(13, 202, 240, 0.4);
            color: white;
        }
        
        .btn-edit {
            background: linear-gradient(135deg, var(--warning-orange), #fd7e14);
            color: white;
        }
        
        .btn-edit:hover {
            background: linear-gradient(135deg, #fd7e14, var(--warning-orange));
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(253, 126, 20, 0.4);
            color: white;
        }
        
        .btn-delete {
            background: linear-gradient(135deg, var(--danger-red), #c82333);
            color: white;
        }
        
        .btn-delete:hover {
            background: linear-gradient(135deg, #c82333, var(--danger-red));
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.4);
            color: white;
        }
        
        .actions-cell {
            white-space: nowrap;
        }
        
        .text-truncate-custom {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
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
        .table-container {
            animation: fadeIn 0.5s ease-out;
        }
        
        /* Responsive */
        @media (max-width: 1200px) {
            .table-custom {
                font-size: 0.8rem;
            }
            
            .table-custom thead th,
            .table-custom tbody td {
                padding: 0.75rem 0.5rem;
            }
        }
        
        @media (max-width: 768px) {
            .page-header {
                padding: 1.5rem;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn-custom {
                width: 100%;
                text-align: center;
            }
            
            .table-container {
                padding: 1rem;
                border-radius: 10px;
            }
            
            .table-responsive {
                margin: -1rem;
                padding: 1rem;
            }
        }
    </style>
</head>

<body style="background-color: #f8f9fa; color: #212529;">
    <div class="main-container">
        <!-- Header -->
        <div class="container mt-5">
            <h1 class="page-title">🔧 Solicitudes de Pedidos de Reparaciones</h1>
            
            <div class="action-buttons">
                <?php if ($tienePermisoEscritura): ?>
                    <a href="create.php" class="btn btn-custom btn-create">
                        ➕ Crear Nueva Solicitud
                    </a>
                <?php endif; ?>
                <a href="<?php echo BASE_URL; ?>/includes/header.php" class="btn btn-custom btn-back">
                    ← Volver al Menú
                </a>
            </div>
        </div>
        
        <!-- Tabla de solicitudes -->
        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-striped" style="color: #212529; background-color: white;">
                    <thead style="background-color: #28a745; color: white;">
                        <tr>
                            <th>N° Sol.</th>
                            <th>Solicitante</th>
                            <th>Fecha</th>
                            <th>Conductor</th>
                            <th>Especialidad</th>
                            <th>Ubicación</th>
                            <th>Grupo</th>
                            <th>Urgencia</th>
                            <th>Mantenimiento</th>
                            <th>Unidad</th>
                            <th>Detalle</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody style="color: #212529;">
                        <?php if (empty($solicitudes)): ?>
                            <tr>
                                <td colspan="13">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">📋</div>
                                        <h3>No hay solicitudes registradas</h3>
                                        <p class="text-muted">Comienza creando una nueva solicitud de reparación</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($solicitudes as $solicitud): ?>
                                <tr>
                                    <!-- N° Solicitud -->
                                    <td>
                                        <strong>#<?php echo str_pad($solicitud['id'], 4, '0', STR_PAD_LEFT); ?></strong>
                                    </td>
                                    
                                    <!-- Solicitante -->
                                    <td><?php echo htmlspecialchars($solicitud['solicitante']); ?></td>
                                    
                                    <!-- Fecha -->
                                    <td>
                                        <?php 
                                        $fecha = new DateTime($solicitud['fecha_solicitud']);
                                        echo $fecha->format('d/m/Y');
                                        ?>
                                    </td>
                                    
                                    <!-- Conductor -->
                                    <td><?php echo htmlspecialchars($solicitud['conductor']); ?></td>
                                    
                                    <!-- Especialidades -->
                                    <td>
                                        <?php 
                                        $especialidades_solicitud = getEspecialidadesBySolicitudId($solicitud['id']);
                                        foreach ($especialidades_solicitud as $esp):
                                        ?>
                                            <span class="badge-especialidad"><?php echo htmlspecialchars($esp); ?></span>
                                        <?php endforeach; ?>
                                    </td>
                                    
                                    <!-- Ubicación -->
                                    <td><?php echo htmlspecialchars($solicitud['ubicacion']); ?></td>
                                    
                                    <!-- Grupo Función -->
                                    <td><?php echo htmlspecialchars(getGrupoFuncionNombre($solicitud['grupo_funcion'])); ?></td>
                                    
                                    <!-- Nivel Urgencia -->
                                    <td>
                                        <?php 
                                        $urgencia = getNivelUrgenciaNombre($solicitud['nivel_urgencia']);
                                        $urgencia_class = match(strtolower($urgencia)) {
                                            'alta', 'crítica' => 'urgencia-alta',
                                            'media', 'moderada' => 'urgencia-media',
                                            default => 'urgencia-baja'
                                        };
                                        ?>
                                        <span class="badge-urgencia <?php echo $urgencia_class; ?>">
                                            <?php echo htmlspecialchars($urgencia); ?>
                                        </span>
                                    </td>
                                    
                                    <!-- Mantenimiento -->
                                    <td><?php echo htmlspecialchars($solicitud['mantenimiento']); ?></td>
                                    
                                    <!-- Unidad -->
                                    <td>
                                        <strong><?php echo htmlspecialchars($solicitud['numero_unidad']); ?></strong>
                                    </td>
                                    
                                    <!-- Observaciones -->
                                    <td>
                                        <div class="text-truncate-custom" title="<?php echo htmlspecialchars($solicitud['observaciones']); ?>">
                                            <?php echo htmlspecialchars($solicitud['observaciones']); ?>
                                        </div>
                                    </td>
                                    
                                    <!-- Estado -->
                                    <td>
                                        <?php 
                                        $estado_actual = getEstadoActual($solicitud['id']);
                                        $estado_nombre = $estado_actual['nombre'];
                                        $estado_class = match(strtolower($estado_nombre)) {
                                            'completado', 'finalizado', 'resuelto' => 'estado-completado',
                                            'en proceso', 'procesando', 'en progreso' => 'estado-proceso',
                                            default => 'estado-pendiente'
                                        };
                                        ?>
                                        <span class="badge-estado <?php echo $estado_class; ?>">
                                            <?php echo htmlspecialchars($estado_nombre); ?>
                                        </span>
                                        
                                        <?php if ($tienePermisoModificar): ?>
                                            <br>
                                            <a href="actualizar_estado.php?id=<?php echo $solicitud['id']; ?>" 
                                               class="btn btn-action btn-update-state">
                                                🔄 Estado
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <!-- Acciones -->
                                    <td class="actions-cell">
                                        <?php if ($tienePermisoModificar): ?>
                                            <a href="update.php?id=<?php echo $solicitud['id']; ?>" 
                                               class="btn btn-action btn-edit"
                                               title="Editar solicitud">
                                                ✏️ Editar
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if ($tienePermisoEliminar): ?>
                                            <a href="delete.php?id=<?php echo $solicitud['id']; ?>" 
                                               class="btn btn-action btn-delete"
                                               title="Eliminar solicitud">
                                                🗑️ Eliminar
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Información adicional -->
        <div class="mt-3 text-muted text-center">
            <small>
                Total de solicitudes: <strong><?php echo count($solicitudes); ?></strong>
            </small>
        </div>
    </div>
    
    <script src="<?php echo BASE_URL; ?>/vendor/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Tooltips para mostrar detalles completos
        document.querySelectorAll('[title]').forEach(el => {
            el.addEventListener('mouseenter', function() {
                this.style.cursor = 'help';
            });
        });
        
        // Resaltar filas con urgencia alta
        document.querySelectorAll('.urgencia-alta').forEach(badge => {
            const row = badge.closest('tr');
            row.style.borderLeft = '4px solid #dc3545';
        });
    </script>
</body>
</html>