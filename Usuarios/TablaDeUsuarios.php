<?php
// Conectar a la base de datos de forma segura
require '../ServerConnect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // El usuario no ha iniciado sesión, redirigirlo a la página de inicio de sesión
    header("Location: ../Login.php");
    exit();
}

// Establece la conexión a la base de datos
$conn = open_database_connection();
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener el nombre de usuario del usuario logeado desde la sesión
$rolUsuario = $_SESSION['rolUsuario'];

// Verificar el rol del usuario
if ((int)$_SESSION['rolUsuario'] > 2) {
    // Si el rol es mayor a 2, el usuario no tiene permiso para acceder al formulario
    header("Location: Main_Usuario.php");
    exit();
}

// Función para dar de baja o de alta al usuario
function actualizarEstadoUsuario($UsuarioID, $NuevoEstado) {
    global $conn;
    
    // Actualiza el estado de la incidencia
    $sql = "UPDATE sistema_usuarios SET Estado = ? WHERE ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $NuevoEstado, $UsuarioID);
    
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

// Función para modificar el rol del usuario
function actualizarRolUsuario($UsuarioID, $Operacion) {
    global $conn;

    // Obtener el rol actual del usuario
    $sqlRolActual = "SELECT Rol_del_usuario FROM sistema_usuarios WHERE ID = ?";
    $stmtRolActual = $conn->prepare($sqlRolActual);
    $stmtRolActual->bind_param("i", $UsuarioID);
    $stmtRolActual->execute();
    $resultado = $stmtRolActual->get_result();
    $fila = $resultado->fetch_assoc();
    $rolActual = $fila['Rol_del_usuario'];

    // Determinar el nuevo rol
    $nuevoRol = ($Operacion === 'Aumentar') ? max(1, $rolActual - 1) : min(4, $rolActual + 1);

    // Actualiza el rol del usuario
    $sql = "UPDATE sistema_usuarios SET Rol_del_usuario = ? WHERE ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $nuevoRol, $UsuarioID);

    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

// Función para resetear la contraseña
function resetPasswordToDefault($UsuarioID) {
    global $conn;
    $defaultPassword = password_hash("SIACIP", PASSWORD_DEFAULT);
    $sql = "UPDATE sistema_usuarios SET Contraseña = ? WHERE ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $defaultPassword, $UsuarioID);
    return $stmt->execute();
}

// Verificar metodo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Cambiar el rol del usuario
    if (isset($_POST['CambiarRol']) && isset($_POST['UsuarioID'])) {
        $UsuarioID = $_POST['UsuarioID'];
        $Operacion = $_POST['CambiarRol']; // 'Aumentar' o 'Disminuir'
        if (actualizarRolUsuario($UsuarioID, $Operacion)) {
            header("Location: TablaDeUsuarios.php");
            exit();
        }
    }

    // Dar de baja al usuario
    if (isset($_POST['BajaUsuario'])) {
        $UsuarioID = $_POST['BajaUsuario'];
        if (actualizarEstadoUsuario($UsuarioID, 0)) {
            header("Location: TablaDeUsuarios.php");
            exit();
        }
    }

    // Dar de alta al usuario
    if (isset($_POST['AltaUsuario'])) {
        $UsuarioID = $_POST['AltaUsuario'];
        if (actualizarEstadoUsuario($UsuarioID, 1)) {
            header("Location: TablaDeUsuarios.php");
            exit();
        }
    }

    // Restablecer contraseña a valor predeterminado
    if (isset($_POST['ResetPassword']) && isset($_POST['UsuarioID'])) {
        $UsuarioID = $_POST['UsuarioID'];
        if (resetPasswordToDefault($UsuarioID)) {
            header("Location: TablaDeUsuarios.php");
            exit();
        }
    }
}

// Consulta SQL
$sql = "SELECT ID,
               Usuario,
               Rol_del_usuario,
               Estado
        FROM sistema_usuarios
        ORDER BY ID ASC";

$result = $conn->query($sql);

// Cerrar conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Usuarios - Tabla de usuarios</title>
  <link rel="icon" href="../css/Images/favicon.ico" type="Image/x-icon">
  <link rel="shortcut icon" href="../css/Images/favicon.ico" type="Image/x-icon">
  <link rel="stylesheet" type="text/css" href="../css/styles.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <link href="https://cdn.datatables.net/v/dt/dt-1.13.8/datatables.min.css" rel="stylesheet">
  <script src="https://cdn.datatables.net/v/dt/dt-1.13.8/datatables.min.js"></script>
</head>
<body class="BodyFondo3" style="background-color: rgb(183, 179, 179);">

<button type="button" class="CustomButton Volver" onclick="window.location.href='Main.php'">Volver</button>

<div id="search-container" style="max-width: 20%;">
  <label for="CustomSearch" style="color: rgb(0, 0, 0);">Buscar en la tabla:</label>
  <input type="text" id="CustomSearch" style="color: rgb(0, 0, 0);" placeholder="Ingrese el valor a buscar...">
</div>

<div id="length-container" style="max-width: 20%;">
  <label for="CustomLength" style="color: rgb(0, 0, 0);">Cantidad a mostrar:</label>
  <select id="CustomLength" style="color: rgb(0, 0, 0);">
    <option value="10">10 registros</option>
    <option value="25">25 registros</option>
    <option value="50">50 registros</option>
    <option value="100">100 registros</option>
  </select>
</div>

<div class="TopCenterDiv">
    <img src="../css/Images/PSF.png" alt="Texto alternativo" width="100%">
</div>

<!-- Tabla de usuarios -->
<div class="SearchTable">
<table id="UserTable" style="display: block; overflow: auto; max-height: 30vw; background-color: rgb(211, 216, 223);">
    <thead>
        <tr>
            <th style="width: 10vw; position: sticky; top: -1px; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">ID</th>
            <th style="width: 10vw; position: sticky; top: -1px; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">USUARIO</th>
            <th style="width: 20vw; position: sticky; top: -1px; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">TIPO DE USUARIO</th>
            <th style="width: 20vw; position: sticky; top: -1px; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">ESTADO</th>
            <th style="width: 40vw; position: sticky; top: -1px; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">ACCIONES</th>
        </tr>
    </thead>
    </tbody>
        <?php
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td style="width: 10vw;">' . htmlspecialchars($row["ID"], ENT_QUOTES, 'UTF-8') . '</td>';
            echo '<td style="width: 10vw;">' . htmlspecialchars($row["Usuario"], ENT_QUOTES, 'UTF-8') . '</td>';

            // TIPO DE USUARIO
            switch ($row["Rol_del_usuario"]) {
                case 1:
                    $color = "black";
                    $rol = "ADMINISTRADOR";
                    break;
                case 2:
                    $color = "red";
                    $rol = "SUPERVISOR";
                    break;
                case 3:
                    $color = "blue";
                    $rol = "ANALISTA";
                    break;
                case 4:
                    $color = "green";
                    $rol = "INVITADO";
                    break;
                default:
                    $color = "gray";
                    $rol = "DESCONOCIDO";
            }
            
            echo '<td style="width: 20vw; color: ' . $color . ';">' . $rol . '</td>';

            // ESTADO
            if ($row["Estado"] == 1) {
                echo '<td style="width: 15vw; color: blue;">' . 'ACTIVO' . '</td>';
            } else {
                echo '<td style="width: 15vw; color: red;">' . 'DADO DE BAJA' . '</td>';
            }

            // Agregamos botones para cada fila
            echo '<td style="width: 45vw;">';

            // Verifica si el usuario actual NO es un Supervisor mirando a un Administrador o a otro Supervisor
            if (!($rolUsuario == 2 && ($row["Rol_del_usuario"] == 1 || $row["Rol_del_usuario"] == 2))) {
                // Botones para manejar las altas y bajas
                if ($row["Estado"] == 1) {
                    echo '<form action="TablaDeUsuarios.php" method="POST" style="display:inline;" onsubmit="return confirm(\'¿Está seguro de que desea dar de baja al usuario?\');">';
                    echo '<input type="hidden" name="BajaUsuario" value="' . $row["ID"] . '">';
                    echo '<button type="submit" class="BTN_Custom BTN-Red">Dar de baja</button>';
                } else {
                    echo '<form action="TablaDeUsuarios.php" method="POST" style="display:inline;" onsubmit="return confirm(\'¿Está seguro de que desea dar de alta al usuario?\');">';
                    echo '<input type="hidden" name="AltaUsuario" value="' . $row["ID"] . '">';
                    echo '<button type="submit" class="BTN_Custom BTN-Blue">Dar de alta</button>';
                }
                echo '</form>';

                // Botón para manejar cambiar el rol de los usuarios
                if (!($rolUsuario == 2 && ($row["Rol_del_usuario"] == 1 || $row["Rol_del_usuario"] == 2))) {
                    // Botón "Aumentar Rol": Solo si el usuario no es un Supervisor intentando subir de nivel a un rango 3 y el usuario no es ya nivel 1
                    if (!($rolUsuario == 2 && $row["Rol_del_usuario"] == 3) && $row["Rol_del_usuario"] != 1) {
                        echo '<form action="TablaDeUsuarios.php" method="POST" style="display:inline;" onsubmit="return confirm(\'¿Está seguro de que desea modificar el rol del usuario?\');">';
                        echo '<input type="hidden" name="UsuarioID" value="' . $row["ID"] . '">';
                        echo '<button type="submit" name="CambiarRol" class="BTN_Custom BTN-Green" value="Aumentar">Aumentar Rol</button>';
                        echo '</form>';
                    }

                    // Botón "Disminuir Rol": Solo si el nivel de usuario no es ya 4
                    if ($row["Rol_del_usuario"] != 4) {
                        echo '<form action="TablaDeUsuarios.php" method="POST" style="display:inline;" onsubmit="return confirm(\'¿Está seguro de que desea modificar el rol del usuario?\');">';
                        echo '<input type="hidden" name="UsuarioID" value="' . $row["ID"] . '">';
                        echo '<button type="submit" name="CambiarRol" class="BTN_Custom BTN-Orange" value="Disminuir">Disminuir Rol</button>';
                        echo '</form>';
                    }
                } else {
                    // Si el usuario es un Supervisor mirando a otro Supervisor o Administrador
                    echo '';
                }

                // Botón de restablecimiento de contraseña
                if ($rolUsuario == 1 || ($rolUsuario == 2 && $row["Rol_del_usuario"] > 2)) {                
                    echo '<form action="TablaDeUsuarios.php" method="POST" style="display:inline;" onsubmit="return confirm(\'¿Está seguro de que desea reestablecer la contraseña del usuario?\');">';
                    echo '<input type="hidden" name="UsuarioID" value="' . $row["ID"] . '">';
                    echo '<button type="submit" name="ResetPassword" class="BTN_Custom BTN-Violet">Restablecer Contraseña</button>';
                    echo '</form>';
                }
                echo '</td>';

            } else {
                // Si el usuario es un Supervisor mirando a un Administrador, no muestra botones
                echo '';
            }

            echo '</td>';
            echo '</tr>';
        }
        ?>
    </tbody>
</table>
</div>

<script>
$(document).ready(function() {
  var table = $('#UserTable').DataTable({
    searching: true, // Habilita el campo de búsqueda de DataTables
    lengthChange: false, // Desactiva el selector de cantidad de registros por página de DataTables
    pageLength: 10, // Establece la cantidad de registros por página predeterminada
    lengthMenu: [10, 25, 50, 100], // Define las opciones del selector de cantidad de registros por página
    dom: 'lBfrtip' // Personaliza la disposición de los elementos de DataTables
  });

  $('#CustomSearch').on('input', function() {
    table.search($(this).val()).draw();
  });

  $('#CustomLength').change(function(){
    table.page.len($(this).val()).draw();
  });
});
</script>

</body>
</html>
