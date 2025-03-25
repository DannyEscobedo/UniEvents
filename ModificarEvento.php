<?php
session_start();  

// Verificar sesión
if (!isset($_SESSION["usuario"])) {
    header("Location: IniciarSesion.php");
    exit();
}

date_default_timezone_set('America/Mexico_City');
include("conexion.php");

// Verificar si se recibió el número de solicitud
if (!isset($_POST["num_solicitud"])) {
    echo "No se recibió el número de solicitud.";
    exit();
}

$num_solicitud = $_POST["num_solicitud"];

// Obtener datos del evento
$sql = "SELECT * FROM solicitud WHERE num_solicitud = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $num_solicitud);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Evento no encontrado.";
    exit();
}

$evento = $result->fetch_assoc();
$hora_inicio = date("h:i A", strtotime($evento['hora_inicio']));
$hora_fin = date("h:i A", strtotime($evento['hora_fin']));

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Evento</title>
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
    <form action="GuardarModificacion.php" method="post">
        <input type="hidden" name="num_solicitud" value="<?php echo $evento['num_solicitud']; ?>">

        <div class="container">
        <h2>Modificar Evento</h2>
        <h2>Al Departamento de Comunicación y Difusión</h2>

        <form id="solicitudForm" method="POST" action="SolicitarEvento.php">
            <label>Fecha de Elaboración:</label>
            <input type="date" name="fecha_elaboracion_visible" value="<?php echo date('Y-m-d'); ?>" disabled>
            <input type="hidden" name="fecha_elaboracion" value="<?php echo date('Y-m-d'); ?>">

            <label>Departamento Solicitante:</label>
