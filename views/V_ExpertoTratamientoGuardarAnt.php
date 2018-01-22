<?php 

include_once '../clases/DtGeneral.php';
header('Content-type: application/json');

$dataTratamiento = json_decode($_POST["data"], true);


if (isset($dataTratamiento)) {
	$result = fn_guardarTratamiento($dataTratamiento[0], $dataTratamiento[1], $dataTratamiento[2], $dataTratamiento[3], $dataTratamiento[4], $dataTratamiento[5], $dataTratamiento[6]);
	$data = array();

	while ($rows = mysql_fetch_assoc($result)) {
		$data = $rows;
	}
	echo json_encode($data);
}

 ?>