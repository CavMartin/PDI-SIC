<?php
// Conectar a la base de datos de forma segura
require '../PHP/ServerConnect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // El usuario no ha iniciado sesión, redirigirlo a la página de inicio de sesión
    header("Location: ../Login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Consultas - Main</title>
  <!-- Navicon -->
  <link rel="icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
  <link rel="shortcut icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
  <!-- CSS -->
  <link rel="stylesheet" type="text/css" href="../CSS/Webkit.css">
  <link rel="stylesheet" type="text/css" href="../CSS/Card.scss">
  <!-- Bootstrap -->
  <script src="../Bootstrap/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="../Bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../Bootstrap/Icons/font/bootstrap-icons.css">
</head>
<body class="bg-secondary" style="overflow-x: hidden;">

<!-- Barra de navegación -->
<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark" style="height: 5vw;">
    <div class="container-fluid d-flex align-items-center justify-content-center">
        
        <!-- Imagen 1 -->
        <div>
            <img src="../CSS/Images/PSF.png" alt="Icono" style="width: 4vw; margin: 1vw;"><!-- Icono -->
        </div>

        <!-- Título centrado -->
        <div class="text-center">
            <h1 class="text-warning">SISTEMA DE CONSULTAS</h1>
        </div>

        <!-- Imagen 2 -->
        <div>
            <img src="../CSS/Images/OJO.png" alt="Icono" style="width: 4vw; margin: 1vw;"><!-- Icono -->
        </div>

        <!-- Botón de navegación a la página principal -->
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <button type="button" id="VolverButton" class="btn btn-warning btn-lg" style="position: fixed; top: 0; left: 0; width: 12vw; height:4vw; font-size: 1.5vw; margin: 0.5vw;" onclick="window.location.href='../Main.php'">
                <i class="bi bi-arrow-left-square-fill"></i> <b>VOLVER</b>
            </button>
        </div>
    </div>
</nav>


<div class="Border">
    <div class="row">

        <div class="col-md-3" id="BandejaDeEntrada">
            <div class="Card" >
              <div class='main'>
                <h2 class="text-center">BANDEJA DE ENTRADA</h2>
                <img src="../CSS/Images/Bandeja.png" alt="Bandeja">
              </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="Card">
              <div class='main'>
                <h2 class="text-center">Consulta por persona</h2>
                <img src="../CSS/Images/Proximamente.png" alt="Proximamente">
              </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="Card">
              <div class='main'>
                <h2 class="text-center">Consulta por lugar</h2>
                <img src="../CSS/Images/Proximamente.png" alt="Proximamente">
              </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="Card">
              <div class='main'>
                <h2 class="text-center">Consulta por vehículo</h2>
                <img src="../CSS/Images/Proximamente.png" alt="Proximamente">
              </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="Card">
              <div class='main'>
                <h2 class="text-center">Consulta por mensaje</h2>
                <img src="../CSS/Images/Proximamente.png" alt="Proximamente">
              </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="Card">
              <div class='main'>
                <h2 class="text-center">Consulta por arma</h2>
                <img src="../CSS/Images/Proximamente.png" alt="Proximamente">
              </div>
            </div>
        </div>

    </div>
</div>


<script>
    // Esperar a que el DOM se cargue completamente
    document.addEventListener('DOMContentLoaded', function () {
        // Seleccionar el elemento por su ID
        var bandejaDeEntrada = document.getElementById('BandejaDeEntrada');

        // Agregar un evento de clic al elemento
        bandejaDeEntrada.addEventListener('click', function () {
            // Redirigir a la nueva página
            window.location.href = 'BandejaDeEntrada.php';
        });
    });
</script>

</body>
</html>