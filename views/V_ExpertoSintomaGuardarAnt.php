<?php 

include_once '../clases/DtGeneral.php';

$dataSintoma = $_POST["Sintoma"];
$dataEdad = $_POST["Edad"];


if (isset($dataSintoma) || isset($dataEdad)) {
	if (fn_guardarSintoma($dataSintoma, $dataEdad)) {
			echo true;
		}else{
			echo false;
		}
}

 ?>