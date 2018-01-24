<?php

include_once '../clases/DtGeneral.php';
header('Content-type: application/json');

$dataTratamiento = json_decode($_POST["data"], true);


if ($dataTratamiento[0] == 0) {
	$result = fn_guardarTratamiento($dataTratamiento[0], $dataTratamiento[1], $dataTratamiento[2], $dataTratamiento[3], $dataTratamiento[4], $dataTratamiento[5], $dataTratamiento[6], $dataTratamiento[7], $dataTratamiento[8]);
	$data = array();

	while ($rows = mysqli_fetch_assoc($result)) {
		$data = $rows;
	}
	echo json_encode($data);
}else {
	$result = fn_guardarTratamiento($dataTratamiento[0], $dataTratamiento[1], $dataTratamiento[2], $dataTratamiento[3], $dataTratamiento[4], $dataTratamiento[5], $dataTratamiento[6], $dataTratamiento[7], $dataTratamiento[8]);
	echo json_encode("");
}

 ?>
