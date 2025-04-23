<?php
session_start();
include("conexion.php");

if (!isset($_SESSION["usuario"])) {
    header("Location: IniciarSesion.php");
    exit();
}

$hoy = date('Y-m-d');

// Mostrar solo eventos aceptados, futuros, y que aún no tengan ficha técnica
$sql_eventos = "
    SELECT s.num_solicitud, s.nombre_evento, s.fecha_evento, s.hora_inicio
    FROM solicitud s
    WHERE s.evento_status = 'Aceptado' 
      AND s.fecha_evento > ?
      AND s.num_solicitud NOT IN (
          SELECT f.idEvento_status FROM ficha_tecnica_evento f
      )
";

$stmt = $conn->prepare($sql_eventos);
$stmt->bind_param("s", $hoy);
$stmt->execute();
$result_eventos = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ficha Técnica del Evento</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');
        body {
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
            background: url('fondo admincontraseña.png') no-repeat center center fixed;
            background-size: cover;
            color: white; /* Para que el texto sea legible sobre la imagen */
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
        .container {
            color: black;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            max-width: 800px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, .2);
        }
        h2 {
            text-align: center;
            color: darkblue;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
        }
        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #25344f;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: darkblue;
        }
        .contenedor-boton {
            text-align: right;
            margin-top: 10px;
        }
        .autoridad {
            margin-bottom: 20px;
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }
        .autoridad input {
            margin-right: 8px;
            margin-top: 5px;
        }
        .invitado-grupo {
        margin-bottom: 15px;
    }
    </style>
</head>
<body>

    <!-- Barra de navegación -->
    <!-- Barra de navegación -->
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

<div class="container">
    <h2>Ficha Técnica del Evento</h2>

    <form method="post" action="GuardarFichaTecnica.php" autocomplete="off">
        <label for="evento">Selecciona un evento aceptado:</label>
        <select name="evento" id="evento" required onchange="mostrarDetallesEvento(this)">
            <option value="">-- Selecciona --</option>
            <?php while($row = $result_eventos->fetch_assoc()): ?>
                <option value='<?= json_encode($row) ?>'><?= $row["nombre_evento"] ?> - <?= $row["fecha_evento"] ?></option>
            <?php endwhile; ?>
        </select>

        <div id="detalles-evento" style="display:none;">
            <label>Nombre del Evento:</label>
            <input type="text" name="nombre_evento" id="nombre_evento" readonly>

            <label>Fecha del Evento:</label>
            <input type="text" name="fecha_evento" id="fecha_evento" readonly>

            <label>Hora de Inicio:</label>
            <input type="text" name="hora_inicio" id="hora_inicio" readonly>

            <label for="atuendo">Atuendo:</label>
            <select name="atuendo" id="atuendo" required>
                <option value="Formal">Formal</option>
                <option value="Semi-Formal">Semi-Formal</option>
                <option value="Casual">Casual</option>
                <option value="Gala">Gala</option>
                <option value="Uniforme Escolar">Uniforme Escolar</option>
                <option value="Deportivo">Deportivo</option>
                <option value="Disfraz">Disfraz</option>
            </select>

           <label for="no_asistentes">Número de Asistentes:</label>
           <input type="number" name="no_asistentes" id="no_asistentes" min="1" max="999" required oninput="validarAsistentes(this)">

        <label for="autoridades">Autoridades:</label>
        <div id="autoridades-container">
            <div class="autoridad">
                <input type="text" name="autoridades[nombre][]" placeholder="Nombre">
                <input type="text" name="autoridades[apellido_paterno][]" placeholder="Apellido Paterno">
                <input type="text" name="autoridades[apellido_materno][]" placeholder="Apellido Materno">
                <input type="text" name="autoridades[cargo][]" placeholder="Cargo">
            </div>
        </div>

        <button type="button" onclick="agregarAutoridad()">+ Agregar otra autoridad</button>
        <br><br>

            <!-- Invitados -->
            <label for="invitados">Invitados:</label>
                <div id="invitados-container">
                    <div class="invitado-grupo">
                        <input type="text" name="invitados[nombre][]" placeholder="Nombre">
                        <input type="text" name="invitados[apellido_paterno][]" placeholder="Apellido Paterno">
                        <input type="text" name="invitados[apellido_materno][]" placeholder="Apellido Materno">
                    </div>
                </div>
                <br>
                <button type="button" onclick="agregarInvitado()">+ Agregar invitado</button>

            <div class="contenedor-boton">
            <button type="submit">Guardar Ficha Técnica</button>
             </div>
        </div>
    </form>
</div>

<script>
    function agregarInvitado() {
        const container = document.getElementById('invitados-container');
        const grupo = document.createElement('div');
        grupo.classList.add('invitado-grupo');

        grupo.innerHTML = `
            <input type="text" name="invitados[nombre][]" placeholder="Nombre" required>
            <input type="text" name="invitados[apellido_paterno][]" placeholder="Apellido Paterno" required>
            <input type="text" name="invitados[apellido_materno][]" placeholder="Apellido Materno" required>
        `;

        container.appendChild(grupo);
    }
</script>

<script>
let autoridadIndex = 1;

function agregarAutoridad() {
    const container = document.getElementById('autoridades-container');
    const nuevaAutoridad = document.createElement('div');
    nuevaAutoridad.classList.add('autoridad');

    nuevaAutoridad.innerHTML = `
        <input type="text" name="autoridades[${autoridadIndex}][nombre]" placeholder="Nombre" required>
        <input type="text" name="autoridades[${autoridadIndex}][apellido_paterno]" placeholder="Apellido Paterno" required>
        <input type="text" name="autoridades[${autoridadIndex}][apellido_materno]" placeholder="Apellido Materno" required>
        <input type="text" name="autoridades[${autoridadIndex}][cargo]" placeholder="Cargo" required>
    `;

    container.appendChild(nuevaAutoridad);
    autoridadIndex++;
}
</script>

<script>
function mostrarDetallesEvento(select) {
    const selected = select.value;
    if (!selected) return document.getElementById('detalles-evento').style.display = 'none';

    const datos = JSON.parse(selected);
    document.getElementById('nombre_evento').value = datos.nombre_evento;
    document.getElementById('fecha_evento').value = datos.fecha_evento;
    document.getElementById('hora_inicio').value = datos.hora_inicio;

    document.getElementById('detalles-evento').style.display = 'block';
}

function agregarCampo(containerId, inputName) {
    const container = document.getElementById(containerId);
    const input = document.createElement("input");
    input.type = "text";
    input.name = inputName;
    input.placeholder = inputName.includes("autoridades") ? "Nombre de la autoridad" : "Nombre del invitado";
    input.required = true;
    container.appendChild(document.createElement("br"));
    container.appendChild(input);
}
</script>

<script>
function validarAsistentes(input) {
    // Elimina caracteres no numéricos (aunque type="number" ya limita esto)
    input.value = input.value.replace(/\D/g, '');

    // Si el valor es 0, lo reemplaza por 1
    if (input.value === '0') {
        input.value = '1';
    }

    // Limita a 3 caracteres
    if (input.value.length > 3) {
        input.value = input.value.slice(0, 3);
    }
}
</script>
</body>
</html>

<?php $conn->close(); ?>
