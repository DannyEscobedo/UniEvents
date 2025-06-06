<?php
session_start();
include("conexion.php");

if (!isset($_SESSION["usuario"])) {
    header("Location: IniciarSesion.php");
    exit();
}

// Buscar eventos aceptados sin personal, cuya hora de inicio esté a menos de una hora
$asignacion_auto_sql = "
SELECT e.num_solicitud, s.fecha_evento, s.hora_inicio
FROM estatus_evento e
INNER JOIN solicitud s ON e.num_solicitud = s.num_solicitud
WHERE e.estatus = 'aceptado'
AND NOT EXISTS (
    SELECT 1 FROM puesto p WHERE p.num_solicitud = e.num_solicitud
)
";

$asignacion_result = $conn->query($asignacion_auto_sql);

while ($evento = $asignacion_result->fetch_assoc()) {
    $fechaEvento = $evento['fecha_evento'];
    $horaInicio = $evento['hora_inicio'];
    $fechaHoraEvento = strtotime("$fechaEvento $horaInicio");
    $unaHoraAntes = strtotime("-1 hour", $fechaHoraEvento);

    if (time() >= $unaHoraAntes) {
        $num_solicitud = $evento['num_solicitud'];

        // Insertar 7 registros, uno por cada rol del 1 al 7
        for ($rol = 1; $rol <= 7; $rol++) {
            $insert_sql = "INSERT INTO puesto (rol_puesto, num_solicitud) VALUES (?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("ii", $rol, $num_solicitud);
            $stmt->execute();
            $stmt->close();
        }
    }
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
    $eventos_aceptados[] = $row;
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
        h3 {
            text-align: center;
            color: red;
            font-size: 18px;
        }

        .tabla-eventos {
            width: 70%;
            margin: 15px auto;
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
            background-color: darkblue;
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
<h3>⚠️¡Recuerda verificar que servicios fueron solicitados antes de asignar personal en "Ver Solicitud"!</h3>
<table class="tabla-eventos">
    <tr>
        <th>Número de solicitud</th>
        <th>Botón</th>
        <th>Ver Solicitud</th>
    </tr>
    <?php foreach ($eventos_aceptados as $evento): ?>
        <tr>
            <td><?= $evento['num_solicitud'] ?></td>
            <td>
                <form action="RegistrarPersonal.php" method="post">
                    <input type="hidden" name="num_solicitud" value="<?= $evento['num_solicitud'] ?>">
                    <button type="submit" class="btn-personal">
                        Registrar Personal para Evento <?= $evento['num_solicitud'] ?>
                    </button>
                </form> 
            </td>
            <td>
                <form action="VerSolicitud.php" method="post">
        <input type="hidden" name="num_solicitud" value="<?= $evento["num_solicitud"] ?>">
        <button type="submit" class="btn btn-mirar"; style="padding: 10px 20px; background-color: darkblue; color: white; border: none; border-radius: 5px; cursor: pointer;">Detalles</button> 
    </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<div style="position: absolute; top: 85%; left: 200px; transform: translateY(-50%);">
    <form action="EstatusEventosAdmin.php" method="get">
        <button type="submit" style="padding: 10px 20px; background-color: #25344f; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Regresar
        </button>
    </form>
</div>
</body>
</html>
