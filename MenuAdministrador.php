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
    <title>Menú Administrador</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.8/main.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.8/main.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.8/locales/es.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');
        body {
            font-family: Poppins, sans-serif;
            margin: 0;
            padding: 0;
            background: url('fondo admin.png') no-repeat center center fixed;
            background-size: cover;
            color: white; /* Para que el texto sea legible sobre la imagen */
        }
        /* Barra de navegación */
        .navbar {
            background-color: darkblue;
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
            background-color: mediumblue;
        }
        /* Contenedor principal */
        .container {
            padding: 20px;
            text-align: right;
            color: black;
            margin-right: 150px;
            margin-top: 35px;
        }
        .container p {
            padding: 20px;
            text-align: right;
            color: black;
            margin-right: 150px;
            margin-top: -35px;
        }
        /* Estilo del calendario */
        #calendario {
            max-width: 900px;
            margin: 20px auto;
        }
    </style>
</head>
<body>

    <!-- Barra de navegación -->
    <div class="navbar">
        <div>
            <a href="MenuAdministrador.php">Inicio</a>
            <a href="CalendarioDisponibilidad.php">Calendario de Disponibilidad</a>
            <a href="GenerarPDF.php">Convertir a PDF</a>
        </div>
        <a href="CerrarSesion.php">Cerrar sesión</a>
    </div>

    <div class="container">
        <h1>¡Bienvenido de nuevo, Administrador!</h1>
        <p>¿En que trabajaremos en esta ocasión?</p>
    </div>
</body>
</html>
