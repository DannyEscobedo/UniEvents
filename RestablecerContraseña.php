<?php
header('Content-Type: text/html; charset=utf-8');
session_start();

// Establece la conexión
$conexion = new mysqli("localhost", "root", "", "unievents");

// En caso de error en la conexión, muestra mensaje de error
if ($conexion->connect_error) {
    die(json_encode(["success" => false, "message" => "Error de conexión a la base de datos."]));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener y limpiar el dato del formulario
    $correo_ins = strtolower(trim($_POST["correo_ins"]));
    $error = "";

    // Validar que el campo no esté vacío
    if (empty($correo_ins)) {
        $error = "El correo institucional es obligatorio.";
    }
    // Validar que el correo tenga exactamente 27 caracteres
    elseif (strlen($correo_ins) != 27) {
        $error = "El correo institucional debe tener exactamente 27 caracteres.";
    }
    // Validar que el correo termine en "@saltillo.tecnm.mx"
    elseif (!str_ends_with($correo_ins, "@saltillo.tecnm.mx")) {
        $error = "El correo debe terminar en '@saltillo.tecnm.mx'.";
    }

    // Si hay errores, devolver respuesta
    if (!empty($error)) {
        echo json_encode(["success" => false, "message" => $error]);
        exit();
    }

    // Verificar si el correo existe en la base de datos
    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo_ins);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        echo json_encode(["success" => true, "message" => "Correo válido."]);
    } else {
        echo json_encode(["success" => false, "message" => "El correo no está registrado."]);
    }

    $stmt->close();
    $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="wrapper">
        <form id="formRecuperar" method="POST">
            <h1>¿Olvidaste tu contraseña?</h1>
            <p>¡La recuperarás aquí!</p>

            <div class="input-box">
                <input type="text" id="correo_ins" name="correo_ins" maxlength="27" placeholder="Correo Institucional">
                <span class="error-message" id="error-correo"></span>
                <i class="bx bxs-envelope"></i>
            </div>

            <button type="submit" id="btnEnviar" class="btn">Enviar correo</button>

            <div class="registro-link">
                <p><a href="IniciarSesion.php">Regresar a Inicio de Sesión</a></p>
            </div>
        </form>
    </div>

    <style>
        .error-message {
            color: red;
            font-size: 11.5px;
            display: block;
            margin-top: 2px;
        }
        .wrapper p {
          font-size: 16px;
          text-align: center;
          color: darkblue;
          font-weight: bold;
          }
    </style>

    <script>
       $(document).ready(function() {
    $("#formRecuperar").submit(function(event) {
        event.preventDefault(); // Evita que la página se recargue

        let correo = $("#correo_ins").val().trim();
        let errorCorreo = $("#error-correo");
        let dominio = "@saltillo.tecnm.mx";

        // Resetear el mensaje de error
        errorCorreo.text("");

        // Validaciones en el frontend
        if (correo === "") {
            errorCorreo.text("Completa este campo.");
            return;
        } 
        if (correo.length !== 27) {  
            errorCorreo.text("El correo debe tener exactamente 27 caracteres.");
            return;
        } 
        if (!correo.endsWith(dominio)) {
            errorCorreo.text("El correo debe terminar en '@saltillo.tecnm.mx'.");
            return;
        }

        // Si pasa las validaciones, enviar la petición al servidor
        $.post("verificar_correo.php", { correo: correo }, function(response) {
            if (response.exists) {
                // Generar un código de 6 dígitos y enviarlo
                let codigo = Math.floor(100000 + Math.random() * 900000);
                $.post("enviar_codigo.php", { correo: correo, codigo: codigo }, function(response) {
                    if (response.success) {
                        alert("Código enviado. Revisa tu correo.");
                        window.location.href = "VerificarCodigo.php"; // Redirigir a la verificación
                    } else {
                        alert("❌ Error al enviar el código. Inténtalo de nuevo.");
                    }
                }, "json");
            } else {
                errorCorreo.text("El correo no está registrado.");
            }
        }, "json");
    });
});
    </script>
</body>
</html>
