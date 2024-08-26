<?php
require 'PHP/ServerConnect.php';

// Verificar estado del login
checkLoginState();

// Obtener el nombre del usuario
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pagina principal</title>
  <!-- Navicon -->
  <link rel="icon" href="CSS/Images/favicon.ico" type="Image/x-icon">
  <link rel="shortcut icon" href="CSS/Images/favicon.ico" type="Image/x-icon">
  <!-- CSS -->
  <link rel="stylesheet" type="text/css" href="CSS/Webkit.css">
  <link rel="stylesheet" type="text/css" href="CSS/Card.scss">
  <!-- JS -->
  <script src="JS/index.js"></script>
  <script src="Usuarios/JS/logout.js"></script>
  <!-- jQuery -->
  <script src="Resources/JQuery/jquery-3.7.1.min.js"></script>
  <!-- SweetAlert -->
  <script src="Resources/SweetAlert2/sweetalert2.min.js"></script>
  <link rel="stylesheet" href="Resources/SweetAlert2/sweetalert2.min.css">
  <!-- Popper.js -->
  <script src="Resources/Popper/popper.min.js"></script>
  <!-- Bootstrap -->
  <script src="Resources/Bootstrap/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="Resources/Bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="Resources/Bootstrap/Icons/font/bootstrap-icons.css">
</head>
<body class="bg-secondary" style="background-image: url('CSS/Images/MainBG.webp'); background-size: cover; background-attachment: fixed; overflow-x: hidden;">

<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
    <div class="container-fluid d-flex justify-content-between align-items-center">

        <!-- Botón de grupo a la izquierda -->
        <div style="position: absolute; left: 0;">
            <button type="button" class="btn btn-outline-primary btn-lg mx-3 fs-4">
                <i class="bi bi-person-badge"></i><?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>
            </button>
        </div>

        <!-- Bloque del título con imágenes, centrado -->
        <div class="d-flex justify-content-center align-items-center flex-grow-1">
            <img src="CSS/Images/PSF.png" class="mx-3" alt="Icono" style="width: 4rem;"><!-- Icono Izquierdo -->
            <h2 class="text-light text-center m-0">SISTEMA DE INVESTIGACIÓN CRIMINAL - PANEL DE CONTROL</h2>
            <img src="CSS/Images/LOGO.png" class="mx-3" alt="Icono" style="width: 4rem;"><!-- Icono Derecho -->
        </div>

        <!-- Menú desplegable a la derecha -->
        <div style="position: absolute; right: 0;">
            <div class="collapse navbar-collapse" id="navbarNavDarkDropdown">
              <ul class="navbar-nav">
                <li class="nav-item dropdown">
                  <button class="btn btn-dark btn-lg dropdown-toggle mx-3" data-bs-toggle="dropdown" aria-expanded="false">
                  MENÚ DESPLEGABLE
                  </button>
                  <ul class="dropdown-menu dropdown-menu-dark">
                    <li><a class="dropdown-item fs-4" href="Usuarios/index.php">Panel de usuario</a></li>
                    <li><a class="dropdown-item text-danger fs-4" href="#" onclick="logout()">Cerrar sesión</a></li>
                    </ul>
                </li>
              </ul>
            </div>
        </div>
    </div>
</nav>

<div style="margin-top: 7rem;">
    <div class="row justify-content-center">

        <div class="col-md-3">
            <div class="Card" id="PARTES" style="border: 0.3rem solid #ffffff; box-shadow: 0 7px 50px 10px #ffffff;">
              <div class='main'>
                <h3 class="text-center" style="color: #ffffff;">CARGA DE PARTES</h3>
                <img src="CSS/Images/Reporte.png" alt="PARTES">
              </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="Card" id="PVE" style="border: 0.3rem solid #008A00; box-shadow: 0 7px 50px 10px #008A00;">
              <div class='main'>
                <h3 class="text-center" style="color: #008A00;">CARGA DE PVE</h3>
                <img src="CSS/Images/PVE.png" alt="PVE">
              </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="Card" style="background: rgb(41,45,46); border: 0.3rem solid #99372A; box-shadow: 0 7px 50px 10px #99372A;" id="CONSULTAS">
              <div class='main'>
                <h3 class="text-center" style="color: white" >CONSUTAS EXTERNAS</h3>
                <img src="CSS/Images/LOGO-SIC.png" alt="CONSULTAS">
              </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>

