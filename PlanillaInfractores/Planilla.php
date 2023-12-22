<?php
// Conectar a la base de datos de forma segura
require '../ServerConnect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // El usuario no ha iniciado sesión, redirigirlo a la página de inicio de sesión
    header("Location: ../Login.php");
    exit();
}

// Conexión a la base de datos
$conn = open_database_connection();
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Función para insertar o actualizar la ficha de infractor
function insertOrUpdateFichaInfractor($conn, $Ficha_Apellido, $Ficha_Nombre, $Ficha_Alias, $Ficha_TipoDNI, $Ficha_DNI, $Ficha_Prontuario, $Ficha_Genero, $Ficha_FechaNacimiento, $Ficha_LugarNacimiento, $Ficha_EstadoCivil, $Ficha_Provincia, $Ficha_Pais, $Ficha_DomiciliosJSON, $Base64FotoIzquierda, $Base64FotoCentral, $Base64FotoDerecha, $Ficha_FechaHecho, $Ficha_LugarHecho, $Ficha_Causa, $Ficha_Juzgado, $Ficha_Fiscalia, $Ficha_Dependencia, $Ficha_Observaciones, $Ficha_Reseña, $Ficha_DescripcionDelSecuestro) {
    $sql = "INSERT INTO ficha_de_infractor (Apellido, Nombre, Alias, TipoDocumento, DocumentoNumero, Prontuario, Genero, FechaNacimiento, LugarNacimiento, EstadoCivil, Provincia, Pais, Domicilio, FotoIzquierda, FotoCentral, FotoDerecha, FechaHecho, LugarHecho, Causa, Juzgado, Fiscalia, Dependencia, Observaciones, Reseña, Secuestro)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE Apellido = ?, Nombre = ?, Alias = ?, TipoDocumento = ?, DocumentoNumero = ?, Prontuario = ?, Genero = ?, FechaNacimiento = ?, LugarNacimiento = ?, EstadoCivil = ?, Provincia = ?, Pais = ?, Domicilio = ?, FotoIzquierda = ?, FotoCentral = ?, FotoDerecha = ?, FechaHecho = ?, LugarHecho = ?, Causa = ?, Juzgado = ?, Fiscalia = ?, Dependencia = ?, Observaciones = ?, Reseña = ?, Secuestro = ?";

    $Ficha_DomiciliosJSON = json_encode($Ficha_DomiciliosJSON);

    $stmtFicha = $conn->prepare($sql);

    if ($stmtFicha) {
        $stmtFicha->bind_param(str_repeat('s', 50), $Ficha_Apellido, $Ficha_Nombre, $Ficha_Alias, $Ficha_TipoDNI, $Ficha_DNI, $Ficha_Prontuario, $Ficha_Genero, $Ficha_FechaNacimiento, $Ficha_LugarNacimiento, $Ficha_EstadoCivil, $Ficha_Provincia, $Ficha_Pais, $Ficha_DomiciliosJSON, $Base64FotoIzquierda, $Base64FotoCentral, $Base64FotoDerecha, $Ficha_FechaHecho, $Ficha_LugarHecho, $Ficha_Causa, $Ficha_Juzgado, $Ficha_Fiscalia, $Ficha_Dependencia, $Ficha_Observaciones, $Ficha_Reseña, $Ficha_DescripcionDelSecuestro, $Ficha_Apellido, $Ficha_Nombre, $Ficha_Alias, $Ficha_TipoDNI, $Ficha_DNI, $Ficha_Prontuario, $Ficha_Genero, $Ficha_FechaNacimiento, $Ficha_LugarNacimiento, $Ficha_EstadoCivil, $Ficha_Provincia, $Ficha_Pais, $Ficha_DomiciliosJSON, $Base64FotoIzquierda, $Base64FotoCentral, $Base64FotoDerecha, $Ficha_FechaHecho, $Ficha_LugarHecho, $Ficha_Causa, $Ficha_Juzgado, $Ficha_Fiscalia, $Ficha_Dependencia, $Ficha_Observaciones, $Ficha_Reseña, $Ficha_DescripcionDelSecuestro);

        $stmtFicha->execute();
        $stmtFicha->close();
    }
}

// Función para insertar personas relacionadas
function insertPersonasRelacionadas($conn, $infractorID, $relacion, $apellido, $nombre, $alias, $tipoDocumento, $documentoNumero, $prontuario, $genero, $domicilio, $informacionDeInteres) {
    $sqlPersona = "INSERT INTO ficha_personas (InfractorID, Relacion, Apellido, Nombre, Alias, TipoDocumento, DocumentoNumero, Prontuario, Genero, Domicilio, InformacionDeInteres)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmtPersona = $conn->prepare($sqlPersona);

    if ($stmtPersona) {
        $stmtPersona->bind_param("issssssssss", $infractorID, $relacion, $apellido, $nombre, $alias, $tipoDocumento, $documentoNumero, $prontuario, $genero, $domicilio, $informacionDeInteres);

        $stmtPersona->execute();
        $stmtPersona->close();
    }
}

