<?php

include_once '../clases/BnGeneral.php';

		$producto = $_GET["producto"];
		$anno = $_GET["anno"];
		$stock = $_GET["stock"];
		$precio = $_GET["precio"];
		$Tipo = $_GET["Tipo"];

		//echo "dadasd".$anno;
		$result = devolverKardexValorizado($producto, $anno, $stock, $precio, $Tipo);
		$data = array();
		while ($rows = mysql_fetch_assoc($result)) {
				$data[] = $rows;
			}

	echo json_encode($data);
	return true;

 ?>
