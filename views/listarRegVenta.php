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
				$data[] = $rows;
			}

	echo json_encode($data);
	return true;

 ?>