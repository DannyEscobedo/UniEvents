<?php
session_start();  // Inicia la sesión

// Verifica si la sesión está activa
if (!isset($_SESSION["usuario"])) {
    header("Location: IniciarSesion.php"); // Redirige al login si no hay sesión
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Solicitante</title>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');
        /* Imagen de fondo */
        body {
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
            background: url('fondo solicitante.png') no-repeat center center fixed;
            background-size: cover;
            color: white; /* Para que el texto sea legible sobre la imagen */
        }
        
        /* Barra de navegación */
        .navbar {
            background-color: #25344f;
            overflow: hidden;
            padding: 7px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
        }
        
        .navbar a:hover {
            background-color: darkblue;
        }

        /* Contenedor principal */
        .container {
            padding: 20px;
            text-align: left;
            color: darkblue;
            margin-left: 150px;
        }

        .container p {
            padding: 20px;
            text-align: left;
            color: black;
            margin-left: 120px;
            margin-top: -35px;
            font-weight: bold;
        }

    </style>
</head>
<body>

    <!-- Barra de navegación -->
    <div class="navbar">
        <div>
            <a href="MenuSolicitante.php">Inicio</a>
            <a href="SolicitarEvento.php">Solicitar Evento</a>
            <a href="EstatusEvento.php">Estatus del Evento</a>
            <a href="SubirFlyer.php">Subir Flyer</a>
            <a href="Perfil.php">Perfil</a>
        </div>
        <a href="CerrarSesion.php">Cerrar sesión</a>
    </div>

    <div class="container">
        <h1>¡Bienvenido de nuevo, Alumn@!</h1>
        <p>¿List@ para comenzar el día?</p>
    </div>

</body>
</html>
