<?php
// Conectar a la base de datos de forma segura
require '../PHP/ServerConnect.php';

// Verificar estado del login
checkLoginState();

// Obtener la información del rol de usuario
$userrole = $_SESSION['userrole'];
$usergroup = $_SESSION['usergroup'];

// Verificar el rol del usuario
if ((int)$_SESSION['userrole'] > 2) {
    // Si el rol es mayor a 2, el usuario no tiene permiso para acceder al formulario
    header("Location: index.php");
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
  <script src="JS/createUser.js"></script>
  <script src="JS/passwordVisibility.js"></script>
  <script src="../JS/TransformarDatos.js"></script>
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
            <h2 class="text-light text-center m-0">FORMULARIO DE CREACIÓN DE USUARIO</h2>
            <img src="../CSS/Images/LOGO.png" class="mx-3" alt="Icono" style="width: 4rem;"><!-- Icono Derecho -->
        </div>
    </div>
</nav>

<div class="container mt-6" style="margin-top: 7rem;">
    <div class="row justify-content-center">
        <div class="col-md-8 bg-white p-4 border border-black rounded">
            <form id="createUserForm">
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

              <label for="Rol" class="mt-1 fs-4 fw-bold">Rol del usuario:</label>
              <select id="Rol" class="form-select fs-4" name="Rol">
                <option value="" disabled selected>Seleccione un rol</option>
                <?php if ($userrole == 1) { ?><option value="2">SUPERVISOR</option><?php } ?>
                <option value="3">ANALISTA</option>
                <option value="4" selected>INVITADO</option>
              </select>

              <?php if ($userrole == 1) { ?>
                <label for="Grupo" class="mt-1 fs-4 fw-bold">Grupo del usuario:</label>
                <select id="Grupo" class="form-select fs-4" name="Grupo" required>
                  <option value="" disabled selected>Seleccione un grupo</option>
                  <option value="SIACIP">SIACIP</option>
                  <option value="UAIC">UAIC</option>
                  <option value="MINSEG">MINSEG</option>
                </select>
              <?php } else { ?>
                <label for="Grupo" class="mt-1 fs-4 fw-bold">Grupo del usuario:</label>
                <select id="Grupo" class="form-select fs-4" name="Grupo" required>
                  <option value="<?php echo $_SESSION['usergroup']; ?>" selected><?php echo $_SESSION['usergroup']; ?></option>
                </select>
              <?php } ?>

              <label for="Apellido_Operador" class="mt-1 fs-4 fw-bold">Apellido del operador:</label>
              <input type="text" class="form-control fs-4" id="Apellido_Operador" name="Apellido_Operador" placeholder="Ingrese el Apellido del operador" onchange="transformarDatosMayusculas('Apellido_Operador')" required>

              <label for="Nombre_Operador" class="mt-1 fs-4 fw-bold">Nombre del operador:</label>
              <input type="text" class="form-control fs-4" id="Nombre_Operador" name="Nombre_Operador" placeholder="Ingrese el nombre del operador" onchange="transformarDatosNompropio('Nombre_Operador')" required>

              <label for="NI_Operador" class="mt-1 fs-4 fw-bold">NI del operador:</label>
              <input type="text" class="form-control fs-4" id="NI_Operador" name="NI_Operador" placeholder="Ingrese el NI del operador" onchange="transformarDatosNumerico('NI_Operador')" required>

              <div class="d-grid mt-4">
                <button type="button" id="createUserBtn" class="btn btn-primary btn-lg fs-1" onclick="createUser()">Crear Usuario</button>
              </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>