// Función genérica para insertar imágenes
function insertImagen($conn, $infractorID, $tipoImagen, $imagen) {
    $sql = "INSERT INTO imagenes (InfractorID, TipoImagen, Imagen)
            VALUES (?, ?, ?)";

    $stmtImagen = $conn->prepare($sql);

    if ($stmtImagen) {
        $stmtImagen->bind_param("iss", $infractorID, $tipoImagen, $imagen);

        $stmtImagen->execute();
        $stmtImagen->close();
    }
}

// Función para insertar perfiles de redes sociales
function insertRedesSociales($conn, $infractorID, $tipoRedSocial, $redSocialLink) {
    $sqlRedSocial = "INSERT INTO redes_sociales (InfractorID, TipoRedSocial, Link)
                     VALUES (?, ?, ?)";

    $stmtRedSocial = $conn->prepare($sqlRedSocial);

    if ($stmtRedSocial) {
        $stmtRedSocial->bind_param("iss", $infractorID, $tipoRedSocial, $redSocialLink);

        $stmtRedSocial->execute();
        $stmtRedSocial->close();
    }
}

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["POST_Action"]) && $_POST["POST_Action"] == "CargarFormulario") {
    $Ficha_Apellido = $_POST["Ficha_Apellido"];
    $Ficha_Nombre = $_POST["Ficha_Nombre"];
    $Ficha_Alias = $_POST["Ficha_Alias"];
    $Ficha_TipoDNI = $_POST["Ficha_TipoDNI"];
    $Ficha_DNI = $_POST["Ficha_DNI"];
    $Ficha_Prontuario = $_POST["Ficha_Prontuario"];
    $Ficha_Genero = $_POST["Ficha_Genero"];
    $Ficha_FechaNacimiento = $_POST["Ficha_FechaNacimiento"];
    $Ficha_LugarNacimiento = $_POST["Ficha_LugarNacimiento"];
    $Ficha_EstadoCivil = $_POST["Ficha_EstadoCivil"];
    $Ficha_Provincia = $_POST["Ficha_Provincia"];
    $Ficha_Pais = $_POST["Ficha_Pais"];
    $Ficha_DomiciliosJSON = $_POST["Ficha_DomiciliosJSON"];
    $Base64FotoIzquierda = $_POST["Base64FotoIzquierda"];
    $Base64FotoCentral = $_POST["Base64FotoCentral"];
    $Base64FotoDerecha = $_POST["Base64FotoDerecha"];
    $Ficha_FechaHecho = $_POST["Ficha_FechaHecho"];
    $Ficha_LugarHecho = $_POST["Ficha_LugarHecho"];
    $Ficha_Causa = $_POST["Ficha_Causa"];
    $Ficha_Juzgado = $_POST["Ficha_Juzgado"];
    $Ficha_Fiscalia = $_POST["Ficha_Fiscalia"];
    $Ficha_Dependencia = $_POST["Ficha_Dependencia"];
    $Ficha_Observaciones = $_POST["Ficha_Observaciones"];
    $Ficha_Reseña = $_POST["Ficha_Reseña"];
    $Ficha_DescripcionDelSecuestro = $_POST["Ficha_DescripcionDelSecuestro"];

    // Llamar a la función para insertar o actualizar la ficha de infractor
    insertOrUpdateFichaInfractor($conn, $Ficha_Apellido, $Ficha_Nombre, $Ficha_Alias, $Ficha_TipoDNI, $Ficha_DNI, $Ficha_Prontuario, $Ficha_Genero, $Ficha_FechaNacimiento, $Ficha_LugarNacimiento, $Ficha_EstadoCivil, $Ficha_Provincia, $Ficha_Pais, $Ficha_DomiciliosJSON, $Base64FotoIzquierda, $Base64FotoCentral, $Base64FotoDerecha, $Ficha_FechaHecho, $Ficha_LugarHecho, $Ficha_Causa, $Ficha_Juzgado, $Ficha_Fiscalia, $Ficha_Dependencia, $Ficha_Observaciones, $Ficha_Reseña, $Ficha_DescripcionDelSecuestro);

    // Obtener el ID del infractor insertado o actualizado
    $infractorID = $conn->insert_id;

    // Obtener el contador de personas relacionadas
    $contadorPersonas = intval($_POST["contadorPersonas"]);

    // Itera a través de las personas relacionadas
    for ($i = 1; $i <= $contadorPersonas; $i++) {
        $relacion = $_POST["PR_Relacion" . $i];
        $apellido = $_POST["PR_Apellido" . $i];
        $nombre = $_POST["PR_Nombre" . $i];
        $alias = $_POST["PR_Alias" . $i];
        $tipoDocumento = $_POST["PR_TipoDNI" . $i];
        $documentoNumero = $_POST["PR_DNI" . $i];
        $prontuario = $_POST["PR_Prontuario" . $i];
        $genero = $_POST["PR_Genero" . $i];
        $domicilio = $_POST["PR_Domicilio" . $i];
        $informacionDeInteres = $_POST["PR_InformacionDeInteres" . $i];

        // Llamar a la función para insertar personas relacionadas
        insertPersonasRelacionadas($conn, $infractorID, $relacion, $apellido, $nombre, $alias, $tipoDocumento, $documentoNumero, $prontuario, $genero, $domicilio, $informacionDeInteres);
    }

    // Obtener el contador de personas relacionadas
    $contadorSeña = intval($_POST["contadorSeña"]);

    // Llamar a la función para insertar señas particulares
    for ($i = 1; $i <= $contadorSeña; $i++) {
        $tipoSeña = $_POST["TipoSeña" . $i];
        $imagenSeña = $_POST["Base64ImagenSeña" . $i];

        insertImagen($conn, $infractorID, $tipoSeña, $imagenSeña);
    }

    // Obtener el contador de personas relacionadas
    $contadorFotografia = intval($_POST["contadorFotografia"]);

    // Llamar a la función para insertar fotografías
    for ($i = 1; $i <= $contadorFotografia; $i++) {
        $tipoFotografia = $_POST["TipoFotografia" . $i];
        $imagenFotografia = $_POST["Base64ImagenFotografia" . $i];

        insertImagen($conn, $infractorID, $tipoFotografia, $imagenFotografia);
    }

    // Obtener el contador de redes sociales
    $contadorRedSocial = intval($_POST["contadorRedSocial"]);

    // Iterar a través de las redes sociales
    for ($i = 1; $i <= $contadorRedSocial; $i++) {
        $tipoRedSocial = $_POST["TipoRedSocial" . $i];
        $redSocialLink = $_POST["RedSocialLink" . $i];

        // Llamar a la función para insertar perfiles de redes sociales
        insertRedesSociales($conn, $infractorID, $tipoRedSocial, $redSocialLink);
    }

    // Al final de todo el procesamiento
    header("Location: ../Main.php");
    exit(); // Asegura que la redirección se efectúe de inmediato
}

