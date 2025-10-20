<?php
require_once '../config/config.php';
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';          
require_once ROOT_PATH . '/sec/auth_check.php';    
require_once ROOT_PATH . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $user = getUserByUsername($username);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['nombre_usuario'];
        $_SESSION['rol_id'] = $user['rol_id'];
        $_SESSION['permissions'] = getUserPermissions($user['id']);

        header("Location: " . BASE_URL . "/includes/header.php");
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - Grupo Horianski</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #1b1b1b; /* Fondo oscuro */
            font-family: 'Roboto', sans-serif; /* Fuente general */
            color: #f8f9fa; /* Color del texto claro */
        }
        .login-container {
            max-width: 400px;
            margin-top: 100px;
            padding: 30px;
            background: linear-gradient(to bottom right, #2d2d2d, #212121); /* Degradado oscuro */
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.7);
        }
        .company-name {
            text-align: center;
            margin-bottom: 20px;
            font-size: 2.5rem; /* Tamaño del texto */
            font-weight: 700; /* Negrita */
            background: linear-gradient(to right, #28a745, #1c7430); /* Degradado verde */
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-transform: capitalize; /* 1er letra mayúsculas */
            letter-spacing: 2px; /* Espaciado de letras */
            font-family: 'Montserrat', sans-serif; /* Fuente más ancha */
        }
        h2 {
            text-align: center;
            color: #ffc107; /* Color ámbar para el título */
            font-weight: 400;
            margin-bottom: 20px;
        }
        .form-label {
            color: #adb5bd; /* Color de etiquetas más claro */
        }
        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            padding: 10px;
            background-color: #212121; /* Fondo del footer */
            color: #f8f9fa; /* Texto claro */
            margin-top: 30px;
        }
        .license-logo {
    width: 20px; /* Ajuste del tamaño del logo */
    vertical-align: middle; /* Alineación vertical del logo */
}
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 login-container">
                <div class="company-name">EldoWay</div>
                <h2 class="mb-4">Iniciar sesión</h2>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <form method="POST" novalidate>
                    <div class="mb-3">
                        <label for="username" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Ingrese su usuario" required pattern="[a-zA-Z0-9]{3,}">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Ingrese su contraseña" required minlength="6">
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg">Ingresar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>  

<footer>
    <p>2025 
    <a href="https://arielcabral.com" style="color: #ffc107;" target="_blank">Ariel Cabral</a>
    <img src="https://image.pngaaa.com/777/3808777-middle.png" alt="Licencia MIT" class="license-logo">
    Licencia MIT
    </p>
</footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
