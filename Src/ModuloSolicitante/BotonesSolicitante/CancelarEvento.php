<?php
session_start();
include("conexion.php");

if (!isset($_SESSION["usuario"])) {
    header("Location: IniciarSesion.php");
    exit();
}

if (isset($_POST["num_solicitud"]) && !empty($_POST["num_solicitud"])) {
    $num_solicitud = $_POST["num_solicitud"];

    $sql = "DELETE FROM solicitud WHERE num_solicitud = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $num_solicitud);

        if ($stmt->execute()) {
            $_SESSION["mensaje"] = "Evento eliminado correctamente.";
        } else {
            $_SESSION["mensaje"] = "Error al eliminar el evento: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION["mensaje"] = "Error en la preparación de la consulta: " . $conn->error;
    }
}

$conn->close();
header("Location: MenuSolicitante.php");
exit();
?>
