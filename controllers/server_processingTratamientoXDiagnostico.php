<?php 

include_once '/../clases/BnGeneral.php';

	$diagnostico = $_GET["diagnostico"];
   $result = devolverTratamientoXDiagnostico($diagnostico);

	$data = array();

	while ($rows = mysql_fetch_assoc($result)) {
		$data[] = $rows;
	}

	echo json_encode($data);


 ?>