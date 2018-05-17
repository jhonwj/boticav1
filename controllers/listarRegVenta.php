<?php 

include_once '../clases/BnGeneral.php';

		$fechaIni = $_GET["fechaIni"];
		$fechaFin = $_GET["fechaFin"];
		$declarado = $_GET["declarado"];

		if($declarado){
			$declarado = 1;
		}else{		
			$declarado = 0;
		}

		$result = ListarRegVenta($fechaIni, $fechaFin, $declarado);
		$data = array();

	while ($rows = mysqli_fetch_assoc($result)) {
		$cliente = obtenerClienteVenta($rows['idDocVenta']);
		$cliente = mysqli_fetch_assoc($cliente);

		$rows = array_merge($rows, $cliente);
		
		if(isset($_GET['codSunat'])) {
			if($rows['CodSunat'] == $_GET['codSunat']) {
				$data[] = $rows;		
			}
		} else {
			$data[] = $rows;
		}
	}

	if(isset($_GET['datatable'])) {
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($data),
			"iTotalDisplayRecords" => count($data),
			"aaData" => $data);
	   
		   echo json_encode($results);
		   return true;
	}

	echo json_encode($data);
	return true;

 ?>