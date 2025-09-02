<?php
session_start();  // Inicia la sesión

// Verifica si la sesión está activa
if (!isset($_SESSION["usuario"])) {
    header("Location: IniciarSesion.php"); // Redirige si no hay sesión activa
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Supervisor</title>

    <!-- Estilos -->
    <style>
         @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');
        /* Imagen de fondo */
        body {
            font-family: Poppins, sans-serif;
            margin: 0;
            padding: 0;
            background: url('fondo superv.png') no-repeat center center fixed;
            background-size: cover;
            color: white; /* Para que el texto sea legible sobre la imagen */
        }
        
        /* Barra de navegación */
        .navbar {
            background-color: #632024;
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
            background-color: darkred;
        }

        /* Contenedor principal */
        .container {
            padding: 20px;
            text-align: center;
            color: darkblue;
        }

        .container p {
            padding: 20px;
            text-align: center;
            color: black;
            margin-top: -35px;
            font-weight: bold;
        }

        /* Calendario */
        #calendar {
            background: white;
            padding: 20px;
            border-radius: 10px;
            border-style: solid;
            color: black;
            width: 90%;
            max-width: 800px;
            margin: auto;
            height: 400px; /* IMPORTANTE: Agrega altura */
        }
    </style>

    <!-- FullCalendar -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>
</head>
<body>
    <!-- Barra de navegación -->
    <div class="navbar">
        <div>
            <a href="MenuSupervisor.php">Inicio</a>
        </div>
        <a href="CerrarSesion.php">Cerrar sesión</a>
    </div>

    <div class="container">
        <h1>¡Bienvenido de nuevo, Supervisor@!</h1>
        <p>¿Fuiste solicitado para algún evento?</p>

        <!-- Calendario -->
        <div id="calendar"></div>
    </div>

    <!-- Script del Calendario -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es', // Calendario en español
                events: [
                    { title: 'Evento 1 - Sonido', start: '2025-03-01', description: 'Se requiere servicio de sonido' },
                    { title: 'Evento 2 - Mesas y Sillas', start: '2025-03-10', description: 'Alquiler de mesas y sillas' },
                    { title: 'Evento 3 - Proyector', start: '2025-03-15', description: 'Se necesita un proyector' }
                ],
                eventClick: function(info) {
                    alert('Detalles del Evento:\n' + info.event.title + '\n' + info.event.extendedProps.description);
                }
            });
            calendar.render();
        });
    </script>
</body>
</html>
