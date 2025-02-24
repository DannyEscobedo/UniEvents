<?php
header('Content-Type: text/html; charset=utf-8');
session_start();
$conexion = new mysqli("localhost", "root", "", "unievents");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$error = ""; // Variable para guardar errores

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener y normalizar los datos del formulario
    $nombres_usuarios = trim($_POST["nombres_usuarios"]);
    $apellido_paterno   = trim($_POST["apellido_paterno"]);
    $apellido_materno   = trim($_POST["apellido_materno"]);
    $num_control        = trim($_POST["num_control"]);
    $correo_ins         = strtolower(trim($_POST["correo_ins"]));
    $contraseña         = $_POST["contraseña"];

    // Comprobación básica
    if (empty($nombres_usuarios) || empty($apellido_paterno) || empty($apellido_materno) ||
        empty($num_control) || empty($correo_ins) || empty($contraseña)) {
        $error = "Todos los campos son obligatorios.";
    } else {
        // Prevenir inyecciones SQL
        $nombres_usuarios = $conexion->real_escape_string($nombres_usuarios);
        $apellido_paterno = $conexion->real_escape_string($apellido_paterno);
        $apellido_materno = $conexion->real_escape_string($apellido_materno);
        $num_control      = $conexion->real_escape_string($num_control);
        $correo_ins       = $conexion->real_escape_string($correo_ins);
        $contraseña       = $conexion->real_escape_string($contraseña);

        // Validar que el correo institucional tenga exactamente 27 caracteres
        if (strlen($correo_ins) != 27) {
            $error = "El correo institucional debe tener exactamente 27 caracteres.";
        } else {
            // Verificar si ya existe un usuario con ese número de control O correo
            $checkSql = "SELECT * FROM usuarios WHERE num_control = '$num_control' OR correo_ins = '$correo_ins'";
            $checkResult = $conexion->query($checkSql);
            if ($checkResult && $checkResult->num_rows > 0) {
                $error = "El usuario ya existe. Cambia tu número de control y correo institucional.";
            }
        }
    }

    // Si no hay error, se procede a insertar
    if (empty($error)) {
        // Si decides almacenar la contraseña en texto plano (no recomendado)
        $contraseña = $contraseña;


        $sql = "INSERT INTO usuarios (nombres_usuarios, apellido_paterno, apellido_materno, num_control, correo_ins, contraseña, rol_usuario) 
            VALUES ('$nombres_usuarios', '$apellido_paterno', '$apellido_materno', '$num_control', '$correo_ins', '$contraseña', '3')";
        if ($conexion->query($sql) === TRUE) {
            // Registro exitoso: muestra banner y redirige
            echo '<!DOCTYPE html>
        <html lang="es">
        <head>
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1">
          <title>Registro Exitoso</title>
          <link rel="stylesheet" href="style.css">
          <style>
        
    .banner {
          width: 420px;
          background: transparent;
          border: 2px solid rgba(255, 255, 255, 0.2);
          backdrop-filter: blur(20px);
          box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
          color: black;
          border-radius: 10px;
          padding: 30px 40px;
          margin: 0 auto;
          text-align: center;
      }
      .banner h1 {
          font-weight: bold;
          color: darkblue;
      }

          </style>
        </head>
        <body>
          <div class="banner">
              <h1>¡Registro exitoso!</h1>
      <p>Tu cuenta ha sido registrada con éxito.</p>
      <p>Serás redirigido al inicio de sesión en 3 segundos.</p>
              </div>
              <script>
              setTimeout(function(){
                  window.location.href = "IniciarSesion.php";
              }, 3000);
          </script>
        </body>
        </html>';
            $conexion->close();
            exit();
        } else {
            $error = "Error: " . $sql . "<br>" . $conexion->error;
        }
    }
    $conexion->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro UniEvents+</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <!-- Mostrar mensaje de error si existe -->
        <?php if (!empty($error)) : ?>
            <div class="error-banner" style="color: red; text-align: center; margin-bottom: 15px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="" id="formRegistro" method="POST">
            <h1>¡Regístrate!</h1>
            <!-- Número de control -->
            <div class="input-box">
                <input type="text" id="num_control" name="num_control" inputmode="numeric" maxlength="8" placeholder="Número de control" oninput="this.value = this.value.replace(/\D/g, '')">
                <span class="error-message" id="error-matricula"></span>
                <i class="bx bxs-notepad"></i>
            </div>
            <!-- Contraseña -->
            <div class="input-box">
                <input type="password" id="contraseña" name="contraseña" minlength="8" maxlength="16" placeholder="Contraseña">
                <button type="button" id="togglePassword" class="eye-button">
                    <i class="bx bx-show"></i>
                </button>
                <span class="error-message" id="error-password"></span>
                <i class="bx bxs-lock-alt"></i>
            </div>
            <!-- Correo Institucional -->
            <div class="input-box">
                <input type="text" id="correo_ins" name="correo_ins" minlength="27" maxlength="27" placeholder="Correo Institucional">
                <span class="error-message" id="error-correo"></span>
                <i class="bx bxs-envelope"></i>
            </div>
            <!-- Nombre(s) -->
            <div class="input-box">
                <input type="text" id="nombres_usuarios" name="nombres_usuarios" placeholder="Nombre(s)" maxlength="22">
                <span class="error-message" id="error-nombres"></span>
                <i class="bx bxs-user"></i>
            </div>
            <!-- Apellido paterno -->
            <div class="input-box">
                <input type="text" id="apellido_paterno" name="apellido_paterno" placeholder="Apellido Paterno" maxlength="20">
                <span class="error-message" id="error-paterno"></span>
                <i class="bx bxs-user"></i>
            </div>
            <!-- Apellido materno -->
            <div class="input-box">
                <input type="text" id="apellido_materno" name="apellido_materno" placeholder="Apellido Materno" maxlength="20">
                <span class="error-message" id="error-materno"></span>
                <i class="bx bxs-user"></i>
            </div>
            <!-- Botón de registrar -->
            <button type="submit" id="btnRegistro" class="btn">Registrar</button>
            <div class="inicioSesion-link">
                <p>¿Ya tienes cuenta? <a href="IniciarSesion.php">Inicia sesión aquí</a></p>
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
        .eye-button {
            background: none;
            border: none;
            cursor: pointer;
            position: absolute;
            right: 26px;
            top: 50%;
            transform: translateY(-50%);
        }
    </style>
    <script>
        // Permitir solo letras en los campos de nombres
        function soloLetras(event) {
            let key = event.key;
            if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(key)) {
                event.preventDefault();
            }
        }
        document.getElementById("nombres_usuarios").addEventListener("keypress", soloLetras);
        document.getElementById("apellido_paterno").addEventListener("keypress", soloLetras);
        document.getElementById("apellido_materno").addEventListener("keypress", soloLetras);

        // Validación de formulario en el cliente
        document.getElementById("formRegistro").addEventListener("submit", function(event) {
            let valid = true;
            let campos = [
                { id: "nombres_usuarios", errorId: "error-nombres", mensaje: "Completa este campo." },
                { id: "apellido_paterno", errorId: "error-paterno", mensaje: "Completa este campo." },
                { id: "apellido_materno", errorId: "error-materno", mensaje: "Completa este campo." },
                { id: "num_control", errorId: "error-matricula", mensaje: "El número de control debe tener 8 dígitos." },
                { id: "correo_ins", errorId: "error-correo", mensaje: "Completa este campo." },
                { id: "contraseña", errorId: "error-password", mensaje: "Completa este campo." }
            ];
            campos.forEach(campo => {
                let input = document.getElementById(campo.id);
                let errorSpan = document.getElementById(campo.errorId);
                if (input.value.trim() === "") {
                    errorSpan.textContent = campo.mensaje;
                    valid = false;
                } else {
                    errorSpan.textContent = "";
                }
            });
            let num_control = document.getElementById("num_control").value.trim();
            let errorMatricula = document.getElementById("error-matricula");
            if (num_control.length !== 8 || isNaN(num_control)) {
                errorMatricula.textContent = "El número de control debe tener 8 dígitos numéricos.";
                valid = false;
            }
            let correo = document.getElementById("correo_ins").value.trim();
            let errorCorreo = document.getElementById("error-correo");
            let dominio = "@saltillo.tecnm.mx";
            if (!correo.endsWith(dominio)) {
                errorCorreo.textContent = "El correo debe terminar en '@saltillo.tecnm.mx'.";
                valid = false;
            }
            let password = document.getElementById("contraseña").value.trim();
            let errorPassword = document.getElementById("error-password");
            let regexMayuscula = /[A-Z]/;
            let regexMinuscula = /[a-z]/;
            let regexNumero = /[0-9]/;
            let regexSimbolo = /[!@#$%^&*(),.?":{}|<>]/;
            let contraseñasComunes = ["123456", "password", "qwerty", "admin123", "abcdef", "12345678", "111111"];
            if (password.length < 8) {
                errorPassword.textContent = "Debe tener al menos 8 caracteres.";
                valid = false;
            } else if (!regexMayuscula.test(password)) {
                errorPassword.textContent = "Debe incluir al menos una letra mayúscula.";
                valid = false;
            } else if (!regexMinuscula.test(password)) {
                errorPassword.textContent = "Debe incluir al menos una letra minúscula.";
                valid = false;
            } else if (!regexNumero.test(password)) {
                errorPassword.textContent = "Debe incluir al menos un número.";
                valid = false;
            } else if (!regexSimbolo.test(password)) {
                errorPassword.textContent = "Debe incluir al menos un símbolo especial (!@#$%^&* etc.).";
                valid = false;
            } else if (contraseñasComunes.includes(password.toLowerCase())) {
                errorPassword.textContent = "No uses una contraseña común.";
                valid = false;
            }
            if (!valid) {
                event.preventDefault();
            }
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const togglePassword = document.getElementById("togglePassword");
            const passwordInput = document.getElementById("contraseña");
            const icon = togglePassword.querySelector("i");
            togglePassword.addEventListener("click", function() {
                if (passwordInput.type === "password") {
                    passwordInput.type = "text";
                    icon.classList.remove("bx-show");
                    icon.classList.add("bx-hide");
                } else {
                    passwordInput.type = "password";
                    icon.classList.remove("bx-hide");
                    icon.classList.add("bx-show");
                }
            });
        });
    </script>
</body>
</html>