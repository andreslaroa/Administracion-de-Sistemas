<?php
session_start(); // Iniciar la sesión

// Función para escribir en el archivo de registro
function writeToLog($message) {
    $logfile = '/var/www/html/login.log';
    // Obtener la dirección IP del cliente
    $ip = $_SERVER['REMOTE_ADDR'];
    // Formatear el mensaje con la fecha, hora, IP y el mensaje proporcionado
    $logMessage = date('Y-m-d H:i:s') . ' [' . $ip . '] - ' . $message . PHP_EOL;
    // Escribir el mensaje en el archivo de registro
    file_put_contents($logfile, $logMessage, FILE_APPEND);
}

// Verificar si se ha enviado una solicitud POST desde el formulario de inicio de sesión
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Configuración de la base de datos (reemplaza con tus credenciales y nombre de la base de datos)
    $servername = "localhost";
    $username_db = "carldres";
    $password_db = "hornazo";
    $database = "usuarios";

    // Conexión a la base de datos
    $conn = new mysqli($servername, $username_db, $password_db, $database);

    // Verificar la conexión
    if ($conn->connect_error) {
        die("Error al conectar con la base de datos: " . $conn->connect_error);
    }

    // Obtener el nombre de usuario y contraseña del formulario de inicio de sesión
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Consulta SQL para buscar el usuario en la base de datos y verificar si está confirmado
    $sql = "SELECT * FROM registros WHERE username = '$username' AND confirmado = 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // El usuario existe y está confirmado, obtener la fila de la base de datos
        $row = $result->fetch_assoc();
        $stored_password = $row['password'];
        $user_role = $row['rol'];

        // Verificar la contraseña en texto plano
        if ($password === $stored_password) {
            // Contraseña correcta, iniciar sesión
            $_SESSION['username'] = $username;
            $_SESSION['rol'] = $user_role;
            // Escribir en el archivo de registro
            writeToLog("Inicio de sesión exitoso para el usuario: $username");
            header("Location: login/pagina-usuario.php"); // Redirigir a la página especial
            exit();
        } else {
            // Contraseña incorrecta, redirigir de vuelta a login.html con mensaje de error
            // Escribir en el archivo de registro
            writeToLog("Intento de inicio de sesión fallido para el usuario: $username");
            header("Location: login.html?error=Contraseña incorrecta");
            exit();
        }
    } else {
        // Usuario no encontrado o no confirmado, redirigir de vuelta a login.html con mensaje de error
        // Escribir en el archivo de registro
        writeToLog("Intento de inicio de sesión fallido para el usuario desconocido: $username");
        header("Location: login.html?error=Usuario no encontrado o no confirmado");
        exit();
    }

    // Cerrar la conexión a la base de datos
    $conn->close();
}
?>