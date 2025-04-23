<?php
session_start();
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Asignar un número de control fijo
    $num_control = '21051412';  // Número de control fijo

    // Obtener datos del formulario
    $nombre_evento = $_POST['nombre_evento'];
    $atuendo = $_POST['atuendo'];
    $no_asistentes = $_POST['no_asistentes'];
    $autoridades = $_POST['autoridades'];
    $invitados = $_POST['invitados'];

    // Insertar en ficha_tecnica_evento
    $stmt = $conn->prepare("INSERT INTO ficha_tecnica_evento (nombre_evento, atuendo, no_asistentes, num_control, idEvento_status) VALUES (?, ?, ?, ?, ?)");
    $evento_status = 1; // Por ejemplo, 1 puede ser "pendiente" o el estado inicial
    $stmt->bind_param("ssiii", $nombre_evento, $atuendo, $no_asistentes, $num_control, $evento_status);

    if ($stmt->execute()) {
        // Obtener el idFicha_tecnica_evento recién insertado
        $idFicha_tecnica_evento = $stmt->insert_id;

        // Insertar autoridades si hay
        if (!empty($autoridades)) {
    foreach ($autoridades['nombre'] as $key => $nombre) {
        // Verificamos que los campos no estén vacíos
        if (!empty($nombre) && !empty($autoridades['apellido_paterno'][$key]) && !empty($autoridades['apellido_materno'][$key]) && !empty($autoridades['cargo'][$key])) {
            $apellido_paterno = $autoridades['apellido_paterno'][$key];
            $apellido_materno = $autoridades['apellido_materno'][$key];
            $cargo = $autoridades['cargo'][$key];

            $stmt_autoridad = $conn->prepare("INSERT INTO autoridades_evento (nombre_autoridad, apellido_paterno, apellido_materno_a, cargo, idFicha_tecnica_evento) VALUES (?, ?, ?, ?, ?)");
            $stmt_autoridad->bind_param("ssssi", $nombre, $apellido_paterno, $apellido_materno, $cargo, $idFicha_tecnica_evento);
            $stmt_autoridad->execute();
        }
    }
}

        // Insertar invitados si hay
if (!empty($invitados)) {
    foreach ($invitados['nombre'] as $key => $nombre) {
        if (!empty($nombre) && !empty($invitados['apellido_paterno'][$key]) && !empty($invitados['apellido_materno'][$key])) {
            $apellido_paterno = $invitados['apellido_paterno'][$key];
            $apellido_materno = $invitados['apellido_materno'][$key];

            $stmt_invitado = $conn->prepare("INSERT INTO invitados (nombre_invitado, apellido_paterno, apellido_materno, idFicha_tecnica_evento) VALUES (?, ?, ?, ?)");
            $stmt_invitado->bind_param("sssi", $nombre, $apellido_paterno, $apellido_materno, $idFicha_tecnica_evento);
            $stmt_invitado->execute();
        }
    }
}

        // Redirigir al administrador después de guardar la ficha técnica
        echo "<script>
    alert('Ficha técnica guardada exitosamente');
    window.location.href = 'FichaTecnica.php';
</script>";
exit;
    } else {
        echo "Error al guardar la ficha técnica del evento.";
    }

    $stmt->close();
    $conn->close();
}
?>
