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
  <title>Consultas - Main</title>
  <link rel="stylesheet" type="text/css" href="../css/styles.css">
  <link rel="icon" href="../css/Images/favicon.ico" type="Image/x-icon">
  <link rel="shortcut icon" href="../css/Images/favicon.ico" type="Image/x-icon">
</head>
<body class="BodyFondo1">

  <button type="button" class="CustomButton Volver" onclick="window.location.href='../Main.php'">Volver</button>

  <button type="button" class="CustomButton B_Fuentes Bandeja" onclick="window.location.href = 'BandejaDeEntrada.php'">Modificar bandeja de entrada</button>

</body>
</html>