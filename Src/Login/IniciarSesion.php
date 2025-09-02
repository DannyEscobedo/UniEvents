<?php
session_start();

// Establece la conexión
$conexion = new mysqli("localhost", "root", "", "unievents");

// En caso de error en la conexion, muestra mensaje de error
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtiene los datos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $num_control = $_POST["num_control"]; 
    $contraseña = $_POST["contraseña"];   

    // Hace consulta a la base de datos
    $sql = "SELECT * FROM usuarios WHERE num_control = ? AND contraseña = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ss", $num_control, $contraseña);
    $stmt->execute();
    $resultado = $stmt->get_result();

       //Guarda al usuario y lo redirige a su menú (1 Admin, 2 Supervisor y 3 por default alumnos)
    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        $_SESSION["usuario"] = $num_control;
        $rol = $fila['rol_usuario'];

        echo json_encode(["success" => true, "redirect" => 
            $rol == 1 ? "MenuAdministrador.php" : 
            ($rol == 2 ? "MenuSupervisor.php" : "MenuSolicitante.php")]);
    } else {
        //en caso de tener error en alguna casilla, muestra mensaje de error
        echo json_encode(["success" => false, "message" => "⚠️ Datos incorrectos. Inténtalo de nuevo"]);
    }
    exit();
}
?>

<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
     <!-- Nombre de la ventana (parte del navegador) -->
    <title>Iniciar Sesión UniEvents+</title>
    <link rel="stylesheet" href="style.css">  
     <!-- Iconos de las casilas -->  
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="wrapper">
        <form id="formLogin" action="IniciarSesion.php" method="POST">
            <h1>Iniciar sesión</h1>

            <!-- Mensaje de error -->
            <div id="error-message" class="error-message" style="display: none;"></div>

            <!-- Número de control (Solo números) -->
            <div class="input-box">
                <input type="text" id="num_control" name="num_control" inputmode="numeric" minlength="8" maxlength="8" placeholder="Número de control" 
                    oninput="this.value = this.value.replace(/\D/g, ''); validarNumControl(this);">
                <span id="num_control_message" class="info-message">Inserta tus 8 caracteres numéricos</span>
                <span id="error-matricula" class="error-message"></span>
                <i class='bx bxs-user'></i>
            </div>

            <!-- Contraseña y su condiciones -->
            <div class="input-box">
                <input type="password" id="contraseña" name="contraseña" minlength="8" maxlength="16" placeholder="Contraseña">
                <button type="button" id="togglePassword" class="eye-button">
                    <i class='bx bx-show'></i>
                </button>
                <span id="error-password" class="error-message"></span>
                <i class='bx bxs-lock-alt'></i>
            </div>

            <!-- Botón de iniciar sesión -->
            <button type="submit" id="btnLogin" class="btn">Ingresar</button>
       
            <!-- Dirigir a RegistrarUsuario.php o RestablecerContraseña.php por medio de los links -->
            <div class="registro-link">
                <p>¿Aún no tienes cuenta? <a href="RegistrarUsuario.php">Regístrate aquí</a></p>
                <p>¿Olvidaste tu contraseña? <a href="RestablecerContraseña.php">Restablecer</a></p>
            </div>
        </form>
    </div>

    <style>
        /* Estilo de mensajes de error */
        .error-message {
            color: red;
            font-size: 12px;
            padding: 5px 10px;
            margin-top: 5px;
            display: none;
            background-color: #f8d7da;
            border-radius: 5px;
        }
        /* Letrero de 8 caracteres de su numero de control */
        .info-message {
            color: darkblue;
            font-size: 12px;
            margin-top: 5px;
        }
        /* Posicion de mensaje de error en las casillas */
        .input-box {
            position: relative;
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
        }
        /* Posicion de las casillas */
        .input-box input {
            width: 100%;
            padding-right: 40px;
        }
        /* Estilo para el botón de mirar/ocultar contraseña */
        .eye-button {
            background: none;
            border: none;
            cursor: pointer;
            position: absolute;
            right: 26px;
            top: 50%;
            transform: translateY(-50%);
        }
        /* Posición por encima de la casilla de numero de control */
        .input-box span#error-matricula {
            position: absolute;
            top: -35px;
            left: 0;
        } 
    </style>

    <script>
        // En caso de error, regresa a IniciarSesion.php
        document.getElementById("formLogin").addEventListener("submit", function(event) {
            event.preventDefault();

            let valid = true;

            // Validación de número de control
            let numControl = document.getElementById("num_control").value.trim();
            let numControlError = document.getElementById("error-matricula");
            
            // Muestra el mensaje de error en caso de estar vacío el campo
            if (numControl === "") {
                numControlError.textContent = "Completa este campo.";
                numControlError.style.display = "block"; 
                valid = false;
            } else {
                // Oculta el mensaje de error si no está vacío el campo
                numControlError.textContent = "";
                numControlError.style.display = "none";
            }

            // Validación de contraseña
            let contraseña = document.getElementById("contraseña").value.trim();
            let contraseñaError = document.getElementById("error-password");

            // Muestra el mensaje de error en caso de estar vacío el campo
            if (contraseña === "") {
                contraseñaError.textContent = "Completa este campo.";
                contraseñaError.style.display = "block"; 
                valid = false;
            } else {
                 // Oculta el mensaje de error si no está vacío el campo
                contraseñaError.textContent = "";
                contraseñaError.style.display = "none";
            }

            // Si no es válido, no se envían los datos
            if (!valid) {
                return;
            }

            // Si es válido, continúa el funcionamiento de las interfaces
            let formData = new FormData(this);
            let errorMessage = document.getElementById("error-message");

            fetch("IniciarSesion.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirecciona a los usuarios si el login es correcto
                    window.location.href = data.redirect; 
                } else {
                    errorMessage.textContent = data.message;
                    // Muestra un mensaje de error en general
                    errorMessage.style.display = "block"; 
                }
            })
            .catch(error => {
                console.error("Error en la petición:", error);
            });
        });
    </script>

    <script>
        //Funcion de contador para la casilla de numero de control
        function validarNumControl(input) {
            let longitud = input.value.length;
            let mensaje = `Inserta tus 8 caracteres numéricos`;

            if (longitud < 8) {
                input.setCustomValidity(`Alarga el número a 8 caracteres (actualmente usas ${longitud} caracteres).`);
            } else {
                input.setCustomValidity("");
            }
            document.getElementById("num_control_message").textContent = mensaje;
             // Muestra el mensaje en vivo
            input.reportValidity();
        }
    </script>

    <script>
        // Funcion del botón mirar/ocultar contraseña
        document.addEventListener("DOMContentLoaded", function() {
            const togglePassword = document.getElementById("togglePassword");
            const passwordInput = document.getElementById("contraseña");
            const icon = togglePassword.querySelector("i");
            
            // Muestra la contraseña
            togglePassword.addEventListener("click", function() {
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
