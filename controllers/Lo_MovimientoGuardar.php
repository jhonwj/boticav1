<?php
include_once("../clases/DtGeneral.php");
include_once("../clases/BnGeneral.php");
header( 'Content-Type: text/html;charset=utf-8' );

$movimiento = json_decode($_POST["movimiento"], true);
$producto = json_decode($_POST["producto"], true);

$remision = json_decode($_POST["remision"], true);
var_dump($remision);exit();
//if (empty($producto[0]["value"])) {
// echo $movimiento[0][0];
// exit();

$fecha = $movimiento[0][11];
$EsCredito =1;
if(!$movimiento[0][10]){
	$EsCredito = 0;
	$fecha = null;
}
$VerificarMovimiento = VerificarMovimiento($movimiento[0][0], $movimiento[0][1], $movimiento[0][2], $movimiento[0][3]);
$row = mysqli_fetch_array($VerificarMovimiento);
if ($row[0]>=1) {
	echo "E";
}else{
	$result = fn_guardarMovimiento($movimiento[0][0], $movimiento[0][1], $movimiento[0][2], $movimiento[0][3], $movimiento[0][4], $movimiento[0][5], $movimiento[0][6], $movimiento[0][7], $movimiento[0][8], $movimiento[0][9], $EsCredito, $fecha,  $movimiento[0][12], floatval($movimiento[0][13]), $movimiento[0][14], $remision['PartidaDist'], $remision['PartidaProv'], $remision['PartidaDpto'], $remision['LlegadaDist'], $remision['LlegadaProv'], $remision['LlegadaDpto'], $remision['DestinatarioRazonSocial'], $remision['DestinatarioRUC'], $remision['TransporteNumPlaca'], $remision['TransporteNumContrato'], $remision['TransporteNumLicencia'], $remision['TransporteRazonSocial'], $remision['TransporteRUC'], $remision['IdDocVenta'] );
	
	foreach ($producto as $key) {
		$tieneIgv = 0;
		if ($key[4]) {
			$tieneIgv = 1;
		}
		fn_guardarMovimientoDet($result, $key[1], $key[2], $key[3], $tieneIgv, $key[5], $key[6], $key[7], $key[8]);
	}
	echo "OK" ;
}


//}
/*else{
	$res = fn_modificarProducto($producto[0]["value"], $producto[2]["value"], $producto[4]["value"], $producto[6]["value"], $producto[8]["value"], $producto[9]["value"], $producto[10]["value"], $producto[11]["value"], $producto[12]["value"], $producto[13]["value"], $producto[14]["value"],$producto[15]["value"],$producto[16]["value"],$producto[17]["value"] , $producto[18]["value"]);
	$Ssql = "DELETE FROM Gen_ProductoCompuestoDet WHERE Gen_ProductoCompuestoDet.Gen_Producto_IdProducto =".$producto[0]["value"];
	$Ssql2 = "DELETE FROM Gen_ProductoDet WHERE Gen_ProductoDet.IdProducto =".$producto[0]["value"];
	if(!ejecutarSQLCommand($Ssql) || !ejecutarSQLCommand($Ssql2)){
		foreach ($CompuestosJson as $key) {
		fn_guardarProductoCompuestoDet($key[0], $producto[0]["value"]);
		}
		foreach ($productoDet as $key) {
		fn_guardarProductoDet($producto[0]["value"], $key[0], $key[2]);
		}
		}
}*/
 ?>
