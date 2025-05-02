<?php
session_start();
include("conexion.php");

if (!isset($_SESSION["usuario"])) {
    header("Location: IniciarSesion.php");
    exit();
}

// Consulta eventos aceptados que aún NO tienen personal asignado
$sql = "
SELECT e.num_solicitud, s.fecha_evento, s.hora_inicio
FROM estatus_evento e
INNER JOIN solicitud s ON e.num_solicitud = s.num_solicitud
WHERE e.estatus = 'aceptado'
AND NOT EXISTS (
    SELECT 1 
    FROM puesto p 
    WHERE p.num_solicitud = e.num_solicitud
)
AND (
    s.fecha_evento > CURDATE() OR
    (s.fecha_evento = CURDATE() AND s.hora_inicio > CURTIME())
)";

$result = $conn->query($sql);

if (!$result) {
    die("Error en la consulta SQL: " . $conn->error);
}

$eventos_aceptados = [];
while ($row = $result->fetch_assoc()) {
    $fechaEvento = $row['fecha_evento'];
    $horaInicio = $row['hora_inicio'];
    $fechaHoraEvento = strtotime("$fechaEvento $horaInicio");
    $ahora = time();

    // Solo guardar si el evento aún no ha pasado
    if ($fechaHoraEvento > $ahora) {
        $eventos_aceptados[] = [
            'num_solicitud' => $row['num_solicitud'],
            'fecha_evento' => $fechaEvento,
            'hora_inicio' => $horaInicio
        ];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Eventos Aceptados</title>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');
        body {
            font-family: "Poppins", sans-serif;
            background: url('fondo admincontraseña.png') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
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

        h2 {
            text-align: center;
            color: darkblue;
            margin-top: 35px;
            font-size: 24px;
        }

        .tabla-eventos {
            width: 70%;
            margin: 30px auto;
            border-collapse: collapse;
            background: white;
        }

        .tabla-eventos th, .tabla-eventos td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }

        .tabla-eventos th {
            background-color: #25344f;
            color: white;
        }

        .btn-personal {
            background-color: #25344f;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-personal:hover {
            background-color: #39588f;
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

<h2>Eventos Aceptados</h2>
<table class="tabla-eventos">
    <tr>
        <th>Número de solicitud</th>
        <th>Botón</th>
    </tr>
    <?php
    include("conexion.php");

    // Verificar si el usuario está logueado
    if (!isset($_SESSION["usuario"])) {
        header("Location: IniciarSesion.php");
        exit();
    }

    // Consulta los eventos aceptados que no tienen personal asignado
    $sql = "SELECT e.num_solicitud 
            FROM estatus_evento e
            WHERE e.estatus = 'aceptado' 
            AND NOT EXISTS (
                SELECT 1 
                FROM puesto p 
                WHERE p.num_solicitud = e.num_solicitud
            )";

    $result = $conn->query($sql);

    // Verificación opcional
    if (!$result) {
        die("Error en la consulta SQL: " . $conn->error);
    }

    // Mostrar los resultados en la tabla
    while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['num_solicitud'] ?></td>
            <td>
                <!-- Formulario para registrar personal -->
                <form action="RegistrarPersonal.php" method="post">
    <!-- Asegúrate de que num_solicitud esté dentro del formulario como un campo hidden -->
    <input type="hidden" name="num_solicitud" value="<?= $row['num_solicitud'] ?>">

    <button type="submit" class="btn-personal">Registrar Personal para Evento <?= $row['num_solicitud'] ?></button>
</form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
<div style="position: absolute; top: 85%; left: 200px; transform: translateY(-50%);">
     <form action="EstatusEventosAdmin.php" method="get">
    <button type="submit" style="padding: 10px 20px; background-color: #25344f; color: white; border: none; border-radius: 5px; cursor: pointer;"> Regresar
    </button>
</form>
</div>
</body>
</html>
