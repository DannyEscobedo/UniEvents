<?php
session_start();
include("conexion.php");

if (!isset($_SESSION["usuario"])) {
    header("Location: IniciarSesion.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $num_solicitud = $_POST["num_solicitud"] ?? null;
    if (!is_numeric($num_solicitud)) {
        die("Número de solicitud inválido.");
    }

    $roles = [
        "Fotografía" => 1,
        "Redes Sociales" => 2,
        "Sonido" => 3,
        "Maestr@ Ceremonia" => 4,
        "Diseñador@" => 5,
        "Imprenta" => 6,
        "Redactor@" => 7
    ];

    if (!empty($_POST["personal"])) {
        $errores = [];
        $registro_exitoso = true;

        foreach ($_POST["personal"] as $seleccion) {
            $partes = explode(" - ", $seleccion);
            if (count($partes) < 2) {
                $errores[] = "Formato incorrecto: " . $seleccion;
                continue;
            }

            $rol = trim($partes[0]);

            if (isset($roles[$rol])) {
                $rol_puesto = $roles[$rol];
                $stmt = $conn->prepare("INSERT INTO puesto (rol_puesto, num_solicitud) VALUES (?, ?)");
                $stmt->bind_param("ii", $rol_puesto, $num_solicitud);

                if (!$stmt->execute()) {
                    $errores[] = "Error con el rol: " . $rol;
                    $registro_exitoso = false;
                }

                $stmt->close();
            } else {
                $errores[] = "Rol desconocido: " . $rol;
                $registro_exitoso = false;
            }
        }

        if ($registro_exitoso && empty($errores)) {
            echo "<script>alert('Personal asignado con éxito'); window.location.href = 'EventosAceptados.php';</script>";
        } else {
            echo "<script>alert('Errores:\n" . implode("\n", $errores) . "'); window.history.back();</script>";
        }
    }

    $conn->close();
}
?>
