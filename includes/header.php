<?php require_once __DIR__ . '/auth.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grupo Horianski</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar-brand img {
            height: 50px;
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>/views/dashboard.php">
                <img src="<?php echo BASE_URL; ?>../extras/lgh.jpg" alt="Logo Grupo Horianski">
                Monitoreo y Control
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <?php if ($_SESSION['user_permissions']['leer']): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                TALLER
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/solicitudes_pedidos_reparaciones/read.php">Pedidos Taller</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                PARÁMETROS
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/unidades/read.php">Unidades</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/categoria_persona/read.php">Categorías de Personal</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownAdmin" role="button" data-bs-toggle="dropdown" aria-expanded="false">SEGURIDAD</a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdownAdmin">
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/roles/read.php">Roles Usuarios</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/usuarios/read.php">Usuarios</a></li>
                            </ul>
                        </li>
                    
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/includes/logout.php">Cerrar sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>