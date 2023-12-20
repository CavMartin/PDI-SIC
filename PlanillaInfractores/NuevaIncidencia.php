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

// Obtener el valor del rol del usuario desde la sesión
$rolUsuario = $_SESSION['rolUsuario'];

// Determinar si el rol del usuario es mayor a 3
$readonly = ($rolUsuario > 1) ? 'readonly' : '';

// Definir la variable $postAction con un valor predeterminado
$postAction = (isset($_POST['POST_Action']) && $_POST['POST_Action'] === 'IniciarSIACIP') ? 'IniciarSIACIP_Submit' : 'EditarIncidencia_Submit';

// Definir la URL de redirección de acuerdo al valor del POST recibido
$redirectUrl = ($_POST['POST_Action'] === 'IniciarSIACIP') ? '../Main.php' : '../Consultas/BandejaDeEntrada.php';

// Definir el texto del botón de acuerdo al valor del POST recibido
$submitButtonText = ($_POST['POST_Action'] === 'IniciarSIACIP') ? 'Iniciar dispositivo SIACIP' : 'Modificar incidencia';

// Función para obtener el año actual
function getCurrentYear() {
    return date("Y");
}

// Función para obtener el número máximo de incidencia
function getMaxIncidenciaNumero($conn, $currentYear) {
    $sqlMaxNum = "SELECT MAX(IncidenciaNumero) AS max_numero FROM sistema_dispositivo_siacip WHERE IncidenciaAño = ?";
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
function getIncidenciaValues($conn, $editarIncidenciaNumero) {
    $sqlSelect = "SELECT IncidenciaNumero, IncidenciaAño FROM sistema_dispositivo_siacip WHERE IncidenciaNumero = ?";
    $stmtSelect = $conn->prepare($sqlSelect);
    $stmtSelect->bind_param("s", $editarIncidenciaNumero);

    if ($stmtSelect->execute()) {
        $resultSelect = $stmtSelect->get_result();

        if ($resultSelect->num_rows > 0) {
            $rowSelect = $resultSelect->fetch_assoc();
            return [
                'IncidenciaNumero' => $rowSelect['IncidenciaNumero'],
                'IncidenciaAño' => $rowSelect['IncidenciaAño'],
            ];
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['POST_Action'])) {
        if ($_POST['POST_Action'] === 'IniciarSIACIP') {
            $currentYear = getCurrentYear();
            $maxNumero = getMaxIncidenciaNumero($conn, $currentYear);

            $newNumero = $maxNumero + 1;
            $newAño = $currentYear;

        } elseif ($_POST['POST_Action'] === 'EditarIncidencia') {
            $editarDispositivoSIACIP = isset($_POST['Incidencia_Numero']) ? $_POST['Incidencia_Numero'] : '';

            if (!empty($editarDispositivoSIACIP)) {
                // Descompone DispositivoSIACIP en IncidenciaNumero e IncidenciaAño
                list($newNumero, $newAño) = explode('-', $editarDispositivoSIACIP);
            }
        }
    }

    // Verificar si se ha enviado el formulario POST y la acción es "IniciarSIACIP_Submit" - Esto crea una nueva carta
    if (isset($_POST['POST_Action']) && $_POST['POST_Action'] === 'IniciarSIACIP_Submit') {
        // Obtener los datos del formulario
        $DispositivoSIACIP = $_POST['DispositivoSIACIP'];
        $tipoHecho = $_POST['IncidenciaTipo'];
        $numero = $_POST['IncidenciaNumero'];
        $año = $_POST['IncidenciaAño'];
        $UsuarioCreador = $_SESSION['usernameID'];

        // Crear DispositivoSIACIP concatenando $numero y $año con un guión
        $DispositivoSIACIP = $numero . '-' . $año;

        // Sentencias preparadas para prevenir inyección SQL
        $stmt = $conn->prepare("INSERT INTO sistema_dispositivo_siacip (DispositivoSIACIP, IncidenciaTipo, IncidenciaNumero, IncidenciaAño, UsuarioCreador)
                VALUES (?, ?, ?, ?, ?)");

        $stmt->bind_param("siiii", $DispositivoSIACIP, $tipoHecho, $numero, $año, $UsuarioCreador);

        if ($stmt->execute()) {
            header('Location: ../Main.php');
            exit();
        } else {
            // Mensaje de error
            die("Error al insertar datos: " . $conn->error);
        }

        $stmt->close();

    } elseif (isset($_POST['POST_Action']) && $_POST['POST_Action'] === 'EditarIncidencia_Submit') {
        // Si la acción es "EditarIncidencia", realiza una actualización en lugar de inserción - Esto edita una carta existente
        $tipoHecho = $_POST['IncidenciaTipo'];
        $numero = $_POST['IncidenciaNumero'];
        $año = $_POST['IncidenciaAño'];

        // Crear DispositivoSIACIP concatenando $numero y $año con un guión
        $DispositivoSIACIP = $numero . '-' . $año;

        // Sentencia SQL de actualización
        $sql = "UPDATE sistema_dispositivo_siacip SET IncidenciaTipo = ? WHERE IncidenciaNumero = ? AND IncidenciaAño = ?";

        // Sentencia preparada
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $tipoHecho, $numero, $año);

        if ($stmt->execute()) {
            header('Location: ../Main.php');
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
    <title>Formulario IP</title>
    <link rel="stylesheet" type="text/css" href="../CSS/styles.css">
    <link rel="icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
    <link rel="shortcut icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="BodyFondo1">

    <div class="Formulario">
        <form id="SIACIP" name="SIACIP" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return confirm('¿Está seguro que desea enviar este formulario?')">
            <h1>Dispositivo SIACIP</h1>
    
            <label style="text-align: center;">Incidencia número:</label>
            <div class="horizontal-container">
                <input type="text" style="text-align: center; margin-right: 0.5vw;" maxlength="5" id="IncidenciaNumero" name="IncidenciaNumero" value="<?php echo $newNumero; ?>" <?php echo $readonly; ?> onchange="transformarDatosNumerico2('IncidenciaNumero')" required>
                <input type="text" style="text-align: center; margin-left: 0.5vw;"  maxlength="4" id="IncidenciaAño" name="IncidenciaAño" value="<?php echo $newAño; ?>" <?php echo $readonly; ?> onchange="transformarDatosNumerico2('IncidenciaAño')" required>
            </div>
    
            <label for="IncidenciaTipo" style="text-align: center;">Tipo de hecho:</label>
            <select id="IncidenciaTipo" style="text-align: center;" name="IncidenciaTipo" required>
                <option value="" disabled selected>Selecciona una opción</option>
                <option value="1">Amenazas de bomba</option>
                <option value="2">Amenazas extorsivas</option>
                <option value="3">Aprehendidos</option>
                <option value="4">Aprehendidos con arma de fuego</option>
                <option value="5">Ataque incendiario</option>
                <option value="6">Disparos de arma de fuego al aire</option>
                <option value="7">Disparos de arma de fuego contra comercios</option>
                <option value="8">Disparos de arma de fuego contra domicilios</option>
                <option value="9">Disparos de arma de fuego contra institución pública</option>
                <option value="10">Disparos de arma de fuego contra personas</option>
                <option value="11">Disparos de arma de fuego contra vehículos</option>
                <option value="22">Evadidos</option>
                <option value="12">Hallazgo de arma de fuego</option>
                <option value="13">Heridos de arma blanca</option>
                <option value="14">Heridos de arma de fuego</option>
                <option value="15">Intimidación pública</option>
                <option value="16">Óbitos</option>
                <option value="17">Óbitos y heridos</option>
                <option value="18">Persona armada</option>
                <option value="19">Secuestro extorsivo</option>
                <option value="20">Usurpaciones</option>
                <option value="21">Otros</option>        
            </select>

            <input type="hidden" name="POST_Action" value="<?php echo $postAction; ?>">

            <button type="submit" class="CustomLargeButton" style="text-align: center; margin-top: 1vw;"><?php echo $submitButtonText; ?></button>
    
        </form>
    
    </div>
    
    <button type="button" class="CustomButton Volver" onclick="window.location.href='<?php echo $redirectUrl; ?>'">Volver</button>

    <script src="Scripts/TransformarDatos.js"></script>

</body>
</html>
