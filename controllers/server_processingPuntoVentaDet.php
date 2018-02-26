<?php

include_once '../clases/BnGeneral.php';

   $result = fn_devolverPuntoVentaSerie($_GET['idPuntoVenta'], $_GET['idTipoDoc']);

	$data = array();

	while ($rows = mysqli_fetch_assoc($result)) {
		$data = $rows;
	}

	echo json_encode($data);

 ?>
