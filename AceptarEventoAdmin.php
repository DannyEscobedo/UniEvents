<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["num_solicitud"])) {
    $num_solicitud = $_POST["num_solicitud"];

    // Obtener fecha, hora_inicio y hora_fin del evento actual
    $stmt = $conn->prepare("SELECT fecha_evento, hora_inicio, hora_fin FROM solicitud WHERE num_solicitud = ?");
    $stmt->bind_param("i", $num_solicitud);
    $stmt->execute();
    $stmt->bind_result($fecha_evento, $hora_inicio, $hora_fin);
    $stmt->fetch();
    $stmt->close();

    // Verificar solapamiento de horarios con eventos ya aceptados en la misma fecha
    $verifica = $conn->prepare("
        SELECT COUNT(*) FROM solicitud 
        WHERE fecha_evento = ?
        AND evento_status = 'Aceptado'
        AND (
            (hora_inicio < ? AND hora_fin > ?) OR
            (hora_inicio < ? AND hora_fin > ?) OR
            (hora_inicio >= ? AND hora_fin <= ?)
        )
    ");
    $verifica->bind_param("sssssss", $fecha_evento, $hora_fin, $hora_inicio, $hora_fin, $hora_fin, $hora_inicio, $hora_fin);
    $verifica->execute();
    $verifica->bind_result($conflictos);
    $verifica->fetch();
    $verifica->close();

    if ($conflictos > 0) {
        echo "<script>
                alert('Ya existe un evento aceptado entre el horario solicitado. Favor de rechazarlo o comunicarse con el solicitante.');
                window.location.href = 'EstatusEventosAdmin.php';
              </script>";
        exit();
    }

    // Si no hay conflictos, aceptar el evento
    $stmt = $conn->prepare("UPDATE solicitud SET evento_status = 'Aceptado' WHERE num_solicitud = ?");
    $stmt->bind_param("i", $num_solicitud);

    if ($stmt->execute()) {
        // Insertar también en la tabla estatus_evento
        $estatus = "aceptado";
        $insert = $conn->prepare("INSERT INTO estatus_evento (estatus, num_solicitud) VALUES (?, ?)");
        $insert->bind_param("si", $estatus, $num_solicitud);
        $insert->execute();
        $insert->close();

        echo "<script>
                alert('Evento aceptado exitosamente.');
                window.location.href = 'EstatusEventosAdmin.php';
              </script>";
    } else {
        echo "<script>
                alert('Error al aceptar el evento.');
                window.location.href = 'EstatusEventosAdmin.php';
              </script>";
    }

    $stmt->close();
    $conn->close();
}
?>
