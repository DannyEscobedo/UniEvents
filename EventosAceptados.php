<?php
session_start();
include("conexion.php");

if (!isset($_SESSION["usuario"])) {
    header("Location: IniciarSesion.php");
    exit();
}

// Consulta eventos aceptados cuya fecha aún no ha pasado
$hoy = date("Y-m-d");
$sql = "SELECT num_solicitud FROM estatus_evento WHERE estatus = 'aceptado'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Eventos Aceptados</title>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');
        body {
            font-family: "Poppins", sans-serif;
            background: url('fondo admincontraseña.png') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
        }

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

        h2 {
            text-align: center;
            color: darkblue;
            margin-top: 35px;
            font-size: 24px;
        }

        .tabla-eventos {
            width: 70%;
            margin: 30px auto;
            border-collapse: collapse;
            background: white;
        }

        .tabla-eventos th, .tabla-eventos td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }

        .tabla-eventos th {
            background-color: #25344f;
            color: white;
        }

        .btn-personal {
            background-color: #25344f;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-personal:hover {
            background-color: #39588f;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div>
        <a href="MenuAdministrador.php">Inicio</a>
        <a href="EstatusEventosAdmin.php">Estatus Eventos</a>
        <a href="AdminRestContraseña.php">Restablecer Contraseña</a>
    </div>
    <a href="CerrarSesion.php">Cerrar sesión</a>
</div>

<h2>Eventos Aceptados</h2>
<table class="tabla-eventos">
    <tr>
        <th>Número de solicitud</th>
        <th>Botón</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['num_solicitud'] ?></td>
            <td>
                <form action="RegistrarPersonal.php" method="post">
                    <input type="hidden" name="num_solicitud" value="<?= $row['num_solicitud'] ?>">
                    <button type="submit" class="btn-personal">Registrar Personal</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
</body>
</html>
