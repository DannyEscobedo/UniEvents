<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/Exception.php';
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';

include("conexion.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo_ins = trim($_POST["correo_ins"]);

    // Validar si el correo existe en la base de datos
    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE correo_ins = ? AND rol_usuario = 3");
$stmt->bind_param("s", $correo_ins);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    // 📌 GENERAR CÓDIGO Y GUARDAR EN SESIÓN
    $codigo = rand(100000, 999999);
    $expira = time() + (10 * 60); // Expira en 10 minutos

    $_SESSION["codigo_recuperacion"] = $codigo;
    $_SESSION["codigo_expiracion"] = $expira;
    $_SESSION["correo_recuperacion"] = $correo_ins;

    // 📧 CONFIGURAR PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'tuemail@gmail.com'; // Cambia esto
        $mail->Password = 'tucontraseña'; // Cambia esto
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('tuemail@gmail.com', 'Soporte UniEvents');
        $mail->addAddress($correo_ins);
        $mail->Subject = "Código de Recuperación";
        $mail->Body = "Tu código de recuperación es: $codigo\n\nEste código expirará en 10 minutos.";

        if ($mail->send()) {
            echo json_encode(["success" => true, "redirect" => "VerificarCodigo.php"]);
        } else {
            echo json_encode(["success" => false, "message" => "⚠️ Error al enviar el correo."]);
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "⚠️ Error de correo: " . $mail->ErrorInfo]);
    }
} else {
    echo json_encode(["success" => false, "message" => "⚠️ El correo no está registrado."]);
}
    $stmt->close();
    $conexion->close();
    exit();
}
?>
