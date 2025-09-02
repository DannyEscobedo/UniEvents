<?php
header('Content-Type: text/html; charset=utf-8'); // Asegura que el contenido se maneje en UTF-8

$servidor = "localhost";
$usuario = "root";
$password = "";
$base_datos = "unievents";

// Crear la conexión
$conn = new mysqli($servidor, $usuario, $password, $base_datos);

// Verificar si la conexión fue exitosa
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
} else {
    //echo "Conectado exitosamente";  // Opcional, solo para confirmar la conexión
}

// Configurar el charset de la conexión para UTF-8
$conn->set_charset("utf8");
?>
