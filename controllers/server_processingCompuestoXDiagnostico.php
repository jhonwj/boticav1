<?php 

include_once '../clases/BnGeneral.php';

	$diagnostico = $_GET["diagnostico"];
   $result = devolverCompuestoXDiagnostico($diagnostico);

	$data = array();

	while ($rows = mysqli_fetch_assoc($result)) {
		$data[] = $rows;
	}

	echo json_encode($data);


 ?>