<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['username'])) {
    echo "Por favor, inicie sesión para cambiar su rol.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir los datos del formulario
    $username_form = $_POST['usuario'];
    $password = $_POST['contrasena'];

    // Nombre de usuario desde la sesión
    $username_session = $_SESSION['username'];

    // Verificar si el usuario de la sesión coincide con el ingresado en el formulario
    if ($username_form !== $username_session) {
        echo "Error: El nombre de usuario no coincide con el de la sesión.";
        exit;
    }

    // Escribir en el archivo de gestiones
    $file_path = '/var/www/html/login/formularios/gestiones/cambio_rol.txt';
    $data = "$username_form:$password"; // Formato: usuario:contraseña
    if (($fp = fopen($file_path, 'a')) !== false) {
        fwrite($fp, $data . PHP_EOL);
        fclose($fp);
        echo "Se ha registrado la solicitud para cambiar el rol de $username_form a $new_role.";
    } else {
        echo "Error al escribir en el archivo de gestiones.";
    }
} else {
    // Mostrar el formulario si no se ha enviado aún
    echo '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cambiar Rol</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                text-align: center;
                padding: 50px;
            }
            .form-container {
                background-color: #fff;
                padding: 20px;
                width: 300px;
                margin: 0 auto;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }
            .form-container input[type="text"],
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
        </style>
    </head>
    <body>
        <div class="form-container">
            <h2>Cambiar Rol</h2>
            <form action="" method="post">
                <input type="text" name="usuario" placeholder="Nombre de usuario" required>
                <input type="password" name="contrasena" placeholder="Contraseña" required>
                <input type="text" name="nuevo_rol" placeholder="Nuevo rol" required>
                <input type="submit" value="Cambiar Rol">
            </form>
        </div>
    </body>
    </html>';
}
?>