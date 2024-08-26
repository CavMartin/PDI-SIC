<?php
// Conectar a la base de datos de forma segura
require '../PHP/ServerConnect.php';

// Verificar estado del login
checkLoginState();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cambiar contraseña</title>
  <link rel="stylesheet" type="text/css" href="../CSS/styles.css">
  <link rel="icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
  <link rel="shortcut icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
  <!-- JS Core -->
  <script src="JS/changePassword.js"></script>
  <script src="JS/passwordVisibility.js"></script>
  <!-- SweetAlert -->
  <script src="../Resources/SweetAlert2/sweetalert2.min.js"></script>
  <link rel="stylesheet" href="../Resources/SweetAlert2/sweetalert2.min.css">
  <!-- Bootstrap -->
  <script src="../Resources/Bootstrap/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="../Resources/Bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../Resources/Bootstrap/Icons/font/bootstrap-icons.css">
</head>
<body class="bg-secondary">

<!-- Barra de navegación -->
<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
    <div class="container-fluid d-flex justify-content-center position-relative">

        <!-- Botón de volver a la izquierda con posición absoluta -->
        <div style="position: absolute; left: 0;">
            <button type="button" class="btn btn-warning btn-lg mx-3" onclick="window.location.href='index.php'">
                <i class="bi bi-arrow-left-square-fill"></i> <b>VOLVER</b>
            </button>
        </div>

        <!-- Bloque del título con imágenes, centrado en la totalidad de la pantalla -->
        <div class="d-flex justify-content-center align-items-center">
            <img src="../CSS/Images/PSF.png" class="mx-3" alt="Icono" style="width: 4rem;"><!-- Icono Izquierdo -->
            <h2 class="text-light text-center m-0">FORMULARIO CAMBIO DE CONTRASEÑA</h2>
            <img src="../CSS/Images/LOGO.png" class="mx-3" alt="Icono" style="width: 4rem;"><!-- Icono Derecho -->
        </div>
    </div>
</nav>

<div class="container mt-6" style="margin-top: 7rem;">
    <div class="row justify-content-center">
        <div class="col-md-8 bg-white p-4 border border-black rounded">
          <form id="changePasswordForm">        
            <!-- Campo de contraseña actual -->
            <label for="current_password" class="fs-4 fw-bold">Contraseña actual:</label>
            <div class="password-container form-group">
            <input type="password" class="form-control fs-4" id="current_password" name="current_password" style="text-align: center;" placeholder="Ingrese su contraseña actual" required>
            <img src="../CSS/Images/Ocultar.png" onclick="togglePasswordVisibility('current_password', this, '../')" class="toggle-password" alt="Mostrar/Ocultar">
            </div>

            <!-- Campo de nueva contraseña -->
            <label for="password" class="mt-3 fs-4 fw-bold">Nueva contraseña:</label>
            <div class="password-container form-group">
            <input type="password" class="form-control fs-4" id="password" name="password" style="text-align: center;" placeholder="Ingrese la nueva contraseña" required>
            <img src="../CSS/Images/Ocultar.png" onclick="togglePasswordVisibility('password', this, '../')" class="toggle-password" alt="Mostrar/Ocultar">
            </div>

            <!-- Campo de confirmar nueva contraseña -->
            <label for="confirm_password" class="mt-3 fs-4 fw-bold">Confirmar nueva contraseña:</label>
            <div class="password-container form-group">
            <input type="password" class="form-control fs-4" id="confirm_password" name="confirm_password" style="text-align: center;" placeholder="Ingrese nuevamente la nueva contraseña" required>
            <img src="../CSS/Images/Ocultar.png" onclick="togglePasswordVisibility('confirm_password', this, '../')" class="toggle-password" alt="Mostrar/Ocultar">
            </div>

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="d-grid mt-4">
              <button class="btn btn-primary fs-2" type="button" onclick="changePassword()">Cambiar contraseña</button>
            </div>
        </form>
      </div>
    </div>
</div>

</body>
</html>
