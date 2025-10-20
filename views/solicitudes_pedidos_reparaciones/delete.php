<?php
$projectRoot = dirname(__FILE__, 3); 
require_once dirname(__DIR__, 2) . '/config/config.php'; 
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';   
require_once ROOT_PATH . '/sec/auth_check.php';       
require_once $projectRoot . '/includes/functions.php'; 

$_SESSION['last_activity'] = time();
requireLogin();

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: read.php");
    exit();
}

$solicitud = getSolicitudPedidoReparacionById($id);
if (!$solicitud) {
    header("Location: read.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (deleteSolicitudPedidoReparacion($id)) {
        header("Location: read.php");
        exit();
    } else {
        $error = "Error al eliminar la solicitud de pedido de reparación";
    }
}

include ROOT_PATH . '/includes/header.php'; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Solicitud - EldoWay</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;600;700&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-green: #28a745;
            --danger-red: #dc3545;
            --danger-dark: #c82333;
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
            border-left: 5px solid var(--danger-red);
        }
        
        .page-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--danger-red);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .warning-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: var(--shadow-md);
            border: 2px solid var(--danger-red);
        }
        
        .warning-message {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 1.25rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        
        .warning-message-title {
            font-weight: 700;
            color: #856404;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        
        .warning-message-text {
            color: #856404;
            margin: 0;
            font-size: 0.95rem;
        }
        
        .solicitud-info {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .solicitud-info-item {
            display: flex;
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .solicitud-info-item:last-child {
            border-bottom: none;
        }
        
        .solicitud-info-label {
            font-weight: 600;
            color: var(--text-muted);
            min-width: 150px;
        }
        
        .solicitud-info-value {
            color: var(--text-dark);
            flex: 1;
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
        
        .btn-danger {
            background: linear-gradient(135deg, var(--danger-red), var(--danger-dark));
            color: white;
            box-shadow: var(--shadow-sm);
        }
        
        .btn-danger:hover {
            background: linear-gradient(135deg, var(--danger-dark), var(--danger-red));
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
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
        
        .page-header, .warning-card {
            animation: fadeIn 0.5s ease-out;
        }
        
        @media (max-width: 768px) {
            .page-header, .warning-card {
                padding: 1.5rem;
            }
            
            .page-title {
                font-size: 1.5rem;
            }
            
            .solicitud-info-item {
                flex-direction: column;
            }
            
            .solicitud-info-label {
                min-width: auto;
                margin-bottom: 0.25rem;
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
                <span>🗑️</span>
                Eliminar Solicitud de Reparación
            </h1>
        </div>
        
        <div class="warning-card">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <strong>⚠️ Error:</strong> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <div class="warning-message">
                <div class="warning-message-title">
                    ⚠️ Advertencia: Esta acción no se puede deshacer
                </div>
                <p class="warning-message-text">
                    Está a punto de eliminar permanentemente esta solicitud de reparación. Toda la información asociada se perderá.
                </p>
            </div>
            
            <div class="solicitud-info">
                <h5 style="color: var(--text-dark); margin-bottom: 1rem; font-weight: 600;">
                    📋 Detalles de la Solicitud a Eliminar
                </h5>
                
                <div class="solicitud-info-item">
                    <span class="solicitud-info-label">N° Solicitud:</span>
                    <span class="solicitud-info-value"><?php echo htmlspecialchars($solicitud['numero_solicitud'] ?? 'N/A'); ?></span>
                </div>
                
                <div class="solicitud-info-item">
                    <span class="solicitud-info-label">Fecha:</span>
                    <span class="solicitud-info-value"><?php echo htmlspecialchars($solicitud['fecha_solicitud'] ?? 'N/A'); ?></span>
                </div>
                
                <div class="solicitud-info-item">
                    <span class="solicitud-info-label">Observaciones:</span>
                    <span class="solicitud-info-value"><?php echo htmlspecialchars(substr($solicitud['observaciones'] ?? 'N/A', 0, 100)); ?><?php echo strlen($solicitud['observaciones'] ?? '') > 100 ? '...' : ''; ?></span>
                </div>
            </div>
            
            <form method="POST" id="formEliminar">
                <div class="btn-group-actions">
                    <button type="submit" class="btn btn-danger">
                        <span>🗑️</span>
                        Confirmar Eliminación
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
        document.getElementById('formEliminar').addEventListener('submit', function(e) {
            if (!confirm('⚠️ ATENCIÓN: ¿Está completamente seguro de que desea eliminar esta solicitud?\n\nEsta acción es IRREVERSIBLE y eliminará toda la información asociada.')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>