<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["num_solicitud"])) {
    $num_solicitud = $_POST["num_solicitud"];

    $stmt = $conn->prepare("UPDATE solicitud SET evento_status = 'Rechazado' WHERE num_solicitud = ?");
    $stmt->bind_param("i", $num_solicitud);

    if ($stmt->execute()) {
        header("Location: EstatusEventosAdmin.php");
    } else {
        echo "Error al rechazar el evento.";
    }

    $stmt->close();
    $conn->close();
}
?>
