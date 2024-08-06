<?php
session_start();

// Configuración de la base de datos
$db_host = 'localhost';
$db_name = 'usuarios';
$db_user = 'carldres';
$db_password = 'hornazo';

// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $confirmacion = $_POST['confirmacion'];
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];

    // Verificar que el texto de confirmación es correcto
    if ($confirmacion !== 'CONFIRMAR') {
        die('El texto de confirmación es incorrecto.');
    }

    // Verificar que el usuario coincide con el de la sesión iniciada
    if ($usuario !== $_SESSION['username']) {
        die('El usuario no coincide con el de la sesión iniciada.');
    }

    // Conectar a la base de datos
    try {
        $dbh = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die('Error al conectar con la base de datos: ' . $e->getMessage());
    }

    // Verificar que la contraseña es correcta
    $stmt = $dbh->prepare('SELECT password FROM registros WHERE username = :username');
    $stmt->bindParam(':username', $usuario);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row || $row['password'] !== $contrasena) {
        die('La contraseña es incorrecta.');
    }

    // Escribir los datos en el archivo de texto
    $file = '/var/www/html/login/formularios/gestiones/baja_sistema.txt';
    $entry = $usuario . ':' . $contrasena . PHP_EOL;
    if (file_put_contents($file, $entry, FILE_APPEND) === false) {
        die('Error al escribir en el archivo.');
    }

    echo 'La solicitud de baja se ha registrado correctamente.';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formularios - Página Especial</title>
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
        .form-selection {
            margin-bottom: 20px;
        }
        .form-container {
            display: none;
            background-color: #fff;
            padding: 20px;
            width: 300px;
            margin: 20px auto;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .form-container.active {
            display: block;
        }
        .form-container h2 {
            margin-bottom: 15px;
        }
        .form-container input[type="text"],
        .form-container input[type="email"],
        .form-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-container input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .form-container input[type="submit"]:hover {
            background-color: #555;
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
    <script>
        function showForm(formId) {
            document.querySelectorAll('.form-container').forEach(function(form) {
                form.classList.remove('active');
            });
            document.getElementById(formId).classList.add('active');
        }
    </script>
</head>
<body>
    <header>
        <h1>Formularios</h1>
        <nav>
            <a href="../pagina-usuario.php">Inicio</a>
            <a href="../../wordpress/wp-login.php">Blog</a>
            <a href="../../correo/">Correo</a>
            <a href="../../nextcloud">Archivos</a>
            <a href="formularios.php">Formularios</a>
            <a href="../logout.php" class="logout-link">Cerrar sesión</a>
        </nav>
    </header>

    <div class="main-content">
        <div class="form-selection">
            <button onclick="showForm('form-alta')">Formulario de Alta</button>
            <button onclick="showForm('form-baja')">Formulario de Baja</button>
            <button onclick="showForm('form-cambiar-contrasena')">Cambiar Contraseña</button>
            <button onclick="showForm('form-baja-sistema')">Baja del Sistema</button>
        </div>

        <div id="form-alta" class="form-container">
            <h2>Formulario de Alta en el Blog</h2>
            <form action="alta.php" method="post">
                <input type="text" name="usuario" placeholder="Nombre de usuario" required>
                <input type="email" name="correo" placeholder="Correo electrónico" required>
                <input type="text" name="nombre" placeholder="Nombre" required>
                <input type="text" name="apellido" placeholder="Apellido" required>
                <input type="password" name="contrasena" placeholder="Contraseña" required>
                <input type="submit" value="Darse de Alta">
            </form>
        </div>

        <div id="form-baja" class="form-container">
            <h2>Formulario de Baja en el Blog</h2>
            <form action="baja.php" method="post">
                <input type="text" name="usuario" placeholder="Nombre de usuario" required>
                <input type="password" name="contrasena" placeholder="Contraseña" required>
                <input type="submit" value="Darse de Baja">
            </form>
        </div>

        <div id="form-cambiar-contrasena" class="form-container">
            <h2>Cambiar Contraseña</h2>
            <h3>Cambiar Contraseña del Sistema</h3>
            <form action="cambiar_contrasena_sistema.php" method="post">
                <input type="password" name="contrasena_actual" placeholder="Contraseña actual" required>
                <input type="password" name="nueva_contrasena" placeholder="Nueva contraseña" required>
                <input type="password" name="confirmar_contrasena" placeholder="Confirmar nueva contraseña" required>
                <input type="submit" value="Cambiar Contraseña">
            </form>

            <h3>Cambiar Contraseña del Blog</h3>
            <form action="cambiar_contrasena_blog.php" method="post">
                <input type="password" name="contrasena_actual" placeholder="Contraseña actual" required>
                <input type="password" name="nueva_contrasena" placeholder="Nueva contraseña" required>
                <input type="password" name="confirmar_contrasena" placeholder="Confirmar nueva contraseña" required>
                <input type="submit" value="Cambiar Contraseña">
            </form>

            <h3>Cambiar Contraseña de Nextcloud</h3>
            <form action="cambiar_contrasena_nextcloud.php" method="post">
                <input type="password" name="contrasena_actual" placeholder="Contraseña actual" required>
                <input type="password" name="nueva_contrasena" placeholder="Nueva contraseña" required>
                <input type="password" name="confirmar_contrasena" placeholder="Confirmar nueva contraseña" required>
                <input type="submit" value="Cambiar Contraseña">
            </form>
        </div>

        <div id="form-baja-sistema" class="form-container active">
            <h2>Baja del Sistema</h2>
            <p>Para confirmar la baja del sistema, por favor escriba "CONFIRMAR" en el siguiente campo de texto. La baja del sistema es irreversible y se eliminarán todos sus datos.</p>
            <form action="baja_sistema.php" method="post">
                <input type="text" name="confirmacion" placeholder="Escriba CONFIRMAR" required>
                <input type="text" name="usuario" placeholder="Nombre de usuario" required>
                <input type="password" name="contrasena" placeholder="Contraseña" required>
                <input type="submit" value="Darse de Baja">
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Carldres - Proveedor de Servicios</p>
    </footer>
</body>
</html>