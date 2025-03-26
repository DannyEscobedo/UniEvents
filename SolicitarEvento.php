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
    $diseno = $_POST["diseno"] ?? "No";
    $impresion = $_POST["impresion"] ?? "No";
    $num_copias = isset($_POST["num_copias"]) && is_numeric($_POST["num_copias"]) ? (int)$_POST["num_copias"] : 0;
    
    // Corregido: Siempre garantizar que el valor sea "Si" o "No"
    $toma_fotografias = (isset($_POST["toma_fotografias"]) && $_POST["toma_fotografias"] === "sí") ? 1 : 0;
    $maestro_ceremonia = (isset($_POST["maestro_ceremonia"]) && $_POST["maestro_ceremonia"] === "sí") ? 1 : 0;
    $display = (isset($_POST["display"]) && $_POST["display"] === "Si") ? 1 : 0;

    $texto_display = $_POST["texto_display"] ?? null;
    $num_control = $_SESSION["usuario"]; // Asegurar que la variable tiene un valor antes de la consulta
    $evento_status = "Pendiente";

    $errores = [];
    // Validar fecha inicio difusion con fecha del evento
    if ($difusion_fecha_inicio <= $fecha_evento) {
        $errores[] = "El inicio de la difusión no puede ser primero o igual que la fecha del evento";
    }
    // Validar fecha término difusion con fecha del evento
    if ($difusion_fecha_termino <= $fecha_evento) {
        $errores[] = "El final de la difusión no puede ser primero que la fecha del evento";
    }
    // Validar rango de horas
    if ($hora_inicio >= $hora_fin) {
        $errores[] = "La hora de inicio debe ser menor que la hora de fin.";
    }
    
    // Validar rango de fechas de difusión
    if ($difusion_fecha_inicio > $difusion_fecha_termino) {
        $errores[] = "La fecha de inicio de difusión no puede ser mayor que la fecha de término.";
    }
    
    // Validar número de copias si opción imprimir está activada
    if ($impresion === "si" && (!is_numeric($num_copias) || $num_copias <= 0)) {
        $errores[] = "El número de copias debe ser un número válido mayor que 0.";
    }
    
    // Validar texto display si opción display está activada
    if ($display === "si" && empty($texto_display)) {
        $errores[] = "Debe ingresar un texto para el display si está habilitado.";
    }
    
    // Si hay errores, mostrar mensaje
    if (!empty($errores)) {
        // Mostrar los errores de manera ordenada
        $errores_html = "<ul>";
        foreach ($errores as $error) {
            $errores_html .= "<li>" . htmlspecialchars($error) . "</li>";
        }
        $errores_html .= "</ul>";

        // Mostramos los errores con estilo en la página
        echo "
        <style>
            .error-message {
                background-color: #f8d7da;
                border-color: #f5c6cb;
                color: #721c24;
                padding: 10px;
                border-radius: 5px;
                font-family: Arial, sans-serif;
                font-size: 16px;
                margin-bottom: 20px;
            }
            .error-message ul {
                margin: 0;
                padding-left: 20px;
            }
            .error-message li {
                list-style-type: square;
            }
        </style>
        <div class='error-message'>
            <h3>Se han encontrado los siguientes errores:</h3>
            $errores_html
        </div>";
        exit;
    }

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

            <label>Departamento Solicitante (Campo Obligatorio):</label>
            <select name="depto_solicitante" required>
                <option value="Depto. Ciencias Básicas">Depto. Ciencias Básicas</option>
                <option value="Depto. Ing. Industrial">Depto. Ing. Industrial</option>
                <option value="Depto. Económico Admin.">Depto. Económico Admin.</option>
                <option value="Depto. Mantenimiento de Equipo">Depto. Mantenimiento de Equipo</option>
                <option value="Depto. Centro de Información">Depto. Centro de Información</option>
                <option value="Depto. Servicios Escolares">Depto. Servicios Escolares</option>
                <option value="Depto. Planeación">Depto. Planeación</option>
                <option value="Depto. Recursos Humanos">Depto. Recursos Humanos</option>
                <option value="Depto. de Calidad">Depto. de Calidad</option>
                <option value="Depto. Metal Metalurgia">Depto. Metal Metalurgia</option>
                <option value="Depto. Recursos materiales y servicios">Depto. Recursos materiales y servicios.</option>
                <option value="Depto. Centro de Idiomas">Depto. Centro de Idiomas</option>
                <option value="Depto. Eléctrica electrónica">Depto. Eléctrica electrónica</option>
                <option value="Depto. Ing. Sistemas">Depto. Ing. Sistemas</option>
                <option value="Depto. Ing Mecatrónica">Depto. Ing Mecatrónica</option>
                <option value="Depto. Vinculacion">Depto. Vinculacion</option>
                <option value="Depto. Educación a Distancia">Depto. Educación a Distancia</option>
                <option value="Depto. Subdirección Admin">Depto. Subdirección Admin</option>
                <option value="Depto. Subdirección Planeación">Depto. Subdirección Planeación</option>
                <option value="Depto. Subdirección Académica">Depto. Subdirección Académica</option>
            </select>

            <label>Nombre del Evento (Campo Obligatorio):</label>
            <input type="text" name="nombre_evento" minlength="2" maxlength="25" required>

            <label>Fecha del Evento (Campo Obligatorio):</label>
            <input type="date" id="fecha_evento" name="fecha_evento" required>

            <div class="checkbox-group">
                <div>
                <label>Hora de Inicio (Campo Obligatorio):</label>
                <select name="hora_inicio" required>
                    <option value="08:00 AM">08:00 AM</option>
                    <option value="08:30 AM">08:30 AM</option>
                    <option value="09:00 AM">09:00 AM</option>
                    <option value="09:30 AM">09:30 AM</option>
                    <option value="10:00 AM">10:00 AM</option>
                    <option value="10:30 AM">10:30 AM</option>
                    <option value="11:00 AM">11:00 AM</option>
                    <option value="11:30 AM">11:30 AM</option>
                    <option value="12:00 PM">12:00 PM</option>
                </select>
            </div>
            <div>
                <label>Hora de Fin (Campo Obligatorio):</label>
                <select name="hora_fin" required>
                    <option value="08:30 AM">08:30 AM</option>
                    <option value="09:00 AM">09:00 AM</option>
                    <option value="09:30 AM">09:30 AM</option>
                    <option value="10:00 AM">10:00 AM</option>
                    <option value="10:30 AM">10:30 AM</option>
                    <option value="11:00 AM">11:00 AM</option>
                    <option value="11:30 AM">11:30 AM</option>
                    <option value="12:00 PM">12:00 PM</option>
                    <option value="12:30 PM">12:30 PM</option>
                </select>
            </div>
            </div>

            <label>Lugar (Campo Obligatorio):</label>
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

            <label>Fecha de Publicación (Campo Obligatorio):</label>
            <input type="date" id="difusion_fecha_inicio" name="difusion_fecha_inicio" required>

            <label>Fecha de término de Publicación (Campo Obligatorio):</label>
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
                <input type="checkbox" id="diploma" name="impresion" value="Diploma" onchange="validarImpresion(this)"> Diploma
                <input type="checkbox" id="banner" name="impresion" value="Banner" onchange="validarImpresion(this)"> Banner digital
            </div>
            <p>*Los diseños que no sean producidos en comunicación y difusión, deberán ser avalados por ese departamento, con el fin de que se ajusten a los lineamientos de identidad gráfica institucional, y para solicitar la producción de un diseño en este departamento se considerar 5 días hábiles de antelación a su posterior reproducción y/o difusión.</p>

            <label>Impresión/Copias:</label>
            <div class="checkbox-group">
                <input type="number" id="num_copias" name="num_copias" min="1" max="5000" disabled oninput="validarLongitud(this)" maxlength="4">
            </div>
            <p>*Anexar por CORREO ELECTRÓNICO los respectivos nombres de quienes recibirán reconocimiento.</p>

            <label>Toma de Fotografías (Campo Obligatorio):</label>
            <div class="checkbox-group">
               <input type="radio" name="toma_fotografias" value="sí" required> Sí
               <input type="radio" name="toma_fotografias" value="no" required> No
            </div>

            <label>Maestro de Ceremonia (Campo Obligatorio):</label>
            <div class="checkbox-group">
                <input type="radio" name="maestro_ceremonia" value="sí" required> Sí
                <input type="radio" name="maestro_ceremonia" value="no" required> No
            </div>

        <form>
        <label>Display (Campo Obligatorio):</label>
        <div class="checkbox-group">
            <input type="checkbox" name="display" id="display_si" onclick="toggleDisplay('si')"> Sí
            <input type="checkbox" name="display" id="display_no" onclick="toggleDisplay('no')"> No
        </div>

        <label>Texto para Display:</label>
        <input type="text" id="texto_display" name="texto_display" disabled>

        <button type="submit">Enviar</button>
    </form>

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
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('input[type="radio"]').forEach(function (radio) {
        radio.addEventListener("click", function () {
            if (this.checked) {
                this.wasChecked = !this.wasChecked;
            }
            if (!this.wasChecked) {
                this.checked = false;
            }
        });
    });
});
</script>

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
    function validarImpresion(clickedCheckbox) {
        let checkboxes = document.querySelectorAll('input[name="impresion"]');
        let numCopias = document.getElementById("num_copias");

        // Desmarca el otro checkbox si uno es seleccionado
        checkboxes.forEach(cb => {
            if (cb !== clickedCheckbox) {
                cb.checked = false;
            }
        });

        // Si se selecciona "Diploma", se habilita el campo de copias
        if (document.getElementById("diploma").checked) {
            numCopias.disabled = false;
        } else {
            numCopias.disabled = true;
            numCopias.value = ""; // 🔥 Limpia el campo si no es "Diploma"
        }
    }

    function validarLongitud(input) {
        input.value = input.value.replace(/^0+/, '');

        if (input.value === '') {
            input.value = 1;
        }

        if (input.value.length > 4) {
            input.value = input.value.slice(0, 4);
        }
    }
