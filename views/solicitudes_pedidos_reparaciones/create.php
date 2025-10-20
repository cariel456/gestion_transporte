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
    <title>Crear Solicitud de Reparación - EldoWay</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;600;700&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <style>
        :root {
            --primary-green: #28a745;
            --primary-green-dark: #1e7e34;
            --accent-amber: #ffc107;
            --text-dark: #212529;
            --text-muted: #6c757d;
            --bg-light: #f8f9fa;
            --border-color: #dee2e6;
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.08);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.12);
        }
        
        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-family: 'Roboto', sans-serif;
            padding-bottom: 3rem;
        }
        
        .main-container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .page-header {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-md);
            border-left: 5px solid var(--primary-green);
        }
        
        .page-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary-green);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .form-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
        }
        
        .section-divider {
            border-top: 2px solid var(--border-color);
            margin: 2rem 0 1.5rem 0;
            padding-top: 1.5rem;
        }
        
        .section-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .section-icon {
            color: var(--primary-green);
            font-size: 1.25rem;
        }
        
        .form-label {
            color: var(--text-dark);
            font-weight: 500;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }
        
        .form-label .required {
            color: #dc3545;
            margin-left: 0.25rem;
        }
        
        .form-control,
        .form-select {
            border: 2px solid var(--border-color);
            border-radius: 8px;
            padding: 0.625rem 0.875rem;
            font-size: 0.95rem;
            color: var(--text-dark);
            background-color: white;
            transition: all 0.3s ease;
        }
        
        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.15);
            background-color: white;
        }
        
        .form-control::placeholder {
            color: #adb5bd;
        }
        
        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }
        
        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--multiple {
            border: 2px solid var(--border-color);
            border-radius: 8px;
            min-height: 44px;
            background-color: white;
        }
        
        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.15);
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px;
            color: var(--text-dark);
        }
        
        .alert {
            border-radius: 8px;
            border: none;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-sm);
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        .btn-group-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px solid var(--border-color);
        }
        
        .btn {
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            border: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-green), var(--primary-green-dark));
            color: white;
            box-shadow: var(--shadow-sm);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-green-dark), var(--primary-green));
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
            color: white;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
            color: white;
        }
        
        .form-hint {
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-top: 0.375rem;
            display: block;
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
        .form-card {
            animation: fadeIn 0.5s ease-out;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .page-header,
            .form-card {
                padding: 1.5rem;
            }
            
            .page-title {
                font-size: 1.5rem;
            }
            
            .btn-group-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Header -->
        <div class="page-header">
            <h1 class="page-title">
                <span>📝</span>
                Nueva Solicitud de Reparación
            </h1>
        </div>
        
        <!-- Formulario -->
        <div class="form-card">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <strong>⚠️ Error:</strong> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" id="formSolicitud">
                
                <!-- Sección: Información Básica -->
                <div class="section-title">
                    <span class="section-icon">👤</span>
                    Información del Solicitante
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="solicitante" class="form-label">
                            Solicitante <span class="required">*</span>
                        </label>
                        <select class="form-select select2" id="solicitante" name="solicitante" required>
                            <option value="">Seleccione un solicitante</option>
                            <?php foreach ($personal as $solicitante): ?>
                                <option value="<?php echo $solicitante['id']; ?>">
                                    <?php echo htmlspecialchars($solicitante['nombre_personal']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="fecha_solicitud" class="form-label">
                            Fecha de Solicitud <span class="required">*</span>
                        </label>
                        <input type="datetime-local" 
                               class="form-control" 
                               id="fecha_solicitud" 
                               name="fecha_solicitud" 
                               value="<?php echo date('Y-m-d\TH:i'); ?>"
                               required>
                        <small class="form-hint">Fecha y hora del reporte</small>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="numero_solicitud" class="form-label">
                        Número de Solicitud
                    </label>
                    <input type="number" 
                           class="form-control" 
                           id="numero_solicitud" 
                           name="numero_solicitud"
                           placeholder="Ej: 2025001">
                    <small class="form-hint">Opcional: Se generará automáticamente si se deja en blanco</small>
                </div>
                
                <!-- Sección: Información de la Unidad -->
                <div class="section-divider">
                    <div class="section-title">
                        <span class="section-icon">🚌</span>
                        Información de la Unidad
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="numero_unidad" class="form-label">
                            Unidad <span class="required">*</span>
                        </label>
                        <select class="form-select select2" id="numero_unidad" name="numero_unidad" required>
                            <option value="">Seleccione una unidad</option>
                            <?php foreach ($unidades as $unidad): ?>
                                <option value="<?php echo $unidad['id']; ?>">
                                    <?php echo htmlspecialchars($unidad['codigo_interno']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="nombre_completo_conductor" class="form-label">
                            Conductor
                        </label>
                        <select class="form-select select2" id="nombre_completo_conductor" name="nombre_completo_conductor">
                            <option value="">Seleccione un conductor</option>
                            <?php foreach ($personal as $persona): ?>
                                <option value="<?php echo $persona['id']; ?>">
                                    <?php echo htmlspecialchars($persona['nombre_personal']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="ubicacion" class="form-label">
                        Ubicación <span class="required">*</span>
                    </label>
                    <select class="form-select select2" id="ubicacion" name="ubicacion" required>
                        <option value="">Seleccione una ubicación</option>
                        <?php foreach ($localidades as $localidad): ?>
                            <option value="<?php echo $localidad['id']; ?>">
                                <?php echo htmlspecialchars($localidad['nombre_localidad']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="form-hint">Lugar donde se encuentra la unidad</small>
                </div>
                
                <!-- Sección: Detalles del Problema -->
                <div class="section-divider">
                    <div class="section-title">
                        <span class="section-icon">🔧</span>
                        Detalles del Problema
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="especialidades" class="form-label">
                            Especialidades Requeridas <span class="required">*</span>
                        </label>
                        <select multiple 
                                class="form-select" 
                                id="especialidades" 
                                name="especialidades[]" 
                                required
                                size="4">
                            <?php foreach ($especialidades as $especialidad): ?>
                                <option value="<?php echo $especialidad['id']; ?>">
                                    <?php echo htmlspecialchars($especialidad['nombre_especialidad']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-hint">Mantén presionado Ctrl/Cmd para seleccionar varios</small>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nivel_urgencia" class="form-label">
                                Nivel de Urgencia <span class="required">*</span>
                            </label>
                            <select class="form-select" id="nivel_urgencia" name="nivel_urgencia" required>
                                <option value="">Seleccione un nivel</option>
                                <?php foreach ($niveles_urgencias as $nivel): ?>
                                    <option value="<?php echo $nivel['id']; ?>">
                                        <?php echo htmlspecialchars($nivel['nombre_urgencia']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="grupo_funcion" class="form-label">
                                Grupo Función <span class="required">*</span>
                            </label>
                            <select class="form-select" id="grupo_funcion" name="grupo_funcion" required>
                                <option value="">Seleccione un grupo</option>
                                <?php foreach ($grupos_funciones as $grupo): ?>
                                    <option value="<?php echo $grupo['id']; ?>">
                                        <?php echo htmlspecialchars($grupo['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="observaciones" class="form-label">
                        Detalle del Pedido <span class="required">*</span>
                    </label>
                    <textarea class="form-control" 
                              id="observaciones" 
                              name="observaciones" 
                              rows="4"
                              placeholder="Describa detalladamente el problema o reparación necesaria..."
                              required></textarea>
                    <small class="form-hint">Sea lo más específico posible sobre el problema</small>
                </div>
                
                <!-- Sección: Asignación -->
                <div class="section-divider">
                    <div class="section-title">
                        <span class="section-icon">👷</span>
                        Asignación de Personal
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="nombre_completo_mantenimiento" class="form-label">
                        Personal de Mantenimiento
                    </label>
                    <select class="form-select select2" id="nombre_completo_mantenimiento" name="nombre_completo_mantenimiento">
                        <option value="">Seleccione personal de mantenimiento</option>
                        <?php foreach ($personal as $persona): ?>
                            <option value="<?php echo $persona['id']; ?>">
                                <?php echo htmlspecialchars($persona['nombre_personal']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="form-hint">Opcional: Se puede asignar posteriormente</small>
                </div>
                
                <!-- Botones de Acción -->
                <div class="btn-group-actions">
                    <button type="submit" class="btn btn-primary">
                        <span>✓</span>
                        Crear Solicitud
                    </button>
                    <a href="read.php" class="btn btn-secondary">
                        <span>✕</span>
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script src="<?php echo BASE_URL; ?>/vendor/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Inicializar Select2
            $('.select2').select2({
                placeholder: "Seleccione una opción",
                allowClear: true,
                width: '100%'
            });
            
            // Validación del formulario
            $('#formSolicitud').on('submit', function(e) {
                const especialidades = $('#especialidades').val();
                
                if (!especialidades || especialidades.length === 0) {
                    e.preventDefault();
                    alert('⚠️ Debe seleccionar al menos una especialidad');
                    $('#especialidades').focus();
                    return false;
                }
                
                // Confirmación antes de enviar
                if (!confirm('¿Confirma que desea crear esta solicitud de reparación?')) {
                    e.preventDefault();
                    return false;
                }
            });
            
            // Resaltar campos requeridos vacíos al perder el foco
            $('input[required], select[required], textarea[required]').on('blur', function() {
                if (!$(this).val()) {
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
        });
    </script>
</body>
</html>