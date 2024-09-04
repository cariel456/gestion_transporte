<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once ROOT_PATH . '/sec/init.php';
require_once __DIR__ . '/session.php'; 
require_once ROOT_PATH . '/sec/auth_check.php';       
requireLogin();
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
                <?php //if (checkPermission('taller', 'leer')): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            TALLER
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <?php //if (checkPermission('taller', 'subitems', 'pedidos_taller')): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/solicitudes_pedidos_reparaciones/read.php">Pedidos Taller</a></li>
                            <?php //endif; ?>
                            <?php //if (checkPermission('taller', 'subitems', 'consultas')): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/solicitudes_pedidos_reparaciones/consultas.php">Consultas</a></li>
                            <?php //endif; ?>
                        </ul>
                    </li>
                <?php //endif; ?>

                <!--UNIDADES-->
                <?php //if (checkPermission('unidades', 'leer')): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownConfig" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        UNIDADES
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownConfig">
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/unidades/read.php">Unidades</a></li>
                    </ul>
                </li>
                <?php //endif; ?>

                <!--HORARIOS-->
                <?php //if (checkPermission('unidades', 'leer')): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownConfig" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        HORARIOS
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownConfig">
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/turnos_distribucion/read.php">Distribución de Turnos</a></li>
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/horarios_interurbanos/read.php">Gestión Horarios Interurbanos</a></li>
                    </ul>
                </li>
                <?php //endif; ?>  

                <!--PERSONAL-->
                <?php //if (checkPermission('personal', 'leer')): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownConfig" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        PERSONAL
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownConfig">
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/personal/read.php">Personal</a></li>
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/categoria_persona/read.php">Categoria Personal</a></li>
                    </ul>
                </li>
                <?php //endif; ?>

                <?php //if (checkPermission('parametros', 'leer')): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownConfig" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        PARÁMETROS
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownConfig">
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/paises/read.php">Países</a></li>
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/provincias/read.php">Provincias</a></li>
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/localidades/read.php">Localidades</a></li>
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/niveles_urgencias/read.php">Niveles de Urgencia</a></li>

                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/especialidades_taller/read.php">Especialidades de Taller</a></li>
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/servicios/read.php">Servicios</a></li>
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/terminales/read.php">Terminales</a></li>
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/turnos/read.php">Turnos</a></li>
                    </ul>
                </li>
                <?php //endif; ?>    
            </div>
        
            <!--CERRAR SESION-->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/includes/logout.php">Cerrar Sesión</a>
                </li>
            </ul>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>