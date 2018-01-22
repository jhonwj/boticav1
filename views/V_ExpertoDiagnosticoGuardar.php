<?php

include_once '../clases/DtGeneral.php';

$dataDiagnostico = json_decode($_POST["data"], true);
$dataTratamiento = json_decode($_POST["data2"], true);
$dataSintoma = json_decode($_POST["data3"], true);

if (empty($dataDiagnostico[4])) {
if (isset($dataDiagnostico)) {
	$result = fn_guardarDiagnostico($dataDiagnostico[0], $dataDiagnostico[1], $dataDiagnostico[2], $dataDiagnostico[3], "jeam");
	$row = mysql_fetch_assoc($result);
	$IdDiagnostico =  $row["IdDiagnostico"];
}
if (isset($dataTratamiento)) {
	foreach ($dataTratamiento as $key) {
		$result = fn_actualizarTratamientoD($key[0], $IdDiagnostico);
	}
}
if (isset($dataSintoma)) {
	foreach ($dataSintoma as $key) {
		$result = fn_actualizarSintomaD($IdDiagnostico, $key[0], "jeam");
	}
}

}else {
	if (isset($dataDiagnostico)) {
	fn_modificarDiagnostico($dataDiagnostico[4], $dataDiagnostico[0], $dataDiagnostico[1], $dataDiagnostico[2], $dataDiagnostico[3], "jeam");
	}
	if (sizeof($dataTratamiento)>0) {
		$Sql = "UPDATE botica.Ve_ExpertoTratamiento SET Ve_ExpertoTratamiento.IdDiagnostico = 0 WHERE Ve_ExpertoTratamiento.IdDiagnostico = ".$dataDiagnostico[4];
		$res = ejecutarSQLCommand($Sql);
	foreach ($dataTratamiento as $key) {
		$result = fn_actualizarTratamientoD($key[0], $dataDiagnostico[4]);
		}
	}else {
		echo "Entrooo";
		$Sql = "DELETE FROM botica.Ve_ExpertoTratamiento WHERE Ve_ExpertoTratamiento.IdDiagnostico = ".$dataDiagnostico[4];
		$res = ejecutarSQLCommand($Sql);
	}
	if (isset($dataSintoma)) {
	$Sql = "DELETE FROM botica.Ve_ExpertoDiagnosticoSintomaDet WHERE Ve_ExpertoDiagnosticoSintomaDet.IdDiagnostico = ".$dataDiagnostico[4];
	$res = ejecutarSQLCommand($Sql);
	foreach ($dataSintoma as $key) {
		$result = fn_actualizarSintomaD($dataDiagnostico[4], $key[0], "jeam");
		}
	}
}


 ?>
