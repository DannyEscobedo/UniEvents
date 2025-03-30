<?php
session_start();

// Verifica si la sesión está activa
if (!isset($_SESSION["usuario"])) {
    header("Location: IniciarSesion.php");
    exit();
}

include("conexion.php");

$mensaje = "";
$errorNumControl = "";
$errorContraseña = "";
$num_control = "";
$nueva_contraseña = "";

// Procesar el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $num_control = trim($_POST["num_control"]);
    $nueva_contraseña = isset($_POST["nueva_contraseña"]) ? trim($_POST["nueva_contraseña"]) : "";

    if (empty($num_control)) {
        $errorNumControl = "Completa este campo.";
    } elseif (!preg_match("/^\d{1,8}$/", $num_control)) {
        $errorNumControl = "Debe contener solo números y máximo 8 dígitos.";
    } else {
        // Verificar si el número de control existe
        $sql = "SELECT * FROM usuarios WHERE num_control = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $num_control);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows == 0) {
            $errorNumControl = "El número de control no está registrado.";
        }
        $stmt->close();
    }

    // Validaciones específicas de la contraseña
    if (empty($nueva_contraseña)) {
        $errorContraseña = "Completa este campo.";
    } else {
        $errores = [];

        if (strlen($nueva_contraseña) < 8) {
            $errores[] = "Debe tener al menos 8 caracteres.";
        }
        if (!preg_match("/[A-Z]/", $nueva_contraseña)) {
            $errores[] = "Debe incluir al menos una letra mayúscula.";
        }
        if (!preg_match("/[a-z]/", $nueva_contraseña)) {
            $errores[] = "Debe incluir al menos una letra minúscula.";
        }
        if (!preg_match("/\d/", $nueva_contraseña)) {
            $errores[] = "Debe incluir al menos un número.";
        }
        if (!preg_match("/[!@#$%^&*(),.?\":{}|<>]/", $nueva_contraseña)) {
            $errores[] = "Debe incluir al menos un símbolo especial (!@#$%^&*)";
        }

        $contraseñasComunes = ["123456", "password", "qwerty", "admin123", "abcdef", "12345678", "111111"];
        if (in_array(strtolower($nueva_contraseña), $contraseñasComunes)) {
            $errores[] = "No uses una contraseña común.";
        }

        if (!empty($errores)) {
            $errorContraseña = implode("<br>", $errores);
        }
    }

    // Si no hay errores, actualizar la contraseña
    if (empty($errorNumControl) && empty($errorContraseña)) {
        // No usar password_hash(), solo guardar la contraseña tal cual
        $sql_update = "UPDATE usuarios SET contraseña = ? WHERE num_control = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ss", $nueva_contraseña, $num_control);  // Actualiza solo la contraseña

        if ($stmt_update->execute()) {
            $mensaje = "Contraseña actualizada correctamente.";
            $num_control = "";  // Limpiar el campo de número de control
            $nueva_contraseña = "";  // Limpiar el campo de la nueva contraseña
        } else {
            $errorContraseña = "Error al actualizar la contraseña.";
        }
        $stmt_update->close();  // Cerrar la sentencia
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer Contraseña</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');
        body {
            font-family: "Poppins", sans-serif;
            background: url('fondo admincontraseña.png') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;    
        }
        .container {
            background: white;
            padding: 90px;
            border-radius: 12px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.15);
            text-align: center;
            width: 400px;
        }
        h2 {
            color: darkblue;
            margin-top: -20px;
            font-size: 28px;
        }
        /* Barra de navegación */
        .navbar {
            background-color: #25344f;
            overflow: hidden;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%; /* Ocupar todo el ancho */
            position: fixed; /* Mantener fija en la parte superior */
            top: 0;
            left: 0;
            z-index: 1000; /* Asegura que esté por encima de otros elementos */
        }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
        }
        .navbar a:hover {
            background-color: darkblue;
        }
        .input-container {
            position: relative;
            margin-bottom: 20px;
            left: 0px;
        }
        .input-container input {
            width: 100%;
            padding: 15px;
            padding-left: 40px; /* Espacio para el ícono */
            border: 1px solid #ccc;
            border-radius: 10px;
            font-size: 16.5px;
            line-height: 1.5; /* Ajusta la altura del texto */
        }
        .input-container i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 22px;
            color: #555;
        }
        .eye-button {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 22px;
            background: none;
            border: none;
            cursor: pointer;
            color: #555;
        }
        .btn {
            background-color: #001F87;
            color: white;
            padding: 15px;
            width: 100%;
            border: none;
            border-radius: 10px;
            font-size: 16.5px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
        }
        .btn:hover {
            background-color: #001060;
        }
        .error-message {
            color: red;
            font-size: 14px;
            text-align: left;
            margin-top: 5px;
            min-height: 18px; /* Reserva espacio incluso si no hay error */
            display: block;
        }
        .success-message {
            color: green;
            font-size: 16px;
            text-align: center;
        }
    </style>
    <script>
        function togglePassword() {
            let passwordField = document.getElementById("nueva_contraseña");
            let toggleIcon = document.getElementById("toggle-password-icon");

            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleIcon.classList.remove("bx-show");
                toggleIcon.classList.add("bx-hide");
            } else {
                passwordField.type = "password";
                toggleIcon.classList.remove("bx-hide");
                toggleIcon.classList.add("bx-show");
            }
        }
    </script>
</head>
<body>

    <!-- Barra de navegación -->
    <div class="navbar">
        <div>
            <a href="MenuAdministrador.php">Inicio</a>
            <a href="CalendarioDisponibilidad.php">Calendario de Disponibilidad</a>
            <a href="AdminRestContraseña.php">Restablecer Contraseña</a>
        </div>
        <a href="CerrarSesion.php">Cerrar sesión</a>
    </div>

    <div class="container">
    <h2>Restablecer Contraseña</h2>

        <form method="POST" action="">
          <div class="input-container">
        <input type="text" name="num_control" placeholder="Número de control" value="<?php echo htmlspecialchars($num_control); ?>" maxlength="8" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
        <?php if (!empty($errorNumControl)): ?>
            <p class="error-message"><?php echo $errorNumControl; ?></p>
        <?php endif; ?>
        <i class='bx bxs-user'></i>
        </div>

            <div class="input-container">
                <input type="password" id="nueva_contraseña" name="nueva_contraseña" placeholder="Nueva contraseña">
                <button type="button" class="eye-button" onclick="togglePassword()">
                    <i class='bx bx-show' id="toggle-password-icon"></i>
                </button>
                <?php if (!empty($errorContraseña)): ?>
                    <p class="error-message"><?php echo $errorContraseña; ?></p>
                <?php endif; ?>
                <i class='bx bxs-lock'></i>
            </div>

            <?php if (!empty($mensaje)): ?>
                <p class="success-message"><?php echo $mensaje; ?></p>
            <?php endif; ?>

            <button type="submit" class="btn">Actualizar Contraseña</button>
        </form>
    </div>
</body>
</html>
