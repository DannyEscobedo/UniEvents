<?php
session_start();  // Asegurar que la sesión esté iniciada antes de usar $_SESSION

include("conexion.php");

// Verifica si la sesión está activa
if (!isset($_SESSION["usuario"])) {
    header("Location: IniciarSesion.php"); // Redirige al login si no hay sesión
    exit();
}

// Obtener todas las solicitudes del usuario actual
$num_control = $_SESSION["usuario"];
$sql = "SELECT num_solicitud, nombre_evento, fecha_evento, evento_status FROM solicitud WHERE num_control = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $num_control);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Estatus del Evento</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
            background: url('fondo admincontraseña.png') no-repeat center center fixed;
            background-size: cover;
            color: black;
        }

        .container {
            max-width: 800px;
            margin: 20px auto; /* Separa la tabla del menú */
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px; /* Espacio entre la tabla y el navbar */
        }

        table, th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #25344f;
            color: white;
        }

        button {
            padding: 5px 10px;
            background-color: #f44336;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #d32f2f;
        }

        h2 {
            color: darkblue;
            text-align: center;
        }

        span {
            font-size: 11px;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div>
        <a href="MenuSolicitante.php">Inicio</a>
        <a href="SolicitarEvento.php">Solicitar Evento</a>
        <a href="EstatusEvento.php">Estatus del Evento</a>
        <a href="FichaTecnica.php">Ficha Técnica del Evento</a>
        <a href="SubirFlyer.php">Subir Flyer</a>
    </div>
    <a href="CerrarSesion.php">Cerrar sesión</a>
</div>

<div class="container">
    <h2>Estatus de Eventos</h2>
    <table>
    <tr>
        <th>Número de Solicitud</th>
        <th>Evento</th>
        <th>Fecha</th>
        <th>Estado</th>
        <th>Botones</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row["num_solicitud"] ?></td>
            <td><?= $row["nombre_evento"] ?></td>
            <td><?= $row["fecha_evento"] ?></td>
            <td><?= $row["evento_status"] ?></td>
            <td>
                <?php if ($row["evento_status"] !== 'Aceptado'): ?>
                    <!-- Si el evento no está en estado 'Aceptado', mostrar los botones de Modificar y Cancelar -->
                    <form action="ModificarEvento.php" method="post" style="display: inline;">
                        <input type="hidden" name="num_solicitud" value="<?= $row["num_solicitud"] ?>">
                        <button type="submit" style="background-color: #ffcc00; color: black; border: none; padding: 5px 10px; cursor: pointer;">Modificar</button>
                    </form>
                    <form action="CancelarEvento.php" method="post" style="display: inline;">
                        <input type="hidden" name="num_solicitud" value="<?= $row["num_solicitud"] ?>">
                        <button type="submit" style="background-color: #f44336; color: white; border: none; padding: 5px 10px; cursor: pointer;" onclick="return confirm('¿Estás seguro de cancelar este evento?');">
                            Cancelar
                        </button>
                    </form>
                <?php else: ?>
                    <!-- Si el evento está en estado 'Aceptado', mostrar un mensaje y deshabilitar los botones -->
                    <span style="color: red; font-weight: bold;">Este evento no puede ser modificado ni cancelado.</span>
                    <button class="disabled-button" disabled>Modificar</button>
                    <button class="disabled-button" disabled>Cancelar</button>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
</div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
