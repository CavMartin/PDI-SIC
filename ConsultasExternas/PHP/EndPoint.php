<?php
    // Esta clase es el punto de entrada para todas las solicitudes AJAX a la DB
    require '../../PHP/ServerConnect.php';
    require 'DataFetcher.php';
    
    class EndPoint { 
        private $conn;
        private $dataFetcher;
    
        public function __construct() {
            $this->conn = open_database_connection('sic_bases_externas');
            $this->dataFetcher = new DataFetcher($this->conn);
        }

        public function handleRequest() {
            header('Content-Type: application/json');
            if (!isset($_REQUEST['action'])) {
                echo json_encode(['status' => 'error', 'message' => 'Sin acción especificada']);
                return;
            }

            $action = $_REQUEST['action'];
        
            switch ($action) {
                case 'fetchDataQuery911': // Caso para obtener datos de la base de datos externa 911
                    $this->fetchDataQuery911();
                    break;
                case 'fetchDataQueryAUOP': // Caso para obtener datos de la base de datos externa AUOP
                    $this->fetchDataQueryAUOP();
                    break;
                default:
                    echo json_encode(['status' => 'error', 'message' => 'Acción especificada desconocida']);
            }
        }

        private function fetchDataQuery911() {
            // Obtener los parámetros de la solicitud
            $params = [
                'FechaDesde' => $_POST['FechaDesde'] ?? '',
                'FechaHasta' => $_POST['FechaHasta'] ?? '',
                'Nota' => $_POST['Nota'] ?? '',
                'Region' => $_POST['Region'] ?? '',
                'Localidad' => $_POST['Localidad'] ?? '',
                'Direccion' => $_POST['Direccion'] ?? '',
                'Tipificacion' => $_POST['Tipificacion'] ?? '',
                'Denunciados' => $_POST['Denunciados'] ?? '',
                'Relato' => $_POST['Relato'] ?? ''
            ];
    
            // Llamar al método fetchDataQuery911 en DataFetcher
            $response = $this->dataFetcher->fetchDataQuery911($params);
    
            // Enviar la respuesta como JSON
            echo json_encode(['status' => 'success', 'data' => $response['pve911']]);
        }

        private function fetchDataQueryAUOP() {
            $params = [
                'FechaDesde' => $_POST['FechaDesde'] ?? '',
                'FechaHasta' => $_POST['FechaHasta'] ?? '',
                'Hora' => $_POST['Hora'] ?? '',
                'OtraDependencia' => $_POST['OtraDependencia'] ?? '',
                'DelitoAUOP' => $_POST['DelitoAUOP'] ?? '',
                'LugardelHecho' => $_POST['LugardelHecho'] ?? '',
                'Barrio' => $_POST['Barrio'] ?? '',
                'Victima' => $_POST['Victima'] ?? '',
                'Imputado' => $_POST['Imputado'] ?? '',
                'RelatoDelHecho' => $_POST['RelatoDelHecho'] ?? ''
            ];

            $response = $this->dataFetcher->fetchDataQueryAUOP($params);

            if ($response !== null) {
                echo json_encode(['status' => 'success', 'data' => $response]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al obtener los datos de la base de datos.']);
            }
        }

        public function __destruct() {
            $this->conn->close();
        }
    }

    // Asegúrate de pasar la configuración de la base de datos al constructor
    $ajaxEndpoint = new EndPoint();
    $ajaxEndpoint->handleRequest();
?>
