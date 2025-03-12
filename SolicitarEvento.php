<?php
session_start();  // Inicia la sesión

// Verifica si la sesión está activa
if (!isset($_SESSION["usuario"])) {
    header("Location: IniciarSesion.php"); // Redirige al login si no hay sesión
    exit();
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
            <a href="SubirFlyer.php">Subir Flyer</a>
            <a href="Perfil.php">Perfil</a>
        </div>
        <a href="CerrarSesion.php">Cerrar sesión</a>
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
</head>
<body>

    <div class="container">
        <h2>Solicitud de Servicios</h2>
        <h2>Al Departamento de Comunicación y Difusión</h2>

        <form id="solicitudForm">
            <label>Fecha de Elaboración:</label>
            <input type="text" id="fecha_elaboracion" readonly>

            <label>Departamento Solicitante:</label>
            <input type="text" name="depto_solicitante" maxlength="45" required>

            <label>Nombre del Evento:</label>
            <input type="text" name="nombre_evento" maxlength="15" required>

            <label>Fecha del Evento:</label>
            <input type="date" name="fecha_evento" required>

            <div class="checkbox-group">
                <div>
                    <label>Hora de Inicio:</label>
                    <select name="hora_inicio" required>
                        <option value="08:00">08:00 AM</option>
                        <option value="09:00">09:00 AM</option>
                        <option value="10:00">10:00 AM</option>
                    </select>
                </div>
                <div>
                    <label>Hora de Fin:</label>
                    <select name="hora_fin" required>
                        <option value="12:00">12:00 PM</option>
                        <option value="13:00">01:00 PM</option>
                        <option value="14:00">02:00 PM</option>
                    </select>
                </div>
            </div>

            <label>Lugar:</label>
            <select name="lugar_evento" required>
                <option value="Auditorio">Auditorio</option>
                <option value="Sala de Juntas">Sala de Juntas</option>
            </select>

            <label>Equipo de Audio:</label>
            <div class="checkbox-group">
                <input type="radio" name="equipo_audio" value="Equipo Audio"> Instalación Equipo de Audio
                <input type="radio" name="equipo_audio" value="Microfono Alámbrico"> Micrófono Alámbrico
                <input type="radio" name="equipo_audio" value="Microfono Inalámbrico"> Micrófono Inalámbrico
            </div>

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
                <input type="radio" name="difusion_externa" value="Presna Escrita"> Prensa Escrita
                <input type="radio" name="difusion_externa" value="TV"> TV
            </div>

            <label>Fecha de Publicación:</label>
            <select name="fecha_publicacion">
                <option value="08:00">08:00 AM</option>
                <option value="10:00">10:00 AM</option>
            </select>

            <label>Fecha de término de Publicación:</label>
            <select name="fecha_terminopublicacion">
                <option value="08:00">08:00 AM</option>
                <option value="10:00">10:00 AM</option>
            </select>

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
                <input type="checkbox" name="impresion" value="Diploma"> Diploma
                <input type="checkbox" name="impresion" value="Banner"> Banner digital plauditorio
            </div>

            <label>Impresión/Copias:</label>
            <div class="checkbox-group">
                <input type="number" id="num_copias" name="num_copias" min="0" max="100" value="0" onchange="toggleDisplay(this.value)">
            </div>


            <label>Toma de Fotografías:</label>
            <div class="checkbox-group">
                <input type="radio" name="display" value="Si" onclick="toggleDisplay(true)"> Sí
                <input type="radio" name="display" value="No" onclick="toggleDisplay(false)"> No
            </div>

            <label>Maestra/o de Ceremonias:</label>
            <div class="checkbox-group">
                <input type="radio" name="display" value="Si" onclick="toggleDisplay(true)"> Sí
                <input type="radio" name="display" value="No" onclick="toggleDisplay(false)"> No
            </div>

            <label>Display:</label>
            <div class="checkbox-group">
                <input type="radio" name="display" value="Si" onclick="toggleDisplay(true)"> Sí
                <input type="radio" name="display" value="No" onclick="toggleDisplay(false)"> No
            </div>

            <label>Texto para Display:</label>
            <input type="text" id="texto_display" name="texto_display" disabled>

            <button type="submit">Enviar</button>
        </form>
    </div>

    <script>
        document.getElementById('fecha_elaboracion').value = new Date().toLocaleDateString('es-ES');

        function toggleDisplay(estado) {
            document.getElementById('texto_display').disabled = !estado;
        }
    </script>
</body>
</html>
