<?php
include("conexion.php");

if (!isset($_POST['num_solicitud'])) {
    echo "No se especificó una solicitud.";
    exit;
}

$num_solicitud = $_POST['num_solicitud'];

$sql = "SELECT * FROM solicitud WHERE num_solicitud = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $num_solicitud);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "No se encontró la solicitud.";
    exit;
}

$solicitud = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Visualizar Solicitud</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');
        body {
            font-family: "Poppins", sans-serif;
            background: url('fondo admincontraseña.png') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
        }
        label {
            font-weight: bold;
            color: darkblue;
            display: block;
            margin-top: 15px;
        }
        p {
            margin: 5px 0;
        }
        h2 {
            text-align: center;
            color: darkblue;
            font-size: 24px;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
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
        .checkbox-group {
            margin-left: 15px;
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

    <div class="container">
    <h2>Visualización de Solicitud</h2>

    <label>Fecha de Elaboración:</label>
    <p><?= htmlspecialchars($solicitud['fecha_elaboracion']) ?></p>

    <label>Departamento Solicitante:</label>
    <p><?= htmlspecialchars($solicitud['depto_solicitante_nombre']) ?></p>

    <label>Nombre del Evento:</label>
    <p><?= htmlspecialchars($solicitud['nombre_evento']) ?></p>

    <label>Fecha del Evento:</label>
    <p><?= htmlspecialchars($solicitud['fecha_evento']) ?></p>

    <label>Hora de Inicio:</label>
    <p><?= htmlspecialchars($solicitud['hora_inicio']) ?></p>

    <label>Hora de Fin:</label>
    <p><?= htmlspecialchars($solicitud['hora_fin']) ?></p>

    <label>Lugar:</label>
    <p><?= htmlspecialchars($solicitud['lugar']) ?></p>

    <label>Equipo de Audio:</label>
    <p><?= htmlspecialchars($solicitud['equipo_audio']) ?></p>

    <label>Difusión Interna:</label>
    <p><?= htmlspecialchars($solicitud['difusion_interna']) ?></p>

    <label>Difusión Externa:</label>
    <p><?= htmlspecialchars($solicitud['difusion_externa']) ?></p>

    <label>Fecha de Publicación:</label>
    <p><?= htmlspecialchars($solicitud['difusion_fecha_inicio']) ?></p>

    <label>Fecha de Término de Publicación:</label>
    <p><?= htmlspecialchars($solicitud['difusion_fecha_termino']) ?></p>

    <label>Diseño:</label>
    <p><?= htmlspecialchars($solicitud['diseno']) ?></p>

    <label>Impresión:</label>
    <p><?= htmlspecialchars($solicitud['impresion']) ?></p>

    <label>Número de Copias:</label>
    <p><?= htmlspecialchars($solicitud['num_copias']) ?></p>

    <label>Toma de Fotografías:</label>
    <p><?= htmlspecialchars($solicitud['toma_fotografias']) ?></p>

    <label>Maestro de Ceremonia:</label>
    <p><?= htmlspecialchars($solicitud['maestro_ceremonia']) ?></p>

    <label>Display:</label>
    <p><?= htmlspecialchars($solicitud['display']) ?></p>

    <label>Texto para Display:</label>
    <p><?= htmlspecialchars($solicitud['texto_display']) ?></p>

        <!-- Boton inferior -->
        <div style="position: absolute; bottom: -750px; left: 920px; right: 0px; display: flex; justify-content: space-between; padding: 0 25px;">
            <!-- Regresar -->
            <button type="button" onclick="window.location.href='EventosAceptados.php'" 
                style="padding: 10px 20px; background-color: #25344f; color: white; border: none; border-radius: 10px; cursor: pointer; font-size: 15px;">
                Regresar
            </button>
        </div>
</div>
</body>
</html>
