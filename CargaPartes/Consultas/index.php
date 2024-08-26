<?php
    // Conectar a la base de datos de forma segura
    require '../../PHP/ServerConnect.php';

    // Verificar estado del login
    checkLoginState();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Consultas - Main</title>
  <!-- Navicon -->
  <link rel="icon" href="../../CSS/Images/favicon.ico" type="Image/x-icon">
  <link rel="shortcut icon" href="../../CSS/Images/favicon.ico" type="Image/x-icon">
  <!-- CSS -->
  <link rel="stylesheet" type="text/css" href="../../CSS/Webkit.css">
  <link rel="stylesheet" type="text/css" href="../../CSS/Card.scss">
  <!-- JavaScript -->
  <script src="JS/consultasSIC.js"></script>
  <!-- Bootstrap -->
  <script src="../../Resources/Bootstrap/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="../../Resources/Bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../Resources/Bootstrap/Icons/font/bootstrap-icons.css">
</head>
<body class="bg-secondary" style="overflow-x: hidden;">

<!-- Barra de navegación -->
<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
    <div class="container-fluid d-flex justify-content-center position-relative">

        <!-- Botón de volver a la izquierda con posición absoluta -->
        <div style="position: absolute; left: 0;">
            <button type="button" class="btn btn-warning btn-lg mx-3" onclick="window.location.href='../index.php'">
                <i class="bi bi-arrow-left-square-fill"></i> <b>VOLVER</b>
            </button>
        </div>

        <!-- Bloque del título con imágenes, centrado -->
        <div class="d-flex justify-content-center align-items-center flex-grow-1">
            <img src="../../CSS/Images/PSF.png" class="mx-3" alt="Icono" style="width: 4rem;"><!-- Icono Izquierdo -->
            <h2 class="text-light text-center m-0">SISTEMA DE INVESTIGACIÓN CRIMINAL - CONSULTAS</h2>
            <img src="../../CSS/Images/LOGO.png" class="mx-3" alt="Icono" style="width: 4rem;"><!-- Icono Derecho -->
        </div>
    </div>
</nav>

<div style="margin-top: 7rem;">
    <div class="row justify-content-center">

        <div class="col-md-4">
            <div class="Card" id="Encabezado">
              <div class='main'>
                <h3 class="text-center">ENCABEZADO</h3>
                <img src="../../CSS/Images/evento.png" alt="Encabezado">
              </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="Card" id="Personas">
              <div class='main'>
                <h3 class="text-center">PERSONAS</h3>
                <img src="../../CSS/Images/Personas.png" alt="Personas">
              </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="Card" id="Lugares">
              <div class='main'>
                <h3 class="text-center">LUGARES</h3>
                <img src="../../CSS/Images/Lugar.png" alt="Proximamente">
              </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="Card" id="Vehiculos">
              <div class='main'>
                <h3 class="text-center">VEHÍCULOS</h3>
                <img src="../../CSS/Images/Vehiculos.png" alt="Proximamente">
              </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="Card" id="Armas">
              <div class='main'>
                <h3 class="text-center">ARMAS</h3>
                <img src="../../CSS/Images/Armas.png" alt="Proximamente">
              </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="Card" id="Secuestros">
              <div class='main'>
                <h3 class="text-center">SECUESTROS</h3>
                <img src="../../CSS/Images/Ampliacion.png" alt="Secuestros">
              </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>