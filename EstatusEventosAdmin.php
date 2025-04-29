<?php
session_start();
include("conexion.php");

if (!isset($_SESSION["usuario"])) {
    header("Location: IniciarSesion.php");
    exit();
}

// Solo obtener eventos pendientes
$sql = "SELECT num_solicitud, nombre_evento, fecha_evento, hora_inicio, hora_fin, evento_status FROM solicitud WHERE evento_status = 'Pendiente'";
$result = $conn->query($sql);

if (isset($_GET['accion'])) {
    if ($_GET['accion'] === 'aceptado') {
        echo "<script>alert('Evento aceptado exitosamente');</script>";
    } elseif ($_GET['accion'] === 'rechazado') {
        echo "<script>alert('Evento rechazado exitosamente');</script>";
    } elseif ($_GET['accion'] === 'error') {
        echo "<script>alert('Ocurrió un error al actualizar el evento');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Administrar Solicitudes</title>
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

        .container {
            max-width: 900px;
            margin: 40px auto;
            background: #ffffff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: darkblue;
            font-size: 24px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #25344f;
            color: white;
        }

        .btn {
            padding: 6px 12px;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .btn-aceptar {
            background-color: #4CAF50;
        }

        .btn-rechazar {
            background-color: #f44336;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <div>
            <a href="MenuAdministrador.php">Inicio</a>
            <a href="EstatusEventosAdmin.php">Estatus Eventos</a>
            <a href="AdminRestContraseña.php">Restablecer Contraseña</a>
        </div>
        <a href="CerrarSesion.php">Cerrar sesión</a>
    </div>

<div class="container">
    <h2>Solicitudes por Atender</h2>
    <table>
    <tr>
        <th>Número de Solicitud</th>
        <th>Evento</th>
        <th>Fecha</th>
        <th>Hora Inicio</th>
        <th>Hora Fin</th>
        <th>Botones</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row["num_solicitud"] ?></td>
            <td><?= $row["nombre_evento"] ?></td>
            <td><?= $row["fecha_evento"] ?></td>
            <td><?= $row["hora_inicio"] ?></td>
            <td><?= $row["hora_fin"] ?></td>
            <td>
                <form action="AceptarEventoAdmin.php" method="post" style="display:inline;">
                    <input type="hidden" name="num_solicitud" value="<?= $row["num_solicitud"] ?>">
                    <button class="btn btn-aceptar" type="submit" data-fecha="<?= $row['fecha_evento'] ?>" onclick="return validarFecha(this);">Aceptar</button>
                </form>
                <form action="CancelarEventoAdmin.php" method="post" style="display:inline;">
                    <input type="hidden" name="num_solicitud" value="<?= $row["num_solicitud"] ?>">
                    <button class="btn btn-rechazar" type="submit" onclick="return confirm('¿Estás seguro de querer rechazar esta solicitud?');">Rechazar</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
</div>

<script>
function validarFecha(boton) {
    const fechaEvento = new Date(boton.getAttribute("data-fecha"));
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    fechaEvento.setHours(0, 0, 0, 0);

    if (fechaEvento < hoy) {
        alert("No se puede aceptar un evento cuya fecha u hora ya pasó. Solo puedes rechazarlo.");
        return false;
    }
    return true;
}
</script>

</body>
</html>

<?php
$conn->close();
?>
