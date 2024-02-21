<?php
    require '../../../PHP/ServerConnect.php'; // Conectar a la base de datos
    require 'DataFetcherPDF.php';

    $conn = open_database_connection();

    if (isset($_POST['IP_Numero']) && !empty($_POST['IP_Numero'])) {
        $IP_Numero = $_POST['IP_Numero'];
        $dataFetcherPDF = new DataFetcherPDF($conn);
        $dataFetcherPDF->fetchDataPDF($IP_Numero);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'El número IP es requerido.']);
    }

    $conn->close();
?>