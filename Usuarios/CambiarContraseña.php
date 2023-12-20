<?php
// Conectar a la base de datos de forma segura
require '../ServerConnect.php';

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
  <link rel="stylesheet" type="text/css" href="../css/styles.css">
  <link rel="icon" href="../css/Images/favicon.ico" type="Image/x-icon">
  <link rel="shortcut icon" href="../css/Images/favicon.ico" type="Image/x-icon">
  <script src="Scripts/CambiarContraseña.js"></script>
  <script src="../Scripts/PasswordVisibility.js"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="BodyFondo1">

<div class="LoginForm">
<form id="changePasswordForm" method="post" onsubmit="return CambiarContraseña()">
        <h1>CAMBIAR CONTRASEÑA</h1>
        
        <!-- Campo de contraseña actual -->
        <label for="current_password" style="text-align: center;">Contraseña actual:</label>
        <div class="password-container">
        <input type="password" id="current_password" name="current_password" style="text-align: center;" placeholder="Contraseña actual" required>
        <img src="../css/Images/Ocultar.png" onclick="togglePasswordVisibility('current_password', this, '../')" class="toggle-password" alt="Mostrar/Ocultar">
        </div>

        <!-- Campo de nueva contraseña -->
        <label for="password" style="text-align: center;">Nueva contraseña:</label>
        <div class="password-container">
        <input type="password" id="password" name="password" style="text-align: center;" placeholder="Nueva contraseña" required>
        <img src="../css/Images/Ocultar.png" onclick="togglePasswordVisibility('password', this, '../')" class="toggle-password" alt="Mostrar/Ocultar">
        </div>

        <!-- Campo de confirmar nueva contraseña -->
        <label for="confirm_password" style="text-align: center;">Confirmar nueva contraseña:</label>
        <div class="password-container">
        <input type="password" id="confirm_password" name="confirm_password" style="text-align: center;" placeholder="Confirmar nueva contraseña" required>
        <img src="../css/Images/Ocultar.png" onclick="togglePasswordVisibility('confirm_password', this, '../')" class="toggle-password" alt="Mostrar/Ocultar">
        </div>
        
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <button type="submit" class="CustomLargeButton" style="text-align: center; margin-top: 1vw;">Cambiar contraseña</button>
    </form>
</div>

<button type="button" class="CustomButton Volver" onclick="window.location.href='Main.php'">Volver</button>

</body>
</html>
