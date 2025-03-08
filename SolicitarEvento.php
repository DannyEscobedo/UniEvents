<?php
session_start();  // Inicia la sesión

// Verifica si la sesión está activa
if (!isset($_SESSION["usuario"])) {
    header("Location: IniciarSesion.php"); // Redirige al login si no hay sesión
    exit();
}
?>
