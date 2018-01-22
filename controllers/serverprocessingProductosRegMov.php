<?php 

	include_once '/../clases/BnGeneral.php';

	$idMov = $_GET["idMov"];

	$result = devolverProductosRegMov($idMov);

	$data = array();

	while ($rows = mysql_fetch_assoc($result)) {
		$data[] = $rows;
	}

	echo json_encode($data);
 ?>