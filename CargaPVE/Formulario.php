<?php
    require '../PHP/ServerConnect.php'; // Conectar a la base de datos
    require 'PHP/DataFetcher.php'; // Clase para recopilar datos

    // Verificar estado del login
    checkLoginState();

    // Cargar la variable de sesión "usergroup"
    $usergroup = $_SESSION['usergroup'];

    // Si el método para acceder a la pagina no es POST, redirige a index.php
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        header("Location: index.php");
        exit();
    }

    // Conexión a la base de datos
    $conn = open_database_connection('carga_pve');
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Recoger valores de POST
    $formularioPVE = isset($_POST['formularioPVE']) ? $_POST['formularioPVE'] : '';

    // Si encabezado no está vacío, recopila los datos usando DataFetcher
    if (!empty($formularioPVE)) {
        // Crear una instancia de DataFetcher pasándole la conexión a la base de datos
        $DataFetcher = new DataFetcher($conn);

        // Llamar al método fetchDataEncabezado para obtener los datos del encabezado
        $encabezadoData = $DataFetcher->fetchDataEncabezado($formularioPVE);
    }

    // Cargar las listas desde el archivo JSON
    $jsonData = file_get_contents('JS/listasMultiples.json');
    $listas = json_decode($jsonData, true);

    // Convertir las cadenas separadas por comas en arrays
    $tipologiasSeleccionadas = isset($encabezadoData['Tipologia']) ? explode(', ', $encabezadoData['Tipologia']) : [];
    $modalidadesSeleccionadas = isset($encabezadoData['ModalidadComisiva']) ? explode(', ', $encabezadoData['ModalidadComisiva']) : [];
    $estupefacientesSeleccionados = isset($encabezadoData['TipoEstupefaciente']) ? explode(', ', $encabezadoData['TipoEstupefaciente']) : [];

    function generateOptions($options, $selectedValues) {
        $html = '';
        foreach ($options as $option) {
            $selected = in_array($option, $selectedValues) ? 'selected' : '';
            $html .= "<option value=\"$option\" $selected>$option</option>";
        }
        return $html;
    }

    // Cierra la conexión a la base de datos
    $conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CargaPVE</title>
  <!-- Favicon -->
  <link rel="icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
  <link rel="shortcut icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
  <!-- CSS -->
  <link rel="stylesheet" type="text/css" href="../CSS/Webkit.css">
  <!-- JS -->
  <script src="JS/handlerAJAX.js"></script>
  <script src="JS/elementosOcultos.js"></script>
  <script src="JS/lugares.js"></script>
  <script src="JS/personas.js"></script>
  <script src="JS/vehiculos.js"></script>
  <script src="../JS/TransformarDatos.js"></script>
  <!-- Datalist -->
  <script src="JS/InicializarDataList.js"></script>
  <datalist id="globalSugerenciasCiudades"></datalist>
  <datalist id="globalSugerenciasProvincias"></datalist>
  <!-- jQuery -->
  <script src="../Resources/JQuery/jquery-3.7.1.min.js"></script>
  <!-- SweetAlert -->
  <script src="../Resources/SweetAlert2/sweetalert2.min.js"></script>
  <link rel="stylesheet" href="../Resources/SweetAlert2/sweetalert2.min.css">
  <!-- Bootstrap -->
  <script src="../Resources/Bootstrap/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="../Resources/Bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../Resources/Bootstrap/Icons/font/bootstrap-icons.css">
  <!-- Selectize -->
  <script src="https://cdn.jsdelivr.net/npm/selectize/dist/js/standalone/selectize.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/selectize/dist/css/selectize.css" />
</head>
<body class="bg-secondary">

<!-- Barra de navegación -->
<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
    <div class="container-fluid d-flex justify-content-center position-relative">   

        <!-- Botón de navegación a la página principal -->
        <div style="position: absolute; left: 0;">
            <button type="button" id="VolverButton" class="btn btn-warning btn-lg m-3" onclick="window.location.href='index.php'">
                <i class="bi bi-arrow-left-square-fill"></i> <b>VOLVER</b>
            </button>
        </div>

        <!-- Bloque del título con imágenes, centrado -->
        <div class="d-flex justify-content-center align-items-center flex-grow-1">
            <img src="../CSS/Images/LOGO2.png" class="mx-3" alt="Icono" style="width: 4rem;"><!-- Icono Izquierdo -->
            <h2 class="text-light text-center m-0">FORMULARIO DE CARGA DE POSIBLES PVE "<?php echo htmlspecialchars($formularioPVE); ?>"</h2>
            <img src="../CSS/Images/LOGO.png" class="mx-3" alt="Icono" style="width: 4rem;"><!-- Icono Derecho -->
        </div>
    </div>
