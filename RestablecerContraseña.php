<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
     <!-- Nombre de la ventana (parte del navegador) -->
    <title>Restablecer</title>
    <link rel="stylesheet" href="style.css">  
     <!-- Iconos de las casilas -->  
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="wrapper">
        <form id="formLogin" action="IniciarSesion.php" method="POST">
            <h1>Restablecer Contraseña</h1>

            <p> Manda tu número de control al correo del administrador para solicitar un cambio en tu contraseña.</p>

            <div class="admin-link">
                <p>Correo del Contacto: <a href="https://mail.google.com/mail/u/0/#inbox?compose=new">pruebaunievents@gmail.com</a></p>
            </div>
       
            <!-- Dirigir a IniciarSesion.php por medio del link -->
            <div class="registro-link">
                <p>¿Quieres regresar? <a href="IniciarSesion.php">Regresar a Iniciar Sesión</a></p>
            </div>
        </form>
    </div>

    <style>
        p {
            color: black;
            text-align: center;
            margin-top: 15px;
        }

        h1 {
            line-height: 50px;
        }

        .registro-link p {
            font-weight: bold;
        }

        .admin-link p {
            font-weight: bold;
            color: darkblue;
        }
    </style>
</body>
</html>
