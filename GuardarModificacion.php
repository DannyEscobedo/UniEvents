<?php
session_start();
include("conexion.php");

if (!isset($_SESSION["usuario"])) {
    header("Location: IniciarSesion.php");
    exit();
}

// Configurar la zona horaria de México
date_default_timezone_set('America/Mexico_City');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["num_solicitud"])) {
    $num_solicitud = $_POST["num_solicitud"];

    // 🔹 Obtener la fecha_elaboracion actual de la BD
    $sql_check = "SELECT fecha_elaboracion FROM solicitud WHERE num_solicitud = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $num_solicitud);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    $row = $result->fetch_assoc();
    $stmt_check->close();

    // 🔹 Si la fecha es 0000-00-00 o NULL, asignar la fecha actual
    $fecha_elaboracion = ($row["fecha_elaboracion"] == "0000-00-00" || empty($row["fecha_elaboracion"])) 
        ? date("Y-m-d") 
        : $row["fecha_elaboracion"];

    // 🔹 Recibir los demás datos del formulario
    $depto_solicitante = isset($_POST["depto_solicitante"]) ? trim($_POST["depto_solicitante"]) : "No especificado";
    $nombre_evento = isset($_POST["nombre_evento"]) ? trim($_POST["nombre_evento"]) : "Sin Nombre";
    $fecha_evento = $_POST["fecha_evento"];
    $hora_inicio = $_POST["hora_inicio"];
    $hora_fin = $_POST["hora_fin"];
    $lugar_evento = $_POST["lugar_evento"];
    $equipo_audio = $_POST["equipo_audio"] ?? "No";
    $difusion_interna = $_POST["difusion_interna"] ?? "No";
    $difusion_externa = $_POST["difusion_externa"] ?? "No";
    $difusion_fecha_inicio = $_POST["difusion_fecha_inicio"] ?? null;
    $difusion_fecha_termino = $_POST["difusion_fecha_termino"] ?? null;
    $diseno = $_POST["diseno"] ?? "No";
    $impresion = $_POST["impresion"] ?? "No";
    $num_copias = $_POST["num_copias"] ?? 0;
    $toma_fotografias = (isset($_POST["toma_fotografias"]) && $_POST["toma_fotografias"] === "sí") ? 1 : 0;
    $maestro_ceremonia = (isset($_POST["maestro_ceremonia"]) && $_POST["maestro_ceremonia"] === "sí") ? 1 : 0;
    $display = (isset($_POST["display"]) && $_POST["display"] === "Si") ? 1 : 0;
    $texto_display = $_POST["texto_display"] ?? null;
    $evento_solicitante_nombre = $_SESSION["nombre"] ?? "Desconocido";

    $fecha_elaboracion = date("Y-m-d H:i:s");

    // 🔹 Actualizar los datos, incluyendo la fecha_elaboracion corregida
    $sql_update = "UPDATE solicitud 
    SET fecha_elaboracion = ?, depto_solicitante_nombre = ?, nombre_evento = ?, 
        fecha_evento = ?, hora_inicio = ?, hora_fin = ?, lugar = ?, 
        equipo_audio = ?, difusion_interna = ?, difusion_externa = ?, 
        difusion_fecha_inicio = ?, difusion_fecha_termino = ?, diseno = ?, 
        impresion = ?, num_copias = ?, toma_fotografias = ?, maestro_ceremonia = ?, 
        display = ?, texto_display = ?, evento_solicitante_nombre = ? 
    WHERE num_solicitud = ?";

    if ($stmt_update = $conn->prepare($sql_update)) {
$stmt_update->bind_param("ssssssssssssssissssss", 
    $fecha_elaboracion, $depto_solicitante, $nombre_evento, 
    $fecha_evento, $hora_inicio, $hora_fin, $lugar_evento, 
    $equipo_audio, $difusion_interna, $difusion_externa, 
    $difusion_fecha_inicio, $difusion_fecha_termino, $diseno, 
    $impresion, $num_copias, $toma_fotografias, $maestro_ceremonia,
    $display, $texto_display, $evento_solicitante_nombre,
    $num_solicitud
);

        if ($stmt_update->execute()) {
            $_SESSION["mensaje"] = "Evento actualizado correctamente.";
        } else {
            $_SESSION["mensaje"] = "Error al actualizar el evento: " . $stmt_update->error;
        }
        $stmt_update->close();
    } else {
        $_SESSION["mensaje"] = "Error en la consulta: " . $conn->error;
    }
}

$conn->close();
header("Location: MenuSolicitante.php");
exit();
?>
