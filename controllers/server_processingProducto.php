<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
	include_once '../clases/BnGeneral.php';

   $result = fn_devolverProducto("","");

	$data = array();

	while ($rows = mysql_fetch_assoc($result)) {
		$data[] = $rows;
	}

	$results = array(
	 "sEcho" => 1,
	 "iTotalRecords" => count($data),
	 "iTotalDisplayRecords" => count($data),
	 "aaData" => $data);
echo "string";
	echo json_encode($results);

 ?>
