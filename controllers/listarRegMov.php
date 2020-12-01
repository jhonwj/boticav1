<?php 

include_once '../clases/BnGeneral.php';

		$fechaIni = $_GET["fechaIni"];
		$fechaFin = $_GET["fechaFin"];
		$declarado = $_GET["declarado"];
		$descripcion = $_GET["descripcion"];
		//echo "adasdd   ".$declarado;

		if($declarado == "true"){
			$declarado = 1;
		}else{		
			$declarado = 0;
		}

		$result = ListarRegNov($fechaIni, $fechaFin, $declarado, $descripcion);
		$data = array();
		while ($rows = mysqli_fetch_assoc($result)) {
				$data[] = $rows;
			}

	echo json_encode($data);
	return true;

 ?>