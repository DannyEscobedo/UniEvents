<?php
session_start();  // Inicia la sesión

// Destruye toda la sesión
session_destroy();

// Redirige al usuario a la página de inicio de sesión
header("Location: IniciarSesion.php");
exit();  // Detiene la ejecución para evitar mostrar contenido no deseado
?>
