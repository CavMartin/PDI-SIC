<?php
    require '../../PHP/ServerConnect.php';
    require 'DataFetcher.php';
    require 'DatabaseManager_Insert.php';
    require 'DatabaseManager_Delete.php';

    class EndPoint_AJAX {
        private $conn;
        private $dataFetcher;
        private $dbManagerInsert;
        private $dbManagerDelete;

        public function __construct() {
            $this->conn = open_database_connection();
            $this->dataFetcher = new DataFetcher($this->conn);
            $this->dbManagerInsert = new DataBaseManager_Insert($this->conn);
            $this->dbManagerDelete = new DataBaseManager_Delete($this->conn);
        }

        public function handleRequest() {
            header('Content-Type: application/json');
            if (!isset($_REQUEST['action'])) {
                echo json_encode(['status' => 'error', 'message' => 'No action specified']);
                return;
            }
        
            $action = $_REQUEST['action'];
        
            switch ($action) {
                case 'insertarDatosReporte':
                    $this->insertarDatosReporte();
                    break;
                case 'getDomicilios':
                    $this->getDomicilios();
                    break;
                case 'eliminarDomicilio':
                    $this->eliminarDomicilio();
                    break;
                case 'getDatosComplementarios':
                    $this->getDatosComplementarios();
                    break;
                case 'eliminarDatoComplementario':
                    $this->eliminarDatoComplementario();
                    break;
                case 'insertarDatosEncabezado':
                    $this->insertarDatosEncabezado();
                    break;
                case 'insertarDatosPersona':
                    $this->insertarDatosPersona();
                    break;
                case 'eliminarPersona':
                    $this->eliminarPersona();
                    break;
                case 'insertarDatosLugar':
                    $this->insertarDatosLugar();
                    break;
                case 'eliminarLugar':
                    $this->eliminarLugar();
                    break;
                case 'insertarDatosVehiculo':
                    $this->insertarDatosVehiculo();
                    break;
                case 'eliminarVehiculo':
                    $this->eliminarVehiculo();
                    break;
                case 'insertarDatosAF':
                    $this->insertarDatosAF();
                    break;
                case 'eliminarAF':
                    $this->eliminarAF();
                    break;
                case 'insertarDatosMensaje':
                    $this->insertarDatosMensaje();
                    break;
                case 'eliminarMensaje':
                    $this->eliminarMensaje();
                    break;
                default:
                    echo json_encode(['status' => 'error', 'message' => 'Unknown action']);
            }
        }

        private function insertarDatosReporte() {
            $formData = $_POST;
            $result = $this->dbManagerInsert->INSERT_Reporte($formData);
            if (is_string($result)) {
                echo $result;
                return;
            }
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Cambios guardados con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al guardar los cambios en el reporte preliminar']);
            }
        }

        private function getDomicilios() {
            $ID_Persona = $_GET['ID_Persona'] ?? '';
            $this->dataFetcher->getDomiciliosJSON($ID_Persona);
        }

        private function eliminarDomicilio() {
            $ID_Lugar = $_GET['ID_Lugar'] ?? '';
            $result = $this->dbManagerDelete->eliminarDomicilio($ID_Lugar);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Domicilio eliminado correctamente']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al eliminar domicilio']);
            }
        }

        private function getDatosComplementarios() {
            $ID_Persona = $_GET['ClavePrimaria'] ?? '';
            $this->dataFetcher->getDatosComplementariosJSON($ID_Persona);
        }

        private function eliminarDatoComplementario() {
            $ID_DatoComplementario = $_GET['ID_DatoComplementario'] ?? '';
            $result = $this->dbManagerDelete->eliminarDatoComplementario($ID_DatoComplementario);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Dato complementario eliminado correctamente']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al eliminar dato complementario']);
            }
        }    

        private function insertarDatosEncabezado() {
            $formData = $_POST;
            $result = $this->dbManagerInsert->INSERT_Encabezado($formData);
            if (is_string($result)) {
                echo $result;
                return;
            }
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Cambios guardados con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al guardar los cambios en el encabezado']);
            }
        }

        private function insertarDatosPersona() {
            $formData = $_POST;
            $result = $this->dbManagerInsert->INSERT_Persona($formData);
            if (is_string($result)) {
                echo $result;
                return;
            }
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Cambios guardados con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al guardar los cambios en el Mensaje']);
            }
        }

        private function eliminarPersona() {
            $ClavePrimaria = $_POST['ClavePrimaria'];
            $result = $this->dbManagerDelete->DELETE_Persona($ClavePrimaria);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Registro eliminado con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el registro']);
            }
        }

        private function insertarDatosLugar() {
            $formData = $_POST;
            $result = $this->dbManagerInsert->INSERT_Lugar($formData);
            if (is_string($result)) {
                echo $result;
                return;
            }
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Cambios guardados con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al guardar los cambios en el Lugar']);
            }
        }

        private function eliminarLugar() {
            $ClavePrimaria = $_POST['ClavePrimaria'];
            $result = $this->dbManagerDelete->DELETE_Lugar($ClavePrimaria);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Registro eliminado con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el registro']);
            }
        }

        private function insertarDatosVehiculo() {
            $formData = $_POST;
            $result = $this->dbManagerInsert->INSERT_Vehiculo($formData);
            if (is_string($result)) {
                echo $result;
                return;
            }
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Cambios guardados con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al guardar los cambios en el Vehiculo']);
            }
        }

        private function eliminarVehiculo() {
            $ClavePrimaria = $_POST['ClavePrimaria'];
            $result = $this->dbManagerDelete->DELETE_Vehiculo($ClavePrimaria);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Registro eliminado con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el registro']);
            }
        }

        private function insertarDatosAF() {
            $formData = $_POST;
            $result = $this->dbManagerInsert->INSERT_Arma($formData);
            if (is_string($result)) {
                echo $result;
                return;
            }
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Cambios guardados con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al guardar los cambios en el arma de fuego']);
            }
        }

        private function eliminarAF() {
            $ClavePrimaria = $_POST['ClavePrimaria'];
            $result = $this->dbManagerDelete->DELETE_AF($ClavePrimaria);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Registro eliminado con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el registro']);
            }
        }

        private function insertarDatosMensaje() {
            $formData = $_POST;
            $result = $this->dbManagerInsert->INSERT_Mensaje($formData);
            if (is_string($result)) {
                echo $result;
                return;
            }
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Cambios guardados con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al guardar los cambios en el Mensaje']);
            }
        }

        private function eliminarMensaje() {
            $ClavePrimaria = $_POST['ClavePrimaria'];
            $result = $this->dbManagerDelete->DELETE_Mensaje($ClavePrimaria);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Registro eliminado con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el registro']);
            }
        }

        public function __destruct() {
            $this->conn->close();
        }
    }

    $ajaxEndpoint = new EndPoint_AJAX();
    $ajaxEndpoint->handleRequest();
?>
