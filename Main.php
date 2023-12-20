<?php
// Conectar a la base de datos de forma segura
require 'ServerConnect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // El usuario no ha iniciado sesión, redirigirlo a la página de inicio de sesión
    header("Location: Login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pagina principal</title>
  <link rel="stylesheet" type="text/css" href="CSS/styles.css">
  <link rel="stylesheet" type="text/css" href="CSS/CustomButtons.css">
  <link rel="icon" href="css/Images/favicon.ico" type="Image/x-icon">
  <link rel="shortcut icon" href="css/Images/favicon.ico" type="Image/x-icon">
  <script src="Scripts/Main.js"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body> 

<form method="post" action="Logout.php">
    <button type="submit" class="CustomButton" style="left: 1%;top: 13%;" name="Logout">Cerrar sesión</button>
</form>

<button type="button" class="CustomButton" style="left: 1%;top: 1%;" onclick="window.location.href='Usuarios/Main.php'">Panel de usuario</button>

<button type="button" class="CustomButton" style="right: 1%;top: 1%;" onclick="window.location.href='Consultas/Main.php'">Sistema de consultas</button>

<button type="submit" class="CustomButton" style="right: 1%;top: 13%;"onclick="window.location.href='PlanillaInfractores/Planilla.php'">Cargar planilla</button>


<!-- Bandeja de entrada -->
<div class="MainTable">
<table>
    <thead>
        <tr>
            <th style="Width: 10vw; Color: White;">ID</th>
            <th style="Width: 10vw; Color: White;">ESTADO</th>
            <th style="Width: 20vw; Color: White;">TIPO DE INCIDENCIA</th>
            <th style="Width: 20vw; Color: White;">FECHA DE CREACIÓN</th>
            <th style="Width: 40vw; Color: White;">ACCIONES</th>
        </tr>
    </thead>
    <tbody>
        <?php
        while ($row = $result->fetch_assoc()) {
            // Formatea la fecha y hora en "DD/MM/AAAA Hora:Minuto:Segundo"
            $fechaFormateada = date("d/m/Y H:i:s", strtotime($row["FechaDeCreacion"]));
        
            echo '<tr>'; // Abrir tr de la fila dinamica
            echo '<td style="Width: 10vw;">' . htmlspecialchars($row["DispositivoSIACIP"], ENT_QUOTES, 'UTF-8') . '</td>';

            echo '<td style="Width: 10vw;">';
            // Formulario para Cerrar Incidencia
            echo '<form action="Main.php" method="POST" style="display:inline;" onsubmit="return ConfirmacionCerrarIncidencia(\'' . htmlspecialchars($row["DispositivoSIACIP"], ENT_QUOTES, 'UTF-8') . '\');">';
            echo '<input type="hidden" name="CerrarIncidencia" value="' . htmlspecialchars($row["DispositivoSIACIP"], ENT_QUOTES, 'UTF-8') . '">';
            echo '<button class="ESTADO_BTN ABIERTO"><span class="NORMAL_TEXT">ABIERTO</span><span class="ON_HOVER">CERRAR INCIDENCIA</span></button>';
            echo '</form>' . '</td>';            

            echo '<td style="Width: 20vw;">' . htmlspecialchars($row["TipoHecho"], ENT_QUOTES, 'UTF-8') . '</td>';
            echo '<td style="Width: 20vw;">' . htmlspecialchars($fechaFormateada, ENT_QUOTES, 'UTF-8') . '</td>';
            
            // Agregamos botones para cada fila
            echo '<td style="Width: 40vw;">';
            echo '<div class="containerCustomBTN">';// Custom buttons
            // Formulario para Reporte Preliminar
            echo '<div class="btn">';
            echo '<form action="PlanillaInfractores/Planilla.php" method="POST" style="display:inline;">';
            echo '<input type="submit" class="btn btnCustom" style="background-color: rgba(45, 178, 255, 0.6);" value="Planilla de infractor">';
            echo '</form>';
            echo '</div>';

            echo '</td>';
            echo '</div>';

            echo '</tr>';// Cerrar tr de la fila dinamica
        }
        ?>
    </tbody>
</table>
</div>

</body>
</html>

