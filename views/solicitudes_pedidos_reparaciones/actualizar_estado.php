<?php
$projectRoot = dirname(__FILE__, 3); 
require_once dirname(__DIR__, 2) . '/config/config.php'; 
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';   
require_once ROOT_PATH . '/sec/auth_check.php';       
require_once $projectRoot . '/includes/functions.php'; 

$_SESSION['last_activity'] = time();
requireLogin();

$solicitud_id = $_GET['id'] ?? null;
if (!$solicitud_id) {
    header("Location: read.php");
    exit();
}

$estados = getAllEstados();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_estado_id = $_POST['nuevo_estado'];
    $personal_id = $_SESSION['user_id'];
    
    if (actualizarEstadoSolicitud($solicitud_id, $nuevo_estado_id, $personal_id)) {
        header("Location: read.php");
        exit();
    } else {
        $error = "Error al actualizar el estado de la solicitud";
    }
}

include ROOT_PATH . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Estado - EldoWay</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;600;700&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    
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
            min-height: 100vh;
            padding-bottom: 3rem;
        }
        
        .main-container {
            max-width: 700px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .page-header {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-md);
            border-left: 5px solid var(--accent-amber);
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
        
        .form-label {
            color: var(--text-dark);
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }
        
        .form-label .required {
            color: #dc3545;
            margin-left: 0.25rem;
        }
        
        .form-select {
            border: 2px solid var(--border-color);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            color: var(--text-dark);
            background-color: white;
            transition: all 0.3s ease;
        }
        
        .form-select:focus {
            border-color: var(--accent-amber);
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.15);
            background-color: white;
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
            background: linear-gradient(135deg, var(--accent-amber), #e0a800);
            color: var(--text-dark);
            box-shadow: var(--shadow-sm);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #e0a800, var(--accent-amber));
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
            color: var(--text-dark);
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
        
        .info-box {
            background-color: #e7f3ff;
            border-left: 4px solid #0066cc;
            padding: 1rem 1.25rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            color: #004085;
        }
        
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
        
        .page-header, .form-card {
            animation: fadeIn 0.5s ease-out;
        }
        
        @media (max-width: 768px) {
            .page-header, .form-card {
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
        <div class="page-header">
            <h1 class="page-title">
                <span>🔄</span>
                Actualizar Estado de Solicitud
            </h1>
        </div>
        
        <div class="form-card">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <strong>⚠️ Error:</strong> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <div class="info-box">
                <strong>ℹ️ Información:</strong> El cambio de estado quedará registrado con su usuario y la fecha/hora actual.
            </div>
            
            <form method="POST">
                <div class="mb-4">
                    <label for="nuevo_estado" class="form-label">
                        Nuevo Estado <span class="required">*</span>
                    </label>
                    <select class="form-select" id="nuevo_estado" name="nuevo_estado" required>
                        <option value="">Seleccione un estado</option>
                        <?php foreach ($estados as $estado): ?>
                            <option value="<?php echo $estado['id']; ?>">
                                <?php echo htmlspecialchars($estado['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="btn-group-actions">
                    <button type="submit" class="btn btn-primary">
                        <span>✓</span>
                        Actualizar Estado
                    </button>
                    <a href="read.php" class="btn btn-secondary">
                        <span>✕</span>
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="<?php echo BASE_URL; ?>/vendor/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            if (!confirm('¿Confirma que desea cambiar el estado de esta solicitud?')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>