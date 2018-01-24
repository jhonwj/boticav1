<?php
include_once("../clases/DtGeneral.php");
include_once("../clases/BnGeneral.php");
//header( 'Content-Type: ;charset=utf-8' );

$cabecerajson = json_decode($_POST['data']);
$tablejson = json_decode($_POST['data2']);
$tableMetodoPagojson = json_decode($_POST['data3']);

if(isset($cabecerajson)){
	//echo false;
	//echo '<script type="text/javascript">console.log("Datos incorrectos! SI'.$tablejson[0].$tablejson[1].$tablejson[2].$tablejson[3].'");</script>';
	//echo "-".$tablejson[0]."-".$tablejson[1]."-".$tablejson[2]."-".$tablejson[3]."-".;
	$Escredito = 0;
	$FechaCredito = "";
	if ($cabecerajson[4]) {
		$Escredito = 1;
		$FechaCredito = $cabecerajson[5];
	}
	$res = "";
	$res = fn_guardarDocVenta("CAJA1", $cabecerajson[1], $cabecerajson[0], $cabecerajson[2], "jeam", $Escredito, $FechaCredito);
	//echo '<script type="text/javascript">console.log("Datos incorrectos! NO'.$res.'");</script>';
			foreach ($tablejson as $key) {
				fn_guardarDocVentaDet($res, $key[0], $key[2], $key[3]);
			}
			foreach ($tableMetodoPagojson as $key) {
				fn_guardarDocVentaMetodoPagoDet($res, $key[0], $key[2], $key[1]);
			}
			echo $res;
}else{
	//printf('<script type="text/javascript">console.log("Datos incorrectos! NO");</script>');
	echo false;
	//echo '<script type="text/javascript">console.log("Datos incorrectos! NO'.$tablejson[0].'");</script>';
}



 ?>
