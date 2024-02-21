<?php
    require 'PHP/ServerConnect.php'; // Conectar a la base de datos

    // Sí el usuario no esta logeado, redirigirlo a la página de inicio de sesión
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header("Location: Login.php");
        exit();
    }

    // Verifica si el rol de usuario almacenado en la sesión es igual a 1
    if (isset($_SESSION['rolUsuario']) && $_SESSION['rolUsuario'] === 1) {
        // Si el rol es igual a 1, habilita los ajustes del INI para mostrar errores en pantalla
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }

    // Establece la conexión a la base de datos
    $conn = open_database_connection();
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Obtener el valor del rol del usuario desde la sesión
    $rolUsuario = $_SESSION['rolUsuario'];

    // Función para obtener el año actual
    function getCurrentYear() {
        return date("Y");
    }

    // Función para obtener el número máximo de incidencia
    function getMaxNumero($conn, $currentYear) {
        $sqlMaxNum = "SELECT MAX(Numero) AS max_numero FROM sistema_planilla_infractores WHERE Año = ?";
        $stmtMaxNum = $conn->prepare($sqlMaxNum);
        $stmtMaxNum->bind_param("s", $currentYear);

        if (!$stmtMaxNum->execute()) {
            die("Error ejecutando la consulta: " . $conn->error);
        }

        $result = $stmtMaxNum->get_result();
        $maxNumero = 0;

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $maxNumero = $row['max_numero'] ? $row['max_numero'] : 0;
        }
        $stmtMaxNum->close();

        return $maxNumero;
    }

    // Función para obtener valores de incidencia existente
    function getIncidenciaValues($conn, $editarNumero) {
        $sqlSelect = "SELECT Numero, Año FROM sistema_planilla_infractores WHERE Numero = ?";
        $stmtSelect = $conn->prepare($sqlSelect);
        $stmtSelect->bind_param("s", $editarNumero);

        if ($stmtSelect->execute()) {
            $resultSelect = $stmtSelect->get_result();

            if ($resultSelect->num_rows > 0) {
                $rowSelect = $resultSelect->fetch_assoc();
                return [
                    'Numero' => $rowSelect['Numero'],
                    'Año' => $rowSelect['Año'],
                ];
            }
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['POST_Action'])) {
            if ($_POST['POST_Action'] === 'Nuevo') {
                $currentYear = getCurrentYear();
                $maxNumero = getMaxNumero($conn, $currentYear);

                $newNumero = $maxNumero + 1;
                $newAño = $currentYear;

            } elseif ($_POST['POST_Action'] === 'EditarIncidencia') {
                $editarID = isset($_POST['Incidencia_Numero']) ? $_POST['Incidencia_Numero'] : '';

                if (!empty($editarID)) {
                    // Descompone ID en Numero e Año
                    list($newNumero, $newAño) = explode('-', $editarID);
                }
            }
        }

        // Verificar si se ha enviado el formulario POST y la acción es "Nuevo_Submit" - Esto crea una nueva carta
        if (isset($_POST['POST_Action']) && $_POST['POST_Action'] === 'Nuevo_Submit') {
            // Obtener los datos del formulario
            $ID = $_POST['ID'];
            $tipoHecho = $_POST['Tipo'];
            $numero = $_POST['Numero'];
            $año = $_POST['Año'];
            $UsuarioCreador = $_SESSION['usernameID'];

            // Crear ID concatenando $numero y $año con un guión
            $ID = $numero . '-' . $año;

            // Sentencias preparadas para prevenir inyección SQL
            $stmt = $conn->prepare("INSERT INTO sistema_planilla_infractores (ID, Tipo, Numero, Año, UsuarioCreador)
                    VALUES (?, ?, ?, ?, ?)");

            $stmt->bind_param("ssiii", $ID, $tipoHecho, $numero, $año, $UsuarioCreador);

            if ($stmt->execute()) {
                header('Location: Main.php');
                exit();
            } else {
                // Mensaje de error
                die("Error al insertar datos: " . $conn->error);
            }

            $stmt->close();

        } elseif (isset($_POST['POST_Action']) && $_POST['POST_Action'] === 'EditarIncidencia_Submit') {
            // Si la acción es "EditarIncidencia", realiza una actualización en lugar de inserción - Esto edita una carta existente
            $tipoHecho = $_POST['Tipo'];
            $numero = $_POST['Numero'];
            $año = $_POST['Año'];

            // Crear ID concatenando $numero y $año con un guión
            $ID = $numero . '-' . $año;

            // Sentencia SQL de actualización
            $sql = "UPDATE sistema_planilla_infractores SET Tipo = ? WHERE Numero = ? AND Año = ?";

            // Sentencia preparada
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sii", $tipoHecho, $numero, $año);

            if ($stmt->execute()) {
                header('Location: Main.php');
                exit();
            } else {
                // Mensaje de error
                die("Error al actualizar datos: " . $conn->error);
            }

            $stmt->close();
        }
    }

    $conn->close(); // Cierra la conexión a la base de datos al final del archivo
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva incidencia</title>
    <!-- Favicon -->
    <link rel="icon" href="CSS/Images/favicon.ico" type="Image/x-icon">
    <link rel="shortcut icon" href="CSS/Images/favicon.ico" type="Image/x-icon">
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="CSS/Webkit.css">
    <link rel="stylesheet" type="text/css" href="CSS/styles.css">
    <!-- JS Core -->
    <script src="JS/TransformarDatos.js"></script>
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap -->
    <script src="Bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="Bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="Bootstrap/Icons/font/bootstrap-icons.css">
</head>
<body class="BodyNuevoFormulario">

<!-- Barra de navegación -->
<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark" style="height: 5vw;">
    <div class="container-fluid d-flex align-items-center justify-content-center">
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <button type="button" class="btn btn-primary btn-lg" style="position: fixed; top: 0; left: 0; width: 12vw; height: 4vw; font-size: 1.5vw; margin: 0.5vw;" onclick="window.location.href='Main.php'">
                <i class="bi bi-arrow-left-square-fill"></i> <b>VOLVER</b>
            </button>
        </div>
    </div>
</nav>

    <div class="FormGlass">
        <form id="SIACIP" name="SIACIP" action="NuevoFormulario.php" method="post" onsubmit="return confirm('¿Está seguro que desea enviar este formulario?')">
            
            <h1 class="text-center text-primary mb-4">FORMULARIO # <?php echo $newNumero; ?>-<?php echo $newAño; ?></h1>

            <input type="hidden" id="Numero" name="Numero" value="<?php echo $newNumero; ?>" required>
            <input type="hidden" id="Año" name="Año" value="<?php echo $newAño; ?>" required>

                <div class="row">
                    <div class="input-group mb-3 fs-4">
                        <label for="Tipo" class="input-group-text fw-bold">TIPO DE FORMULARIO:</label>
                        <select id="Tipo" class="form-select" name="Tipo" required>
                            <option value="FICHA DE INFRACTORES">FICHA DE INFRACTORES</option>
                        </select>
                    </div>
                </div>

            <input type="hidden" name="POST_Action" value="Nuevo_Submit">

            <div class="d-grid my-4">
              <button type="submit" class="btn btn-primary btn-lg fs-2">GENERAR FORMULARIO</button>
            </div>
    
        </form>
    
    </div>

</body>
</html>
