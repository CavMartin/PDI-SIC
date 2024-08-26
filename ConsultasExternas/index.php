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
  <!-- JavaScript -->
  <script src="JS/consultasExternas.js"></script>
  <!-- Bootstrap -->
  <script src="../Resources/Bootstrap/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="../Resources/Bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../Resources/Bootstrap/Icons/font/bootstrap-icons.css">
</head>
<body class="bg-secondary" style="background-image: url('../CSS/Images/MainBG.webp'); background-size: cover; background-attachment: fixed; overflow-x: hidden;">

<!-- Barra de navegación -->
<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
    <div class="container-fluid d-flex justify-content-center position-relative">

        <!-- Botón de volver a la izquierda con posición absoluta -->
        <div style="position: absolute; left: 0;">
            <button type="button" id="VolverButton" class="btn btn-warning btn-lg mx-3" onclick="window.location.href='../index.php'">
                <i class="bi bi-arrow-left-square-fill"></i> <b>VOLVER</b>
            </button>
        </div>

        <!-- Bloque del título con imágenes, centrado en la totalidad de la pantalla -->
        <div class="d-flex justify-content-center align-items-center">
            <img src="../CSS/Images/PSF.png" class="mx-3" alt="Icono" style="width: 4rem;"><!-- Icono Izquierdo -->
            <h2 class="text-light text-center m-0">SISTEMA DE INVESTIGACIÓN CRIMINAL - CONSULTAS EXTERNAS</h2>
            <img src="../CSS/Images/LOGO.png" class="mx-3" alt="Icono" style="width: 4rem;"><!-- Icono Derecho -->
        </div>
    </div>
</nav>

<div style="margin-top: 7rem;">
    <div class="row justify-content-center">

        <div class="col-md-4">
          <div class="Card" id="PDI" style="background: rgb(244,242,229); border: 0.3rem solid #99372A; box-shadow: 0 7px 50px 10px #99372A;">
              <div class='main'>
                <h3 class="text-center" style="color: #99372A">PDI - OTROS PARTES</h3>
                <img src="../CSS/Images/LOGO.png" alt="PDI">
              </div>
            </div>
        </div>

        <div class="col-md-4">
          <div class="Card" id="AUOP" style="background: rgb(244,242,229); border: 0.3rem solid #205281; box-shadow: 0 7px 50px 10px #205281;">
              <div class='main'>
                <h3 class="text-center" style="color: #205281">URI - A.U.O.P</h3>
                <img src="../CSS/Images/AUOP.png" alt="AUOP">
              </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="Card" id="PVE" style="background: rgb(244,242,229); border: 0.3rem solid #008A00; box-shadow: 0 7px 50px 10px #008A00;">
              <div class='main'>
                <h3 class="text-center" style="color: #008A00">PVE - 911</h3>
                <img src="../CSS/Images/Lugar.png" alt="PVE">
              </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>