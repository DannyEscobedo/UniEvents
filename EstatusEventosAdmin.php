<?php
session_start();
include("conexion.php");

if (!isset($_SESSION["usuario"])) {
    header("Location: IniciarSesion.php");
    exit();
}

$sql = "SELECT num_solicitud, nombre_evento, fecha_evento, hora_inicio, hora_fin, evento_status FROM solicitud";
$result = $conn->query($sql);
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

        .accepted {
            background-color: #c8e6c9 !important;
        }

        .rejected {
            background-color: #ffcdd2 !important;
        }

        .disabled {
            opacity: 0.6;
            pointer-events: none;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <div>
            <a href="MenuAdministrador.php">Inicio</a>
            <a href="EstatusEventosAdmin.php">Estatus Eventos</a>
            <a href="CalendarioDisponibilidad.php">Calendario de Disponibilidad</a>
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
        <th>Hora Inicio</th> <!-- nueva columna -->
        <th>Hora Fin</th>    <!-- nueva columna -->
        <th>Estatus</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <?php
            $rowClass = "";
            $disabled = "";

            if ($row['evento_status'] === 'Aceptado') {
                $rowClass = "accepted";
                $disabled = "disabled";
            } elseif ($row['evento_status'] === 'Rechazado') {
                $rowClass = "rejected";
                $disabled = "disabled";
            }
        ?>
        <tr class="<?= $rowClass ?>">
            <td><?= $row["num_solicitud"] ?></td>
            <td><?= $row["nombre_evento"] ?></td>
            <td><?= $row["fecha_evento"] ?></td>
            <td><?= $row["hora_inicio"] ?></td> <!-- nuevo dato -->
            <td><?= $row["hora_fin"] ?></td>     <!-- nuevo dato -->
            <td>
                <form action="AceptarEventoAdmin.php" method="post" style="display:inline;">
                    <input type="hidden" name="num_solicitud" value="<?= $row["num_solicitud"] ?>">
                    <button 
                        class="btn btn-aceptar <?= $disabled ?>" 
                        type="submit" 
                        data-fecha="<?= $row['fecha_evento'] ?>" 
                        onclick="return validarFecha(this);" 
                        <?= $disabled ?>>
                        Aceptar
                    </button>
                </form>
                <form action="CancelarEventoAdmin.php" method="post" style="display:inline;">
                    <input type="hidden" name="num_solicitud" value="<?= $row["num_solicitud"] ?>">
                    <button class="btn btn-rechazar <?= $disabled ?>" type="submit" <?= $disabled ?> onclick="return confirm('¿Estás seguro de querer rechazar esta solicitud?');">Rechazar</button>
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
        alert("No se puede aceptar un evento cuya fecha ya pasó. Solo puedes rechazarlo.");
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
