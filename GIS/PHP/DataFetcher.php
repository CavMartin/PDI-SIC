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
                                            IP_Numero, 
                                            IP_Fecha, 
                                            IP_Hora, 
                                            IP_TipoHecho, 
                                            IP_GrupoHecho, 
                                            IP_Origen, 
                                            IP_Carta911, 
                                            IP_RecursosAsignados, 
                                            IP_ResultadoDeLaIncidencia,
                                            L_Calle, 
                                            L_AlturaCatastral,
                                            L_CalleDetalle, 
                                            L_Interseccion1, 
                                            L_Interseccion2,
                                            L_Localidad,
                                            L_Coordenadas
                                        FROM 
                                            entidad_incidencia_priorizada eip
                                        INNER JOIN 
                                            entidad_lugares el ON IP_Numero = FK_Encabezado;");
        return $lugaresData;
    }

}
?>