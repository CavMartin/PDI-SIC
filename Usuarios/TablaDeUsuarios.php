<?php
// Conectar a la base de datos de forma segura
require '../PHP/ServerConnect.php';

// Verificar estado del login
checkLoginState();

// Establece la conexión a la base de datos
$conn = open_database_connection('sistema_horus');
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener el nombre de usuario del usuario logeado desde la sesión
$userrole = $_SESSION['userrole'];
$usergroup = $_SESSION['usergroup'];

// Verificar el rol del usuario
if ((int)$_SESSION['userrole'] > 2) {
    // Si el rol es mayor a 2, el usuario no tiene permiso para acceder al formulario
    header("Location: index.php");
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
function actualizaruserrole($UsuarioID, $Operacion) {
    global $conn;

    // Obtener el rol actual del usuario
    $sqlRolActual = "SELECT Rol FROM sistema_usuarios WHERE ID = ?";
    $stmtRolActual = $conn->prepare($sqlRolActual);
    $stmtRolActual->bind_param("i", $UsuarioID);
    $stmtRolActual->execute();
    $resultado = $stmtRolActual->get_result();
    $fila = $resultado->fetch_assoc();
    $rolActual = $fila['Rol'];

    // Determinar el nuevo rol
    $nuevoRol = ($Operacion === 'Aumentar') ? max(1, $rolActual - 1) : min(4, $rolActual + 1);

    // Actualiza el rol del usuario
    $sql = "UPDATE sistema_usuarios SET Rol = ? WHERE ID = ?";
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
        if (actualizaruserrole($UsuarioID, $Operacion)) {
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

// Consulta SQL basada en el grupo del usuario
if ($usergroup === 'ADMINISTRADOR') {
    $sql = "SELECT ID, Usuario, Rol, Grupo, Estado
            FROM sistema_usuarios
            ORDER BY ID ASC";
} else {
    $sql = "SELECT ID, Usuario, Rol, Grupo, Estado
            FROM sistema_usuarios
            WHERE Grupo = ?
            ORDER BY ID ASC";
}

$stmt = $conn->prepare($sql);

if ($usergroup !== 'ADMINISTRADOR') {
    $stmt->bind_param("s", $usergroup);
}

$stmt->execute();
$result = $stmt->get_result();

// Cerrar conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Usuarios - Tabla de usuarios</title>
  <link rel="icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
  <link rel="shortcut icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
  <link rel="stylesheet" type="text/css" href="../CSS/styles.css">
  <!-- JQuery -->
  <script src="../Resources/JQuery/jquery-3.7.1.min.js"></script>
  <!-- SweetAlert -->
  <script src="../Resources/SweetAlert2/sweetalert2.min.js"></script>
  <link rel="stylesheet" href="../Resources/SweetAlert2/sweetalert2.min.css">
  <!-- DataTables -->
  <script src="../Resources/DataTables/datatables.min.js"></script>
  <!-- Bootstrap -->
  <script src="../Resources/Bootstrap/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="../Resources/Bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../Resources/Bootstrap/Icons/font/bootstrap-icons.css">
</head>
<body class="bg-secondary" style="overflow-x: hidden;">

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
            <h2 class="text-light text-center m-0">TABLA DE USUARIOS</h2>
            <img src="../CSS/Images/LOGO.png" class="mx-3" alt="Icono" style="width: 4rem;"><!-- Icono Derecho -->
        </div>

        <div style="position: absolute; right: 1rem;">
            <div class="input-group my-2">
                <span class="input-group-text fw-bold">Buscar:</span>
                <input type="text" class="form-control" id="CustomSearch" name="CustomSearch" placeholder="Ingrese el valor a buscar...">
            </div>
        </div>
    </div>
</nav>

<!-- Contenedor principal para la tabla -->
<div class="row justify-content-center" style="margin-top: 10rem;">
    <div class="col-10"> <!-- Define el ancho de la columna donde estará la tabla -->
        <!-- Tabla de usuarios -->
        <table id="UserTable" class="table table-bordered table-hover text-center" style="vertical-align: middle;">
            <thead>
                <tr class="table-dark fs-5">
                    <th>ID</th>
                    <th>USUARIO</th>
                    <th>GRUPO</th>
                    <th>NIVEL DE USUARIO</th>
                    <th>ESTADO</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            </tbody>
                <?php
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($row["ID"], ENT_QUOTES, 'UTF-8') . '</td>';
                        echo '<td>' . htmlspecialchars($row["Usuario"], ENT_QUOTES, 'UTF-8') . '</td>';
                        echo '<td>' . htmlspecialchars($row["Grupo"], ENT_QUOTES, 'UTF-8') . '</td>';

                        // TIPO DE USUARIO
                        switch ($row["Rol"]) {
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
                    
                        echo '<td style="color: ' . $color . ';">' . $rol . '</td>';
                    
                        // ESTADO
                        if ($row["Estado"] == 1) {
                            echo '<td style="color: blue;">' . 'ACTIVO' . '</td>';
                        } else {
                            echo '<td style="color: red;">' . 'DADO DE BAJA' . '</td>';
                        }
                    
                        // Agregamos botones para cada fila
                        echo '<td style="width: 45vw;">';
                    
                        // Verifica si el usuario actual NO es un Supervisor mirando a un Administrador o a otro Supervisor
                        if (!($userrole == 2 && ($row["Rol"] == 1 || $row["Rol"] == 2))) {
                            // Botones para manejar las altas y bajas
                            if ($row["Estado"] == 1) {
                                echo '<form action="TablaDeUsuarios.php" method="POST" style="display:inline;" onsubmit="return confirm(\'¿Está seguro de que desea dar de baja al usuario?\');">';
                                echo '<input type="hidden" name="BajaUsuario" value="' . $row["ID"] . '">';
                                echo '<button type="submit" class="btn btn-danger mx-1">Dar de baja</button>';
                            } else {
                                echo '<form action="TablaDeUsuarios.php" method="POST" style="display:inline;" onsubmit="return confirm(\'¿Está seguro de que desea dar de alta al usuario?\');">';
                                echo '<input type="hidden" name="AltaUsuario" value="' . $row["ID"] . '">';
                                echo '<button type="submit" class="btn btn-primary mx-1">Dar de alta</button>';
                            }
                            echo '</form>';
                        
                            // Botón para manejar cambiar el rol de los usuarios
                            if (!($userrole == 2 && ($row["Rol"] == 1 || $row["Rol"] == 2))) {
                                // Botón "Aumentar Rol": Solo si el usuario no es un Supervisor intentando subir de nivel a un rango 3 y el usuario no es ya nivel 1
                                if (!($userrole == 2 && $row["Rol"] == 3) && $row["Rol"] != 1) {
                                    echo '<form action="TablaDeUsuarios.php" method="POST" style="display:inline;" onsubmit="return confirm(\'¿Está seguro de que desea modificar el rol del usuario?\');">';
                                    echo '<input type="hidden" name="UsuarioID" value="' . $row["ID"] . '">';
                                    echo '<button type="submit" name="CambiarRol" class="btn btn-success mx-1" value="Aumentar">Aumentar Rol</button>';
                                    echo '</form>';
                                }
                            
                                // Botón "Disminuir Rol": Solo si el nivel de usuario no es ya 4
                                if ($row["Rol"] != 4) {
                                    echo '<form action="TablaDeUsuarios.php" method="POST" style="display:inline;" onsubmit="return confirm(\'¿Está seguro de que desea modificar el rol del usuario?\');">';
                                    echo '<input type="hidden" name="UsuarioID" value="' . $row["ID"] . '">';
                                    echo '<button type="submit" name="CambiarRol" class="btn btn-warning mx-1" value="Disminuir">Disminuir Rol</button>';
                                    echo '</form>';
                                }
                            } else {
                                // Si el usuario es un Supervisor mirando a otro Supervisor o Administrador
                                echo '';
                            }
                        
                            // Botón de restablecimiento de contraseña
                            if ($userrole == 1 || ($userrole == 2 && $row["Rol"] > 2)) {                
                                echo '<form action="TablaDeUsuarios.php" method="POST" style="display:inline;" onsubmit="return confirm(\'¿Está seguro de que desea reestablecer la contraseña del usuario?\');">';
                                echo '<input type="hidden" name="UsuarioID" value="' . $row["ID"] . '">';
                                echo '<button type="submit" name="ResetPassword" class="btn btn-secondary mx-1">Restablecer Contraseña</button>';
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
</div>

<script>
    $(document).ready(function() {
        let table = $('#UserTable').DataTable({
            language: {
                url: '../Resources/DataTables/Spanish.json',
            },
            searching: true,
            lengthChange: false,
            pageLength: 10,
        });

        $('#CustomSearch').on('keyup', function() {
            table.search(this.value).draw();
        });
    });
</script>

</body>
</html>