// Cierra la conexión a la base de datos
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha de infractor</title>
    <link rel="stylesheet" type="text/css" href="../CSS/styles.css">
    <link rel="icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
    <link rel="shortcut icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
    <script src="../Scripts/TransformarDatos.js"></script>
    <script src="../Scripts/ManejarImagenes.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
</head>

<body class="BodyFondo1">

<div class="Formulario">
<form id="FichaInfraccion" name="FichaInfraccion" action="Planilla.php" enctype="multipart/form-data" method="post" onsubmit="return confirm('¿Está seguro que desea enviar este formulario?')">

<!-- Campos ocultos, necesarios para el funcionamiento de la aplicacion -->
<input type="hidden" id="POST_Action" name="POST_Action" value="CargarFormulario" readonly>

    <h1><u>Ficha de infractor - Ley 23.737</u></h1>

<div class="horizontal-container">
    <div class="Div3XLine">
    <label for="Ficha_Apellido">Apellido/s:</label>
    <input type="text" id="Ficha_Apellido" name="Ficha_Apellido" maxlength="50" onchange="transformarDatosMayusculas('Ficha_Apellido')">
    </div>

    <div class="Div3XLine">
    <label for="Ficha_Nombre">Nombre/s:</label>
    <input type="text" id="Ficha_Nombre" name="Ficha_Nombre" maxlength="50" onchange="transformarDatosNompropio('Ficha_Nombre')">
    </div>

    <div class="Div3XLine">
    <label for="Ficha_Alias">Alias / Apodo:</label>
    <input type="text" id="Ficha_Alias" name="Ficha_Alias" maxlength="50" onchange="transformarDatosNompropio('Ficha_Alias')">
    </div>
</div>

<div class="horizontal-container">
    <div class="Div3XLine">
    <label for="Ficha_TipoDNI">Tipo de documento:</label>
    <select id="Ficha_TipoDNI" style="text-align: center;" name="Ficha_TipoDNI" required>
    <option disabled selected>Selecciona una opción</option>
      <option value="D.N.I." selected>D.N.I.</option>
      <option value="L.E.">L.E.</option>
      <option value="L.C">L.C.</option>
      <option value="Pasaporte">Pasaporte</option>
    </select>
    </div>

<div class="Div3XLine">
    <label for="Ficha_DNI">Número de documento:</label>
    <input type="text" id="Ficha_DNI" name="Ficha_DNI" maxlength="10" onchange="transformarDatosNumerico('Ficha_DNI')">
    </div>

    <div class="Div3XLine">
    <label for="Ficha_Prontuario">Prontuario:</label>
    <input type="text" id="Ficha_Prontuario" name="Ficha_Prontuario" maxlength="50">
    </div>
</div>

