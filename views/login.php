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
    <title>Iniciar sesión - EldoWay</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --bg-dark: #1b1b1b;
            --bg-gradient-start: #2d2d2d;
            --bg-gradient-end: #212121;
            --text-light: #f8f9fa;
            --text-muted: #adb5bd;
            --green-start: #28a745;
            --green-end: #1c7430;
            --accent-amber: #ffc107;
            --footer-bg: #212121;
        }
        
        body {
            background-color: var(--bg-dark);
            font-family: 'Roboto', sans-serif;
            color: var(--text-light);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            padding: 2rem 0;
        }
        
        .login-card {
            max-width: 450px;
            width: 100%;
            background: linear-gradient(to bottom right, var(--bg-gradient-start), var(--bg-gradient-end));
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.7);
            overflow: hidden;
        }
        
        .company-name {
            font-size: clamp(2rem, 5vw, 2.5rem);
            font-weight: 700;
            background: linear-gradient(135deg, var(--green-start), var(--green-end));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-transform: capitalize;
            letter-spacing: 3px;
            font-family: 'Montserrat', sans-serif;
            margin-bottom: 0;
        }
        
        .login-title {
            color: var(--accent-amber);
            font-weight: 400;
            font-size: 1.5rem;
        }
        
        .form-label {
            color: var(--text-muted);
            font-weight: 500;
        }
        
        .form-control {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-light);
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.08);
            border-color: var(--green-start);
            color: var(--text-light);
            box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
        }
        
        .form-control::placeholder {
            color: rgba(173, 181, 189, 0.6);
        }
        
        .btn-success {
            background: linear-gradient(135deg, var(--green-start), var(--green-end));
            border: none;
            font-weight: 600;
            letter-spacing: 1px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .btn-success:hover {
            background: linear-gradient(135deg, var(--green-end), var(--green-start));
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }
        
        .btn-success:active {
            transform: translateY(0);
        }
        
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.2);
            border-color: rgba(220, 53, 69, 0.4);
            color: #ff6b6b;
        }
        
        .footer-custom {
            background-color: var(--footer-bg);
            color: var(--text-light);
            padding: 1rem 0;
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .footer-custom a {
            color: var(--accent-amber);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer-custom a:hover {
            color: var(--green-start);
            text-decoration: underline;
        }
        
        .license-logo {
            width: 20px;
            height: 20px;
            vertical-align: middle;
            margin: 0 0.25rem;
            opacity: 0.8;
        }
        
        /* Animación de entrada */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-card {
            animation: fadeInUp 0.6s ease-out;
        }
        
        /* Validación Bootstrap personalizada */
        .was-validated .form-control:invalid {
            border-color: #dc3545;
            background-image: none;
        }
        
        .was-validated .form-control:valid {
            border-color: var(--green-start);
            background-image: none;
        }
        
        .invalid-feedback {
            color: #ff6b6b;
            font-size: 0.875rem;
        }
    </style>
</head>

<body>
    <div class="main-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-11 col-sm-10 col-md-8 col-lg-6 col-xl-5">
                    <div class="card login-card border-0">
                        <div class="card-body p-4 p-md-5">
                            <!-- Nombre de la empresa -->
                            <div class="text-center mb-3">
                                <h1 class="company-name">EldoWay</h1>
                            </div>
                            
                            <!-- Título -->
                            <h2 class="text-center login-title mb-4">Iniciar sesión</h2>
                            
                            <!-- Mensaje de error -->
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-triangle-fill me-2" viewBox="0 0 16 16">
                                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                                    </svg>
                                    <?php echo htmlspecialchars($error); ?>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Formulario -->
                            <form method="POST" class="needs-validation" novalidate>
                                <!-- Usuario -->
                                <div class="mb-4">
                                    <label for="username" class="form-label">Usuario</label>
                                    <input type="text" 
                                           class="form-control form-control-lg" 
                                           id="username" 
                                           name="username" 
                                           placeholder="Ingrese su usuario" 
                                           pattern="[a-zA-Z0-9]{3,}"
                                           required 
                                           autofocus>
                                    <div class="invalid-feedback">
                                        El usuario debe tener al menos 3 caracteres alfanuméricos.
                                    </div>
                                </div>
                                
                                <!-- Contraseña -->
                                <div class="mb-4">
                                    <label for="password" class="form-label">Contraseña</label>
                                    <input type="password" 
                                           class="form-control form-control-lg" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Ingrese su contraseña" 
                                           minlength="4"
                                           required>
                                    <div class="invalid-feedback">
                                        La contraseña debe tener al menos 4 caracteres.
                                    </div>
                                </div>
                                
                                <!-- Botón de envío -->
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        Ingresar
                                    </button>
                                </div>
                            </form>
                            
                            <!-- Enlaces adicionales 
                            <div class="text-center mt-4">
                                <small class="text-muted">
                                    ¿Problemas para acceder? 
                                    <a href="#" class="text-decoration-none" style="color: var(--accent-amber);">Contactar soporte</a>
                                </small>
                            </div>-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer-custom">
        <div class="container">
            <p class="mb-0">
                © 2025 
                <a href="https://arielcabral.com" target="_blank" rel="noopener noreferrer">Ariel Cabral</a>
                <img src="https://image.pngaaa.com/777/3808777-middle.png" 
                     alt="Licencia MIT" 
                     class="license-logo"
                     loading="lazy">
                Licencia MIT
            </p>
        </div>
    </footer>

    <script src="<?php echo BASE_URL; ?>/vendor/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Validación nativa de Bootstrap
        (function() {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            
            Array.from(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
        
        // Auto-dismiss de alertas después de 5 segundos
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            }, 5000);
        });
        
        // Prevenir envío de formulario vacío
        document.querySelector('form').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            
            if (!username || !password) {
                e.preventDefault();
                alert('Por favor complete todos los campos');
            }
        });
    </script>
</body>
</html>