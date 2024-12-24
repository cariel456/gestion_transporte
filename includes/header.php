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
    <title>Grupo Horianski</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar-brand img { height: 80px; margin-right: 10px; margin-left: 0; }
        .navbar-nav .nav-link { color: rgba(255,255,255,.75); }
        .navbar-nav .nav-link:hover { color: rgba(255,255,255,1); }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="<?php echo BASE_URL; ?>/includes/header.php">
            <img src="<?php echo BASE_URL; ?>../extras/lgh.jpg" alt="Logo Grupo Horianski">
            Monitoreo y Control
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if (checkPermission($conn, $userId, 'taller')): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            TALLER
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <?php if (checkPermission($conn, $userId, 'pedidos_taller')): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/solicitudes_pedidos_reparaciones/read.php">Pedidos Taller</a></li>
                            <?php endif; ?>
                            <?php if (checkPermission($conn, $userId, 'consultas_taller')): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/solicitudes_pedidos_reparaciones/consultas.php">Consultas</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <?php if (checkPermission($conn, $userId, 'unidades')): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUnidades" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            UNIDADES
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownUnidades">
                            <?php if (checkPermission($conn, $userId, 'unidades_read')): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/unidades/read.php">Ver Unidades</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <?php if (checkPermission($conn, $userId, 'horarios')): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownHorarios" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            HORARIOS
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownHorarios">
                            <?php if (checkPermission($conn, $userId, 'distribucion_turnos')): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/turnos_distribucion/read.php">Distribución de Turnos</a></li>
                            <?php endif; ?>
                            <?php if (checkPermission($conn, $userId, 'horarios_interurbanos')): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/horarios_interurbanos/read.php">Gestión Horarios Interurbanos</a></li>
                            <?php endif; ?>
                            <?php if (checkPermission($conn, $userId, 'horarios_urbanos')): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/horarios_urbanos/index.php">Horarios Urbanos</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <?php if (checkPermission($conn, $userId, 'personal')): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownPersonal" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            PERSONAL
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownPersonal">
                            <?php if (checkPermission($conn, $userId, 'personal_read')): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/personal/read.php">Personal</a></li>
                            <?php endif; ?>
                            <?php if (checkPermission($conn, $userId, 'categoria_personal')): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/categoria_persona/read.php">Categoria Personal</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <?php if (checkPermission($conn, $userId, 'parametros')): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownParametros" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            PARÁMETROS
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownParametros">
                            <?php if (checkPermission($conn, $userId, 'parametros_paises')): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/paises/read.php">Países</a></li>
                            <?php endif; ?>
                            <?php if (checkPermission($conn, $userId, 'parametros_provincias')): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/provincias/read.php">Provincias</a></li>
                            <?php endif; ?>
                            <?php if (checkPermission($conn, $userId, 'parametros_localidades')): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/localidades/read.php">Localidades</a></li>
                            <?php endif; ?>
                            <?php if (checkPermission($conn, $userId, 'parametros_niveles_urgencias')): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/niveles_urgencias/read.php">Niveles de Urgencia</a></li>
                            <?php endif; ?>
                            <?php if (checkPermission($conn, $userId, 'parametros_especialidades_taller')): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/especialidades_taller/read.php">Especialidades de Taller</a></li>
                            <?php endif; ?>
                            <?php if (checkPermission($conn, $userId, 'parametros_servicios')): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/servicios/read.php">Servicios</a></li>
                            <?php endif; ?>
                            <?php if (checkPermission($conn, $userId, 'parametros_terminales')): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/terminales/read.php">Terminales</a></li>
                            <?php endif; ?>
                            <?php if (checkPermission($conn, $userId, 'parametros_turnos')): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/turnos/read.php">Turnos</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/includes/logout.php">Cerrar Sesión</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>