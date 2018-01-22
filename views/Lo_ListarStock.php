<?php

include_once '../clases/BnGeneral.php';

		$producto = $_GET["producto"];
		$fechaIni = $_GET["fechaIni"];
		$fechaFin = $_GET["fechaFin"];
		$Tipo = $_GET["Tipo"];

		$result = ListarReporteKardex($producto, $fechaIni, $fechaFin, $Tipo);
		$data = array();
		while ($rows = mysql_fetch_assoc($result)) {
				$data[] = $rows;
			}

	echo json_encode($data);
	return true;

 ?>
