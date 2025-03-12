<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: IniciarSesion.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario de Disponibilidad</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.8/main.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.8/main.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.8/locales/es.js"></script>
    <style>
        body {
            font-family: Poppins, sans-serif;
            text-align: center;
        }
        #calendario {
            max-width: 900px;
            margin: auto;
        }
    </style>
</head>
<body>

    <h1>Calendario de Disponibilidad</h1>
    <div id="calendario"></div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var calendarioEl = document.getElementById("calendario");
            var calendario = new FullCalendar.Calendar(calendarioEl, {
                initialView: "dayGridMonth",
                locale: "es",
                events: [
                    { title: "Evento 1", start: "2025-03-05" },
                    { title: "Evento 2", start: "2025-03-10", end: "2025-03-12" }
                ]
            });
            calendario.render();
        });
    </script>
</body>
</html>