<div class="horizontal-container">
    <div class="Div3XLine">
    <label for="Ficha_Genero">Género:</label>
    <select id="Ficha_Genero" style="text-align: center;" name="Ficha_Genero" required>
        <option disabled selected>Selecciona el género</option>
        <option value="Varón" selected>Varón</option>
        <option value="Mujer">Mujer</option>
        <option value="Otro">Otro</option>
        <option value="Desconocido">Desconocido</option>
    </select>
    </div>

    <div class="Div3XLine">
    <label for="Ficha_FechaNacimiento" >Fecha de nacimiento:</label>
    <input type="date" id="Ficha_FechaNacimiento" name="Ficha_FechaNacimiento" style="text-align: center; margin-right: 0.5vw;" required>
    </div>

    <div class="Div3XLine">
    <label for="Ficha_LugarNacimiento">Lugar de nacimiento:</label>
    <input type="text" id="Ficha_LugarNacimiento" name="Ficha_LugarNacimiento" maxlength="100">
    </div>
</div>

<div class="horizontal-container">
    <div class="Div3XLine">
    <label for="Ficha_EstadoCivil">Estado civil:</label>
    <select id="Ficha_EstadoCivil" style="text-align: center;" name="Ficha_EstadoCivil" required>
    <option disabled selected>Selecciona una opción</option>
        <option value="Sin datos" selected>Sin datos</option>
        <option value="Casada/o">Casada/o</option>
        <option value="Concubinato">Concubinato</option>
        <option value="Conviviente">Conviviente</option>
        <option value="Divorciada/o">Divorciada/o</option>
        <option value="Soltera/o">Soltera/o</option>
        <option value="Unión civil">Unión civil</option>
        <option value="Viuda/o">Viuda/o</option>
    </select>
    </div>

    <div class="Div3XLine">
    <label for="Ficha_Provincia">Provincia:</label>
    <input type="text" id="Ficha_Provincia" name="Ficha_Provincia" value="SANTA FE"  maxlength="50">
    </div>

    <div class="Div3XLine">
    <label for="Ficha_Pais">País de origen:</label>
    <input type="text" id="Ficha_Pais" name="Ficha_Pais" value="ARGENTINA"  maxlength="50">
    </div>
</div>

<div id="DomiciliosContainer">
<input type="hidden" id="Ficha_DomiciliosJSON" name="Ficha_DomiciliosJSON">
    <!-- Aquí se crearán los Domicilios dinámicamente -->
</div>

<button type="button" id="agregarDomicilio" class="CustomButton B_Lugares">Agregar Domicilio</button>
<button type="button" id="eliminarUltimoDomicilio" class="CustomButton B_Lugares">Eliminar Domicilio</button>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var domicilios = []; // Inicializamos un array para almacenar los domicilios

    var botonAgregarDomicilio = document.getElementById("agregarDomicilio");
    var botonEliminarUltimoDomicilio = document.getElementById("eliminarUltimoDomicilio");
    var contenedorDomicilios = document.getElementById("DomiciliosContainer");
    var Ficha_DomiciliosJSON = document.getElementById("Ficha_DomiciliosJSON");

    function actualizarJSON() {
        // Modificar la propiedad "domicilio" a "Domicilio" antes de convertir a JSON
        var domiciliosModificados = domicilios.map(function(domicilio) {
            return { id: domicilio.id, Domicilio: domicilio.domicilio.charAt(0).toUpperCase() + domicilio.domicilio.slice(1) };
        });
        Ficha_DomiciliosJSON.value = JSON.stringify(domiciliosModificados);
    }

    botonAgregarDomicilio.addEventListener("click", function() {
        var nuevoDomicilio = { id: domicilios.length + 1, domicilio: "" };

        var nuevoDomicilioContainer = document.createElement("div");
        nuevoDomicilioContainer.innerHTML = `
            <div>
                <label for="Ficha_Domicilio${nuevoDomicilio.id}">Domicilio ${nuevoDomicilio.id}:</label>
                <input type="text" id="Ficha_Domicilio${nuevoDomicilio.id}" name="Ficha_Domicilio${nuevoDomicilio.id}" maxlength="100">
            </div>
        `;

        // Agregar el nuevo domicilio al formulario
        contenedorDomicilios.appendChild(nuevoDomicilioContainer);

        // Actualizar el JSON
        actualizarJSON();

        // Manejar cambios en el campo de domicilio
        var nuevoValorDomicilio = document.getElementById(`Ficha_Domicilio${nuevoDomicilio.id}`);
        nuevoValorDomicilio.addEventListener("input", function() {
            nuevoDomicilio.domicilio = this.value;
            actualizarJSON();
        });

        // Agregar el nuevo domicilio al array
        domicilios.push(nuevoDomicilio);
    });

    botonEliminarUltimoDomicilio.addEventListener("click", function() {
        if (domicilios.length > 0) {
            // Solo eliminar si hay domicilios creados
            var ultimoDomicilioContainer = contenedorDomicilios.lastChild;
            contenedorDomicilios.removeChild(ultimoDomicilioContainer);

            // Eliminar el último domicilio del array
            domicilios.pop();

            // Actualizar el JSON
            actualizarJSON();
        }
    });

    // Llama a la función para agregar el primer domicilio al cargar la página
    botonAgregarDomicilio.click();
});
</script>

