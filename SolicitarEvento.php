<?php
session_start();  // Iniciar sesión

// Verifica si la sesión está activa
if (!isset($_SESSION["usuario"])) {
    header("Location: IniciarSesion.php"); // Redirige al login si no hay sesión
    exit();
}

// Configurar la zona horaria de México
date_default_timezone_set('America/Mexico_City');

$fecha_elaboracion = isset($_POST["fecha_elaboracion"]) && !empty($_POST["fecha_elaboracion"]) 
    ? $_POST["fecha_elaboracion"] 
    : date("Y-m-d");  // Si está vacío, usar la fecha actual del servidor

// Incluir el archivo de conexión a la base de datos
include("conexion.php");

// Verificar si el nombre del solicitante ya está en la sesión
if (!isset($_SESSION["nombre"])) {
    $num_control = $_SESSION["usuario"];  // Definir aquí antes de la consulta
    
    $sql_nombre = "SELECT nombres_usuarios, apellido_paterno, apellido_materno FROM usuarios WHERE num_control = ?";
    if ($stmt_nombre = $conn->prepare($sql_nombre)) {
        $stmt_nombre->bind_param("s", $num_control); // Pasar correctamente la variable aquí
        $stmt_nombre->execute();
        $stmt_nombre->bind_result($nombre, $apellido_paterno, $apellido_materno);

        if ($stmt_nombre->fetch()) {
            $_SESSION["nombre"] = trim("$nombre $apellido_paterno $apellido_materno");
        } else {
            $_SESSION["nombre"] = "Desconocido"; // Si no encuentra el usuario
        }

        $stmt_nombre->close();
    }
}

// Ahora $_SESSION["nombre"] ya tiene el nombre del usuario
$evento_solicitante_nombre = $_SESSION["nombre"];

// Verificar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ver los datos recibidos del formulario (para depuración)
    echo "<pre>";
    var_dump($_POST);
    echo "</pre>";

    // Recibir los datos del formulario
    $depto_solicitante = $_POST["depto_solicitante"] ?? "No especificado";
    $nombre_evento = isset($_POST["nombre_evento"]) ? trim($_POST["nombre_evento"]) : "Sin Nombre";
    $fecha_evento = $_POST["fecha_evento"];
    $hora_inicio = $_POST["hora_inicio"];
    $hora_fin = $_POST["hora_fin"];
    $lugar_evento = $_POST["lugar_evento"];
    $equipo_audio = $_POST["equipo_audio"] ?? "No";
    $difusion_interna = $_POST["difusion_interna"] ?? "No";
    $difusion_externa = $_POST["difusion_externa"] ?? "No";
    $difusion_fecha_inicio = $_POST["difusion_fecha_inicio"] ?? null;
    $difusion_fecha_termino = $_POST["difusion_fecha_termino"] ?? null;
    $diseno = $_POST["diseno"] ?? null;
    $impresion = $_POST["impresion"] ?? "No seleccionado";
    $num_copias = isset($_POST["num_copias"]) && is_numeric($_POST["num_copias"]) ? (int)$_POST["num_copias"] : 0;
    
    // Corregido: Siempre garantizar que el valor sea "Si" o "No"
   $toma_fotografias = (isset($_POST["toma_fotografias"]) && $_POST["toma_fotografias"] === "sí") ? "Si" : "No";
   $maestro_ceremonia = (isset($_POST["maestro_ceremonia"]) && $_POST["maestro_ceremonia"] === "sí") ? "Si" : "No";
   $display = (isset($_POST["display"]) && $_POST["display"] === "Si") ? "Si" : "No";

    $texto_display = $_POST["texto_display"] ?? null;
    $num_control = $_SESSION["usuario"]; // Asegurar que la variable tiene un valor antes de la consulta
    $evento_status = "Pendiente";

    // Nombre del solicitante
    $evento_solicitante_nombre = isset($_SESSION["nombre"]) ? trim($_SESSION["nombre"]) : "Desconocido";

    // Preparar la consulta SQL
    $sql = "INSERT INTO solicitud 
        (fecha_elaboracion, depto_solicitante_nombre, nombre_evento, fecha_evento, hora_inicio, hora_fin, lugar, equipo_audio, 
         difusion_interna, difusion_externa, difusion_fecha_inicio, difusion_fecha_termino, diseno, 
         impresion, num_copias, toma_fotografias, maestro_ceremonia, display, texto_display, num_control, evento_status, evento_solicitante_nombre) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssssssssssssssisisssss", 
            $fecha_elaboracion, $depto_solicitante, $nombre_evento, 
            $fecha_evento, $hora_inicio, $hora_fin, $lugar_evento, 
            $equipo_audio, $difusion_interna, $difusion_externa, 
            $difusion_fecha_inicio, $difusion_fecha_termino, $diseno, 
            $impresion, $num_copias, $toma_fotografias, 
            $maestro_ceremonia, $display, $texto_display, 
            $num_control, $evento_status, $evento_solicitante_nombre
        );

       if ($stmt->execute()) {
    $_SESSION["mensaje"] = "Solicitud enviada correctamente.";
} else {
    $_SESSION["mensaje"] = "Error al guardar la solicitud: " . $stmt->error;
}

