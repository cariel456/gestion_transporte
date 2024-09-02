<?php 
$projectRoot = dirname(__FILE__, 3); 
require_once dirname(__DIR__, 2) . '/config/config.php'; 
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';   
require_once ROOT_PATH . '/sec/auth_check.php';       
require_once $projectRoot . '/includes/functions.php'; 

requireLogin();

$horarios = getAllHorariosInterurbanos();  

include ROOT_PATH . '/includes/header.php'; 
?>  

<!DOCTYPE html> 
<html lang="es"> 
<head>     
    <meta charset="UTF-8">     
    <meta name="viewport" content="width=device-width, initial-scale=1.0">     
    <title>Listar Horarios Interurbanos</title>     
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"> 
</head> 
<body>     
    <div class="container mt-5">         
        <h1>Horarios Interurbanos</h1>         
        <?php if (isset($_SESSION['message'])): ?>             
            <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>         
        <?php endif; ?>    
        <a href="create.php" class="btn btn-primary">Crear Nuevo Horario Interurbano</a>
        <a href="exportar_pdf.php" class="btn btn-success" target="_blank">Exportar Todos a PDF</a>
        <a href="<?php echo BASE_URL; ?>/includes/header.php" class="btn btn-secondary">Cancelar</a>     
        <table class="table table-striped">             
            <thead>                 
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
                        <a href="view_horario.php?id=<?php echo $horario['id']; ?>" class="btn btn-info btn-sm">Ver</a>                         
                        <a href="delete_horario.php?id=<?php echo $horario['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de que desea eliminar este horario?')">Eliminar</a>
                        <a href="export_single.php?id=<?php echo $horario['id']; ?>" class="btn btn-success btn-sm" target="_blank">Exportar este horario a PDF</a>
                    </td>                 
                </tr>                 
                <?php endforeach; ?>             
            </tbody>         
        </table>         
    </div>      
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script> 
</body> 
</html>