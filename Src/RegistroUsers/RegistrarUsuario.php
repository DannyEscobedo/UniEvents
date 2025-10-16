<?php
header('Content-Type: text/html; charset=utf-8');
session_start();

// Establece la conexión
$conexion = new mysqli("localhost", "root", "", "unievents");

// En caso de error en la conexion, muestra mensaje de error
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$error = ""; // Variable para guardar errores

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener y acomodar los datos del formulario
    $nombres_usuarios = trim($_POST["nombres_usuarios"]);
    $apellido_paterno   = trim($_POST["apellido_paterno"]);
    $apellido_materno   = trim($_POST["apellido_materno"]);
    $num_control        = trim($_POST["num_control"]);
    $correo_ins         = strtolower(trim($_POST["correo_ins"]));
    $contraseña         = $_POST["contraseña"];

    // Comprobar que los campos estén llenos
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
           // Comprobación de datos duplicados
        $checkNumControl = "SELECT * FROM usuarios WHERE num_control = '$num_control'";
        $checkCorreo = "SELECT * FROM usuarios WHERE correo_ins = '$correo_ins'";

        // Consultas para comparar si existen los datos
        $checkNumControlResult = $conexion->query($checkNumControl);
        $checkCorreoResult = $conexion->query($checkCorreo);

        if ($checkNumControlResult && $checkNumControlResult->num_rows > 0 && $checkCorreoResult && $checkCorreoResult->num_rows > 0) {
    // Si tanto el número de control como el correo están duplicados
        $error = "El número de control y el correo institucional ya están registrados.";
        } elseif ($checkNumControlResult && $checkNumControlResult->num_rows > 0) {
        // Si solo el número de control está duplicado
        $error = "El número de control ya está registrado.";
        } elseif ($checkCorreoResult && $checkCorreoResult->num_rows > 0) {
        // Si solo el correo institucional está duplicado
        $error = "El correo institucional ya está registrado.";
       }
      }   
    }
    // Si no hay error, se procede a insertar el registro
    if (empty($error)) {
        // Almacenar contraseña
        $contraseña = $contraseña;  // Guardar contraseña

        // Insertar un nuevo registro por default con el rol 3 (rol_usuario)
        try {
            $sql = "INSERT INTO usuarios (nombres_usuarios, apellido_paterno, apellido_materno, num_control, correo_ins, contraseña, rol_usuario) 
                    VALUES ('$nombres_usuarios', '$apellido_paterno', '$apellido_materno', '$num_control', '$correo_ins', '$contraseña', '3')";
            if ($conexion->query($sql) === TRUE) {
                // Muestra banner de Registro exitoso y redirige al IniciarSesion.php
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
                //En caso de error, no deja insertar el registro
            } else {
                $error = "Error al insertar: " . $conexion->error;
            }
        } catch (mysqli_sql_exception $e) {
            // Manejar excepciones si hay algún error al insertar (por ejemplo, correo duplicado)
            if ($e->getCode() == 1062) {
                $error = "El correo ya está registrado.";
            } else {
                $error = "Error desconocido: " . $e->getMessage();
            }
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
    <!-- Nombre de la ventana (parte del navegador) -->
    <title>Registro UniEvents+</title>
    <link rel="stylesheet" href="style.css"> 
    <!-- Iconos de las casillas -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">

        <!-- Mostrar mensaje de error si existe el usuario -->
        <?php if (!empty($error)) : ?>
            <div class="error-banner" style="color: red; text-align: center; margin-bottom: 15px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

          <!-- Título del apartado transparente -->
        <form action="" id="formRegistro" method="POST">
            <h1>¡Regístrate!</h1>

            <!-- Número de control (Solo Números) -->
            <div class="input-box">
                <input type="text" id="num_control" name="num_control" inputmode="numeric" maxlength="8" placeholder="Número de control" oninput="this.value = this.value.replace(/\D/g, '')">
                <span class="error-message" id="error-matricula"></span>
                <i class="bx bxs-notepad"></i>
            </div>

            <!-- Contraseña y su condiciones -->
            <div class="input-box">
                <input type="password" id="contraseña" name="contraseña" minlength="8" maxlength="16" placeholder="Contraseña">
                <button type="button" id="togglePassword" class="eye-button">
                    <i class="bx bx-show"></i>
                </button>
                <span class="error-message" id="error-password"></span>
                <i class="bx bxs-lock-alt"></i>
            </div>

            <!-- Correo Institucional y su condiciones -->
           <div class="input-box correo-box">
    <div class="correo-completo">
        <input type="text" id="correo_usuario" name="correo_usuario" maxlength="9" placeholder="Ej. L21051423">
        <span class="correo-dominio">@saltillo.tecnm.mx</span>
    </div>
    <!-- Campo oculto donde se guardará el correo completo -->
    <input type="hidden" id="correo_ins" name="correo_ins">
    <span class="error-message" id="error-correo"></span>
    <i class="bx bxs-envelope"></i>
</div>
            <!-- Nombre(s) y su condiciones -->
            <div class="input-box">
                <input type="text" id="nombres_usuarios" name="nombres_usuarios" placeholder="Nombre(s)" maxlength="22">
                <span class="error-message" id="error-nombres"></span>
                <i class="bx bxs-user"></i>
            </div>

            <!-- Apellido paterno y su condiciones -->
            <div class="input-box">
                <input type="text" id="apellido_paterno" name="apellido_paterno" placeholder="Apellido Paterno" maxlength="20">
                <span class="error-message" id="error-paterno"></span>
                <i class="bx bxs-user"></i>
            </div>

            <!-- Apellido materno y su condiciones -->
            <div class="input-box">
                <input type="text" id="apellido_materno" name="apellido_materno" placeholder="Apellido Materno" maxlength="20">
                <span class="error-message" id="error-materno"></span>
                <i class="bx bxs-user"></i>
            </div>

            <!-- Botón de registrar y su condiciones -->
            <button type="submit" id="btnRegistro" class="btn">Crear Cuenta</button>
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

        /* === Estilo del campo de correo fusionado === */
.correo-completo {
    display: flex;
    align-items: center;
    position: relative;
    background: rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(0, 0, 0, 0.4);
    border-radius: 40px;
    padding: 0 15px;
    height: 45px;
    width: 100%;
    backdrop-filter: blur(10px);
    transition: 0.3s ease;
}

.correo-completo:focus-within {
    border-color: rgba(0, 102, 255, 0.5);
    box-shadow: 0 0 8px rgba(0, 102, 255, 0.3);
}

.correo-completo input {
    flex: 1;
    border: none;
    outline: none;
    background: transparent;
    color: #000;
    font-size: 16px;
    padding-left: 10px;
    padding-right: 130px;
}

.correo-dominio {
    position: absolute;
    right: 50px;
    color: rgba(0, 0, 0, 0.4); 
    font-size: 14px;
    pointer-events: none;
    user-select: none;
}

    </style>

<script>
// === FEATURE FLAG ===
false = Versión B (correo con dominio fijo + botón "Crear Cuenta")
true = Versión A (correo completo + botón "Registrar")

let featureFlag = Math.random() < 0.5; // 50% A / 50% B

// === Referencias base ===
let contenedorCorreo = document.querySelector(".correo-box") || document.querySelector(".input-box:nth-of-type(3)");
let btn = document.getElementById("btnRegistro");

// === CAMBIO DE CAMPO DE CORREO SEGÚN LA VERSIÓN ===
if (featureFlag) {
    // 🔹 Versión B (correo con dominio fijo)
    contenedorCorreo.innerHTML = `
        <div class="correo-completo">
            <input type="text " id="correo_usuario" name="correo_usuario" maxlength="15" placeholder="Ej. L21051423">
            <span class="correo-dominio">@saltillo.tecnm.mx</span>
        </div>
        <input type="hidden" id="correo_ins" name="correo_ins">
        <span class="error-message" id="error-correo"></span>
        <i class="bx bxs-envelope"></i>
        
    `;
    btn.textContent = "Crear Cuenta"; // cambio del texto del botón
    console.log("✅ Versión B activa: correo con dominio fijo + botón 'Crear Cuenta'");
} else {
    // 🔹 Versión A (correo completo)
    contenedorCorreo.innerHTML = `
        <input type="text" id="correo_ins" name="correo_ins" maxlength="9" placeholder="Correo Institucional">
        <span class="error-message" id="error-correo"></span>
        <i class="bx bxs-envelope"></i>
    `;
    btn.textContent = "Registrar"; // texto original
    console.log("✅ Versión A activa: correo completo + botón 'Registrar'");
}

// === MONITOREO DE INTERACCIÓN ===
btn.addEventListener("click", () => {
    if (featureFlag) {
        console.log("🧪 Usuario interactuó con Versión B (correo con dominio fijo)");
    } else {
        console.log("🧪 Usuario interactuó con Versión A (correo completo)");
    }
});
</script>


  <script>
document.getElementById("formRegistro").addEventListener("submit", function(event) {
    let valid = true;

    // === Validar campos vacíos (excepto correo, que se valida aparte)
    let campos = [
        { id: "nombres_usuarios", errorId: "error-nombres", mensaje: "Completa este campo." },
        { id: "apellido_paterno", errorId: "error-paterno", mensaje: "Completa este campo." },
        { id: "apellido_materno", errorId: "error-materno", mensaje: "Completa este campo." },
        { id: "num_control", errorId: "error-matricula", mensaje: "El número de control debe tener 8 dígitos." },
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

    // === Validar número de control ===
    let num_control = document.getElementById("num_control").value.trim();
    let errorMatricula = document.getElementById("error-matricula");
    if (num_control.length !== 8 || isNaN(num_control)) {
        errorMatricula.textContent = "El número de control debe tener 8 dígitos numéricos.";
        valid = false;
    } else {
        errorMatricula.textContent = "";
    }

    // === Combinar el correo visible con el dominio fijo ===
    let correoUsuario = document.getElementById("correo_usuario").value.trim();
    let dominioFijo = "@saltillo.tecnm.mx";
    let correoCompleto = correoUsuario + dominioFijo;

    // ✅ Aquí llenamos el campo oculto ANTES de enviar
    document.getElementById("correo_ins").value = correoCompleto;

    // === Validar el correo institucional ===
    let errorCorreo = document.getElementById("error-correo");
    if (correoUsuario === "") {
        errorCorreo.textContent = "Completa este campo.";
        valid = false;
    } else if (!/^[a-zA-Z0-9.]+$/.test(correoUsuario)) {
        errorCorreo.textContent = "Solo se permiten letras, números y puntos.";
        valid = false;
    } else {
        errorCorreo.textContent = "";
    }

    // === Validar la contraseña ===
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
    } else {
        errorPassword.textContent = "";
    }

    // === Detener envío si hay errores ===
    if (!valid) {
        event.preventDefault();
    }
});
</script>


    <script>
        //Funcion mirar/ocultar contraseña
        document.addEventListener("DOMContentLoaded", function() {
            const togglePassword = document.getElementById("togglePassword");
            const passwordInput = document.getElementById("contraseña");
            const icon = togglePassword.querySelector("i");
            togglePassword.addEventListener("click", function() {

                // Muestra la contraseña
                if (passwordInput.type === "password") {
                    passwordInput.type = "text";
                    icon.classList.remove("bx-show");
                    icon.classList.add("bx-hide");
                } else {
                    // Oculta la contraseña
                    passwordInput.type = "password";
                    icon.classList.remove("bx-hide");
                    icon.classList.add("bx-show");
                }
            });
        });
    </script>
</body>
</html>
