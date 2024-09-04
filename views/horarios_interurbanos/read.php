<?php 
$projectRoot = dirname(__FILE__, 3); 
require_once dirname(__DIR__, 2) . '/config/config.php'; 
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';   
require_once ROOT_PATH . '/sec/auth_check.php';       
require_once $projectRoot . '/includes/functions.php'; 

requireLogin();

$horarios = getAllHorariosInterurbanos();  
$rol_id = $_SESSION['rol_id'];

include ROOT_PATH . '/includes/header.php'; 
?>  

<!DOCTYPE html> 
<html lang="es"> 
<head>     
    <meta charset="UTF-8">     
    <meta name="viewport" content="width=device-width, initial-scale=1.0">     
    <title>Listar Horarios Interurbanos</title>     
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head> 
<body class="bg-light">     
    <div class="container mt-5">         
        <h1 class="mb-4">Horarios Interurbanos</h1>         
        <?php if (isset($_SESSION['message'])): ?>             
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>         
        <?php endif; ?>
        
        <div class="mb-4">
        <?php if ($rol_id == 1): ?>
            <a href="create.php" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>Crear Nuevo Horario
            </a>
        <?php endif?>
            <a href="exportar_pdf.php" class="btn btn-success" target="_blank">
                <i class="fas fa-file-pdf me-2"></i>Exportar Todos a PDF
            </a>
            <a href="<?php echo BASE_URL; ?>/includes/header.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover table-striped">             
                <thead class="table-dark">                 
                    <tr>                     
                        <th>Servicio 1</th>                     
                        <th>Servicio 2</th>                     
                        <th>Servicio 3</th>                     
                        <th>Terminal Salida</th>                     
                        <th>Terminal Llegada</th>
                        <th>Descripcion</th>                     
                        <th>Acciones</th>                 
                    </tr>             
                </thead>             
                <tbody>                 
                    <?php foreach ($horarios as $horario): ?>                 
                    <tr>                     
                        <td><?php echo $horario['servicio1_nombre']; ?></td>                     
                        <td><?php echo $horario['servicio2_nombre']; ?></td>                     
                        <td><?php echo $horario['servicio3_nombre']; ?></td>                     
                        <td><?php echo $horario['terminal_salida_nombre']; ?></td>                     
                        <td><?php echo $horario['terminal_llegada_nombre']; ?></td>
                        <td><?php echo $horario['descripcion']; ?></td>                      
                        <td>                         
                            <div class="btn-group" role="group" aria-label="Acciones de horario">
                                <?php if ($rol_id == 1): ?>
                                    <a href="view_horario.php?id=<?php echo $horario['id']; ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a><?php endif; ?>  
                                <?php if ($rol_id == 1): ?>                       
                                <a href="delete_horario.php?id=<?php echo $horario['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('¿Está seguro de que desea eliminar este horario?')">
                                    <i class="fas fa-trash-alt"></i>
                                </a><?php endif; ?>  
                                <a href="export_single.php?id=<?php echo $horario['id']; ?>" class="btn btn-outline-success btn-sm" target="_blank">
                                    <i class="fas fa-file-export"></i>
                                </a>
                            </div>
                        </td>                 
                    </tr>                 
                    <?php endforeach; ?>             
                </tbody>         
            </table>
        </div>         
    </div>      
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> 
</body> 
</html>