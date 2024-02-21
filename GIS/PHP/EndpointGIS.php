<?php
    require '../../PHP/ServerConnect.php';
    require 'DataFetcher.php';

    class EndpointGIS {
        private $conn;
        private $dataFetcher;

        public function __construct() {
            // Establecer la conexión a la base de datos
            $this->conn = open_database_connection();
            if ($this->conn->connect_error) {
                die("Error de conexión: " . $this->conn->connect_error);
            }

            // Crear una instancia de DataFetcher
            $this->dataFetcher = new DataFetcher($this->conn);
        }

        public function obtenerDatosGIS() {
            $datos = $this->dataFetcher->ObtenerDatosGIS();

            // Devolver los datos en formato JSON
            header('Content-Type: application/json');
            echo json_encode($datos);
        }

        public function __destruct() {
            // Cerrar la conexión a la base de datos
            $this->conn->close();
        }
    }

    // Instanciar y usar la clase EndpointGIS
    $EndpointGIS = new EndpointGIS();
    $EndpointGIS->obtenerDatosGIS();
?>