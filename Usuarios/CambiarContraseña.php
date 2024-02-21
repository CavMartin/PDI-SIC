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
  <title>Cambiar contraseña</title>
  <link rel="stylesheet" type="text/css" href="../CSS/styles.css">
  <link rel="icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
  <link rel="shortcut icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
  <!-- JS Core -->
  <script src="JS/CambiarContraseña.js"></script>
  <script src="../JS/PasswordVisibility.js"></script>
  <!-- SweetAlert -->
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Bootstrap -->
  <script src="../Bootstrap/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="../Bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../Bootstrap/Icons/font/bootstrap-icons.css">
</head>
<body class="bg-secondary">

<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark" style="height: 5vw;">
    <div class="container-fluid d-flex align-items-center justify-content-center">
        <!-- Imagen 1 -->
        <div>
            <img src="../CSS/Images/PSF.png" alt="Icono" style="width: 4vw; margin: 1vw;"><!-- Icono -->
        </div>

        <!-- Título centrado -->
        <div class="text-center">
            <h1 class="text-light">FORMULARIO DE CAMBIO DE CONTRASEÑA</h1>
        </div>

        <!-- Imagen 2 -->
        <div>
            <img src="../CSS/Images/OJO.png" alt="Icono" style="width: 4vw; margin: 1vw;"><!-- Icono -->
        </div>

        <!-- Botón de navegación -->
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <button type="button" class="btn btn-primary btn-lg" style="position: fixed; top: 0; left: 0; width: 12vw; height: 4vw; font-size: 1.5vw; margin: 0.5vw;" onclick="window.location.href='Main.php'">
                <i class="bi bi-arrow-left-square-fill"></i> <b>VOLVER</b>
            </button>
        </div>
    </div>
</nav>

<div class="container mt-6" style="margin-top: 7vw;">
    <div class="row justify-content-center">
        <div class="col-md-8 bg-white p-4 border border-black rounded">
          <form id="changePasswordForm" method="post" onsubmit="return CambiarContraseña()">        
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
              <button class="btn btn-primary fs-2" type="submit">Cambiar contraseña</button>
            </div>
        </form>
      </div>
    </div>
</div>

</body>
</html>
