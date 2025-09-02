<?php
session_start();
include("conexion.php");

if (!isset($_SESSION["usuario"])) {
    header("Location: IniciarSesion.php");
    exit();
}

// Verifica si se recibió correctamente por POST
if (!isset($_POST["num_solicitud"])) {
    die("No se ha proporcionado un número de solicitud válido.");
}

$num_solicitud = $_POST["num_solicitud"];

// Validar que sea numérico
if (!is_numeric($num_solicitud)) {
    die("El número de solicitud no es válido.");
}

// Resto de tu lógica para registrar el personal
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Asignar valores numéricos a los roles
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
            // Separar el rol del nombre
            $partes = explode(" - ", $seleccion);
            if (count($partes) < 2) {
                $errores[] = "Formato incorrecto para la selección: " . $seleccion;
                continue;
            }

            $rol = trim($partes[0]);

            if (array_key_exists($rol, $roles)) {
                $rol_puesto = $roles[$rol];

                // Agregar num_solicitud al INSERT
                $stmt = $conn->prepare("INSERT INTO puesto (rol_puesto, num_solicitud) VALUES (?, ?)");
                $stmt->bind_param("ii", $rol_puesto, $num_solicitud);

                if (!$stmt->execute()) {
                    $errores[] = "Error al insertar el rol: " . $rol;
                    $registro_exitoso = false;
                }

                $stmt->close();
            } else {
                $errores[] = "Rol desconocido: " . $rol;
                $registro_exitoso = false;
            }
        }

        // Verificar si hubo errores
        if ($registro_exitoso && empty($errores)) {
            echo "<script>alert('Personal asignado con éxito'); window.location.href = 'EventosAceptados.php';</script>";
        } else {
            $mensaje_error = implode("\n", $errores);
            echo "<script>alert('Ocurrió un error al registrar el personal: " . $mensaje_error . "'); window.history.back();</script>";
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Personal</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');
        body {
           font-family: "Poppins", sans-serif;
            background: url('fondo admincontraseña.png') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
        }

        /* Barra de navegación */
        .navbar {
            background-color: #25344f;
            overflow: hidden;
            padding: 7px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
        }
        .navbar a:hover {
            background-color: darkblue;
        }

        .form-container {
            background-color: #fff;
            max-width: 600px;
            margin: auto;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        h2 {
            text-align: center;
            color: darkblue;
            font-size: 24px;
        }

        strong {
            color: darkblue;
        }

        .section {
            margin-bottom: 20px;
        }

        .section label {
            display: block;
            margin: 8px 0;
        }
    </style>
</head>
<body>
    <!-- Barra de navegación -->
    <div class="navbar">
        <div>
            <a href="MenuAdministrador.php">Inicio</a>
            <a href="EstatusEventosAdmin.php">Gestionar Eventos</a>
            <a href="AdminRestContraseña.php">Restablecer Contraseña</a>
        </div>
        <a href="CerrarSesion.php">Cerrar sesión</a>
    </div>

<div class="form-container" style="position: relative; padding-bottom: 80px;">
    <h2>Personal a Asignar</h2>
    <form action="GuardarPersonal.php" method="POST" onsubmit="return validarFormulario();">
    <input type="hidden" name="num_solicitud" value="<?= htmlspecialchars($num_solicitud) ?>">

        <div class="section"><strong>Fotografía:</strong>
        <label><input type="checkbox" name="personal[]" value="Fotografía - Juan Carlos Antonio Aviles" onclick="seleccionarSoloUno(this)"> Juan Carlos Antonio Aviles</label>
        <label><input type="checkbox" name="personal[]" value="Fotografía - Cutberto Escobedo Hernandez" onclick="seleccionarSoloUno(this)"> Cutberto Escobedo Hernandez</label>
    </div>

        <div class="section"><strong>Redes Sociales:</strong>
            <label><input type="checkbox" name="personal[]" value="Redes Sociales - Pablo Luis Davila Hernandez" onclick="seleccionarSoloUno(this)"> Pablo Luis Davila Hernandez</label>
            <label><input type="checkbox" name="personal[]" value="Redes Sociales - Sara Guadalupe Hernandez Roman" onclick="seleccionarSoloUno(this)"> Sara Guadalupe Hernandez Roman</label>
        </div>

        <div class="section"><strong>Sonido:</strong>
            <label><input type="checkbox" name="personal[]" value="Sonido - Esmeralda Chiariglione Bianchi" onclick="seleccionarSoloUno(this)"> Esmeralda Chiariglione Bianchi</label>
            <label><input type="checkbox" name="personal[]" value="Sonido - Bernardo Delgado Solis" onclick="seleccionarSoloUno(this)"> Bernardo Delgado Solis</label>
        </div>

        <div class="section"><strong>Maestr@ Ceremonia:</strong>
            <label><input type="checkbox" name="personal[]" value="Maestr@ Ceremonia - Nayeli Alejandra Espitia Hernandez" onclick="seleccionarSoloUno(this)"> Nayeli Alejandra Espitia Hernandez</label>
            <label><input type="checkbox" name="personal[]" value="Maestr@ Ceremonia - Maria Fernanda Escobedo Hernandez" onclick="seleccionarSoloUno(this)"> Maria Fernanda Escobedo Hernandez</label>
        </div>

        <div class="section"><strong>Diseñador@:</strong>
            <label><input type="checkbox" name="personal[]" value="Diseñador@ - Ruben Melendez Rodriguez" onclick="seleccionarSoloUno(this)"> Ruben Melendez Rodriguez</label>
            <label><input type="checkbox" name="personal[]" value="Diseñador@ - Alan Alejandro Davila Hernandez" onclick="seleccionarSoloUno(this)"> Alan Alejandro Davila Hernandez</label>
            <label><input type="checkbox" name="personal[]" value="Diseñador@ - Cesar Ivan García Meza" onclick="seleccionarSoloUno(this)"> Cesar Ivan García Meza</label>
        </div>

        <div class="section"><strong>Imprenta:</strong>
            <label><input type="checkbox" name="personal[]" value="Imprenta - Juan Manuel Sandoval" onclick="seleccionarSoloUno(this)"> Juan Manuel Sandoval</label>
            <label><input type="checkbox" name="personal[]" value="Imprenta - Rocío Sanchez Martinez" onclick="seleccionarSoloUno(this)"> Rocío Sanchez Martinez</label>
        </div>

        <div class="section"><strong>Redactor@:</strong>
            <label><input type="checkbox" name="personal[]" value="Redactor@ - Irais Sarahi Morales Vazquez" onclick="seleccionarSoloUno(this)"> Irais Sarahi Morales Vazquez</label>
            <label><input type="checkbox" name="personal[]" value="Redactor@ - Daniel Esquivel Torres" onclick="seleccionarSoloUno(this)"> Daniel Esquivel Torres</label>
        </div>

        <!-- Botones inferior -->
        <div style="position: absolute; bottom: 15px; left: 0; right: 0; display: flex; justify-content: space-between; padding: 0 25px;">
            <!-- Regresar -->
            <button type="button" onclick="window.location.href='EventosAceptados.php'" 
                style="padding: 10px 20px; background-color: #25344f; color: white; border: none; border-radius: 10px; cursor: pointer; font-size: 15px;">
                Regresar
            </button>

            <!-- Guardar -->
            <button type="submit" 
                style="padding: 10px 20px; background-color: #25344f; color: white; border: none; border-radius: 10px; cursor: pointer; font-size: 15px;">
                Guardar
            </button>
        </div>
    </form>
</div>

<script>
function seleccionarSoloUno(checkbox) {
    const seccion = checkbox.closest('.section'); // Encuentra la sección actual
    const checkboxes = seccion.querySelectorAll('input[type="checkbox"]'); // Solo los de esa sección

    checkboxes.forEach(cb => {
        if (cb !== checkbox) cb.checked = false; // Desmarca los otros de la misma sección
    });
}
</script>

<script>
function validarFormulario() {
    const checkboxes = document.querySelectorAll('input[type="checkbox"][name="personal[]"]');
    let alMenosUnoSeleccionado = false;

    checkboxes.forEach(cb => {
        if (cb.checked) alMenosUnoSeleccionado = true;
    });

    if (!alMenosUnoSeleccionado) {
        alert("Debes asignar al menos a una persona.");
        return false; // bloquea el envío
    }
}
</script>

</body>
</html>