</nav>

<button type="button" id="AgregarDomicilio" class="btn btn-primary btn-lg text-start" style="position: fixed; top: 6rem; right: 1rem; width: 13%;">
    <i class="bi bi-house-add"></i> Agregar domicilio
</button>
<button type="button" id="AgregarPersona" class="btn btn-primary btn-lg text-start" style="position: fixed; top: 11rem; right: 1rem; width: 13%;">
    <i class="bi bi-person-badge"></i> Agregar persona
</button>
<button type="button" id="AgregarVehiculo" class="btn btn-primary btn-lg text-start" style="position: fixed; top: 16rem; right: 1rem; width: 13%;">
    <i class="bi bi-car-front-fill"></i> Agregar vehiculo
</button>
<button type="button" class="btn btn-success btn-lg" style="position: fixed; bottom: 1rem; right: 1rem; width: 13%;" onclick="guardarCambios()">
    <i class="bi bi-floppy"></i> <b>Guardar</b>
</button>

<!-- Formulario de carga/edición de entidad -->
<div class="row px-2" style="top: 6rem; position: absolute; width: 85%;">
    <form id="CargarFormulario" name="CargarFormulario" enctype="multipart/form-data" method="post">
        <div><!-- Campos ocultos necesarios para el funcionamiento de la aplicacion -->
            <input type="hidden" id="formularioPVE" name="formularioPVE" value="<?php echo htmlspecialchars($formularioPVE); ?>">
        </div>

        <div id="EntidadPrincipal" class="border border-black rounded bg-light px-3 mx-1"><!-- Entidad principal del formulario -->
            <div class="row mt-3">
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold">FECHA:</span>
                        <input type="date" class="form-control text-center" id="Fecha" name="Fecha" value="<?php echo isset($encabezadoData['Fecha']) ? $encabezadoData['Fecha'] : ''; ?>" required>
                    </div>
                </div>
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold">HORA:</span>
                        <input type="time" class="form-control text-center" id="Hora" name="Hora" value="<?php echo isset($encabezadoData['Hora']) ? $encabezadoData['Hora'] : ''; ?>">
                    </div>
                </div>
        <?php
            if ($usergroup == 'URII') {
                // Si el grupo de usuario es URII, la opción se oculta
                echo '<input type="text" id="Fuente" name="Fuente" value="URII" hidden>';
            } else {
                // Para cualquier otro grupo de usuario, muestra las opciones de selección
                echo '
                <div class="col">
                    <div class="input-group mb-3">
                        <label for="Fuente" class="input-group-text fw-bold">FUENTE:</label>
                        <select id="Fuente" class="form-select text-center" name="Fuente" required>
                            <option value="" disabled selected>Seleccione la Fuente</option>
                            <option value="0800" ' . ((isset($encabezadoData["Fuente"]) && $encabezadoData["Fuente"] == "0800") ? "selected" : "") . '>0800</option>
                            <option value="911" ' . ((isset($encabezadoData["Fuente"]) && $encabezadoData["Fuente"] == "911") ? "selected" : "") . '>911</option>
                        </select>
                    </div>
                </div>';
            }
        ?>
                <div class="col">
                    <div class="input-group mb-3">
                    <?php
                        if ($usergroup == 'URII') {
                            // Si el grupo de usuario es URII, cambia la LABEL
                            echo '<span class="input-group-text fw-bold">NÚMERO DE REPORTE ASOCIADO:</span>';
                        } else {
                            // Para cualquier otro grupo de usuario, muestra las opciones de selección
                            echo '<span class="input-group-text fw-bold">NÚMERO DE CARTA 911:</span>';
                        }
                    ?>
                        <input type="text" class="form-control" id="ReporteAsociado" name="ReporteAsociado" maxlength="50" value="<?php echo isset($encabezadoData['ReporteAsociado']) ? $encabezadoData['ReporteAsociado'] : ''; ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="input-group mb-3">
                        <label for="Tipologia" class="input-group-text fw-bold">TIPIFICACIÓN:</label>
                        <select id="Tipologia" class="form-control" name="Tipologia[]" multiple="multiple" required>
                            <?= generateOptions($listas['Tipologia'], $tipologiasSeleccionadas); ?>
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="input-group mb-3">
                        <label for="ModalidadComisiva" class="input-group-text fw-bold">MODALIDAD COMISIVA:</label>
                        <select id="ModalidadComisiva" class="form-control" name="ModalidadComisiva[]" multiple="multiple" required>
                            <?= generateOptions($listas['ModalidadComisiva'], $modalidadesSeleccionadas); ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="input-group mb-3">
                        <label for="TipoEstupefaciente" class="input-group-text fw-bold">TIPO DE ESTUPEFACIENTE:</label>
                        <select id="TipoEstupefaciente" class="form-control" name="TipoEstupefaciente[]" multiple="multiple" required>
                            <?= generateOptions($listas['TipoEstupefaciente'], $estupefacientesSeleccionados); ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="input-group mb-3">
                        <label class="input-group-text fw-bold col-8" for="ConnivenciaPolicial">¿SE MENCIONA CONNIVENCIA POLICIAL?:</label>
                        <select class="form-select text-center col-4" id="ConnivenciaPolicial" name="ConnivenciaPolicial" required>
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="1"<?php if (isset($encabezadoData['ConnivenciaPolicial']) && $encabezadoData['ConnivenciaPolicial'] == 1) echo ' selected'; ?>>SÍ</option>
                            <option value="0"<?php if (!isset($encabezadoData['ConnivenciaPolicial']) || $encabezadoData['ConnivenciaPolicial'] == 0) echo ' selected'; ?>>NO</option>
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="input-group mb-3">
                        <label class="input-group-text fw-bold col-8" for="PosiblesUsurpaciones">¿SE MENCIONAN POSIBLES USURPACIONES?:</label>
                        <select class="form-select text-center col-4" id="PosiblesUsurpaciones" name="PosiblesUsurpaciones" required>
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="1"<?php if (isset($encabezadoData['PosiblesUsurpaciones']) && $encabezadoData['PosiblesUsurpaciones'] == 1) echo ' selected'; ?>>SÍ</option>
                            <option value="0"<?php if (!isset($encabezadoData['PosiblesUsurpaciones']) || $encabezadoData['PosiblesUsurpaciones'] == 0) echo ' selected'; ?>>NO</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="input-group mb-3">
                        <label class="input-group-text fw-bold col-8" for="UsoAF">¿SE MENCIONA EL USO DE ARMAS DE FUEGO?:</label>
                        <select class="form-select text-center col-4" id="UsoAF" name="UsoAF" required>
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="1"<?php if (isset($encabezadoData['UsoAF']) && $encabezadoData['UsoAF'] == 1) echo ' selected'; ?>>SÍ</option>
                            <option value="0"<?php if (!isset($encabezadoData['UsoAF']) || $encabezadoData['UsoAF'] == 0) echo ' selected'; ?>>NO</option>
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="input-group mb-3">
                        <label class="input-group-text fw-bold col-8" for="ParticipacionDeMenores">¿SE MENCIONA LA PARTICIPACIÓN DE MENORES?:</label>
                        <select class="form-select text-center col-4" id="ParticipacionDeMenores" name="ParticipacionDeMenores" required>
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="1"<?php if (isset($encabezadoData['ParticipacionDeMenores']) && $encabezadoData['ParticipacionDeMenores'] == 1) echo ' selected'; ?>>SÍ</option>
                            <option value="0"<?php if (!isset($encabezadoData['ParticipacionDeMenores']) || $encabezadoData['ParticipacionDeMenores'] == 0) echo ' selected'; ?>>NO</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="input-group mb-3">
                        <label class="input-group-text fw-bold col-8" for="Relevancia">RELEVANCIA DE LA INFORMACIÓN:</label>
                        <select class="form-select text-center col-4" id="Relevancia" name="Relevancia" required>
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="BAJA"<?php if (isset($encabezadoData['Relevancia']) && $encabezadoData['Relevancia'] == 'BAJA') echo ' selected'; ?>>BAJA</option>
                            <option value="MEDIA"<?php if (!isset($encabezadoData['Relevancia']) || $encabezadoData['Relevancia'] == 'MEDIA') echo ' selected'; ?>>MEDIA</option>
                            <option value="ALTA"<?php if (!isset($encabezadoData['Relevancia']) || $encabezadoData['Relevancia'] == 'ALTA') echo ' selected'; ?>>ALTA</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="input-group mb-3">
                        <label class="input-group-text fw-bold col-8" for="ParticipacionOrgCrim">¿SE MENCIONA LA PARTICIPACIÓN DE ALGUNA ORGANIZACIÓN CRIMINAL?:</label>
                        <select class="form-select text-center col-4" id="ParticipacionOrgCrim" name="ParticipacionOrgCrim" required>
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="1"<?php if (isset($encabezadoData['ParticipacionOrgCrim']) && $encabezadoData['ParticipacionOrgCrim'] == 1) echo ' selected'; ?>>SÍ</option>
                            <option value="0"<?php if (!isset($encabezadoData['ParticipacionOrgCrim']) || $encabezadoData['ParticipacionOrgCrim'] == 0) echo ' selected'; ?>>NO</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row" id="orgCriminalBlock" style="display: none;">
                <div class="col">
                    <div class="input-group mb-3">
                        <label for="OrganizacionCriminal" class="input-group-text fw-bold"> ORGANIZACIÓN CRIMINAL MENCIONADA:</label>
                        <select id="OrganizacionCriminal" class="form-select text-center" name="OrganizacionCriminal">
                            <option value="" disabled selected>Seleccione la organización criminal</option>
                            <option value="ABREGU" <?= (isset($encabezadoData['OrganizacionCriminal']) && $encabezadoData['OrganizacionCriminal'] == 'ABREGU') ? 'selected' : ''; ?>>ABREGU</option>
                            <option value="ALVARADO" <?= (isset($encabezadoData['OrganizacionCriminal']) && $encabezadoData['OrganizacionCriminal'] == 'ALVARADO') ? 'selected' : ''; ?>>ALVARADO</option>
                            <option value="BRANDON BAY" <?= (isset($encabezadoData['OrganizacionCriminal']) && $encabezadoData['OrganizacionCriminal'] == 'BRANDON BAY') ? 'selected' : ''; ?>>BRANDON BAY</option>
                            <option value="CHUCKY MONEDITA" <?= (isset($encabezadoData['OrganizacionCriminal']) && $encabezadoData['OrganizacionCriminal'] == 'CHUCKY MONEDITA') ? 'selected' : ''; ?>>CHUCKY MONEDITA</option>
                            <option value="COTO MEDRANO" <?= (isset($encabezadoData['OrganizacionCriminal']) && $encabezadoData['OrganizacionCriminal'] == 'COTO MEDRANO') ? 'selected' : ''; ?>>COTO MEDRANO</option>
                            <option value="DREISZIGACKER" <?= (isset($encabezadoData['OrganizacionCriminal']) && $encabezadoData['OrganizacionCriminal'] == 'DREISZIGACKER') ? 'selected' : ''; ?>>DREISZIGACKER</option>
                            <option value="FRAN RIQUELME" <?= (isset($encabezadoData['OrganizacionCriminal']) && $encabezadoData['OrganizacionCriminal'] == 'FRAN RIQUELME') ? 'selected' : ''; ?>>FRAN RIQUELME</option>
                            <option value="FRENTUDO" <?= (isset($encabezadoData['OrganizacionCriminal']) && $encabezadoData['OrganizacionCriminal'] == 'FRENTUDO') ? 'selected' : ''; ?>>FRENTUDO</option>
                            <option value="FUNES" <?= (isset($encabezadoData['OrganizacionCriminal']) && $encabezadoData['OrganizacionCriminal'] == 'FUNES') ? 'selected' : ''; ?>>FUNES</option>
                            <option value="GUILLE CANTERO" <?= (isset($encabezadoData['OrganizacionCriminal']) && $encabezadoData['OrganizacionCriminal'] == 'GUILLE CANTERO') ? 'selected' : ''; ?>>GUILLE CANTERO</option>
                            <option value="LICHI ROMERO" <?= (isset($encabezadoData['OrganizacionCriminal']) && $encabezadoData['OrganizacionCriminal'] == 'LICHI ROMERO') ? 'selected' : ''; ?>>LICHI ROMERO</option>
                            <option value="LOS HORMIGUITAS" <?= (isset($encabezadoData['OrganizacionCriminal']) && $encabezadoData['OrganizacionCriminal'] == 'LOS HORMIGUITAS') ? 'selected' : ''; ?>>LOS HORMIGUITAS</option>
                            <option value="LOS MENORES" <?= (isset($encabezadoData['OrganizacionCriminal']) && $encabezadoData['OrganizacionCriminal'] == 'LOS MENORES') ? 'selected' : ''; ?>>LOS MENORES</option>
                            <option value="MAFILIA" <?= (isset($encabezadoData['OrganizacionCriminal']) && $encabezadoData['OrganizacionCriminal'] == 'MAFILIA') ? 'selected' : ''; ?>>MAFILIA</option>
                            <option value="MAURICIO AYALA" <?= (isset($encabezadoData['OrganizacionCriminal']) && $encabezadoData['OrganizacionCriminal'] == 'MAURICIO AYALA') ? 'selected' : ''; ?>>MAURICIO AYALA</option>
                            <option value="MONOS" <?= (isset($encabezadoData['OrganizacionCriminal']) && $encabezadoData['OrganizacionCriminal'] == 'MONOS') ? 'selected' : ''; ?>>MONOS</option>
                            <option value="MOROCHO MANSILLA" <?= (isset($encabezadoData['OrganizacionCriminal']) && $encabezadoData['OrganizacionCriminal'] == 'MOROCHO MANSILLA') ? 'selected' : ''; ?>>MOROCHO MANSILLA</option>
                            <option value="NOVELINO" <?= (isset($encabezadoData['OrganizacionCriminal']) && $encabezadoData['OrganizacionCriminal'] == 'NOVELINO') ? 'selected' : ''; ?>>NOVELINO</option>
                            <option value="OLGA TATA MEDINA" <?= (isset($encabezadoData['OrganizacionCriminal']) && $encabezadoData['OrganizacionCriminal'] == 'OLGA TATA MEDINA') ? 'selected' : ''; ?>>OLGA TATA MEDINA</option>
                            <option value="PICUDOS" <?= (isset($encabezadoData['OrganizacionCriminal']) && $encabezadoData['OrganizacionCriminal'] == 'PICUDOS') ? 'selected' : ''; ?>>PICUDOS</option>
                            <option value="POLLO VINARDI" <?= (isset($encabezadoData['OrganizacionCriminal']) && $encabezadoData['OrganizacionCriminal'] == 'POLLO VINARDI') ? 'selected' : ''; ?>>POLLO VINARDI</option>
                            <option value="SALTEÑO VILLAZON" <?= (isset($encabezadoData['OrganizacionCriminal']) && $encabezadoData['OrganizacionCriminal'] == 'SALTEÑO VILLAZON') ? 'selected' : ''; ?>>SALTEÑO VILLAZON</option>
                            <option value="TRIPI" <?= (isset($encabezadoData['OrganizacionCriminal']) && $encabezadoData['OrganizacionCriminal'] == 'TRIPI') ? 'selected' : ''; ?>>TRIPI</option>
                            <option value="UNGARO" <?= (isset($encabezadoData['OrganizacionCriminal']) && $encabezadoData['OrganizacionCriminal'] == 'UNGARO') ? 'selected' : ''; ?>>UNGARO</option>
                            <option value="VIEJO CANTERO" <?= (isset($encabezadoData['OrganizacionCriminal']) && $encabezadoData['OrganizacionCriminal'] == 'VIEJO CANTERO') ? 'selected' : ''; ?>>VIEJO CANTERO</option>
                            <option value="OTRA" <?= (isset($encabezadoData['OrganizacionCriminal']) && !in_array($encabezadoData['OrganizacionCriminal'], ['ABREGU', 'ALVARADO', 'BRANDON BAY', 'CHUCKY MONEDITA', 'COTO MEDRANO', 'DREISZIGACKER', 'FRAN RIQUELME', 'FRENTUDO', 'FUNES', 'GUILLE CANTERO', 'LICHI ROMERO', 'LOS HORMIGUITAS', 'LOS MENORES', 'MAFILIA', 'MAURICIO AYALA', 'MONOS', 'MOROCHO MANSILLA', 'NOVELINO', 'OLGA TATA MEDINA', 'PICUDOS', 'POLLO VINARDI', 'SALTEÑO VILLAZON', 'TRIPI', 'UNGARO', 'VIEJO CANTERO'])) ? 'selected' : ''; ?>>OTRA</option>
                        </select>
                    </div>
                </div>
                <div class="col" id="otraOrgCriminalBlock" style="display: none;">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold">ESPECIFIQUE LA ORGANIZACIÓN CRIMINAL:</span>
                        <input type="text" class="form-control" id="OtraOrganizacionCriminal" name="OtraOrganizacionCriminal" maxlength="25" value="<?php echo isset($encabezadoData['OrganizacionCriminal']) && !in_array($encabezadoData['OrganizacionCriminal'], ['ABREGU', 'ALVARADO', 'BRANDON BAY', 'CHUCKY MONEDITA', 'COTO MEDRANO', 'DREISZIGACKER', 'FRAN RIQUELME', 'FRENTUDO', 'FUNES', 'GUILLE CANTERO', 'LICHI ROMERO', 'LOS HORMIGUITAS', 'LOS MENORES', 'MAFILIA', 'MAURICIO AYALA', 'MONOS', 'MOROCHO MANSILLA', 'NOVELINO', 'OLGA TATA MEDINA', 'PICUDOS', 'POLLO VINARDI', 'SALTEÑO VILLAZON', 'TRIPI', 'UNGARO', 'VIEJO CANTERO']) ? $encabezadoData['OrganizacionCriminal'] : ''; ?>" onchange="transformarDatosMayusculas('OtraOrganizacionCriminal')">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="input-group mb-3">
                    <span class="input-group-text fw-bold col-1">RELATO:</span>
                    <textarea id="Relato" class="form-control" name="Relato" rows="10" required><?php echo isset($encabezadoData['Relato']) ? $encabezadoData['Relato'] : ''; ?></textarea>
                </div>
            </div>

            <div class="row">
                <div class="input-group mb-3">
                    <span class="input-group-text fw-bold col-1">VALORACIÓN:</span>
                    <textarea id="Valoracion" class="form-control" name="Valoracion" rows="10"><?php echo isset($encabezadoData['Valoracion']) ? $encabezadoData['Valoracion'] : ''; ?></textarea>
                </div>
            </div>

        </div>

        <div id="DomiciliosRelacionados"><!-- Entidad secundaria - Domicilios -->
        </div>

        <div id="PersonasRelacionadas"><!-- Entidad secundaria - Personas -->
        </div>
    
        <div id="VehiculosRelacionados"><!-- Entidad secundaria - Vehiculos -->
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function initializeSelectize() {
        // Inicializar Selectize en cada campo select con los valores ya presentes
        $('#Tipologia').selectize({
            plugins: ['remove_button'],
            delimiter: ',',
            persist: false,
            create: function(input) {
                return { value: input, text: input };
            }
        });

        $('#ModalidadComisiva').selectize({
            plugins: ['remove_button'],
            delimiter: ',',
            persist: false,
            create: function(input) {
                return { value: input, text: input };
            }
        });

        $('#TipoEstupefaciente').selectize({
            plugins: ['remove_button'],
            delimiter: ',',
            persist: false,
            create: function(input) {
                return { value: input, text: input };
            }
        });
    }

    initializeSelectize();  // Inicializar Selectize una vez cargado el DOM
});
</script>

<script>
$(document).ready(function() {
    function toggleOrgCriminalBlock() {
        if ($('#ParticipacionOrgCrim').val() == '1') {
            $('#orgCriminalBlock').show();
        } else {
            $('#orgCriminalBlock').hide();
            $('#otraOrgCriminalBlock').hide();
            $('#OrganizacionCriminal').val('');
            $('#OtraOrganizacionCriminal').val('');
        }
    }

    function toggleOtraOrgCriminalBlock() {
        if ($('#OrganizacionCriminal').val() == 'OTRA') {
            $('#otraOrgCriminalBlock').show();
        } else {
            $('#otraOrgCriminalBlock').hide();
            $('#OtraOrganizacionCriminal').val('');
        }
    }

    $('#ParticipacionOrgCrim').change(toggleOrgCriminalBlock);
    $('#OrganizacionCriminal').change(toggleOtraOrgCriminalBlock);

    // Initialize on page load
    toggleOrgCriminalBlock();
    toggleOtraOrgCriminalBlock();
});
</script>

</body>
</html>
