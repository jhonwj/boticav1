<?php 

	include_once '/../clases/BnGeneral.php';

	$almacen = $_GET["almacen"];

   $result = ListarReporteStock($almacen,"");

	$data = array();

	while ($rows = mysql_fetch_assoc($result)) {
		$data[] = $rows;
	}

	$results = array(
	 "sEcho" => 1,
	 "iTotalRecords" => count($data),
	 "iTotalDisplayRecords" => count($data),
	 "aaData" => $data);

	echo json_encode($results);

 ?>