<?php
// Conectar a la base de datos de forma segura
require 'ServerConnect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // El usuario no ha iniciado sesión, redirigirlo a la página de inicio de sesión
    header("Location: Login.php");
    exit();
}

// Establece la conexión a la base de datos
$conn = open_database_connection();
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Utilizar una sentencia preparada
$sql = "SELECT ID, 
               Region,
               Causa,
               Reseña,
               FechaDeCreacion 
        FROM ficha_de_infractor
        ORDER BY FechaDeCreacion DESC 
        LIMIT 25";

$stmt = $conn->prepare($sql);

if ($stmt) {
    // Ejecutar la consulta
    $stmt->execute();

    // Obtener los resultados
    $result = $stmt->get_result();
    
    // Cerrar la sentencia preparada
    $stmt->close();
} else {
    // Manejo de errores si la preparación de la consulta falla
    echo "Error en la preparación de la consulta.";
}

// Cerrar la conexión
$conn->close();
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

<div class="LOGO">
    <h1>S.I.C.N.</h1>
    <div>
        <img src="CSS/Images/PDI.png" alt="">
    </div>
</div>

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
                <th style="min-width: 5vw; Color: White;">ID</th>
                <th style="min-width: 10vw; Color: White;">FECHA DE CREACIÓN</th>
                <th style="min-width: 10vw; Color: White;">REGIÓN</th>
                <th style="min-width: 20vw; Color: White;">CAUSA</th>
                <th style="min-width: 52vw; Color: White;">RESEÑA</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $result->fetch_assoc()) {
            // Formatea la fecha y hora en "DD/MM/AAAA Hora:Minuto:Segundo"
            $fechaFormateada = date("d/m/Y H:i:s", strtotime($row["FechaDeCreacion"]));
        
            echo '<tr>'; // Abrir tr de la fila dinamica
            echo '<td style="min-width: 5vw;">' . htmlspecialchars($row["ID"], ENT_QUOTES, 'UTF-8') . '</td>';         
            echo '<td style="min-width: 10vw;">' . htmlspecialchars($fechaFormateada, ENT_QUOTES, 'UTF-8') . '</td>';
            echo '<td style="min-width: 10vw;">' . htmlspecialchars($row["Region"], ENT_QUOTES, 'UTF-8') . '</td>';
            echo '<td style="min-width: 20vw;">' . htmlspecialchars($row["Causa"], ENT_QUOTES, 'UTF-8') . '</td>';
            echo '<td style="min-width: 52vw;">' . htmlspecialchars($row["Reseña"], ENT_QUOTES, 'UTF-8') . '</td>';
            echo '</tr>';// Cerrar tr de la fila dinamica
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>

