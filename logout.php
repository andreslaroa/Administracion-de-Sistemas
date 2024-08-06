<?php
// Iniciar sesión (si no está iniciada)
session_start();

// Destruir todas las variables de sesión
session_unset();
session_destroy();

// Redireccionar a la página de inicio de sesión
header("Location: ../login.html");
exit();
?>