<div class="horizontal-container">
    <div class="Div3XLine">
    <label for="FotoIzquierda">Identificación izquierda:</label>
    <input type="file" id="FotoIzquierda" name="FotoIzquierda" accept="image/*" onchange="procesarImagen(event, 'previewFotoIzquierda', 'Base64FotoIzquierda')">
    <img id="previewFotoIzquierda" class="preview2" src="" alt="Previsualización de imagen">
    <textarea id="Base64FotoIzquierda" name="Base64FotoIzquierda" hidden></textarea>
    </div>

    <div class="Div3XLine">
    <label for="FotoCentral">Identificación central:</label>
    <input type="file" id="FotoCentral" name="FotoCentral" accept="image/*" onchange="procesarImagen(event, 'previewFotoCentral', 'Base64FotoCentral')">
    <img id="previewFotoCentral" class="preview2" src="" alt="Previsualización de imagen">
    <textarea id="Base64FotoCentral" name="Base64FotoCentral" hidden></textarea>
    </div>

    <div class="Div3XLine">
    <label for="FotoDerecha">Identificación derecha:</label>
    <input type="file" id="FotoDerecha" name="FotoDerecha" accept="image/*" onchange="procesarImagen(event, 'previewFotoDerecha', 'Base64FotoDerecha')">
    <img id="previewFotoDerecha" class="preview2" src="" alt="Previsualización de imagen">
    <textarea id="Base64FotoDerecha" name="Base64FotoDerecha" hidden></textarea>
    </div>
</div>

<h2 style="text-align: left;">Detalles de la infracción</h2>

<div class="horizontal-container">
    <div style="width: 30%;">
    <label for="Ficha_FechaHecho" style="text-align: center;">Fecha del hecho:</label>
    <input type="date" id="Ficha_FechaHecho" name="Ficha_FechaHecho" style="text-align: center; margin-right: 0.5vw;">
    </div>

    <div style="width: 65%;">
    <label for="Ficha_LugarHecho">Lugar del procedimiento:</label>
    <input type="text" id="Ficha_LugarHecho" name="Ficha_LugarHecho"  maxlength="100">
    </div>
</div>

<div>
    <label for="Ficha_Causa">Causa:</label>
    <input type="text" id="Ficha_Causa" name="Ficha_Causa">
</div>

<div class="horizontal-container">
    <div class="Div3XLine">
    <label for="Ficha_Juzgado">Juzgado:</label>
    <input type="text" id="Ficha_Juzgado" name="Ficha_Juzgado">
    </div>

    <div class="Div3XLine">
    <label for="Ficha_Fiscalia">Fiscalía:</label>
    <input type="text" id="Ficha_Fiscalia" name="Ficha_Fiscalia">
    </div>

    <div class="Div3XLine">
    <label for="Ficha_Dependencia">Dependencia:</label>
    <input type="text" id="Ficha_Dependencia" name="Ficha_Dependencia">
    </div>
</div>

<div><!-- Observaciones -->
    <label for="Ficha_Observaciones">Observaciones:</label>
    <textarea type="text" id="Ficha_Observaciones" name="Ficha_Observaciones"></textarea>
</div>

<div><!-- Breve reseña del hecho -->
    <label for="Ficha_Reseña">Breve reseña del hecho:</label>
    <textarea type="text" id="Ficha_Reseña" name="Ficha_Reseña"></textarea>
</div>

<div><!-- Descripcion del secuestro -->
    <label for="Ficha_DescripcionDelSecuestro">Descripción del secuestro:</label>
    <textarea type="text" id="Ficha_DescripcionDelSecuestro" name="Ficha_DescripcionDelSecuestro"></textarea>
</div>

<h2><u>Información complementaria del infractor</u></h2><!-- Aqui comienza la información complementaria -->

