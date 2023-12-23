<?php
// Conectar a la base de datos de forma segura
require '../ServerConnect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // El usuario no ha iniciado sesión, redirigirlo a la página de inicio de sesión
    header("Location: ../Login.php");
    exit();
}

// Obtener la información del rol de usuario
$rolUsuario = $_SESSION['rolUsuario'];

// Verificar el rol del usuario
if ((int)$_SESSION['rolUsuario'] > 2) {
    // Si el rol es mayor a 2, el usuario no tiene permiso para acceder al formulario
    header("Location: Main_Usuario.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Crear usuario</title>
  <link rel="stylesheet" type="text/css" href="../css/styles.css">
  <link rel="icon" href="../css/Images/favicon.ico" type="Image/x-icon">
  <link rel="shortcut icon" href="../css/Images/favicon.ico" type="Image/x-icon">
  <script src="Scripts/RegistrarUsuario.js"></script>
  <script src="../Scripts/TransformarDatos.js"></script>
  <script src="../Scripts/PasswordVisibility.js"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script></head>
<body class="BodyFondo1">

<div class="LoginForm">
    <form id="registerUserForm" method="post" onsubmit="return RegistrarUsuario()">
        <h2>REGISTRO DE USUARIO</h2>
        <label for="username" style="text-align: center;">Nombre de usuario:</label>
        <input type="text" id="username" name="username" style="text-align: center;" onchange="transformarDatosMayusculas('username')" required>

        <label for="password" style="text-align: center;">Contraseña:</label>
        <div class="password-container">
        <input type="password" id="password" name="password" style="text-align: center;" required>
        <img src="../css/Images/Ocultar.png" onclick="togglePasswordVisibility('password', this, '../')" class="toggle-password" alt="Mostrar/Ocultar">
        </div>
        
        <label for="confirm_password" style="text-align: center;">Confirmar contraseña:</label>
        <div class="password-container">
        <input type="password" id="confirm_password" name="confirm_password" style="text-align: center;" required>
        <img src="../css/Images/Ocultar.png" onclick="togglePasswordVisibility('confirm_password', this, '../')" class="toggle-password" alt="Mostrar/Ocultar">
        </div>

        <label for="Rol_del_usuario" style="text-align: center;">Rol del Usuario:</label>
        <select id="Rol_del_usuario" name="Rol_del_usuario" style="text-align: center;">
          <option value="" disabled selected>Seleccione un rol</option>
          <?php if ($rolUsuario == 1) { ?><option value="2">SUPERVISOR</option><?php } ?>
          <option value="3">ANALISTA</option>
          <option value="4" selected>INVITADO</option>
        </select>

        <label for="Apellido_Operador" style="text-align: center;">Apellido del operador:</label>
        <input type="text" id="Apellido_Operador" name="Apellido_Operador" style="text-align: center;" onchange="transformarDatosMayusculas('Apellido_Operador')" required>

        <label for="Nombre_Operador" style="text-align: center;">Nombre del operador:</label>
        <input type="text" id="Nombre_Operador" name="Nombre_Operador" style="text-align: center;" onchange="transformarDatosNompropio('Nombre_Operador')" required>

        <label for="NI_Operador" style="text-align: center;">NI del operador:</label>
        <input type="text" id="NI_Operador" name="NI_Operador" style="text-align: center;" onchange="transformarDatosNumerico('NI_Operador')" required>
        
        <label for="Region" style="text-align: center;">Región:</label>
        <select id="Region" name="Region" style="text-align: center;">
          <option value="" disabled selected>Seleccione una región</option>
          <?php if ($rolUsuario == 1) { ?><option value="División informática y tecnología">División informática y tecnología</option><?php } ?>
          <option value="REGIÓN 1">REGIÓN 1</option>
          <option value="REGIÓN 2">REGIÓN 2</option>
          <option value="REGIÓN 3">REGIÓN 3</option>
          <option value="REGIÓN 4">REGIÓN 4</option>
          <option value="REGIÓN 5">REGIÓN 5</option>
        </select>

        <button type="submit" class="CustomLargeButton" style="text-align: center; margin-top: 1vw;">Registrar nuevo usuario</button>
    </form>
</div>

<script>
</script>

<button type="button" class="CustomButton Volver" onclick="window.location.href='Main.php'">Volver</button>

</body>
</html>
