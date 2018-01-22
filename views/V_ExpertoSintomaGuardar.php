<?php

include_once '../clases/DtGeneral.php';

$dataSintoma = $_POST["Sintoma"];
$dataIdSintoma = $_POST["IdSintoma"];


if (empty($dataIdSintoma)) {
	fn_guardarSintoma($dataSintoma);
	echo "Sintoma agregado";
}else {
	fn_modificarSintoma($dataIdSintoma, $dataSintoma);
	echo "Sintoma modificado";
}


 ?>
