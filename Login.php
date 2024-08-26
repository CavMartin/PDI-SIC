<?php
// Conectar a la base de datos de forma segura
require 'PHP/ServerConnect.php';

// Verifica si el usuario ha iniciado sesión
if (isset($_SESSION['loggedin']) == true) {
    // El usuario no ha iniciado sesión, redirigirlo a la página de inicio de sesión
    header("Location: index.php");
    exit();
}
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
  <link rel="stylesheet" type="text/css" href="CSS/Login.css">
  <!-- JS -->
  <script src="Usuarios/JS/login.js"></script>
  <script src="JS/TransformarDatos.js"></script>
  <script src="Usuarios/JS/passwordVisibility.js"></script>
  <!-- jQuery -->
  <script src="Resources/JQuery/jquery-3.7.1.min.js"></script>
  <!-- SweetAlert -->
  <script src="Resources/SweetAlert2/sweetalert2.min.js"></script>
  <link rel="stylesheet" href="Resources/SweetAlert2/sweetalert2.min.css">
  <!-- Bootstrap -->
  <script src="Resources/Bootstrap/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="Resources/Bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="Resources/Bootstrap/Icons/font/bootstrap-icons.css">
</head>
<body class="bg-secondary" style="background-image: url('CSS/Images/MainBG.webp'); background-size: cover; background-attachment: fixed;">

<!-- Barra de navegación -->
<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <!-- Bloque del título con imágenes, centrado -->
        <div class="d-flex justify-content-center align-items-center flex-grow-1">
            <img src="CSS/Images/PSF.png" class="mx-3" alt="Icono" style="width: 4rem;"><!-- Icono Izquierdo -->
            <h2 class="text-light text-center m-0">SISTEMA DE INVESTIGACIÓN CRIMINAL</h2>
            <img src="CSS/Images/LOGO.png" class="mx-3" alt="Icono" style="width: 4rem;"><!-- Icono Derecho -->
        </div>
    </div>
</nav>

<div class="LoginGlass">
    <form id="loginForm" class="text-center">
        <label for="username" class="text-light fw-bold fs-4 mb-1">USUARIO:</label>
        <input type="text" class="form-control fw-bold text-center fs-4 mb-3" id="username" name="username" autocomplete="username" onchange="transformarDatosMayusculas('username')" required>

        <label for="password" class="text-light fw-bold fs-4 mb-1">CONTRASEÑA:</label>
        <div class="password-container form-group">
        <input type="password" class="form-control text-center fs-4" id="password" name="password" autocomplete="current-password" required>
        <img src="CSS/Images/Ocultar.png" onclick="togglePasswordVisibility('password', this)" class="toggle-password" alt="Mostrar/Ocultar">
        </div>

        <div class="d-grid mt-4">
            <button type="button" id="loginBtn" class="btn btn-danger btn-lg fs-1">Iniciar sesión</button>
        </div>
    </form>
</div>

</body>
</html>
