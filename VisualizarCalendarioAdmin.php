<?php
session_start();
include("conexion.php");

if (!isset($_SESSION["usuario"])) {
    header("Location: IniciarSesion.php");
    exit();
}

$hoy = date('Y-m-d');
$sql = "SELECT nombre_evento, fecha_evento, hora_inicio, hora_fin 
        FROM solicitud 
        WHERE evento_status = 'Aceptado' AND fecha_evento >= ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $hoy);
$stmt->execute();
$result = $stmt->get_result();

$eventos = [];
while ($row = $result->fetch_assoc()) {
    $eventos[] = [
        'title' => $row['nombre_evento'],
        'start' => $row['fecha_evento'] . 'T' . $row['hora_inicio'],
        'end'   => $row['fecha_evento'] . 'T' . $row['hora_fin'],
    ];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Calendario de Eventos</title>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');
        body {
             overflow: hidden;
            font-family: "Poppins", sans-serif;
            background: url('fondo admincontraseña.png') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
        }
        h2{
            text-align: center;
            color: darkblue;
            font-size: 24px;
            margin-top: 30px;
        }
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
        #calendar {
             width: 37%; /* Hace que el calendario ocupe todo el ancho disponible */
             height: -5px;
            margin: 0 auto;
            font-size: 12.5px; /* Mantén el tamaño del texto si lo deseas */
            padding: 0; /* Sin padding extra */
        }
    </style>
</head>
<body>

<div class="navbar">
        <div>
            <a href="MenuAdministrador.php">Inicio</a>
            <a href="EstatusEventosAdmin.php">Gestionar Eventos</a>
            <a href="AdminRestContraseña.php">Restablecer Contraseña</a>
        </div>
        <a href="CerrarSesion.php">Cerrar sesión</a>
    </div>

<h2 style="text-align: center;">Calendario de Eventos</h2>
<div id='calendar'></div>

<div style="position: absolute; top: 85%; left: 200px; transform: translateY(-50%);">
     <form action="EstatusEventosAdmin.php" method="get">
    <button type="submit" style="padding: 10px 20px; background-color: #25344f; color: white; border: none; border-radius: 5px; cursor: pointer;"> Regresar
    </button>
</form>
</div>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek'
            },
            events: <?= json_encode($eventos) ?>
        });

        calendar.render();
    });
</script>
</body>
</html>
