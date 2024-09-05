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

        function getUserRoleId($userId) {
            global $conn; // Asumiendo que tienes una conexión global a la base de datos
            $query = "SELECT rol_id FROM usuarios WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            return $row['rol_id'];
        }

        function getUserPermissions($rol_id) {
            global $conn; // Asumiendo que tienes una conexión global a la base de datos
            $query = "SELECT permisos FROM roles_usuarios WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $rol_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            return $row['permisos'];
        }

        // Obtener el rol_id del usuario
        $rol_id = getUserRoleId($user['id']);
        $_SESSION['rol_id'] = $rol_id; // Guardar en una variable de sesión

         // Obtener los permisos del usuario
         $permissionsJson = getUserPermissions($rol_id);
         $permissions = json_decode($permissionsJson, true);
         $_SESSION['permissions'] = $permissions; // Guardar en una variable de sesión

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin-top: 100px;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .company-name {
            text-align: center;
            margin-bottom: 30px;
            color: #007bff;
        }
        .logo-container {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 login-container">
            <div class="logo-container text-center mb-4">
                 <img src="../extras/lgh.jpg" alt="Grupo Horianski Logo" class="img-fluid" style="max-width: 200px;">
            </div>
                <h2 class="text-center mb-4">Iniciar sesión</h2>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">Ingresar</button>
                <!--<a href="registro.php" class="btn btn-secondary btn-lg">Registrar usuario</a>-->
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>