<select name="depto_solicitante" required>
     <option value="Depto. Ciencias Básicas" <?= ($evento['depto_solicitante_nombre'] ?? '') == 'Depto. Ciencias Básicas' ? 'selected' : ''; ?>>Depto. Ciencias Básicas</option>
    <option value="Depto. Ing. Industrial" <?= ($evento['depto_solicitante_nombre'] ?? '') == 'Depto. Ing. Industrial' ? 'selected' : ''; ?>>Depto. Ing. Industrial</option>
    <option value="Depto. Económico Admin." <?= ($evento['depto_solicitante_nombre'] ?? '') == 'Depto. Económico Admin.' ? 'selected' : ''; ?>>Depto. Económico Admin.</option>
    <option value="Depto. Mantenimiento de Equipo" <?= ($evento['depto_solicitante_nombre'] ?? '') == 'Depto. Mantenimiento de Equipo' ? 'selected' : ''; ?>>Depto. Mantenimiento de Equipo</option>
    <option value="Depto. Centro de Información" <?= ($evento['depto_solicitante_nombre'] ?? '') == 'Depto. Centro de Información' ? 'selected' : ''; ?>>Depto. Centro de Información</option>
    <option value="Depto. Servicios Escolares" <?= ($evento['depto_solicitante_nombre'] ?? '') == 'Depto. Servicios Escolares' ? 'selected' : ''; ?>>Depto. Servicios Escolares</option>
    <option value="Depto. Planeación" <?= ($evento['depto_solicitante_nombre'] ?? '') == 'Depto. Planeación' ? 'selected' : ''; ?>>Depto. Planeación</option>
    <option value="Depto. Recursos Humanos" <?= ($evento['depto_solicitante_nombre'] ?? '') == 'Depto. Recursos Humanos' ? 'selected' : ''; ?>>Depto. Recursos Humanos</option>
    <option value="Depto. de Calidad" <?= ($evento['depto_solicitante_nombre'] ?? '') == 'Depto. de Calidad' ? 'selected' : ''; ?>>Depto. de Calidad</option>
    <option value="Depto. Metal Metalurgia" <?= ($evento['depto_solicitante_nombre'] ?? '') == 'Depto. Metal Metalurgia' ? 'selected' : ''; ?>>Depto. Metal Metalurgia</option>
    <option value="Depto. Recursos materiales y servicios" <?= ($evento['depto_solicitante_nombre'] ?? '') == 'Depto. Recursos materiales y servicios' ? 'selected' : ''; ?>>Depto. Recursos materiales y servicios</option>
    <option value="Depto. Centro de Idiomas" <?= ($evento['depto_solicitante_nombre'] ?? '') == 'Depto. Centro de Idiomas' ? 'selected' : ''; ?>>Depto. Centro de Idiomas</option>
    <option value="Depto. Eléctrica electrónica" <?= ($evento['depto_solicitante_nombre'] ?? '') == 'Depto. Eléctrica electrónica' ? 'selected' : ''; ?>>Depto. Eléctrica Electrónica</option>
    <option value="Depto. Ing. Sistemas" <?= ($evento['depto_solicitante_nombre'] ?? '') == 'Depto. Ing. Sistemas' ? 'selected' : ''; ?>>Depto. Ing. Sistemas</option>
    <option value="Depto. Ing Mecatrónica" <?= ($evento['depto_solicitante_nombre'] ?? '') == 'Depto. Ing. Mecatrónica' ? 'selected' : ''; ?>>Depto. Ing. Mecatrónica</option>
    <option value="Depto. Vinculacion" <?= ($evento['depto_solicitante_nombre'] ?? '') == 'Depto. Vinculacion' ? 'selected' : ''; ?>>Depto. Vinculación</option>
    <option value="Depto. Educación a Distancia" <?= ($evento['depto_solicitante_nombre'] ?? '') == 'Depto. Educación a Distancia' ? 'selected' : ''; ?>>Depto. Educación a Distancia</option>
    <option value="Depto. Subdirección Admin" <?= ($evento['depto_solicitante_nombre'] ?? '') == 'Depto. Subdirección Admin' ? 'selected' : ''; ?>>Depto. Subdirección Admin</option>
    <option value="Depto. Subdirección Planeación" <?= ($evento['depto_solicitante_nombre'] ?? '') == 'Depto. Subdirección Planeación' ? 'selected' : ''; ?>>Depto. Subdirección Planeación</option>
    <option value="Depto. Subdirección Académica" <?= ($evento['depto_solicitante_nombre'] ?? '') == 'Depto. Subdirección Académica' ? 'selected' : ''; ?>>Depto. Subdirección Académica</option>
    </select>

            <label>Nombre del Evento:<label>
            <input type="text" name="nombre_evento" value="<?= $evento['nombre_evento'] ?? ''; ?>" maxlength="25" required>

            <label>Fecha del Evento:</label>
                <input type="date" id="fecha_evento" name="fecha_evento" value="<?= $evento['fecha_evento'] ?? ''; ?>" required>

            <div class="checkbox-group">
    <div>
        <label>Hora de Inicio (Campo Obligatorio):</label>
        <select name="hora_inicio" required>
            <option value="08:00 AM" <?= $hora_inicio == "08:00 AM" ? 'selected' : ''; ?>>08:00 AM</option>
            <option value="08:30 AM" <?= $hora_inicio == "08:30 AM" ? 'selected' : ''; ?>>08:30 AM</option>
            <option value="09:00 AM" <?= $hora_inicio == "09:00 AM" ? 'selected' : ''; ?>>09:00 AM</option>
            <option value="09:30 AM" <?= $hora_inicio == "09:30 AM" ? 'selected' : ''; ?>>09:30 AM</option>
            <option value="10:00 AM" <?= $hora_inicio == "10:00 AM" ? 'selected' : ''; ?>>10:00 AM</option>
            <option value="10:30 AM" <?= $hora_inicio == "10:30 AM" ? 'selected' : ''; ?>>10:30 AM</option>
            <option value="11:00 AM" <?= $hora_inicio == "11:00 AM" ? 'selected' : ''; ?>>11:00 AM</option>
            <option value="11:30 AM" <?= $hora_inicio == "11:30 AM" ? 'selected' : ''; ?>>11:30 AM</option>
            <option value="12:00 PM" <?= $hora_inicio == "12:00 PM" ? 'selected' : ''; ?>>12:00 PM</option>
            <option value="12:30 PM" <?= $hora_inicio == "12:30 PM" ? 'selected' : ''; ?>>12:30 PM</option>
            <option value="01:00 PM" <?= $hora_inicio == "01:00 PM" ? 'selected' : ''; ?>>01:00 PM</option>
            <option value="01:30 PM" <?= $hora_inicio == "01:30 PM" ? 'selected' : ''; ?>>01:30 PM</option>
        </select>
    </div>

    <div>
        <label>Hora de Fin (Campo Obligatorio):</label>
        <select name="hora_fin" required>
            <option value="08:30 AM" <?= $hora_fin == "08:30 AM" ? 'selected' : ''; ?>>08:30 AM</option>
            <option value="09:00 AM" <?= $hora_fin == "09:00 AM" ? 'selected' : ''; ?>>09:00 AM</option>
            <option value="09:30 AM" <?= $hora_fin == "09:30 AM" ? 'selected' : ''; ?>>09:30 AM</option>
            <option value="10:00 AM" <?= $hora_fin == "10:00 AM" ? 'selected' : ''; ?>>10:00 AM</option>
            <option value="10:30 AM" <?= $hora_fin == "10:30 AM" ? 'selected' : ''; ?>>10:30 AM</option>
            <option value="11:00 AM" <?= $hora_fin == "11:00 AM" ? 'selected' : ''; ?>>11:00 AM</option>
            <option value="11:30 AM" <?= $hora_fin == "11:30 AM" ? 'selected' : ''; ?>>11:30 AM</option>
            <option value="12:00 PM" <?= $hora_fin == "12:00 PM" ? 'selected' : ''; ?>>12:00 PM</option>
            <option value="12:30 PM" <?= $hora_fin == "12:30 PM" ? 'selected' : ''; ?>>12:30 PM</option>
            <option value="01:00 PM" <?= $hora_fin == "01:00 PM" ? 'selected' : ''; ?>>01:00 PM</option>
            <option value="01:30 PM" <?= $hora_fin == "01:30 PM" ? 'selected' : ''; ?>>01:30 PM</option>
            <option value="02:00 PM" <?= $hora_fin == "02:00 PM" ? 'selected' : ''; ?>>02:00 PM</option>
        </select>
    </div>
