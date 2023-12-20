<?php
// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    // Consulta SQL preparada
$sql = "INSERT INTO ficha_de_infractor (Apellido, Nombre, Alias, TipoDocumento, DocumentoNumero, Prontuario, Genero, FechaNacimiento, LugarNacimiento, EstadoCivil, Provincia, Pais, Domicilio)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
ON DUPLICATE KEY UPDATE Apellido = ?, Nombre = ?, Alias = ?, TipoDocumento = ?, DocumentoNumero = ?, Prontuario = ?, Genero = ?, FechaNacimiento = ?, LugarNacimiento = ?, EstadoCivil = ?, Provincia = ?, Pais = ?, Domicilio = ?";

// Preparar la consulta
$stmt = $conn->prepare($sql);

if ($stmt) {
// Vincular parámetros
$stmt->bind_param("ssssssssssssssssssssssssss", $Ficha_Apellido, $Ficha_Nombre, $Ficha_Alias, $Ficha_TipoDNI, $Ficha_DNI, $Ficha_Prontuario, $Ficha_Genero, $Ficha_FechaNacimiento, $Ficha_LugarNacimiento, $Ficha_EstadoCivil, $Ficha_Provincia, $Ficha_Pais, $Ficha_DomiciliosJSON, $Ficha_Apellido, $Ficha_Nombre, $Ficha_Alias, $Ficha_TipoDNI, $Ficha_DNI, $Ficha_Prontuario, $Ficha_Genero, $Ficha_FechaNacimiento, $Ficha_LugarNacimiento, $Ficha_EstadoCivil, $Ficha_Provincia, $Ficha_Pais, $Ficha_DomiciliosJSON);

// Convertir el array de domicilios a JSON
$Ficha_DomiciliosJSON = json_encode($domicilios);

// Ejecutar la consulta
if ($stmt->execute()) {
echo "Registro insertado o actualizado correctamente.";
} else {
echo "Error en la ejecución de la consulta: " . $stmt->error;
}

// Cerrar la consulta preparada
$stmt->close();
} else {
echo "Error en la preparación de la consulta: " . $conn->error;
}

    // Cerrar la conexión a la base de datos
    $conn->close();
}
?>
