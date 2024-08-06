<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["username"], $_POST["email"], $_POST["password"])) {
    // Recuperar los datos del formulario
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Conectar a la base de datos
    $servername = "localhost";
    $username_db = "carldres";
    $password_db = "hornazo";
    $database = "usuarios";

    $conn = new mysqli($servername, $username_db, $password_db, $database);

    // Verificar la conexión a la base de datos
    if ($conn->connect_error) {
       die("Error de conexión a la base de datos: " . $conn->connect_error);
    }

    // Preparar la consulta SQL
    $sql = "INSERT INTO registros (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Verificar la preparación de la consulta
    if (!$stmt) {
       die("Error al preparar la consulta: " . $conn->error);
    }

    // Vincular parámetros y ejecutar la consulta
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        echo "¡Registro exitoso!<br>";

        // Crear un nuevo usuario en el sistema Debian 10
        $command = "sudo useradd -m -s /bin/bash $username";
        $output = shell_exec($command);

        echo "Usuario de sistema creado exitosamente.";
    } else {
        echo "Error al registrar usuario: " . $stmt->error;
    }

    // Cerrar la conexión a la base de datos
    $stmt->close();
    $conn->close();
}
?>