<div><!-- Señas particulares del infractor -->
    <div id="SeñasParticularesContainer">
    <h3>* Señas particulares</h3>
    <input type="hidden" id="contadorSeña" name="contadorSeña" value="0" readonly>
    <!-- Aquí se crearán las señas particulares dinámicamente -->
    </div>

    <div class="button-container">
    <button type="button" id="agregarSeña" class="CustomButton B_Fuentes">Agregar Seña</button>
    <button type="button" id="eliminarSeña" class="CustomButton B_Fuentes">Eliminar Seña</button>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        var contadorSeña = 0; // Inicializamos el contador en 0

        var botonAgregarSeña = document.getElementById("agregarSeña");
        var botonEliminarSeña = document.getElementById("eliminarSeña");
        var contenedorSeñasParticulares = document.getElementById("SeñasParticularesContainer");

        botonAgregarSeña.addEventListener("click", function() {
            contadorSeña++; // Aumentamos el contador
            var nuevaSeñaContainer = document.createElement("div");
            nuevaSeñaContainer.innerHTML = `
                <input type="hidden" id="Seña_NumeroDeOrden${contadorSeña}" name="Seña_NumeroDeOrden" value="${contadorSeña}" readonly>
                <div style="width: 30%;">
                    <label for="TipoSeña${contadorSeña}">Tipo de Seña #${contadorSeña}:</label>
                    <select id="TipoSeña${contadorSeña}" style="text-align: center;" name="TipoSeña${contadorSeña}" required>
                        <option disabled selected>Selecciona una opción</option>
                        <option value="Seña particular - Tatuaje">Tatuaje</option>
                        <option value="Seña particular - Cicatriz">Cicatriz</option>
                        <option value="Seña particular - Otras">Otras</option>
                    </select>

                    <label for="ImagenSeña${contadorSeña}">Imagen de la seña particular:</label>
                    <input type="file" id="ImagenSeña${contadorSeña}" name="ImagenSeña${contadorSeña}" accept="image/*" onchange="procesarImagen(event, 'previewImagenSeña${contadorSeña}', 'Base64ImagenSeña${contadorSeña}')">
                    <img id="previewImagenSeña${contadorSeña}" class="preview2" src="" alt="Previsualización de imagen">
                    <textarea id="Base64ImagenSeña${contadorSeña}" name="Base64ImagenSeña${contadorSeña}" hidden></textarea>
                </div>
            `;

            // Agregar la nueva señal particular al formulario
            contenedorSeñasParticulares.appendChild(nuevaSeñaContainer);

            // Actualizar el valor del campo oculto contadorSeña
            document.getElementById("contadorSeña").value = contadorSeña;
        });

        botonEliminarSeña.addEventListener("click", function() {
            if (contadorSeña > 0) {
                // Solo eliminar si hay más de una señal particular creada
                var ultimaSeñaContainer = document.querySelector("#SeñasParticularesContainer > div:last-child");
                contenedorSeñasParticulares.removeChild(ultimaSeñaContainer);
                contadorSeña--;

                // Actualizar el valor del campo oculto contadorSeña
                document.getElementById("contadorSeña").value = contadorSeña;
            }
        });
    });
    </script>
</div>

<div><!-- Aqui comienzan las fotografias -->
    <div id="FotografiasContainer">
    <h3>* Registros fotografícos</h3>
    <input type="hidden" id="contadorFotografia" name="contadorFotografia" value="0" readonly>
    <!-- Aquí se crearán las fotografías dinámicamente -->
    </div>

    <div class="button-container">
    <button type="button" id="agregarFotografia" class="CustomButton B_Fotografias">Agregar Fotografía</button>
    <button type="button" id="eliminarFotografia" class="CustomButton B_Fotografias">Eliminar Fotografía</button>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        var contadorFotografia = 0; // Inicializamos el contador en 0

        var botonAgregarFotografia = document.getElementById("agregarFotografia");
        var botonEliminarFotografia = document.getElementById("eliminarFotografia");
        var contenedorFotografias = document.getElementById("FotografiasContainer");

        botonAgregarFotografia.addEventListener("click", function() {
            contadorFotografia++; // Aumentamos el contador
            var nuevaFotografiaContainer = document.createElement("div");
            nuevaFotografiaContainer.innerHTML = `
                <input type="hidden" id="Fotografia_NumeroDeOrden${contadorFotografia}" name="Fotografia_NumeroDeOrden" value="${contadorFotografia}" readonly>
                <div style="width: 30%;">
                    <label for="TipoFotografia${contadorFotografia}">Tipo de Fotografía #${contadorFotografia}:</label>
                    <select id="TipoFotografia${contadorFotografia}" style="text-align: center;" name="TipoFotografia${contadorFotografia}" required>
                        <option disabled selected>Selecciona una opción</option>
                        <option value="Fotografias de la investigación">Fotografias de la investigación</option>
                        <option value="Fotografias del secuestro">Fotografias del secuestro</option>
                        <option value="Otras fotografías">Otras fotografías</option>
                    </select>

                    <label for="ImagenFotografia${contadorFotografia}">Imagen de la fotografía:</label>
                    <input type="file" id="ImagenFotografia${contadorFotografia}" name="ImagenFotografia${contadorFotografia}" accept="image/*" onchange="procesarImagen(event, 'previewImagenFotografia${contadorFotografia}', 'Base64ImagenFotografia${contadorFotografia}')">
                    <img id="previewImagenFotografia${contadorFotografia}" class="preview2" src="" alt="Previsualización de imagen">
                    <textarea id="Base64ImagenFotografia${contadorFotografia}" name="Base64ImagenFotografia${contadorFotografia}" hidden></textarea>
                </div>
            `;

            // Agregar la nueva fotografía al formulario
            contenedorFotografias.appendChild(nuevaFotografiaContainer);

            // Actualizar el valor del campo oculto contadorFotografia
            document.getElementById("contadorFotografia").value = contadorFotografia;
        });

        botonEliminarFotografia.addEventListener("click", function() {
            if (contadorFotografia > 0) {
                // Solo eliminar si hay más de una fotografía creada
                var ultimaFotografiaContainer = document.querySelector("#FotografiasContainer > div:last-child");
                contenedorFotografias.removeChild(ultimaFotografiaContainer);
                contadorFotografia--;

                // Actualizar el valor del campo oculto contadorFotografia
                document.getElementById("contadorFotografia").value = contadorFotografia;
            }
        });
    });
    </script>
