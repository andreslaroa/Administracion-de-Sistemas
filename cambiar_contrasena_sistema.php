<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['username'])) {
    echo "Por favor, inicie sesión para cambiar su contraseña.";
    exit;
}

// Conexión a la base de datos
$db_host = 'localhost';
$db_name = 'usuarios';
$db_user = 'carldres';
$db_pass = 'hornazo';

try {
    $dbh = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error al conectar a la base de datos: " . $e->getMessage();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir los datos del formulario
    $current_password = $_POST['contrasena_actual'];
    $new_password = $_POST['nueva_contrasena'];
    $confirm_password = $_POST['confirmar_contrasena'];

    // Verificar que las contraseñas nuevas coinciden
    if ($new_password !== $confirm_password) {
        echo "Las contraseñas nuevas no coinciden.";
        exit;
    }

    // Nombre de usuario desde la sesión
    $username = $_SESSION['username'];

    try {
        // Consulta para obtener la contraseña actual del usuario
        $stmt = $dbh->prepare("SELECT password FROM registros WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            echo "Error: No se encontró el usuario en la base de datos.";
            exit;
        }

        $stored_password = $row['password'];

        // Verificar si la contraseña actual coincide con la contraseña almacenada en la base de datos
        if ($current_password !== $stored_password) {
            echo "La contraseña actual es incorrecta.";
            exit;
        }

        // Escribir en el archivo de gestiones si la contraseña actual es correcta y la nueva coincide con la confirmación
        $file_path = '/var/www/html/login/formularios/gestiones/cambio_contrasena_sistema.txt';
        $data = "$username:$current_password:$new_password\n"; // Agregar un salto de línea al final
        if (file_put_contents($file_path, $data, FILE_APPEND) !== false) {
            echo "Se ha registrado la solicitud para cambiar la contraseña de $username.";
        } else {
            echo "Error al escribir en el archivo de gestiones.";
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    // Mostrar el formulario si no se ha enviado aún
    echo '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cambiar Contraseña del Sistema</title>
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
            <h2>Cambiar Contraseña del Sistema</h2>
            <form action="" method="post">
                <input type="password" name="contrasena_actual" placeholder="Contraseña actual" required>
                <input type="password" name="nueva_contrasena" placeholder="Nueva contraseña" required>
                <input type="password" name="confirmar_contrasena" placeholder="Confirmar nueva contraseña" required>
                <input type="submit" value="Cambiar Contraseña">
            </form>
        </div>
    </body>
    </html>';
}
?>