</div>

            <label>Lugar:</label>
            <select name="lugar_evento" required>
                <option value="Auditorio Segundo Rodriguez" <?= ($evento['lugar'] ?? '') == "Auditorio Segundo Rodriguez" ? 'selected' : ''; ?>>Auditorio Ing. Segundo Rodriguez Alvarez</option>
                <option value="Auditorio Tecnológico" <?= ($evento['lugar'] ?? '') == "Auditorio Tecnológico" ? 'selected' : ''; ?>>Auditorio Tecnológico</option>
                <option value="Auditorio Ing. Ricardo Peart" <?= ($evento['lugar'] ?? '') == "Auditorio Ing. Ricardo Peart" ? 'selected' : ''; ?>>Auditorio Ing. Ricardo Peart</option>
                <option value="Auditorio Vinculación" <?= ($evento['lugar'] ?? '') == "Auditorio Vinculación" ? 'selected' : ''; ?>>Auditorio Vinculación</option>
                <option value="Plazoleta Media Luna" <?= ($evento['lugar'] ?? '') == "Plazoleta Media Luna" ? 'selected' : ''; ?>>Plazoleta Media Luna</option>
                <option value="Plazoleta Techada" <?= ($evento['lugar'] ?? '') == "Plazoleta Techada" ? 'selected' : ''; ?>>Plazoleta Techada</option>
                <option value="Canchas" <?= ($evento['lugar'] ?? '') == "Canchas" ? 'selected' : ''; ?>>Canchas</option>
                <option value="Estadio" <?= ($evento['lugar'] ?? '') == "Estadio" ? 'selected' : ''; ?>>Estadio</option>
                <option value="Alberca" <?= ($evento['lugar'] ?? '') == "Alberca" ? 'selected' : ''; ?>>Alberca</option>
                <option value="Gimnasio" <?= ($evento['lugar'] ?? '') == "Gimnasio" ? 'selected' : ''; ?>>Gimnasio</option>
                <option value="Jardines" <?= ($evento['lugar'] ?? '') == "Jardines" ? 'selected' : ''; ?>>Jardines</option>
            </select>

            <label>Equipo de Audio:</label>
            <div class="checkbox-group">
                <input type="radio" name="equipo_audio" value="Instal. Equipo Audio" <?= ($evento['equipo_audio'] == "Instal. Equipo Audio") ? 'checked' : ''; ?>> Instalación Equipo de Audio
                <input type="radio" name="equipo_audio" value="Microfono Alámbrico" <?= ($evento['equipo_audio'] == "Microfono Alámbrico") ? 'checked' : ''; ?>> Micrófono Alámbrico
                <input type="radio" name="equipo_audio" value="Microfono Inalámbrico" <?= ($evento['equipo_audio'] == "Microfono Inalámbrico") ? 'checked' : ''; ?>> Microfono Inalámbrico
                <input type="radio" name="equipo_audio" value="Microfono Podium" <?= ($evento['equipo_audio'] == "Microfono Podium") ? 'checked' : ''; ?>> Microfono para Podium
                <input type="radio" name="equipo_audio" value="Cable laptop" <?= ($evento['equipo_audio'] == "Cable laptop") ? 'checked' : ''; ?>> Cable para audio laptop
                <input type="radio" name="equipo_audio" value="Micro Diadema" <?= ($evento['equipo_audio'] == "Micro Diadema") ? 'checked' : ''; ?>> Micro Diadema
                <input type="radio" name="equipo_audio" value="Micro Solapa" <?= ($evento['equipo_audio'] == "Micro Solapa") ? 'checked' : ''; ?>> Micro Solapa
            </div>
            <p>*NO SE CUENTA CON CABLES PARA MAC</p>

            <label>Difusión Interna:</label>
            <div class="checkbox-group">
                <input type="radio" name="difusion_interna" value="Volante" <?= ($evento['difusion_interna'] == "Volante") ? 'checked' : ''; ?>> Volante
                <input type="radio" name="difusion_interna" value="Oficios" <?= ($evento['difusion_interna'] == "Oficios") ? 'checked' : ''; ?>> Oficios
                <input type="radio" name="difusion_interna" value="Correo Electrónico" <?= ($evento['difusion_interna'] == "Correo Electrónico") ? 'checked' : ''; ?>> Correo Electrónico
            </div>

            <label>Difusión Externa:</label>
            <div class="checkbox-group">
                <input type="radio" name="difusion_externa" value="Pagina Web" <?= ($evento['difusion_externa'] == "Pagina Web") ? 'checked' : ''; ?>> Página Web
                <input type="radio" name="difusion_externa" value="Redes Sociales" <?= ($evento['difusion_externa'] == "Redes Sociales") ? 'checked' : ''; ?>> Redes Sociales
                <input type="radio" name="difusion_externa" value="Radio" <?= ($evento['difusion_externa'] == "Radio") ? 'checked' : ''; ?>> Radio
                <input type="radio" name="difusion_externa" value="Prensa Escrita" <?= ($evento['difusion_externa'] == "Prensa Escrita") ? 'checked' : ''; ?>> Prensa Escrita
                <input type="radio" name="difusion_externa" value="TV" <?= ($evento['difusion_externa'] == "TV") ? 'checked' : ''; ?>> TV
            </div>

            <label>Fecha de Publicación:</label>
                <input type="date" id="difusion_fecha_inicio" name="difusion_fecha_inicio" value="<?= $evento['difusion_fecha_inicio'] ?? ''; ?>" required>

                <label>Fecha de término de Publicación:</label>
                <input type="date" id="difusion_fecha_termino" name="difusion_fecha_termino" value="<?= $evento['difusion_fecha_termino'] ?? ''; ?>" required>

            <label>Diseño:</label>
            <div class="checkbox-group">
                 <input type="radio" name="diseno" value="Poster" <?= ($evento['diseno'] == 'Poster') ? 'checked' : ''; ?>> Póster
                 <input type="radio" name="diseno" value="Tríptico" <?= ($evento['diseno'] == 'Tríptico') ? 'checked' : ''; ?>> Tríptico
                 <input type="radio" name="diseno" value="Folleto" <?= ($evento['diseno'] == 'Folleto') ? 'checked' : ''; ?>> Folleto
                 <input type="radio" name="diseno" value="Invitacion" <?= ($evento['diseno'] == 'Invitacion') ? 'checked' : ''; ?>> Invitación
                 <input type="radio" name="diseno" value="Lona" <?= ($evento['diseno'] == 'Lona') ? 'checked' : ''; ?>> Lona
            </div>

            <label>Impresión y Otros:</label>
            <div class="checkbox-group">
                <input type="radio" id="diploma" name="impresion" value="Diploma" 
                onchange="validarImpresion()" <?= ($evento['impresion'] == 'Diploma') ? 'checked' : ''; ?>> Diploma
                <input type="radio" id="banner" name="impresion" value="Banner" 
                onchange="validarImpresion()" <?= ($evento['impresion'] == 'Banner') ? 'checked' : ''; ?>> Banner digital
            </div>

            <label>Impresión/Copias:</label>
            <div class="checkbox-group">
                <input type="number" id="num_copias" name="num_copias" min="1" max="5000" 
                value="<?= $evento['num_copias'] ?? 0; ?>" disabled oninput="validarLongitud(this)" maxlength="4">
            </div>
            <p>*Anexar por CORREO ELECTRÓNICO los respectivos nombres de quienes recibirán reconocimiento.</p>

            <label>Toma de Fotografías:</label>
            <div class="checkbox-group">
                <input type="radio" name="toma_fotografias" value="1" <?= ($evento['toma_fotografias'] == 1) ? 'checked' : ''; ?> required> Sí
                <input type="radio" name="toma_fotografias" value="0" <?= ($evento['toma_fotografias'] == 0) ? 'checked' : ''; ?> required> No
            </div>

            <label>Maestro/a de Ceremonias:</label>
            <div class="checkbox-group">
                <input type="radio" name="maestro_ceremonia" value="1" <?= ($evento['maestro_ceremonia'] == 1) ? 'checked' : ''; ?> required> Sí
                <input type="radio" name="maestro_ceremonia" value="0" <?= ($evento['maestro_ceremonia'] == 0) ? 'checked' : ''; ?> required> No
            </div>

            <label>Display:</label> 
            <div class="checkbox-group">
                <input type="radio" name="display" value="Si" onclick="toggleDisplay()" <?= ($evento['display'] ?? '') == 'Si' || ($evento['display'] ?? '') == '1' ? 'checked' : ''; ?> required> Sí
                <input type="radio" name="display" value="No" onclick="toggleDisplay()" <?= ($evento['display'] ?? '') == 'No' || ($evento['display'] ?? '') == '0' ? 'checked' : ''; ?> required> No
            </div>

            <label>Texto para Display:</label>
            <input type="text" id="texto_display" name="texto_display" value="<?= $evento['texto_display'] ?? ''; ?>">

            <button type="submit">Enviar modificación</button>
        </form>
    </div>

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
        let numCopias = document.getElementById("num_copias");

        if (diploma) {
            numCopias.disabled = false;
        } else {
            numCopias.disabled = true;
            numCopias.value = ''; // Se limpia el campo en lugar de poner 0
        }
    }

    function validarLongitud(input) {
        // Eliminar ceros iniciales
        input.value = input.value.replace(/^0+/, '');
        
        // Si el campo está vacío después de eliminar ceros, establecerlo en 0
        if (input.value === '') {
            input.value = 0;
        }

        // Limitar la longitud a 4 dígitos
        if (input.value.length > 4) {
            input.value = input.value.slice(0, 4);
        }
    }
</script>

        <script>
    document.addEventListener("DOMContentLoaded", function() {
        toggleDisplay(); // Aplicar restricciones al cargar la página
    });

    function toggleDisplay() {
        let textoDisplay = document.getElementById('texto_display');
        let seleccion = document.querySelector('input[name="display"]:checked').value;

        if (seleccion === "Si") {
            textoDisplay.disabled = false;
        } else {
            textoDisplay.disabled = true;
            textoDisplay.value = ""; // Limpiar campo si es "No"
        }
    }

    function validarFormulario() {
        let display = document.querySelector('input[name="display"]:checked').value;
        let textoDisplay = document.getElementById('texto_display').value.trim();

        if (display === "No" && textoDisplay !== "") {
            alert("No puedes ingresar texto en el Display si está marcado como 'No'.");
            return false; // Evita enviar el formulario
        }
        return true;
    }
</script>

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
            width: 100%; /* Hace que el botón ocupe todo el ancho */
            display: block; /* Asegura que ocupe toda la línea */
            text-align: center; /* Centra el texto dentro del botón */
        }

        button:hover {
            background: darkblue;
        }
    </style>
    </form>
</body>
</html>
