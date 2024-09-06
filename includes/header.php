<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once ROOT_PATH . '/sec/init.php';
require_once __DIR__ . '/session.php'; 
require_once ROOT_PATH . '/sec/auth_check.php';       
requireLogin();

$permissions = $_SESSION['permissions'];

function hasPermission($section, $action = null) {
    global $permissions;
    if ($action) {
        return in_array($action, $permissions[$section]);
    }
    return isset($permissions[$section]);
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grupo Horianski</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar-brand img {
            height: 80px;
            margin-right: 10px;
            margin-left: 0;
        }
        .navbar-nav .nav-link {
            color: rgba(255,255,255,.75);
        }
        .navbar-nav .nav-link:hover {
            color: rgba(255,255,255,1);
        }
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
                <?php if (hasPermission('taller')): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            TALLER
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <?php if (hasPermission('taller', 'pedidos_taller')): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/solicitudes_pedidos_reparaciones/read.php">Pedidos Taller</a></li>
                            <?php endif; ?>
                            <?php if (hasPermission('taller', 'consultas')): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/solicitudes_pedidos_reparaciones/consultas.php">Consultas</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if (hasPermission('unidades')): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownConfig" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            UNIDADES
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownConfig">
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/unidades/read.php">Unidades</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if (hasPermission('horarios')): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownConfig" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            HORARIOS
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownConfig">
                            <?php if (hasPermission('horarios', 'distribucion_turnos')): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/turnos_distribucion/read.php">Distribución de Turnos</a></li>
                            <?php endif; ?>
                            <?php if (hasPermission('horarios', 'gestion_horarios_interurbanos')): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/horarios_interurbanos/read.php">Gestión Horarios Interurbanos</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if (hasPermission('personal')): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownConfig" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            PERSONAL
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownConfig">
                            <?php if (hasPermission('personal', 'personal')): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/personal/read.php">Personal</a></li>
                            <?php endif; ?>
                            <?php if (hasPermission('personal', 'categoria_personal')): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/categoria_persona/read.php">Categoria Personal</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if (hasPermission('parametros')): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownConfig" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            PARÁMETROS
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownConfig">
                            <?php if (hasPermission('parametros', 'paises')): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/paises/read.php">Países</a></li>
                            <?php endif; ?>
                            <?php if (hasPermission('parametros', 'provincias')): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/provincias/read.php">Provincias</a></li>
                            <?php endif; ?>
                            <?php if (hasPermission('parametros', 'localidades')): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/localidades/read.php">Localidades</a></li>
                            <?php endif; ?>
                            <?php if (hasPermission('parametros', 'niveles_urgencias')): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/niveles_urgencias/read.php">Niveles de Urgencia</a></li>
                            <?php endif; ?>
                            <?php if (hasPermission('parametros', 'especialidades_taller')): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/especialidades_taller/read.php">Especialidades de Taller</a></li>
                            <?php endif; ?>
                            <?php if (hasPermission('parametros', 'servicios')): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/servicios/read.php">Servicios</a></li>
                            <?php endif; ?>
                            <?php if (hasPermission('parametros', 'terminales')): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/terminales/read.php">Terminales</a></li>
                            <?php endif; ?>
                            <?php if (hasPermission('parametros', 'turnos')): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/turnos/read.php">Turnos</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        
            <!--CERRAR SESION-->
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