</script>

<script>
    // Esta función verifica si alguno de los checkboxes está marcado
function validateDisplay() {
    if (!document.getElementById('display_si').checked && !document.getElementById('display_no').checked) {
        alert("El campo Display es obligatorio. Debe seleccionar 'Sí' o 'No'.");
        return false;
    }
    return true;
}

// Llamar a esta función cuando se envíe el formulario
document.querySelector("form").onsubmit = function () {
    return validateDisplay();
};
</script>

<script>
    function toggleDisplay(opcion) {
        let textoDisplay = document.getElementById('texto_display');
        let checkboxSi = document.getElementById('display_si');
        let checkboxNo = document.getElementById('display_no');
        
        if (opcion === 'si') {
            textoDisplay.disabled = false; // Habilitar campo de texto
            checkboxNo.checked = false; // Desmarcar "No"
        } 
        
        if (opcion === 'no') {
            textoDisplay.disabled = true; // Deshabilitar campo de texto
            textoDisplay.value = ""; // Limpiar campo de texto
            checkboxSi.checked = false; // Desmarcar "Sí"
        }

        // Limpiar campo si "Sí" es desmarcado
        if (!checkboxSi.checked && !checkboxNo.checked) {
            textoDisplay.value = ""; // Limpiar el campo de texto si no se selecciona ninguna opción
            textoDisplay.disabled = true; // Bloquear campo de texto
        }
    }

    // Validar que no se envíe el formulario si el campo de texto está vacío cuando "Sí" está seleccionado
    document.querySelector('form').addEventListener('submit', function(event) {
        let textoDisplay = document.getElementById('texto_display');
        let checkboxSi = document.getElementById('display_si');

        if (checkboxSi.checked && textoDisplay.value.trim() === '') {
            alert('Por favor, ingrese un texto para el display');
            event.preventDefault(); // Evita que el formulario se envíe
        }
    });
</script>
</body>
</html>
