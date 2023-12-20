<?php
// Conectar a la base de datos de forma segura
require 'ServerConnect.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="CSS/styles.css">
    <link rel="icon" href="css/Images/favicon.ico" type="Image/x-icon">
    <link rel="shortcut icon" href="css/Images/favicon.ico" type="Image/x-icon">
    <script src="Scripts/Login.js"></script>
    <script src="Scripts/PasswordVisibility.js"></script>
    <script src="Scripts/TransformarDatos.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="BodyFondo1">

<div class="LoginForm">
    <form id="loginForm" method="post" onsubmit="return Logear(event)">
        <h2>INICIO DE SESIÓN</h2>
        <label for="username" style="text-align: center;">Nombre de usuario:</label>
        <input type="text" id="username" name="username" style="text-align: center;" onchange="transformarDatosMayusculas('username')" required>
        
        <label for="password" style="text-align: center;">Contraseña:</label>
        <div class="password-container">
        <input type="password" id="password" name="password" style="text-align: center;" required>
        <img src="css/Images/Ocultar.png" onclick="togglePasswordVisibility('password', this)" class="toggle-password" alt="Mostrar/Ocultar">
        </div>

        <button type="submit" class="CustomLargeButton" style="text-align: center; margin-top: 1vw;">Iniciar sesión</button>
    </form>
</div>

</body>
</html>