$stmt->close();
$conn->close();

// Redirigir a MenuSolicitante.php después de procesar todo
header("Location: MenuSolicitante.php");
exit();
}
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Servicios</title>
     <div class="navbar">
        <div>
            <a href="MenuSolicitante.php">Inicio</a>
            <a href="SolicitarEvento.php">Solicitar Evento</a>
            <a href="EstatusEvento.php">Estatus del Evento</a>
            <a href="FichaTecnica.php">Ficha Técnica del Evento</a>
            <a href="SubirFlyer.php">Subir Flyer</a>
        </div>
        <a href="CerrarSesion.php">Cerrar sesión</a>
    </div>
    
</head>
<body>

    <div class="container">
        <h2>Solicitud de Servicios</h2>
        <h2>Al Departamento de Comunicación y Difusión</h2>

        <form id="solicitudForm" method="POST" action="SolicitarEvento.php">
            <label>Fecha de Elaboración:</label>
            <input type="date" name="fecha_elaboracion_visible" value="<?php echo date('Y-m-d'); ?>" disabled>
            <input type="hidden" name="fecha_elaboracion" value="<?php echo date('Y-m-d'); ?>">

            <label>Departamento Solicitante:</label>
            <select name="depto_solicitante" required>
                <option value="Depto. Sistemas Computacionales">Depto. Sistemas Computacionales</option>
                <option value="Depto. Industrial">Depto. Industrial</option>
                <option value="Depto. Gestión Empresarial">Depto. Gestión Empresarial</option>
                <option value="Depto. Mecánica">Depto. Mecánica</option>
                <option value="Depto. Materiales">Depto. Materiales</option>
                <option value="Depto. Mecatrónica">Depto. Mecatrónica</option>
            </select>

            <label>Nombre del Evento:</label>
            <input type="text" name="nombre_evento" maxlength="25" required>

            <label>Fecha del Evento:</label>
            <input type="date" id="fecha_evento" name="fecha_evento" required>

            <div class="checkbox-group">
                <div>
                    <label>Hora de Inicio:</label>
                    <select name="hora_inicio" required>
                        <option value="08:00">08:00 AM</option>
                        <option value="08:30">08:30 AM</option>
                        <option value="09:00">09:00 AM</option>
                        <option value="09:30">09:30 AM</option>
                        <option value="10:00">10:00 AM</option>
                        <option value="10:30">10:30 AM</option>
                        <option value="11:00">11:00 AM</option>
                        <option value="11:30">11:30 AM</option>
                        <option value="12:00">12:00 PM</option>
                        <option value="12:30">12:30 PM</option>
                        <option value="13:00">01:00 PM</option>
                        <option value="13:30">01:30 PM</option>
                    </select>
                </div>
                <div>
                    <label>Hora de Fin:</label>
                    <select name="hora_fin" required>
                        <option value="08:30">08:30 AM</option>
                        <option value="09:00">09:00 AM</option>
                        <option value="09:30">09:30 AM</option>
                        <option value="10:00">10:00 AM</option>
                        <option value="10:30">10:30 AM</option>
                        <option value="11:00">11:00 AM</option>
                        <option value="11:30">11:30 AM</option>
                        <option value="12:00">12:00 PM</option>
                        <option value="12:30">12:30 PM</option>
                        <option value="13:00">01:00 PM</option>
                        <option value="13:30">01:30 PM</option>
                        <option value="14:00">02:00 PM</option>
                    </select>
                </div>
            </div>

            <label>Lugar:</label>
            <select name="lugar_evento" required>
                <option value="Auditorio Segundo Rodriguez">Auditorio Ing. Segundo Rodriguez Alvarez</option>
                <option value="Auditorio Tecnológico">Auditorio Tecnológico</option>
                <option value="Auditorio Ing. Ricardo Peart">Auditorio Ing. Ricardo Peart</option>
                <option value="Auditorio Vinculación">Auditorio Vinculación</option>
                <option value="Plazoleta Media Luna">Plazoleta Media Luna</option>
                <option value="Plazoleta Techada">Plazoleta Techada</option>
                <option value="Canchas">Canchas</option>
                <option value="Estadio">Estadio</option>
                <option value="Alberca">Alberca</option>
                <option value="Gimnasio">Gimnasio</option>
                <option value="Jardines">Jardines</option>
            </select>
            <p>*SI EL EVENTO ES CANCELADO, FAVOR DE AVISAR A LA BREVEDAD POSIBLE</p>

            <label>Equipo de Audio:</label>
            <div class="checkbox-group">
                <input type="radio" name="equipo_audio" value="Instal. Equipo Audio"> Instalación Equipo de Audio
                <input type="radio" name="equipo_audio" value="Microfono Alámbrico"> Micrófono Alámbrico
                <input type="radio" name="equipo_audio" value="Microfono Inalámbrico"> Micrófono Inalámbrico
                <input type="radio" name="equipo_audio" value="Microfono Podium"> Micrófono para Podium
                <input type="radio" name="equipo_audio" value="Cable laptop"> Cable para audio laptop
                <input type="radio" name="equipo_audio" value="Micro Diadema"> Micro Diadema
                <input type="radio" name="equipo_audio" value="Micro Solapa"> Micro Solapa
            </div>
            <p>*NO SE CUENTA CON CABLES PARA MAC</p>

            <label>Difusión Interna:</label>
            <div class="checkbox-group">
                <input type="radio" name="difusion_interna" value="Volante"> Volante
                <input type="radio" name="difusion_interna" value="Oficios"> Oficios
                <input type="radio" name="difusion_interna" value="Correo Electrónico"> Correo Electrónico
            </div>

            <label>Difusión Externa:</label>
            <div class="checkbox-group">
                <input type="radio" name="difusion_externa" value="Pagina Web"> Página Web
                <input type="radio" name="difusion_externa" value="Redes Sociales"> Redes Sociales
                <input type="radio" name="difusion_externa" value="Radio"> Radio
                <input type="radio" name="difusion_externa" value="Prensa Escrita"> Prensa Escrita
                <input type="radio" name="difusion_externa" value="TV"> TV
            </div>

            <label>Fecha de Publicación:</label>
            <input type="date" id="difusion_fecha_inicio" name="difusion_fecha_inicio" required>

            <label>Fecha de término de Publicación:</label>
            <input type="date" id="difusion_fecha_termino" name="difusion_fecha_termino" required>

            <label>Diseño:</label>
            <div class="checkbox-group">
                <input type="radio" name="diseno" value="Poster"> Póster
                <input type="radio" name="diseno" value="Tríptico"> Tríptico
                <input type="radio" name="diseno" value="Folleto"> Folleto
                <input type="radio" name="diseno" value="Invitacion"> Invitación
                <input type="radio" name="diseno" value="Lona"> Lona
            </div>

            <label>Impresión y Otros:</label>
            <div class="checkbox-group">
                <input type="radio" id="diploma" name="impresion" value="Diploma" onchange="validarImpresion()" required> Diploma
                <input type="radio" id="banner" name="impresion" value="Banner" onchange="validarImpresion()" required> Banner digital
            </div>
            <p>*Los diseños que no sean producidos en comunicación y difusión, deberán ser avalados por ese departamento, con el fin de que se ajusten a los lineamientos de identidad gráfica institucional, y para solicitar la producción de un diseño en este departamento se considerar 5 días hábiles de antelación a su posterior reproducción y/o difusión.</p>

            <label>Impresión/Copias:</label>
            <div class="checkbox-group">
                <input type="number" id="num_copias" name="num_copias" min="1" max="5000" value="0" disabled oninput="validarLongitud(this)" maxlength="4">
            </div>
            <p>*Anexar por CORREO ELECTRÓNICO los respectivos nombres de quienes recibirán reconocimiento.</p>

            <label>Toma de Fotografías:</label>
            <div class="checkbox-group">
               <input type="radio" name="toma_fotografias" value="sí" required> Sí
               <input type="radio" name="toma_fotografias" value="no" required> No
            </div>

            <label>Maestro de Ceremonia:</label>
            <div class="checkbox-group">
                <input type="radio" name="maestro_ceremonia" value="sí" required> Sí
                <input type="radio" name="maestro_ceremonia" value="no" required> No
            </div>

            <label>Display:</label>
            <div class="checkbox-group">
               <input type="radio" name="display" value="Si" onclick="toggleDisplay()" required> Sí
               <input type="radio" name="display" value="No" onclick="toggleDisplay()" required> No
           </div>

            <label>Texto para Display:</label>
                <input type="text" id="texto_display" name="texto_display" disabled>

            <button type="submit">Enviar</button>
                </form>
            </div>

        <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fff7f6;
            color: black;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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

        .checkbox-group {
            display: flex;
            justify-content: space-between;
            margin-top: 5px;
        }

        /* Reducir el tamaño de todos los checkboxes y radios */
            input[type="checkbox"],
            input[type="radio"] {
            transform: scale(0.8); /* Ajusta el tamaño en un 80% del original */
            width: 15px; /* Tamaño fijo */
            height: 15px; /* Tamaño fijo */
            margin-right: 5px; /* Espacio entre el input y el texto */
        }

        h2 {
            text-align: center;
            line-height: 19px;
            color: darkblue;
        }

        p {
            text-align: justify;
            line-height: 17px;
            color: #1b5bbc;
            font-size: 14px;
            font-weight: bold;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            margin-top: 10px;
        }

        input, select {
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
        }

        .checkbox-group {
            display: flex;
            justify-content: space-between;
            margin-top: 5px;
            font-size: 14.5px;
            text-align: left;
        }

          .radio-group {
           display: flex;
           align-items: center;
           gap: 240px; /* Espacio entre los elementos */
        }

        button {
            background: #25344f;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }

        button:hover {
            background: darkblue;
        }
    </style>

        <script>
        document.addEventListener("DOMContentLoaded", function() {
            let hoy = new Date().toISOString().split("T")[0]; // Obtener la fecha actual en formato YYYY-MM-DD

            // Aplicar restricción de fecha mínima a los 3 campos
            document.getElementById("fecha_evento").setAttribute("min", hoy);
            document.getElementById("difusion_fecha_inicio").setAttribute("min", hoy);
            document.getElementById("difusion_fecha_termino").setAttribute("min", hoy);
        });
        </script>

       <script>
        document.getElementById('fecha_elaboracion').value = new Date().toLocaleDateString('es-ES');
        document.getElementById('solicitudForm').addEventListener('submit', function() {
        document.getElementById('fecha_elaboracion').disabled = false;
           });
       </script>

      <script>
        document.addEventListener("DOMContentLoaded", function() {
            validarImpresion(); // Llamar a la función cuando la página cargue
        });

        function validarImpresion() {
            let diploma = document.getElementById("diploma").checked;
            let banner = document.getElementById("banner").checked;
            let numCopias = document.getElementById("num_copias");

            if (diploma) {
                numCopias.disabled = false;
            } else {
                numCopias.disabled = true;
                numCopias.value = 0;
            }
        }

        function validarLongitud(input) {
            if (input.value.length > 4) {
                input.value = input.value.slice(0, 4); // Recortar a 4 dígitos si se pasa
            }
        }
        </script>

<script>
    function toggleDisplay() {
    let textoDisplay = document.getElementById('texto_display');
    let seleccion = document.querySelector('input[name="display"]:checked').value;
    
    if (seleccion === "Si") {
        textoDisplay.disabled = false; // Permitir escribir
    } else {
        textoDisplay.disabled = true; // Bloquear escritura
        textoDisplay.value = ""; // Limpiar campo si es "No"
    }
}
</script>
</body>
</html>
