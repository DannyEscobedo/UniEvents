<?php
session_start();  // Inicia la sesión

// Verifica si la sesión está activa
if (!isset($_SESSION["usuario"])) {
    // Si no está autenticado, redirige a la página de inicio de sesión
    header("Location: IniciarSesion.php");
    exit();  // Detiene la ejecución para evitar mostrar el contenido de la página
}

// Si la sesión está activa, el contenido del menú se muestra
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Menú Supervisor</title>
</head>
<body>
    <h1>¡Bienvenido de nuevo, Supervisor@!</h1>
     <a href="CerrarSesion.php">Cerrar sesión</a>  <!-- Enlace para cerrar sesión -->
</body>
</html>
