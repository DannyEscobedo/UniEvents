<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Código</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="wrapper">
        <form id="formCodigo" method="POST">
            <h1>Verificación de Código</h1>
            <link rel="stylesheet" href="style.css">
            <p>Ingresa el código de 6 dígitos que hemos enviado a tu correo</p>
            
            <div class="input-box">
                <input type="text" id="codigo" name="codigo" maxlength="6" placeholder="Código de 6 dígitos" required>
                <span class="error-message" id="error-codigo"></span>
            </div>
            
            <p id="timer">Tiempo restante: 10:00</p>
            
            <button type="submit" id="btnVerificar" class="btn">Verificar Código</button>
        </form>
    </div>

    <script>
        let tiempoRestante = 600; // 10 minutos en segundos
        let timerInterval = setInterval(() => {
            let minutos = Math.floor(tiempoRestante / 60);
            let segundos = tiempoRestante % 60;
            
            document.getElementById("timer").textContent = `Tiempo restante: ${minutos}:${segundos < 10 ? '0' : ''}${segundos}`;
            
            if (tiempoRestante <= 0) {
                clearInterval(timerInterval);
                alert("El código ha expirado. Solicita uno nuevo.");
                window.location.href = "RestablecerContraseña.php";
            }
            tiempoRestante--;
        }, 1000);
    </script>
</body>
</html>
