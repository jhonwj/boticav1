<?php 

include_once '/../clases/BnGeneral.php';

	$edad = $_POST["edad"];
   $result = fn_devolverSintomas("");

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