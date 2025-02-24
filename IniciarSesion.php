<?php
session_start();
$conexion = new mysqli("localhost", "root", "", "unievents");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $num_control = $_POST["num_control"]; 
    $contraseña = $_POST["contraseña"];   

    $sql = "SELECT * FROM usuarios WHERE num_control = ? AND contraseña = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ss", $num_control, $contraseña);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        $_SESSION["usuario"] = $num_control;
        $rol = $fila['rol_usuario'];

        echo json_encode(["success" => true, "redirect" => 
            $rol == 1 ? "MenuAdministrador.php" : 
            ($rol == 2 ? "MenuSupervisor.php" : "MenuSolicitante.php")]);
    } else {
        echo json_encode(["success" => false, "message" => "⚠️ Datos incorrectos. Inténtalo de nuevo"]);
    }
    exit();
}
?>

<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión UniEvents+</title>
    <link rel="stylesheet" href="style.css">    
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="wrapper">
        <form id="formLogin" action="IniciarSesion.php" method="POST">
            <h1>Iniciar sesión</h1>

            <!-- Mensaje de error justo encima del botón -->
<div id="error-message" class="error-message" style="display: none;"></div>

            <!-- Numero de control (Solo números) -->
            <div class="input-box">
                <input type="text" id="num_control" name="num_control" inputmode="numeric" minlength="8" maxlength="8" placeholder="Número de control" required 
                    oninput="this.value = this.value.replace(/\D/g, '')">
                <span id="info-message" class="info-message">Inserta tus 8 caracteres numéricos</span>
                <span id="error-matricula" class="error-message"></span>
                <i class='bx bxs-user'></i>
            </div>

            <!-- Contraseña -->
            <div class="input-box">
                <input type="password" id="contraseña" name="contraseña" minlength="8" maxlength="16" placeholder="Contraseña" required>
                <button type="button" id="togglePassword" class="eye-button">
                    <i class='bx bx-show'></i>
                </button>
                <span id="error-password" class="error-message"></span>
                <i class='bx bxs-lock-alt'></i>
            </div>

            <!-- Botón de iniciar sesión -->
            <button type="submit" id="btnLogin" class="btn">Ingresar</button>

            <div class="registro-link">
                <p>¿Aún no tienes cuenta? <a href="Registro.php">Regístrate aquí</a></p>
            </div>
        </form>
    </div>

    <style>
        .error-message {
            color: red;
            font-size: 12px;
            padding: 5px 10px;
            margin-top: 5px;
            display: none; /* Oculto por defecto */
            background-color: #f8d7da;
            border-radius: 5px;
        }

        .info-message {
            color: darkblue;
            font-size: 12px;
            margin-top: 5px;
        }

        /* Asegura que el mensaje de error aparezca justo debajo del input */
        .input-box {
            position: relative;
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
        }

        .input-box input {
            width: 100%;
            padding-right: 40px;
        }

        /* Estilo para el botón de ver contraseña */
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
        // Validación al enviar el formulario (presionar Enter o clic en "Ingresar")
document.getElementById("formLogin").addEventListener("submit", function(event) {
    event.preventDefault(); // Evita la recarga de la página

    let formData = new FormData(this);
    let errorMessage = document.getElementById("error-message");

    fetch("IniciarSesion.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect; // Redirecciona si es correcto
        } else {
            errorMessage.textContent = data.message;
            errorMessage.style.display = "block"; // Muestra el mensaje de error
        }
    })
    .catch(error => {
        console.error("Error en la petición:", error);
    });
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

<script>
    //Siempre inicia con IniciarSesion
    document.addEventListener("DOMContentLoaded", function() {
        if (window.location.pathname !== "/UniEvents/IniciarSesion.php") {
            window.location.href = "/UniEvents/IniciarSesion.php";
        }
    });
</script>
</body>
</html>