<?php
// Conectar a la base de datos de forma segura
require 'PHP/ServerConnect.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Favicon -->
    <link rel="icon" href="CSS/Images/favicon.ico" type="Image/x-icon">
    <link rel="shortcut icon" href="CSS/Images/favicon.ico" type="Image/x-icon">
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="CSS/styles.css">
    <!-- JS -->
    <script src="JS/Login.js"></script>
    <script src="JS/PasswordVisibility.js"></script>
    <script src="JS/TransformarDatos.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap -->
    <script src="Bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="Bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="Bootstrap/Icons/font/bootstrap-icons.css">
</head>
<body class="BodyNuevoFormulario">

<div class="LoginGlass">
    <form id="loginForm" method="post" onsubmit="return Logear(event)">
        <h2 class="text-primary text-center mb-4">INICIO DE SESIÓN</h2>
        <label for="username" class="text-primary fw-bold fs-4 mb-1">USUARIO:</label>
        <input type="text" class="form-control fs-4 mb-3" id="username" name="username" onchange="transformarDatosMayusculas('username')" required>

        <label for="password" class="text-primary fw-bold fs-4 mb-1">CONTRASEÑA:</label>
        <div class="password-container form-group">
        <input type="password" class="form-control fs-4" id="password" name="password" required>
        <img src="CSS/Images/Ocultar.png" onclick="togglePasswordVisibility('password', this)" class="toggle-password" alt="Mostrar/Ocultar">
        </div>

        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-primary btn-lg fs-1">Iniciar sesión</button>
        </div>
    </form>
</div>

</body>
</html>