<?php
    require '../config/conexion.php';

    Class Documento 
    {
        public function __construct()
        {

        }

        public function insertar($idsector,$idtipo_documento,$idusuario,$codigo,$nombre,$descripcion,$num_revision,$vigencia)
        {global $conexion;
            $sql = "SELECT NOW() AS fechahora";
			//Devuelve fecha actual
			$fecha_hora = ejecutarConsultaSimpleFila($sql);
			
			
			$sql = "INSERT INTO 
                        documento (
                            idsector,
							idtipo_documento,
							idusuario,
							fecha_hora,
                            codigo,
                            nombre,
                            descripcion,
                            num_revision,
                            vigencia,
                            condicion
                        ) 
                    VALUES (
                        '$idsector',
						'$idtipo_documento',
						'$idusuario',
						'$fecha_hora[fechahora]',
                        '$codigo',
                        '$nombre',
                        '$descripcion',
                        '$num_revision',
                        '$vigencia',
                        '1')";
            //echo 'Variable sql -> '.$sql.'</br>';
			$iddocumentonew=ejecutarConsulta_retornarID($sql);
			$sql= "SELECT iddocumento FROM documento 
                   WHERE iddocumento='$iddocumentonew'";
            echo '<script>console.log("'.$conexion->error.'");</script>';
			return ejecutarConsulta($sql);
        }

        public function editar($iddocumento,$idsector,$idtipo_documento,$idusuario,$codigo,$nombre,$descripcion,$num_revision,$vigencia)
        {global $conexion;
            $sw = true;
            $sql = "UPDATE documento SET 
                    idsector ='$idsector',
					idtipo_documento ='$idtipo_documento',
                    idusuario ='$idusuario',
					codigo = '$codigo', 
                    nombre = '$nombre', 
                    descripcion = '$descripcion' ,
                    num_revision = '$num_revision', 
                    vigencia = '$vigencia' 
                    WHERE iddocumento='$iddocumento'";
            //echo 'Variable sql -> '.$sql.'</br>';
            ejecutarConsulta($sql) or $sw = false;
            echo '<script>console.log("'.$conexion->error.'");</script>';
            return $sw;
        }
		
		
        //METODOS PARA ACTIVAR ARTICULOS
        public function desactivar($iddocumento)
        {
            $sql= "UPDATE documento SET condicion='0' 
                   WHERE iddocumento='$iddocumento'";
            
            return ejecutarConsulta($sql);
        }

        public function activar($iddocumento)
        {
            $sql= "UPDATE documento SET condicion='1' 
                   WHERE iddocumento='$iddocumento'";
            
            return ejecutarConsulta($sql);
        }

        //METODO PARA MOSTRAR LOS DATOS DE UN REGISTRO A MODIFICAR
        public function mostrar($iddocumento)
        {
            $sql = "SELECT 
					d.iddocumento,
					d.idusuario,
					d.nombre as nombre,
					d.idsector,
					d.idtipo_documento,
					d.descripcion,
					d.num_revision,
					d.vigencia,
					a.fuente as nombrearchivo,
					a.carpeta,
					d.condicion
					FROM documento d
					LEFT JOIN archivo a
					ON d.iddocumento = a.iddocumento
					WHERE d.iddocumento='$iddocumento'";
			
            return ejecutarConsultaSimpleFila($sql);
        }
		
		public function listarDetalle($iddocumento)
        {
            $sql = "SELECT 
					a.iddocumento,
					a.carpeta,
					a.fuente
					FROM archivo a
					WHERE a.iddocumento='$iddocumento'";
			//echo 'Variable sql -> '.$sql.'</br>';
			return ejecutarConsulta($sql);
        }
		
		public function ver($iddocumento)
        {
            $sql = "SELECT 
					a.iddocumento,
					a.carpeta,
					a.fuente
					FROM archivo a
					WHERE a.iddocumento='$iddocumento'";
			//echo 'Variable sql -> '.$sql.'</br>';
			return ejecutarConsultaSimpleFila($sql);
        }
		
		
        //METODO PARA LISTAR LOS REGISTROS
        public function listar()
        {
            $sql = "SELECT 
                    d.iddocumento, 
                    d.idsector,
					d.fecha_hora as fecha,
                    s.nombre as sector,
                    d.idtipo_documento, 
                    td.descripcion as tipodocumento,
					d.codigo,
                    d.nombre,
                    d.descripcion,
                    d.num_revision,
                    d.vigencia,
                    d.imagen,
                    d.condicion 
                    FROM documento d 
                    INNER JOIN sector s 
                    ON d.idsector = s.idsector
					INNER JOIN tipo_documento td
					ON d.idtipo_documento = td.idtipo_documento";

            return ejecutarConsulta($sql);
        }
				
		public function listarInstructivos($idsector)
		{
			$sql="SELECT 
					d.iddocumento,
					d.codigo,
					d.nombre,
					d.descripcion,
					d.idsector,
					s.nombre as sector,
					d.condicion 
				FROM documento d 
				INNER JOIN sector s
				ON d.idsector=s.idsector
				WHERE NOT d.idtipo_documento=1
				AND d.idsector='$idsector'
                AND d.condicion = 1
				ORDER by d.nombre asc";
			//echo 'Variable sql -> '.$sql.'</br>';	
			return ejecutarConsulta($sql);		
		}
		
		public function listarInstructivosUno($iddocsec)
        {
            $sql = "CALL prParseArrayv2('".$iddocsec."')";
			//echo 'Variable sql -> '.$sql.'</br>';
			return ejecutarConsulta($sql);
        }
		
		
		
		//METODO PARA LISTAR LOS REGISTROS Y MOSTRAR EN EL SELECT
        public function selectPro($idsector)
        {
            $sql = "SELECT * FROM documento 
					WHERE idtipo_documento = 1
                    AND condicion = 1
					AND idsector='$idsector'
                    ORDER BY nombre ASC";

            return ejecutarConsulta($sql);
        }
		
       
		//METODO PARA LISTAR LOS REGISTROS
        public function listarSimple($tipo)
        {
            $sql = "SELECT iddocumento,codigo,nombre, descripcion FROM documento
            WHERE idtipo_documento REGEXP '$tipo'
            AND condicion = 1
            ORDER BY nombre ASC";

            return ejecutarConsulta($sql);
        }
        public function listarRevision($iddocumento)
        {
            $sql = "SELECT num_revision, fecha_vigencia FROM impresion
            WHERE iddocumento = '$iddocumento'
            GROUP BY num_revision";

            return ejecutarConsulta($sql);
        }

    }

?>