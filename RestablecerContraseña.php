<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Menú Solicitante</title>
     <link rel="stylesheet" href="style.css"> 
</head>
<body>
    <div class="wrapper">
        <form id="formLogin" action="IniciarSesion.php" method="POST">
            <h1>¿Olvidaste tu contraseña?</h1>
            <p>¡La recuperarás aquí!</p>

            <style>
          .wrapper p {
	      font-size: 16px;
	      text-align: center;
	      color: darkblue;
          }
            </style>

            <!-- Correo Institucional y su condiciones -->
            <div class="input-box">
                <input type="text" id="correo_ins" name="correo_ins" maxlength="27" placeholder="Correo Institucional">
                <span class="error-message" id="error-correo"></span>
                <i class="bx bxs-envelope"></i>
            </div>

            <!-- Botón de enviar correo institucional -->
            <button type="submit" id="btnEnviar" class="btn">Enviar</button>

            <!-- Dirigir a IniciarSesion.php por medio del link -->
            <div class="registro-link">
                <p><a href="IniciarSesion.php">Regresar a Inicio de Sesión</a></p>
            </div>
        </form>
    </div>
</body>
</html>