</div>

<div><!-- Aqui comienzan las redes sociales -->
    <div id="RedesSocialesContainer">
    <h3>* Perfiles de redes sociales</h3>
    <input type="hidden" id="contadorRedSocial" name="contadorRedSocial" value="0" readonly>
    <!-- Aquí se crearán los perfiles de redes sociales dinámicamente -->
    </div>

    <div class="button-container">
    <button type="button" id="agregarRedSocial" class="CustomButton B_Fuentes">Agregar Red Social</button>
    <button type="button" id="eliminarRedSocial" class="CustomButton B_Fuentes">Eliminar Red Social</button>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        var contadorRedSocial = 0; // Inicializamos el contador en 0

        var botonAgregarRedSocial = document.getElementById("agregarRedSocial");
        var botonEliminarRedSocial = document.getElementById("eliminarRedSocial");
        var contenedorRedesSociales = document.getElementById("RedesSocialesContainer");

        botonAgregarRedSocial.addEventListener("click", function() {
            contadorRedSocial++; // Aumentamos el contador
            var nuevaRedSocialContainer = document.createElement("div");
            nuevaRedSocialContainer.innerHTML = `
                <input type="hidden" id="RedSocial_NumeroDeOrden${contadorRedSocial}" name="RedSocial_NumeroDeOrden" value="${contadorRedSocial}" readonly>
                <div class="horizontal-container">
                    <div style="width: 30%;">
                        <label for="RedSocialTipo${contadorRedSocial}">Red social #${contadorRedSocial}:</label>
                        <select id="RedSocialTipo${contadorRedSocial}" style="text-align: center;" name="RedSocialTipo${contadorRedSocial}" required>
                            <option disabled selected>Selecciona una opción</option>
                            <option value="Facebook">Facebook</option>
                            <option value="Instagram">Instagram</option>
                            <option value="Twitter">Twitter</option>
                            <option value="TikTok">TikTok</option>
                            <option value="Linkedin">Linkedin</option>
                            <option value="Otros">Otros</option>
                        </select>
                    </div>
                    <div style="width: 65%;">
                        <label for="RedSocialLink${contadorRedSocial}">Link:</label>
                        <input type="text" id="RedSocialLink${contadorRedSocial}" name="RedSocialLink${contadorRedSocial}" maxlength="250">
                    </div>
                </div>
            `;

            // Agregar la nueva red social al formulario
            contenedorRedesSociales.appendChild(nuevaRedSocialContainer);

            // Actualizar el valor del campo oculto contadorRedSocial
            document.getElementById("contadorRedSocial").value = contadorRedSocial;
        });

        botonEliminarRedSocial.addEventListener("click", function() {
            if (contadorRedSocial > 0) {
                // Solo eliminar si hay más de una red social creada
                var ultimaRedSocialContainer = document.querySelector("#RedesSocialesContainer > div:last-child");
                contenedorRedesSociales.removeChild(ultimaRedSocialContainer);
                contadorRedSocial--;

                // Actualizar el valor del campo oculto contadorRedSocial
                document.getElementById("contadorRedSocial").value = contadorRedSocial;
            }
        });
    });
    </script>
</div>

