<?php
// Iniciar sesión (si no está iniciada)
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["username"])) {
    // No hay sesión activa, redirigir a la página de inicio de sesión
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Especial - Carldres</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        header, footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 10px 0;
        }
        nav {
            background-color: #f4f4f4;
            padding: 10px 0;
            text-align: center;
        }
        nav a {
            text-decoration: none;
            color: #333;
            padding: 0 10px;
        }
        nav a:hover {
            color: #000;
            font-weight: bold;
        }
        .main-content {
            padding: 20px;
            text-align: center;
        }
        .recommendation {
            background-color: #fff;
            padding: 20px;
            width: 300px;
            margin: 20px auto;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .recommendation h2 {
            margin-bottom: 15px;
        }
        .logout-link {
            display: block;
            margin-top: 20px;
            text-decoration: none;
            color: #333;
            background-color: #ccc;
            padding: 10px 20px;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        .logout-link:hover {
            background-color: #999;
        }
    </style>
</head>
<body>
    <header>
        <h1>Bienvenido a la Página Especial</h1>
        <nav>
            <a href="pagina-usuario.php">Inicio</a>
            <a href="../wordpress/wp-login.php">Blog</a>
            <a href="../correo/">Correo</a>
            <a href="../../nextcloud">Archivos</a>
            <a href="formularios/formularios.php">Formularios</a>
            <a href="logout.php" class="logout-link">Cerrar sesión</a>
        </nav>
    </header>

    <div class="main-content">
        <p>¡Has iniciado sesión correctamente!</p>
        <p>Aquí te dejamos una recomendación de uso del servidor:</p>
        <div class="recommendation">
            <h2>Recomendación:</h2>
            <p>Para aprovechar al máximo nuestro servidor, asegúrate de utilizar las funciones avanzadas proporcionadas en tu panel de control. No dudes en contactar con nuestro equipo de soporte si necesitas asistencia adicional.</p>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Carldres - Proveedor de Servicios</p>
    </footer>
</body>
</html>