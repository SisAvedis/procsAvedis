<?php
    
    require_once '../modelos/Consultas.php';

    $consulta = new Consultas();
    require_once '../modelos/Persona.php';

    $persona = new Persona();

    $idimpresion=isset($_POST["idimpresion"])? $_POST["idimpresion"]:"";
    $entregado=isset($_POST["entregado"])?$_POST["entregado"]:"0";

    switch($_GET["op"])
    {

        case 'consultadocumento':
			$idsector = $_GET['idsector'];
            $rspta = $consulta->consultadocumento($idsector);
            $data = Array();

            while ($reg = $rspta->fetch_object()) {
                $data[] = array(
                    "0"=>$reg->sector,
                    "1"=>$reg->nombre,
                    "2"=>$reg->descripcion,
                    "3"=>$reg->vigencia,
                    "4"=>($reg->condicion == '1') ?
						 '<button class="btn btn-warning" onclick="mostrar('.$reg->iddocumento.')"><li class="fa fa-eye"></li></button></a>'
						 //'<a target="_blank" href="'.$reg->carpeta.$reg->fuente.'" <button class="btn btn-success" ><li class="fa fa-eye"></li></button></a>'
						 //'<span class="label bg-green">Aceptado</span>'
                         :      
                         '<span class="label bg-red"></span>'
                );
            }
            $results = array(
                "sEcho"=>1, //Informacion para el datable
                "iTotalRecords" =>count($data), //enviamos el total de registros al datatable
                "iTotalDisplayRecords" => count($data), //enviamos el total de registros a visualizar
                "aaData" =>$data
            );
            echo json_encode($results);
        break;
        case 'consultadocumentoExcel':
			$idsector = $_GET['idsector'];
            $rspta = $consulta->consultadocumentoExcel($idsector);
            $data = Array();

            while ($reg = $rspta->fetch_object()) {
                $data[] = array(
                    "0"=>$reg->sector,
                    "1"=>$reg->nombre,
                    "2"=>$reg->descripcion,
                    "3"=>$reg->vigencia,
                    "4"=>'Activo'
                );
            }
            $results = array(
                "sEcho"=>1, //Informacion para el datable
                "iTotalRecords" =>count($data), //enviamos el total de registros al datatable
                "iTotalDisplayRecords" => count($data), //enviamos el total de registros a visualizar
                "aaData" =>$data
            );
            echo json_encode($results);
        break;
        case 'consultaimpresiones':
			 $iddocumento = $_GET['iddocumento'];
			//  $num_revision= $_GET['num_revision'];
            $rspta = $consulta->consultaimpresiones($iddocumento);
            $data = Array();
            $cont = 0;

            while ($reg = $rspta->fetch_object()) {
                $data[] = array(
                    "0"=>$reg->usuario.'<input  name="idimpresion[]" type="hidden" value="'.$reg->idimpresion.'" >',
                    "1"=>$reg->documento,
                    "2"=>$reg->fecha_vigencia,
                    "3"=>$reg->num_revision,
                    "4"=>($reg->vigente == '1') ? 'Sí': 'No',
                    "5"=>$reg->impresiones,
                    "6"=>'<button class="btn btn-success" onclick="listarEntregas('.$reg->idimpresion.')"><i class="fa fa-eye"></i></button>'
                );
                $cont++;
            }
            $results = array(
                "sEcho"=>1, //Informacion para el datable
                "iTotalRecords" =>count($data), //enviamos el total de registros al datatable
                "iTotalDisplayRecords" => count($data), //enviamos el total de registros a visualizar
                "aaData" =>$data
            );
            echo json_encode($results);
        break;
        case 'consultaentregas':
			 $idimpresion = $_GET['idimpresion'];
            $rspta = $consulta->consultaentregas($idimpresion);
            $data = Array();
            $cont = 0;

            while ($reg = $rspta->fetch_object()) {
                $data[] = array(
                    "0"=>'<button class="btn btn-danger" onclick="deleteDetalle('.$reg->identrega.')"><i class="fa fa-close"></i>
                    </button>',
                    "1"=>$cont+1
                    .'<input  id="cont'.$cont.'" type="hidden" value="'.($cont+1).'" >',
                    "2"=>$reg->nombre
                    .'<input  name="identrega[]" type="hidden" value="'.$reg->identrega.'" >'
                    .'<input  name="idimpresion[]" type="hidden" value="'.$reg->idimpresion.'" >',
                    "3"=>($reg->entregado == 1)
                    ?'<input  name="entregado[]" id="entregado'.$cont.'" type="hidden" value="'.$reg->entregado.'" >'
                    .'<input type="checkbox" id="entregadoCheck" onclick="checkHandler('.$cont.',$(this))" style="width:20px;height:20px;"  checked>'
                    :'<input  name="entregado[]" id="entregado'.$cont.'" type="hidden" value="'.$reg->identrega.'" >'
                    .'<input type="checkbox" id="entregadoCheck" onclick="checkHandler('.$cont.',$(this))" style="width:20px;height:20px;"  >'
                    ,
                );
                $cont++;
            }
            $results = array(
                "sEcho"=>1, //Informacion para el datable
                "iTotalRecords" =>count($data), //enviamos el total de registros al datatable
                "iTotalDisplayRecords" => count($data), //enviamos el total de registros a visualizar
                "aaData" =>$data
            );
            echo json_encode($results);
        break;
		
		case 'listarDetalle':
		//Recibimos el iddocumento
		$id=$_GET['id'];
        

		$rspta = $consulta->listarDetalle($id);
		echo '<thead style="background-color:#A9D0F5">
                                    <th>Opciones</th>
                                    <th>Codigo</th>
									<th>Descripcion</th>
									<th>Fecha de Vigencia</th>
									<th>Fecha</th>
                                </thead>';

		while ($reg = $rspta->fetch_object())
				{
					if($reg->ruta == ''){
						echo '<tr class="filas"><td><td>'.$reg->nombre.'</td><td>'.htmlspecialchars($reg->nombre).'</td><td>'.htmlspecialchars($reg->descripcion).'</td>';
					}else{
                        //if($reg->idtipo_documento > 1){
						echo ($reg->idtipo_documento == 1)?
                        '<tr class="filas" style="background-color:LemonChiffon; border: black 5px">

                        <td><a target="_blank" href="visorpdf.php?file='.$reg->ruta.'" <button class="btn btn-success" >
                        <li class="fa fa-eye"></li></button></a></td>
                        <td>'.$reg->nombre.'</td>
                        <td>'.htmlspecialchars($reg->descripcion).'</td>'
                        .'<td>'.htmlspecialchars($reg->vigencia).'</td>'
                        .'<td>'.$reg->fecha.'</td></tr>'
                        :'<tr class="filas"><td><a target="_blank" href="visorpdf.php?file='.$reg->ruta.'" <button class="btn btn-success" >
                        <li class="fa fa-eye"></li></button></a></td>
                        <td>'.$reg->nombre.'</td>
                        <td>'.htmlspecialchars($reg->descripcion).'</td>'
                        .'<td>'.htmlspecialchars($reg->vigencia).'</td>'
                        .'<td>'.$reg->fecha.'</td></tr>';
                       /* }
                        else{
                          echo  '<tr class="filas"><td><a target="_blank" href="'.$reg->ruta.'" <button class="btn btn-success" ><li class="fa fa-eye"></li></button></a></td><td>'.$reg->nombre.'</td><td>'.htmlspecialchars($reg->nombre).'</td><td>'.htmlspecialchars($reg->descripcion).'</td>';
                        }*/
					}
						
					
					//echo '<tr class="filas"><td></td><td>'.$reg->nombre.'</td><td>'.htmlspecialchars($reg->nombre).'</td><td>'.htmlspecialchars($reg->descripcion).'</td>';
				}
		;
	break;

    case 'selectPersona':
        $rspta=$persona->listarPA();
        while($reg = $rspta->fetch_object())
					{
						echo '<option value='.$reg->idpersona.'>'
                        .$reg->nombre.
						'</option>';
            }
    break;

    
    }

?>