<div><!-- Personas relacionadas al infractor -->
    <div class="PersonasContainer" id="PersonasContainer">
    <h3>* Personas relacionadas al infractor</h3>
    <input type="hidden" id="contadorPersonas" name="contadorPersonas" value="0" readonly>
    </div>

    <div class="button-container">
    <!-- Botón para agregar una nueva instancia de persona -->
    <button type="button" id="agregarPersona" class="CustomButton B_Personas">Agregar persona</button>
    <button type="button" id="eliminarUltimaPersona" class="CustomButton B_Personas">Quitar persona</button>
    </div>

    <script>
    // Contador para llevar un registro de las personas agregadas
    let contadorPersonas = 0;

    // Función para agregar una nueva instancia de persona
    function agregarPersona() {
    contadorPersonas++;

    const nuevaPersona = document.createElement("div");
    nuevaPersona.innerHTML = `
        <h2 style="text-align: center;">Persona #${contadorPersonas}</h2>

        <input type="hidden" id="Persona_NumeroDeOrden${contadorPersonas}" name="Persona_NumeroDeOrden${contadorPersonas}" value="${contadorPersonas}" readonly>

        <div>
            <label for="PR_Relacion${contadorPersonas}">Relación:</label>
            <input type="text" id="PR_Relacion${contadorPersonas}" name="PR_Relacion${contadorPersonas}" maxlength="100">
        </div>

        <div class="horizontal-container">
            <div class="Div3XLine">
                <label for="PR_Apellido${contadorPersonas}">Apellido/s:</label>
                <input type="text" id="PR_Apellido${contadorPersonas}" name="PR_Apellido${contadorPersonas}" maxlength="50" onchange="transformarDatosMayusculas('Apellido${contadorPersonas}')">
            </div>

            <div class="Div3XLine">
                <label for="PR_Nombre${contadorPersonas}">Nombre/s:</label>
                <input type="text" id="PR_Nombre${contadorPersonas}" name="PR_Nombre${contadorPersonas}" maxlength="50" onchange="transformarDatosNompropio('Nombre${contadorPersonas}')">
            </div>

            <div class="Div3XLine">
                <label for="PR_Alias${contadorPersonas}">Alias / Apodo:</label>
                <input type="text" id="PR_Alias${contadorPersonas}" name="PR_Alias${contadorPersonas}" maxlength="50" onchange="transformarDatosNompropio('Alias${contadorPersonas}')">
            </div>
        </div>

        <div class="horizontal-container">
            <div class="Div3XLine">
                <label for="PR_TipoDNI${contadorPersonas}">Tipo de documento:</label>
                <select id="PR_TipoDNI${contadorPersonas}" style="text-align: center;" name="PR_TipoDNI${contadorPersonas}" required>
                    <option disabled selected>Selecciona una opción</option>
                    <option value="D.N.I." selected>D.N.I.</option>
                    <option value="L.E.">L.E.</option>
                    <option value="L.C">L.C</option>
                    <option value="Pasaporte">Pasaporte</option>
                </select>
            </div>

            <div class="Div3XLine">
                <label for="PR_DNI${contadorPersonas}">Número de documento:</label>
                <input type="text" id="PR_DNI${contadorPersonas}" name="PR_DNI${contadorPersonas}" maxlength="10" onchange="transformarDatosNumerico('DNI${contadorPersonas}')">
            </div>

            <div class="Div3XLine">
                <label for="PR_Prontuario${contadorPersonas}">Prontuario:</label>
                <input type="text" id="PR_Prontuario${contadorPersonas}" name="PR_Prontuario${contadorPersonas}" maxlength="50">
            </div>
        </div>

        <div class="horizontal-container">
            <div style="width: 30%;">
                <label for="PR_Genero${contadorPersonas}">Género:</label>
                <select id="PR_Genero${contadorPersonas}" style="text-align: center;" name="PR_Genero${contadorPersonas}" required>
                    <option disabled selected>Selecciona el género</option>
                    <option value="Varón">Varón</option>
                    <option value="Mujer">Mujer</option>
                    <option value="Otro">Otro</option>
                    <option value="Desconocido">Desconocido</option>
                </select>
            </div>

            <div style="width: 65%;">
                <label for="PR_Domicilio${contadorPersonas}">Domicilio:</label>
                <input type="text" id="PR_Domicilio${contadorPersonas}" name="PR_Domicilio${contadorPersonas}" maxlength="100">
            </div>
        </div>

        <label for="PR_InformacionDeInteres${contadorPersonas}">Información de interés:</label>
        <textarea type="text" id="PR_InformacionDeInteres${contadorPersonas}" name="PR_InformacionDeInteres${contadorPersonas}"></textarea>
    `;

    // Agrega la nueva instancia de persona al contenedor
    document.getElementById("PersonasContainer").appendChild(nuevaPersona);

    // Actualiza el valor del campo oculto contadorPersonas
    document.getElementById("contadorPersonas").value = contadorPersonas;
    }

    // Agrega un evento click al botón "Agregar Persona"
    document.getElementById("agregarPersona").addEventListener("click", agregarPersona);

    // Función para eliminar la última instancia de persona
    function eliminarUltimaPersona() {
    if (contadorPersonas > 0) {
        // Obtiene el contenedor de personas
        const personasContainer = document.getElementById("PersonasContainer");

        // Obtiene la última instancia de persona
        const ultimaPersona = personasContainer.lastElementChild;

        // Elimina la última instancia de persona
        personasContainer.removeChild(ultimaPersona);

        // Reduce el contador
        contadorPersonas--;

        // Actualiza el valor del campo oculto contadorPersonas
        document.getElementById("contadorPersonas").value = contadorPersonas;
    }
    }

    // Agrega un evento click al botón "Eliminar Última Persona"
    document.getElementById("eliminarUltimaPersona").addEventListener("click", eliminarUltimaPersona);
    </script>
</div>

<button type="submit" class="CustomButton Submit">Guardar cambios</button>

</form>
</div>

<button type="button" id="Volver" class="CustomButton Volver">Volver</button>

<!-- Aside right -->
<aside class="Main_Aside">
    <div class="Main_Aside_LOGO">
        <img src="../CSS/Images/PSF.png" alt="">
    </div>
    <div class="Main_Aside_LOGO">
        <img src="../CSS/Images/PDI.png" alt="">
    </div>
</aside>

<script>
    // Agrega un evento click al botón "Volver"
    document.getElementById("Volver").addEventListener("click", function() {
        // Muestra una alerta para confirmar la acción
        if (confirm('¿Estás seguro de que quieres volver a la página principal? Los cambios efectuados en el formulario no se guardarán.')) {
            // Redirecciona a la página principal si el usuario confirma
            window.location.href = '../Main.php';
        }
    });
</script>

</body>
</html>
