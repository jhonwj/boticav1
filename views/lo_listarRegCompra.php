<?php

include_once '../clases/BnGeneral.php';

		$periodoT = $_GET["periodoT"];
		$declarado = $_GET["declarado"];
		//echo "adasdd   ".$declarado;

		if($declarado == "true"){
			$declarado = 1;
		}else{
			$declarado = 0;
		}

		$result = ListarRegCompraContable($periodoT, $declarado);
		$data = array();
		while ($rows = mysqli_fetch_assoc($result)) {
				$data[] = $rows;
			}

	echo json_encode($data);
	return true;

 ?>
