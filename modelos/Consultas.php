<?php
    require '../config/conexion.php';

    Class Consultas
    {
        public function __construct()
        {

        }

        public function consultadocumento($idsector)
        {
            $sql = "SELECT 
                        s.nombre as sector,
					CONCAT(td.codigo,'-00',d.iddocumento) AS codigo,
                    d.iddocumento,
                        d.nombre,
						d.descripcion,
						d.vigencia,
						d.condicion
                    FROM
                        documento d
					INNER JOIN sector s
					ON d.idsector = s.idsector
                    INNER JOIN tipo_documento td 
				    ON d.idtipo_documento = td.idtipo_documento
					WHERE 
						s.idsector IN($idsector)
                        AND d.idtipo_documento = 1
					AND
                        d.condicion = 1
					
                    
                    ";
			//echo $sql.'</br>';
            return ejecutarConsulta($sql);
        }
        public function consultadocumentoExcel($idsector)
        {
            $sql = "CALL `prListarDocumentosExcel`('$idsector')
                    ";
			//echo $sql.'</br>';
            return ejecutarConsulta($sql);
        }
        public function consultaimpresiones($iddocumento)
        {
            $sql = "SELECT i.idimpresion,u.nombre as usuario, d.nombre as documento, i.fecha_vigencia, i.num_revision, i.entregado, i.vigente,
            (SELECT COUNT(*) FROM entrega e where e.idimpresion = i.idimpresion and entregado = 1) AS impresiones
            FROM impresion i
            LEFT JOIN usuario u
            ON i.idusuario = u.idusuario
            LEFT JOIN documento d
            ON d.iddocumento = i.iddocumento
            WHERE i.iddocumento = '$iddocumento'
            AND d.condicion = 1
            AND i.vigente = 1
            AND i.idusuario NOT IN (1,2,3,4,5,6)
            ORDER BY i.num_revision DESC
                    ";
			//echo $sql.'</br>';
            return ejecutarConsulta($sql);
        }
        public function consultaentregas($idimpresion)
        {
            $sql = "SELECT e.identrega,e.idimpresion,IFNULL(p.nombre,REPLACE(e.idoperario,'0|','')) as nombre,e.entregado
            FROM  entrega e
            LEFT JOIN personas_actuales p
            ON e.idoperario = p.idpersona
            WHERE e.idimpresion = $idimpresion ";
			//echo $sql.'</br>';
            return ejecutarConsulta($sql);
        }
		
		public function muestradocumentos($iddocumento)
        {
            $sql = "CALL prTraerArchivos('".$iddocumento."')";
			//echo 'Variable sql -> '.$sql.'</br>';
			return ejecutarConsulta($sql);
        }
		
		public function listarDetalle($iddocumento)
        {
            $sql = "CALL prTraerArchivos('".$iddocumento."')";
			//echo 'Variable sql -> '.$sql.'</br>';
			return ejecutarConsulta($sql);
        }
		
		public function totalProcedimiento()
        {
            $sql= "SELECT 
                        IFNULL(COUNT(iddocumento),0) as cantidad_procedimiento
                    FROM
                        documento
                    WHERE
                        idtipo_documento = 1
                        AND condicion = 1";
            
            return ejecutarConsulta($sql);
        }
		
		public function totalInstructivo()
        {
            $sql= "SELECT 
                        IFNULL(COUNT(iddocumento),0) as cantidad_instructivo
                    FROM
                        documento
                    WHERE
                        idtipo_documento = 2
                        AND condicion = 1";
            
            return ejecutarConsulta($sql);
        }
		
		public function procedimientos12meses()
        {$sql = "SET lc_time_names = es_ES";
            ejecutarConsulta($sql);
            $sql= "SELECT 
                        CONCAT(UCASE(LEFT(DATE_FORMAT(fecha_hora,'%M'), 1)), 
                             LCASE(SUBSTRING(DATE_FORMAT(fecha_hora,'%M'), 2))) as fecha,
                        COUNT(iddocumento) as total
                    FROM
                        documento
                    WHERE
						idtipo_documento = 1 
                        AND condicion = 1
					GROUP BY
                        MONTH(fecha_hora) 
                    ORDER BY
                        fecha_hora
                    ASC limit 0,12";
            
            return ejecutarConsulta($sql);
        }
		
		public function instructivos12meses()
        {$sql = "SET lc_time_names = es_ES";
            ejecutarConsulta($sql);

            $sql= "SELECT 
                        CONCAT(UCASE(LEFT(DATE_FORMAT(fecha_hora,'%M'), 1)), 
                             LCASE(SUBSTRING(DATE_FORMAT(fecha_hora,'%M'), 2))) as fecha,
                        COUNT(iddocumento) as total
                    FROM
                        documento
                    WHERE
						idtipo_documento = 2 
                        AND condicion = 1
					GROUP BY
                        MONTH(fecha_hora) 
                    ORDER BY
                        fecha_hora
                    ASC limit 0,12";
            
            return ejecutarConsulta($sql);
        }
       

    }


?>