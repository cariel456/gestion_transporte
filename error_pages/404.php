<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error 404 - Página No Encontrada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            padding: 0;
            margin: 0;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            flex-direction: column;
        }
        .error-page {
            text-align: center;
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .error-page h1 {
            font-size: 6rem;
            font-weight: bold;
            color: #e74c3c;
        }
        .error-page h2 {
            font-size: 2rem;
            color: #555;
            margin-bottom: 20px;
        }
        .error-page p {
            font-size: 1.1rem;
            color: #777;
            margin-bottom: 30px;
        }
        .error-page .btn {
            padding: 10px 20px;
            font-size: 1rem;
            text-transform: uppercase;
            background-color: #2980b9;
            border: none;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }
        .error-page .btn:hover {
            background-color: #1f6699;
        }
        .error-page .logo {
            width: 100px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="error-page">
        <h1>404</h1>
        <h2>¡Oops! Página no encontrada</h2>
        <p>La página que estás buscando no existe o ha sido movida. Puede que hayas escrito mal la URL, o el enlace que seguiste ya no esté disponible.</p>
        <a href="<?php echo ROOT_URL; ?>" class="btn">Volver al Inicio</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
