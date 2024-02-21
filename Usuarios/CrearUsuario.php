<?php
// Conectar a la base de datos de forma segura
require '../PHP/ServerConnect.php';

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
  <link rel="stylesheet" type="text/css" href="../CSS/styles.css">
  <link rel="icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
  <link rel="shortcut icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
  <!-- JS Core -->
  <script src="JS/RegistrarUsuario.js"></script>
  <script src="../JS/TransformarDatos.js"></script>
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
            <h1 class="text-light">FORMULARIO DE CREACIÓN DE USUARIO</h1>
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
            <form id="registerUserForm" method="post" onsubmit="return RegistrarUsuario()">
              <label for="username" class="fs-4 fw-bold">Nombre de usuario:</label>
              <input type="text" class="form-control fs-4" id="username" name="username" placeholder="Ingrese el nombre de usuario" onchange="transformarDatosMayusculas('username')" required>

              <label for="password" class="mt-1 fs-4 fw-bold">Contraseña:</label>
                <div class="password-container">
                  <input type="password" class="form-control fs-4" id="password" name="password" placeholder="Ingrese la contraseña" required>
                  <img src="../CSS/Images/Ocultar.png" onclick="togglePasswordVisibility('password', this, '../')" class="toggle-password" alt="Mostrar/Ocultar">
                </div>

              <label for="confirm_password" class="mt-1 fs-4 fw-bold">Confirmar contraseña:</label>
                <div class="password-container">
                  <input type="password" class="form-control fs-4" id="confirm_password" name="confirm_password" placeholder="Ingrese la contraseña nuevamente" required>
                  <img src="../CSS/Images/Ocultar.png" onclick="togglePasswordVisibility('confirm_password', this, '../')" class="toggle-password" alt="Mostrar/Ocultar">
                </div>

              <label for="Rol_del_usuario" class="mt-1 fs-4 fw-bold">Rol del usuario:</label>
              <select id="Rol_del_usuario" class="form-select fs-4" name="Rol_del_usuario">
                <option value="" disabled selected>Seleccione un rol</option>
                <?php if ($rolUsuario == 1) { ?><option value="2">SUPERVISOR</option><?php } ?>
                <option value="3">ANALISTA</option>
                <option value="4" selected>INVITADO</option>
              </select>

              <label for="Apellido_Operador" class="mt-1 fs-4 fw-bold">Apellido del operador:</label>
              <input type="text" class="form-control fs-4" id="Apellido_Operador" name="Apellido_Operador" placeholder="Ingrese el Apellido del operador" onchange="transformarDatosMayusculas('Apellido_Operador')" required>

              <label for="Nombre_Operador" class="mt-1 fs-4 fw-bold">Nombre del operador:</label>
              <input type="text" class="form-control fs-4" id="Nombre_Operador" name="Nombre_Operador" placeholder="Ingrese el nombre del operador" onchange="transformarDatosNompropio('Nombre_Operador')" required>

              <label for="NI_Operador" class="mt-1 fs-4 fw-bold">NI del operador:</label>
              <input type="text" class="form-control fs-4" id="NI_Operador" name="NI_Operador" placeholder="Ingrese el NI del operador" onchange="transformarDatosNumerico('NI_Operador')" required>

              <div class="d-grid mt-4">
                <button class="btn btn-primary fs-2" type="submit">Registrar nuevo usuario</button>
              </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>
