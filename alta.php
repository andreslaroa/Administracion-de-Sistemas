<?php
// Inicia la sesión
session_start();

// Recibe los datos del formulario
$usuario = $_POST['usuario'];
$correo = $_POST['correo'];
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$contrasena = $_POST['contrasena'];

// Define los datos para la solicitud a la API de WordPress
$url = 'https://34.16.182.84/wordpress/wp-json/wp/v2/users';
$username = 'carldres';
$password = 'rSk3voWRu0hQbZHP0TukRHDM';

// Inicia la sesión cURL
$ch = curl_init();

// Configura las opciones de la sesión cURL para obtener la lista de usuarios
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");

// Ejecuta la solicitud para obtener la lista de usuarios
$response = curl_exec($ch);

// Verifica si hubo errores en la solicitud
if ($response === false) {
    echo 'Curl error: ' . curl_error($ch);
} else {
    // Decodifica la respuesta JSON en un array asociativo de usuarios
    $usuarios = json_decode($response, true);

    // Función para obtener los nombres de usuario existentes
    function obtenerNombresDeUsuarios($usuarios) {
        $nombres = [];
        foreach ($usuarios as $usuario) {
            $nombres[] = $usuario['username'];
        }
        return $nombres;
    }

    // Obtiene los nombres de usuario existentes
    $nombresDeUsuarios = obtenerNombresDeUsuarios($usuarios);

    // Verifica si el nombre de usuario del formulario coincide con el de la sesión web
    if ($_SESSION['username'] === $usuario) {
        echo 'El nombre de usuario coincide con el de la sesión web.';
    

    // Verifica si ya existe un usuario con el mismo nombre de usuario
    if (in_array($usuario, $nombresDeUsuarios)) {
        echo ' El nombre de usuario ya existe. Por favor, elige uno diferente.';
    } else {
        // Define los datos del nuevo usuario
        $data = array(
            'username' => $usuario,
            'password' => $contrasena,
            'email' => $correo,
            'first_name' => $nombre,
            'last_name' => $apellido
        );

        // Configura las opciones de la sesión cURL para crear el nuevo usuario
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // Ejecuta la solicitud para crear el nuevo usuario
        $response = curl_exec($ch);

        // Verifica si hubo errores en la solicitud
        if ($response === false) {
            echo 'Curl error: ' . curl_error($ch);
        } else {
            echo 'Usuario creado exitosamente';
        }
    }
} else {
    echo 'El nombre de usuario no coincide con el de la sesión web.';
}
}

// Cierra la sesión cURL
curl_close($ch);
?>