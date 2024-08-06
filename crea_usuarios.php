<?php

// Parámetros de conexión a la base de datos
$db_name = 'usuarios';
$db_host = 'localhost';
$db_user = 'carldres';
$db_pass = 'hornazo';

try {
    // Conectar a la base de datos
    $dbh = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta SQL para obtener usuarios confirmados que no existen en el sistema
    $sql = "SELECT username, password, email FROM registros WHERE confirmado = 1";
    $sth = $dbh->prepare($sql);
    $sth->execute();

    // Recorrer resultados y crear usuarios y directorios si no existen en el sistema
    while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
        $username = $row['username'];
        $password = $row['password'];
        $email = $row['email'];

        // Comprobar si el usuario ya existe en el sistema
        $user_exists = shell_exec("id -u $username 2>/dev/null");
        if (!$user_exists) {
            // Crear usuario y directorio home
            $user_home_dir = "/home/$username";
            shell_exec("sudo useradd -m -d $user_home_dir -s /bin/bash $username");

            // Establecer la contraseña para el usuario desde la base de datos
            shell_exec("echo '$username:$password' | sudo chpasswd");

            // Mostrar mensaje de creación
            echo "Usuario $username creado con éxito.\n";

            // Crear directorio home y la carpeta "archivos"
            if (!file_exists($user_home_dir)) {
                mkdir($user_home_dir, 0770, true);
            }

            // Crear usuario en Nextcloud
            $nextcloud_url = 'https://34.16.182.84/nextcloud/ocs/v1.php/cloud/users';
            $nextcloud_admin_user = 'carldres';
            $nextcloud_admin_pass = 'hornazo';
            $nextcloud_group = 'alumnos';

            $curl_command = "curl -k -X POST $nextcloud_url " .
                            "-d userid=\"$username\" " .
                            "-d password=\"$password\" " .
                            "-d email=\"$email\" " .
                            "-d quota=\"80MB\" " .
                            "-d groups[]=\"$nextcloud_group\" " .
                            "-H \"OCS-APIRequest: true\" " .
                            "-u \"$nextcloud_admin_user:$nextcloud_admin_pass\"";

            // Ejecutar el comando cURL y capturar la salida
            $curl_output = shell_exec($curl_command . " 2>&1");

            // Decodificar la respuesta JSON de Nextcloud
            $response = json_decode($curl_output, true);

            // Verificar si la respuesta contiene un mensaje de error
            if (isset($response['ocs']['meta']['status']) && $response['ocs']['meta']['status'] === 'failure') {
                echo "Error al crear el usuario $username en Nextcloud: " . $response['ocs']['meta']['message'] . "\n";
            } else {
                echo "Usuario $username creado en Nextcloud con éxito.\n";
            }

            // Para depuración, imprimir la respuesta completa
            echo "Respuesta de Nextcloud: $curl_output\n";
        }
    }

    // Cerrar la conexión a la base de datos
    $dbh = null;

} catch (PDOException $e) {
    echo "Error al conectar a la base de datos: " . $e->getMessage();
}
?>