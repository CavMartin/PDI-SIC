<?php
class DataFetcher {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    private function fetchData($query) {
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();
        return $data;
    }

    public function ObtenerDatosGIS() {
        $lugaresData = $this->fetchData("SELECT 
                                            ID, 
                                            Fecha, 
                                            Tipo, 
                                            Juzgado, 
                                            Dependencia, 
                                            Causa, 
                                            Relato,
                                            L_Calle, 
                                            L_AlturaCatastral,
                                            L_CalleDetalle, 
                                            L_Interseccion1, 
                                            L_Interseccion2,
                                            L_Localidad,
                                            L_Coordenadas
                                        FROM 
                                            entidad_encabezado
                                        INNER JOIN 
                                            entidad_lugares ON ID = FK_Encabezado;");
        return $lugaresData;
    }

}
?>