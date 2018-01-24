<?php 

include_once '../clases/BnGeneral.php';

	$diagnostico = $_GET["diagnostico"];
	$edad = $_GET["edad"];
   $result = fn_devolverDiagnosticoXTratamiento($diagnostico, $edad);

	$data = array();

	while ($rows = mysqli_fetch_assoc($result)) {
		$data[] = $rows;
	}

	echo json_encode($data);


 ?>