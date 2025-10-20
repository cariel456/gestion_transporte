<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/db_config.php';
require_once ROOT_PATH . '/sec/init.php';
require_once __DIR__ . '/session.php'; 
require_once ROOT_PATH . '/sec/auth_check.php';    

requireLogin();

$_SESSION['last_activity'] = time();

if (!isset($conn) || $conn->connect_error) {
    die("Error de conexión a la base de datos: " . ($conn->connect_error ?? "Conexión no establecida"));
}

function checkPermission($conn, $userId, $ventana) {
    
    static $userRolId = null;

    if ($userRolId === null) {
        $stmt = $conn->prepare("SELECT rol_id FROM usuarios WHERE id = ?");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($userRolId);
        $stmt->fetch();
        $stmt->close();

        if (!$userRolId) {
            return false;
        }
    }

    $permitido=0;

    // Verificar si el rol tiene permiso para la ventana específica
    $stmt = $conn->prepare("SELECT permitido FROM permisos_ventanas_roles WHERE rol_id = ? AND ventana = ?");
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param("is", $userRolId, $ventana);
    $stmt->execute();
    $stmt->bind_result($permitido);
    $stmt->fetch();
    $stmt->close();

    return $permitido == 1;
}

// Asumiendo que $userId está definido en alguna parte anterior del script, por ejemplo:
$userId = $_SESSION['user_id'] ?? null; // Asegúrate de que esta línea esté correcta según tu sistema de sesiones
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EldoWay - Sistema de Gestión</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-dark-primary: #1b1b1b;
            --bg-dark-secondary: #212121;
            --text-light: #f8f9fa;
            --text-muted: #adb5bd;
            --green-start: #28a745;
            --green-end: #1c7430;
            --accent-amber: #ffc107;
            --hover-green: rgba(40, 167, 69, 0.1);
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--bg-dark-primary);
            color: var(--text-light);
        }
        
        /* Navbar personalizado */
        .navbar-custom {
            background: linear-gradient(135deg, var(--bg-dark-secondary) 0%, #2d2d2d 100%);
            border-bottom: 2px solid var(--green-start);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
        }
        
        /* Logo EldoWay */
        .navbar-brand-custom {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--green-start), var(--green-end));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 2px;
            text-transform: capitalize;
            margin-right: 1rem;
            transition: transform 0.3s ease;
        }
        
        .navbar-brand-custom:hover {
            transform: scale(1.05);
        }
        
        /* Separador visual */
        .brand-separator {
            color: var(--text-muted);
            margin: 0 0.75rem;
            font-size: 1.5rem;
        }
        
        /* Subtítulo */
        .navbar-subtitle {
            color: var(--accent-amber);
            font-size: 0.9rem;
            font-weight: 400;
            letter-spacing: 1px;
        }
        
        /* Links del navbar */
        .nav-link-custom {
            color: var(--text-muted) !important;
            font-weight: 500;
            letter-spacing: 0.5px;
            padding: 0.5rem 1rem !important;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .nav-link-custom::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--green-start), var(--green-end));
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        
        .nav-link-custom:hover {
            color: var(--text-light) !important;
            background-color: var(--hover-green);
            border-radius: 6px;
        }
        
        .nav-link-custom:hover::after {
            width: 80%;
        }
        
        /* Dropdown menu */
        .dropdown-menu-custom {
            background: linear-gradient(to bottom, #2d2d2d, var(--bg-dark-secondary));
            border: 1px solid rgba(40, 167, 69, 0.3);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);
            margin-top: 0.5rem;
        }
        
        .dropdown-item-custom {
            color: var(--text-muted);
            transition: all 0.2s ease;
            padding: 0.75rem 1.5rem;
            border-left: 3px solid transparent;
        }
        
        .dropdown-item-custom:hover {
            background-color: var(--hover-green);
            color: var(--text-light);
            border-left-color: var(--green-start);
            padding-left: 1.75rem;
        }
        
        /* Botón cerrar sesión */
        .btn-logout {
            background: linear-gradient(135deg, var(--green-start), var(--green-end));
            color: white;
            border: none;
            padding: 0.5rem 1.25rem;
            border-radius: 6px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        
        .btn-logout:hover {
            background: linear-gradient(135deg, var(--green-end), var(--green-start));
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
            color: white;
        }
        
        /* Toggler personalizado */
        .navbar-toggler-custom {
            border: 2px solid var(--green-start);
            padding: 0.5rem 0.75rem;
        }
        
        .navbar-toggler-custom:focus {
            box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
        }
        
        /* Responsive adjustments */
        @media (max-width: 991px) {
            .navbar-brand-custom {
                font-size: 1.5rem;
            }
            
            .navbar-subtitle {
                font-size: 0.8rem;
            }
            
            .nav-link-custom {
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }
            
            .dropdown-menu-custom {
                background: var(--bg-dark-secondary);
            }
        }
        /* Agregar después de la línea 120 (después de @media) */
.page-subtitle {
    background: linear-gradient(135deg, var(--bg-dark-secondary), #2d2d2d);
    border-left: 4px solid var(--green-start);
    padding: 1rem 1.5rem;
    margin: 1.5rem 0;
    color: var(--text-muted);
    font-size: 0.95rem;
    font-weight: 300;
    letter-spacing: 0.5px;
    border-radius: 6px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

.page-subtitle strong {
    color: var(--accent-amber);
    font-weight: 500;
}
    </style>
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
    <div class="container-fluid px-3 px-lg-4">
        <!-- Brand -->
        <a class="navbar-brand d-flex align-items-center text-decoration-none" href="<?php echo BASE_URL; ?>/includes/header.php">
            <span class="navbar-brand-custom">EldoWay</span>
            <!--<span class="brand-separator d-none d-lg-inline">|</span>
            <span class="navbar-subtitle d-none d-lg-inline">Monitoreo y Control</span>-->
        </a>
        
        <!-- Toggler button -->
        <button class="navbar-toggler navbar-toggler-custom" 
                type="button" 
                data-bs-toggle="collapse" 
                data-bs-target="#navbarNav" 
                aria-controls="navbarNav" 
                aria-expanded="false" 
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Navbar content -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                
                <!-- REPORT -->
                <li class="nav-item">
                    <a class="nav-link nav-link-custom" href="<?php echo BASE_URL; ?>/upload_and_filter.php">
                        REPORT
                    </a>
                </li>

                <!-- TALLER -->
                <?php if (checkPermission($conn, $userId, 'taller')): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle nav-link-custom" 
                           href="#" 
                           id="navbarDropdownTaller" 
                           role="button" 
                           data-bs-toggle="dropdown" 
                           aria-expanded="false">
                            TALLER
                        </a>
                        <ul class="dropdown-menu dropdown-menu-custom" aria-labelledby="navbarDropdownTaller">
                            <?php if (checkPermission($conn, $userId, 'pedidos_taller')): ?>
                                <li><a class="dropdown-item dropdown-item-custom" href="<?php echo BASE_URL; ?>/views/solicitudes_pedidos_reparaciones/read.php">Pedidos Taller</a></li>
                            <?php endif; ?>
                            <?php if (checkPermission($conn, $userId, 'consultas_taller')): ?>
                                <li><a class="dropdown-item dropdown-item-custom" href="<?php echo BASE_URL; ?>/views/solicitudes_pedidos_reparaciones/consultas.php">Consultas</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- UNIDADES -->
                <?php if (checkPermission($conn, $userId, 'unidades')): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle nav-link-custom" 
                           href="#" 
                           id="navbarDropdownUnidades" 
                           role="button" 
                           data-bs-toggle="dropdown" 
                           aria-expanded="false">
                            UNIDADES
                        </a>
                        <ul class="dropdown-menu dropdown-menu-custom" aria-labelledby="navbarDropdownUnidades">
                            <?php if (checkPermission($conn, $userId, 'unidades_read')): ?>
                                <li><a class="dropdown-item dropdown-item-custom" href="<?php echo BASE_URL; ?>/views/unidades/read.php">Ver Unidades</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- HORARIOS -->
                <?php if (checkPermission($conn, $userId, 'horarios')): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle nav-link-custom" 
                           href="#" 
                           id="navbarDropdownHorarios" 
                           role="button" 
                           data-bs-toggle="dropdown" 
                           aria-expanded="false">
                            HORARIOS
                        </a>
                        <ul class="dropdown-menu dropdown-menu-custom" aria-labelledby="navbarDropdownHorarios">
                            <?php if (checkPermission($conn, $userId, 'distribucion_turnos')): ?>
                                <li><a class="dropdown-item dropdown-item-custom" href="<?php echo BASE_URL; ?>/views/turnos_distribucion/read.php">Distribución de Turnos</a></li>
                            <?php endif; ?>
                            <?php if (checkPermission($conn, $userId, 'horarios_interurbanos')): ?>
                                <li><a class="dropdown-item dropdown-item-custom" href="<?php echo BASE_URL; ?>/views/horarios_interurbanos/read.php">Gestión Horarios Interurbanos</a></li>
                            <?php endif; ?>
                            <?php if (checkPermission($conn, $userId, 'horarios_urbanos')): ?>
                                <li><a class="dropdown-item dropdown-item-custom" href="<?php echo BASE_URL; ?>/views/horarios_urbanos/index.php">Horarios Urbanos</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- PERSONAL -->
                <?php if (checkPermission($conn, $userId, 'personal')): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle nav-link-custom" 
                           href="#" 
                           id="navbarDropdownPersonal" 
                           role="button" 
                           data-bs-toggle="dropdown" 
                           aria-expanded="false">
                            PERSONAL
                        </a>
                        <ul class="dropdown-menu dropdown-menu-custom" aria-labelledby="navbarDropdownPersonal">
                            <?php if (checkPermission($conn, $userId, 'personal_read')): ?>
                                <li><a class="dropdown-item dropdown-item-custom" href="<?php echo BASE_URL; ?>/views/personal/read.php">Personal</a></li>
                            <?php endif; ?>
                            <?php if (checkPermission($conn, $userId, 'categoria_personal')): ?>
                                <li><a class="dropdown-item dropdown-item-custom" href="<?php echo BASE_URL; ?>/views/categoria_persona/read.php">Categoria Personal</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- PARÁMETROS -->
                <?php if (checkPermission($conn, $userId, 'parametros')): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle nav-link-custom" 
                           href="#" 
                           id="navbarDropdownParametros" 
                           role="button" 
                           data-bs-toggle="dropdown" 
                           aria-expanded="false">
                            PARÁMETROS
                        </a>
                        <ul class="dropdown-menu dropdown-menu-custom" aria-labelledby="navbarDropdownParametros">
                            <?php if (checkPermission($conn, $userId, 'parametros_paises')): ?>
                                <li><a class="dropdown-item dropdown-item-custom" href="<?php echo BASE_URL; ?>/views/paises/read.php">Países</a></li>
                            <?php endif; ?>
                            <?php if (checkPermission($conn, $userId, 'parametros_provincias')): ?>
                                <li><a class="dropdown-item dropdown-item-custom" href="<?php echo BASE_URL; ?>/views/provincias/read.php">Provincias</a></li>
                            <?php endif; ?>
                            <?php if (checkPermission($conn, $userId, 'parametros_localidades')): ?>
                                <li><a class="dropdown-item dropdown-item-custom" href="<?php echo BASE_URL; ?>/views/localidades/read.php">Localidades</a></li>
                            <?php endif; ?>
                            <?php if (checkPermission($conn, $userId, 'parametros_niveles_urgencias')): ?>
                                <li><a class="dropdown-item dropdown-item-custom" href="<?php echo BASE_URL; ?>/views/niveles_urgencias/read.php">Niveles de Urgencia</a></li>
                            <?php endif; ?>
                            <?php if (checkPermission($conn, $userId, 'parametros_especialidades_taller')): ?>
                                <li><a class="dropdown-item dropdown-item-custom" href="<?php echo BASE_URL; ?>/views/especialidades_taller/read.php">Especialidades de Taller</a></li>
                            <?php endif; ?>
                            <?php if (checkPermission($conn, $userId, 'parametros_servicios')): ?>
                                <li><a class="dropdown-item dropdown-item-custom" href="<?php echo BASE_URL; ?>/views/servicios/read.php">Servicios</a></li>
                            <?php endif; ?>
                            <?php if (checkPermission($conn, $userId, 'parametros_terminales')): ?>
                                <li><a class="dropdown-item dropdown-item-custom" href="<?php echo BASE_URL; ?>/views/terminales/read.php">Terminales</a></li>
                            <?php endif; ?>
                            <?php if (checkPermission($conn, $userId, 'parametros_turnos')): ?>
                                <li><a class="dropdown-item dropdown-item-custom" href="<?php echo BASE_URL; ?>/views/turnos/read.php">Turnos</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
            
            <!-- Cerrar sesión -->
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link btn-logout" href="<?php echo BASE_URL; ?>/includes/logout.php">
                        Cerrar Sesión
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script src="<?php echo BASE_URL; ?>/vendor/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

<script>
// Cerrar dropdown al hacer click en un item
document.querySelectorAll('.dropdown-item-custom').forEach(item => {
    item.addEventListener('click', function() {
        const dropdown = bootstrap.Dropdown.getInstance(this.closest('.dropdown').querySelector('[data-bs-toggle="dropdown"]'));
        if (dropdown) {
            dropdown.hide();
        }
    });
});

// Añadir clase activa al dropdown actual
document.addEventListener('DOMContentLoaded', function() {
    const currentPath = window.location.pathname;
    document.querySelectorAll('.dropdown-item-custom').forEach(item => {
        if (item.getAttribute('href') === currentPath) {
            item.classList.add('active');
            item.closest('.dropdown').querySelector('.dropdown-toggle').classList.add('active');
        }
    });
});
</script>

</body>
</html>